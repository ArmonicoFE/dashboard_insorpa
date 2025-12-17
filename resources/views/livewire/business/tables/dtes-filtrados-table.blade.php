<div>
    <div class="row mb-3">
        <fieldset class="col-md-12 border rounded">
            <legend class="float-none w-auto px-2 fs-6 fw-bold">Filtros de Búsqueda</legend>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="fecha_inicio" class="form-label">Desde:</label>
                    <input type="date" wire:model.live.debounce.500ms="fecha_inicio" class="form-control"
                        placeholder="Desde">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_fin" class="form-label">Hasta:</label>
                    <input type="date" wire:model.live.debounce.500ms="fecha_fin" class="form-control"
                        placeholder="Hasta">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tienda" class="form-label">Tienda:</label>
                    <select wire:model.live.debounce.500ms="tienda" class="form-select">
                        <option value="">Todas las tiendas</option>
                        @foreach ($tiendas as $tienda)
                            <option value="{{ $tienda->codigo }}">{{ $tienda->codigo }} - {{ $tienda->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="transaccion" class="form-label">Estado:</label>
                    <select wire:model.live.debounce.500ms="estado" class="form-select">
                        <option value="">TODOS</option>
                        <option value="PROCESADO">PROCESADO</option>
                        <option value="RECHAZADO">RECHAZADO</option>
                        <option value="ANULADO">ANULADO</option>
                        <option value="CONTINGENCIA">CONTINGENCIA</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="documento_receptor" class="form-label">Documento Receptor:</label>
                    <input type="text" wire:model.live.debounce.500ms="documento_receptor" class="form-control"
                        placeholder="Documento Receptor">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="nombre_receptor" class="form-label">Nombre Receptor:</label>
                    <input type="text" wire:model.live.debounce.500ms="nombre_receptor" class="form-control"
                        placeholder="Nombre Receptor">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="total_min" class="form-label">Monto Operación (Desde):</label>
                    <input type="number" wire:model.live.debounce.500ms="total_min" class="form-control"
                        placeholder="Total Mínimo" step="0.01" min="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="total_max" class="form-label">Monto Operación (Hasta):</label>
                    <input type="number" wire:model.live.debounce.500ms="total_max" class="form-control"
                        placeholder="Total Máximo" step="0.01" min="0">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="cod_generacion" class="form-label">Código Generación:</label>
                    <input type="text" wire:model.live.debounce.500ms="cod_generacion" class="form-control"
                        placeholder="Código Generación">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="sello_recibido" class="form-label">Sello Recibido:</label>
                    <input type="text" wire:model.live.debounce.500ms="sello_recibido" class="form-control"
                        placeholder="Sello Recibido">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="numero_control" class="form-label">Número de Control:</label>
                    <input type="text" wire:model.live.debounce.500ms="numero_control" class="form-control"
                        placeholder="Número de Control">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="transaccion" class="form-label">Transacción:</label>
                    <input type="text" wire:model.live.debounce.500ms="transaccion" class="form-control"
                        placeholder="Transacción">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <button wire:click="resetFilters" class="btn btn-primary me-2">Limpiar Filtros</button>
                    <button wire:click="exportToExcel" 
                            wire:loading.attr="disabled" 
                            wire:target="exportToExcel"
                            class="btn btn-success me-2">
                        <span wire:loading.remove wire:target="exportToExcel">
                            <i class="fas fa-file-excel me-1"></i>Exportar a Excel
                        </span>
                        <span wire:loading wire:target="exportToExcel">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Exportando...
                        </span>
                    </button>
                    @if($totalFiltered > 0)
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Se exportarán {{ number_format($totalFiltered) }} registros
                        </small>
                    @endif
                </div>
            </div>
        </fieldset>
    </div>

    <!-- Tabla -->
    <div class="position-relative">
        <!-- Overlay con Loader -->
        <div wire:loading.class="d-flex" wire:loading.class.remove="d-none" 
             wire:target="fecha_inicio,fecha_fin,estado,tienda,documento_receptor,nombre_receptor,cod_generacion,sello_recibido,numero_control,total_min,total_max,perPage,nextPage,previousPage,resetFilters,exportToExcel,transaccion"
             class="position-absolute w-100 h-100 d-none justify-content-center align-items-center" 
             style="background-color: rgba(255, 255, 255, 0.9); z-index: 1000; top: 0; left: 0; min-height: 400px;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-2">
                    <strong class="text-primary">
                        <span wire:loading wire:target="exportToExcel">Generando archivo Excel...</span>
                        <span wire:loading wire:target="fecha_inicio,fecha_fin,estado,tienda,documento_receptor,nombre_receptor,cod_generacion,sello_recibido,numero_control,total_min,total_max,perPage,nextPage,previousPage,resetFilters,transaccion">Cargando datos...</span>
                    </strong>
                </div>
            </div>
        </div>

        <table class="small table table-bordered table-hover table-striped w-100 align-middle">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tienda</th>
                    <th>Transacción</th>
                    <th>Receptor</th>
                    <th>Importe</th>
                    <th>IVA</th>
                    <th>Total</th>
                    <th>Tipo DTE</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Cod. Generación.</th>
                    <th>Número de Control</th>
                    <th>Sello de Recepción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dtes as $dte)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($dte->fh_procesamiento)->setTimezone('America/El_Salvador')->format('Y-m-d H:i:s') }}
                        </td>
                        <td>{{ $dte->tienda }}</td>
                        <td>{{ $dte->transaccion }}</td>
                        <td>{{ $dte->documento_receptor }} <br> {{ $dte->nombre_receptor }}</td>
                        <td>${{ number_format($dte->neto, 2, '.', ',') }}</td>
                        <td>${{ number_format($dte->iva, 2, '.', ',') }}</td>
                        <td>${{ number_format($dte->total, 2, '.', ',') }}</td>
                        <td>{{ $tiposDte[$dte->tipo_dte] ?? 'Desconocido' }}</td>
                        <td>{{ $dte->estado }}</td>
                        <td>{{ $dte->observaciones != '[]' ? $dte->observaciones : '' }}</td>
                        <td>{{ $dte->cod_generacion }}</td>
                        <td>{{ $dte->numero_control }}</td>
                        <td>
                            <p class="text-break">{{ $dte->sello_recibido }}</p>
                        </td>
                        <td>
                            @if ($dte->estado === 'CONTINGENCIA' || $dte->estado === 'RECHAZADO')
                            @else
                                <div class="d-inline-flex">
                                    <a href="{{ $dte->enlace_pdf }}"
                                        class="btn btn-sm btn-danger ms-1 d-flex align-items-center" target="_blank">PDF</a>
                                    <a href="{{ $dte->enlace_json }}"
                                        class="btn btn-sm btn-success ms-1 d-flex align-items-center"
                                        target="_blank">JSON</a>
                                    <a href="{{ $dte->enlace_ticket }}"
                                        class="btn btn-sm btn-warning ms-1 d-flex align-items-center"
                                        target="_blank">Tiquete</a>
                                    <button type="button"
                                        class="btn btn-primary btn-sm ms-1 btn-modal d-flex align-items-center"
                                        data-bs-toggle="modal" data-bs-target="#mailModal"
                                        data-id="{{ $dte->cod_generacion }}">
                                        Reenviar Correo
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <label>Mostrar
                <select wire:model.live.debounce.500ms="perPage"
                    class="form-select form-select-sm w-auto d-inline-block">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                registros por página
            </label>
        </div>
        
        <!-- Información de paginación -->
        <div class="text-muted">
            @if($totalFiltered > 0)
                Mostrando del {{ $fromRecord }} al {{ $toRecord }} de {{ $totalFiltered }} registros
                @if($totalFiltered != $totalRecords)
                    (filtrado de {{ $totalRecords }})
                @endif
            @else
                No hay registros para mostrar
            @endif
        </div>
        
        <!-- Botones de navegación -->
        <div>
            <button wire:click="previousPage" 
                    class="btn btn-sm btn-outline-primary me-2" 
                    @if(!$this->hasPreviousPage()) disabled @endif>
                &laquo; Anterior
            </button>
            
            <span class="me-2">
                Página {{ $this->page }} de {{ $totalFiltered > 0 ? ceil($totalFiltered / $perPage) : 1 }}
            </span>
            
            <button wire:click="nextPage" 
                    class="btn btn-sm btn-outline-primary" 
                    @if(!$this->hasNextPage()) disabled @endif>
                Siguiente &raquo;
            </button>
        </div>
    </div>
</div>
