@extends ('layouts.admin')

@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Audit Trail</h3>
    </div>
</div>

<form action="{{ route('audit_logs.index') }}" method="GET" autocomplete="off">
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="date" id="desde" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="evento">Evento</label>
                <select id="evento" name="evento" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($eventos as $clave => $nombre)
                        <option value="{{ $clave }}" {{ ($filtros['evento'] ?? '') === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="modelo">Modulo</label>
                <select id="modelo" name="modelo" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($modelos as $clase => $nombre)
                        <option value="{{ $clase }}" {{ ($filtros['modelo'] ?? '') === $clase ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="user_id">Usuario</label>
                <select id="user_id" name="user_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ (string) ($filtros['user_id'] ?? '') === (string) $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }} - {{ $usuario->email }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="registro">Registro</label>
                <input type="number" id="registro" name="registro" class="form-control" value="{{ $filtros['registro'] ?? '' }}" placeholder="ID">
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="ip">IP</label>
                <input type="text" id="ip" name="ip" class="form-control" value="{{ $filtros['ip'] ?? '' }}" placeholder="IP">
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Buscar</button>
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label>&nbsp;</label>
                <a href="{{ route('audit_logs.index') }}" class="btn btn-default btn-block">Limpiar</a>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Evento</th>
                        <th>Modulo</th>
                        <th>Registro</th>
                        <th>Usuario</th>
                        <th>IP</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditorias as $auditoria)
                        @php
                            $modelo = $modelos[$auditoria->auditable_type] ?? class_basename($auditoria->auditable_type);
                            $modalId = 'audit-detail-' . $auditoria->id;
                        @endphp
                        <tr>
                            <td>{{ optional($auditoria->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if ($auditoria->event === 'CREATED')
                                    <span class="label label-success">Creacion</span>
                                @elseif ($auditoria->event === 'ANULACION')
                                    <span class="label label-danger">Anulacion</span>
                                @else
                                    <span class="label label-info">Actualizacion</span>
                                @endif
                            </td>
                            <td>{{ $modelo }}</td>
                            <td>{{ $auditoria->auditable_id }}</td>
                            <td>{{ optional($auditoria->user)->name ?? 'Sistema' }}</td>
                            <td>{{ $auditoria->ip_address ?? 'Sin IP' }}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#{{ $modalId }}">
                                    Ver
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">Detalle de Auditoria #{{ $auditoria->id }}</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Evento:</strong> {{ $eventos[$auditoria->event] ?? $auditoria->event }}</p>
                                                <p><strong>Modulo:</strong> {{ $modelo }}</p>
                                                <p><strong>Registro:</strong> {{ $auditoria->auditable_id }}</p>
                                                <p><strong>Usuario:</strong> {{ optional($auditoria->user)->name ?? 'Sistema' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>IP:</strong> {{ $auditoria->ip_address ?? 'Sin IP' }}</p>
                                                <p><strong>Metodo:</strong> {{ $auditoria->request_method ?? 'Sin dato' }}</p>
                                                <p><strong>Motivo:</strong> {{ $auditoria->reason ?? 'Sin motivo registrado' }}</p>
                                                <p><strong>Transaccion:</strong> {{ $auditoria->transaction_uuid ?? 'Sin dato' }}</p>
                                            </div>
                                        </div>

                                        <p><strong>URL:</strong> {{ $auditoria->url ?? 'Sin URL' }}</p>
                                        <p><strong>User Agent:</strong> {{ $auditoria->user_agent ?? 'Sin dato' }}</p>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4>Valores Anteriores</h4>
                                                <pre style="white-space: pre-wrap; max-height: 320px; overflow:auto;">{{ json_encode($auditoria->old_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                            <div class="col-md-6">
                                                <h4>Valores Nuevos</h4>
                                                <pre style="white-space: pre-wrap; max-height: 320px; overflow:auto;">{{ json_encode($auditoria->new_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron registros de auditoria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditorias->appends($filtros)->render() }}
    </div>
</div>
@endsection
