<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class DtesFiltradosExport implements
    FromQuery,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithMapping
{
    protected array $filters;
    protected array $tiposDte;

    public function __construct(array $filters)
    {
        $this->filters = $filters;

        $this->tiposDte = [
            '01' => 'Factura Electrónica',
            '03' => 'Crédito Fiscal',
            '04' => 'Nota de Remisión',
            '05' => 'Nota de Crédito',
            '07' => 'Comprobante de Retención',
            '11' => 'Factura de Exportación',
            '14' => 'Factura de Sujeto Excluido',
        ];
    }

    /**
     * Query principal (SIN JSON, SIN funciones)
     */
    public function query()
    {
        return DB::connection('pgsql')->table('v_dtes')
            ->select([
                'fh_procesamiento',
                'tienda',
                'transaccion',
                'documento_receptor',
                'nombre_receptor',
                'neto',
                'iva',
                'total',
                'tipo_dte',
                'estado',
                'observaciones',
                'cod_generacion',
                'numero_control',
                'sello_recibido',
            ])
            ->when(
                $this->filters['fecha_inicio'],
                fn($q) =>
                $q->where('fh_procesamiento', '>=', $this->filters['fecha_inicio'])
            )
            ->when(
                $this->filters['fecha_fin'],
                fn($q) =>
                $q->where('fh_procesamiento', '<=', $this->filters['fecha_fin'])
            )
            ->when(
                $this->filters['estado'],
                fn($q) =>
                $q->where('estado', $this->filters['estado'])
            )
            ->when(
                $this->filters['tienda'],
                fn($q) =>
                $q->where('tienda', 'ILIKE', "%{$this->filters['tienda']}%")
            )
            ->when(
                $this->filters['transaccion'],
                fn($q) =>
                $q->where('transaccion', 'ILIKE', "%{$this->filters['transaccion']}%")
            )
            ->orderBy('fh_procesamiento');
    }


    /**
     * Mapeo de datos para formatear la fecha
     */
    public function map($row): array
    {
        return [
            $row->fh_procesamiento ? Carbon::parse($row->fh_procesamiento)->timezone('America/El_Salvador')->format('d/m/Y H:i:s') : '',
            $row->tienda,
            $row->transaccion,
            $row->documento_receptor,
            $row->nombre_receptor,
            $row->neto,
            $row->iva,
            $row->total,
            $this->tiposDte[$row->tipo_dte] ?? $row->tipo_dte,
            $row->estado,
            $row->observaciones,
            $row->cod_generacion,
            $row->numero_control,
            $row->sello_recibido,
        ];
    }

    /**
     * Encabezados
     */
    public function headings(): array
    {
        return [
            'Fecha',
            'Tienda',
            'Transacción',
            'Documento Receptor',
            'Nombre Receptor',
            'Neto',
            'IVA',
            'Total',
            'Tipo DTE',
            'Estado',
            'Observación',
            'Código Generación',
            'Número de Control',
            'Sello de Recepción',
        ];
    }


    /**
     * Estilos
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
            ],
        ];
    }
}
