<?php

namespace App\Livewire\Business\Tables;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\DtesFiltradosExport;
use App\Models\Tienda;
use Maatwebsite\Excel\Facades\Excel;

class DtesFiltradosTable extends Component
{
    use WithPagination;

    // Filtros
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;
    public $tienda;
    public $documento_receptor;
    public $nombre_receptor;
    public $cod_generacion;
    public $sello_recibido;
    public $numero_control;
    public $total_min;
    public $total_max;
    public $transaccion;

    public $perPage = 10;
    public $page = 1;
    public $totalRecords = 0;
    public $totalFiltered = 0;

    public $tiposDte = [
        '01' => 'Factura Electrónica',
        '03' => 'Crédito Fiscal',
        '04' => 'Nota de Remisión',
        '05' => 'Nota de Crédito',
        '07' => 'Comprobante de Retención',
        '11' => 'Factura de Exportación',
        '14' => 'Factura de Sujeto Excluido',
    ];

    protected $queryString = [
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'tienda',
        'documento_receptor',
        'nombre_receptor',
        'cod_generacion',
        'sello_recibido',
        'numero_control',
        'total_min',
        'total_max',
        'transaccion',
        'perPage',
        'page'
    ];

    public function updating($name)
    {
        $this->resetPage();
    }

    public function nextPage()
    {
        if ($this->page * $this->perPage < $this->totalFiltered) {
            $this->page++;
        }
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function hasNextPage()
    {
        return $this->page * $this->perPage < $this->totalFiltered;
    }

    public function hasPreviousPage()
    {
        return $this->page > 1;
    }

    public function getFromRecord()
    {
        return (($this->page - 1) * $this->perPage) + 1;
    }

    public function getToRecord()
    {
        return min($this->page * $this->perPage, $this->totalFiltered);
    }

    protected function baseQuery()
    {
        return DB::connection('pgsql')->table('v_dtes')
            ->when(
                $this->fecha_inicio,
                fn($q) =>
                $q->where(
                    'fh_procesamiento',
                    '>=',
                    date('Y-m-d H:i:s', strtotime($this->fecha_inicio . 'T00:00:00 +6 hours'))
                )
            )
            ->when(
                $this->fecha_fin,
                fn($q) =>
                $q->where(
                    'fh_procesamiento',
                    '<=',
                    date('Y-m-d H:i:s', strtotime($this->fecha_fin . 'T23:59:59 +6 hours'))
                )
            )
            ->when(
                $this->estado && $this->estado !== 'TODOS',
                fn($q) =>
                $q->where('estado', $this->estado)
            )
            ->when(
                $this->tienda,
                fn($q) =>
                $q->where('tienda', 'ILIKE', "%{$this->tienda}%")
            )
            ->when(
                $this->transaccion,
                fn($q) =>
                $q->where('transaccion', 'ILIKE', "%{$this->transaccion}%")
            )
            ->when(
                $this->documento_receptor,
                fn($q) =>
                $q->where('documento_receptor', 'ILIKE', "%{$this->documento_receptor}%")
            )
            ->when(
                $this->nombre_receptor,
                fn($q) =>
                $q->where('nombre_receptor', 'ILIKE', "%{$this->nombre_receptor}%")
            )
            ->when(
                $this->cod_generacion,
                fn($q) =>
                $q->where('cod_generacion', 'ILIKE', "%{$this->cod_generacion}%")
            )
            ->when(
                $this->sello_recibido,
                fn($q) =>
                $q->where('sello_recibido', 'ILIKE', "%{$this->sello_recibido}%")
            )
            ->when(
                $this->numero_control,
                fn($q) =>
                $q->where('numero_control', 'ILIKE', "%{$this->numero_control}%")
            )
            ->when(
                $this->total_min,
                fn($q) =>
                $q->where('total_numeric', '>=', $this->total_min)
            )
            ->when(
                $this->total_max,
                fn($q) =>
                $q->where('total_numeric', '<=', $this->total_max)
            );
    }

    public function render()
    {
        $query = $this->baseQuery();

        // Conteo filtrado (rápido con índices)
        $this->totalFiltered = (clone $query)->count();

        // Total general (cacheable si quieres)
        $this->totalRecords = DB::connection('pgsql')->table('v_dtes')->count();

        // Datos paginados
        $dtes = $query
            ->orderByDesc('fh_procesamiento')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        // Tiendas desde columna normalizada
        // $tiendas = DB::connection('pgsql')->table('v_dtes')
        //     ->select('tienda')
        //     ->whereNotNull('tienda')
        //     ->distinct()
        //     ->orderBy('tienda')
        //     ->get();
        $tiendas = Tienda::orderBy('codigo')->get();

        return view('livewire.business.tables.dtes-filtrados-table', [
            'dtes' => $dtes,
            'tiendas' => $tiendas,
            'fromRecord' => $dtes->firstItem(),
            'toRecord' => $dtes->lastItem(),
            'totalRecords' => $this->totalRecords,
            'totalFiltered' => $this->totalFiltered,
        ]);
    }


    public function resetFilters()
    {
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->estado = '';
        $this->tienda = '';
        $this->documento_receptor = '';
        $this->nombre_receptor = '';
        $this->cod_generacion = '';
        $this->sello_recibido = '';
        $this->numero_control = '';
        $this->total_min = '';
        $this->total_max = '';

        $this->page = 1;
        $this->resetPage();
    }

    public function exportToExcel()
    {
        $filters = [
            'fecha_inicio' => $this->fecha_inicio
                ? date('Y-m-d H:i:s', strtotime($this->fecha_inicio . 'T00:00:00 +6 hours'))
                : null,
            'fecha_fin' => $this->fecha_fin
                ? date('Y-m-d H:i:s', strtotime($this->fecha_fin . 'T23:59:59 +6 hours'))
                : null,
            'estado' => $this->estado !== 'TODOS' ? $this->estado : null,
            'tienda' => $this->tienda,
            'transaccion' => $this->transaccion,
            'documento_receptor' => $this->documento_receptor,
            'nombre_receptor' => $this->nombre_receptor,
            'total_min' => $this->total_min,
            'total_max' => $this->total_max,
        ];

        return Excel::download(
            new DtesFiltradosExport($filters),
            'Reporte_DTEs_' . now()->format('Ymd_His') . '.xlsx'
        );
    }


}
