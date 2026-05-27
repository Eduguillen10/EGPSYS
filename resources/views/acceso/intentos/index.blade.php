@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Intentos de Acceso</h3>
    </div>
</div>

<form action="{{ route('login_attempts.index') }}" method="GET" autocomplete="off">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="date" id="desde" name="desde" class="form-control" value="{{ $filtros['desde'] ?? '' }}">
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" class="form-control" value="{{ $filtros['hasta'] ?? '' }}">
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="email">Usuario</label>
                <input type="text" id="email" name="email" class="form-control" value="{{ $filtros['email'] ?? '' }}" placeholder="Email">
            </div>
        </div>

        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="correcto" {{ ($filtros['estado'] ?? '') === 'correcto' ? 'selected' : '' }}>Correcto</option>
                    <option value="fallido" {{ ($filtros['estado'] ?? '') === 'fallido' ? 'selected' : '' }}>Fallido</option>
                </select>
            </div>
        </div>

        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">Buscar</button>
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
                        <th>Usuario</th>
                        <th>Contrasena</th>
                        <th>IP</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($intentos as $intento)
                        <tr>
                            <td>{{ optional($intento->attempted_at)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $intento->email ?? 'Sin usuario' }}</td>
                            <td>{{ $intento->password_mask ?? 'Sin dato' }}</td>
                            <td>{{ $intento->ip_address ?? 'Sin IP' }}</td>
                            <td>
                                @if ($intento->successful)
                                    <span class="label label-success">Correcto</span>
                                @else
                                    <span class="label label-danger">Fallido</span>
                                @endif
                            </td>
                            <td>{{ $intento->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron intentos de acceso.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $intentos->appends($filtros)->render() }}
    </div>
</div>
@endsection
