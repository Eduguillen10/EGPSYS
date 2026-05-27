@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Estados de Referenciales</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('referenciales.estados.index') }}" method="GET" autocomplete="off">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="recurso">Referencial</label>
                        <select id="recurso" name="recurso" class="form-control">
                            @foreach ($referenciales as $clave => $item)
                                <option value="{{ $clave }}" {{ $recurso === $clave ? 'selected' : '' }}>
                                    {{ $item['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control">
                            @foreach (['Inactivo', 'Activo', 'Todos'] as $opcion)
                                <option value="{{ $opcion }}" {{ $estado === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                    <div class="form-group">
                        <label for="searchText">Buscar</label>
                        <input type="text" id="searchText" name="searchText" class="form-control" value="{{ $searchText }}" placeholder="Descripcion, documento, codigo...">
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Referencial</th>
                        <th>Datos</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $registro)
                        <tr>
                            <td>{{ $registro->{$primaryKey} }}</td>
                            <td>{{ $config['label'] }}</td>
                            <td>
                                @foreach ($config['display'] as $field)
                                    @if (filled($registro->{$field} ?? null))
                                        <div><strong>{{ str_replace('_', ' ', ucfirst($field)) }}:</strong> {{ $registro->{$field} }}</div>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @if ($registro->estado === 'Activo')
                                    <span class="label label-success">Activo</span>
                                @else
                                    <span class="label label-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                @if ($registro->estado === 'Activo')
                                    <form action="{{ route('referenciales.estados.inactivar', [$recurso, $registro->{$primaryKey}]) }}" method="POST" style="display:inline;">
                                        {{ csrf_field() }}
                                        {{ method_field('PATCH') }}
                                        <button type="submit" class="btn btn-xs btn-danger">Inactivar</button>
                                    </form>
                                @else
                                    <form action="{{ route('referenciales.estados.reactivar', [$recurso, $registro->{$primaryKey}]) }}" method="POST" style="display:inline;">
                                        {{ csrf_field() }}
                                        {{ method_field('PATCH') }}
                                        <button type="submit" class="btn btn-xs btn-success">Reactivar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron registros para el filtro seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $registros->appends([
            'recurso' => $recurso,
            'estado' => $estado,
            'searchText' => $searchText,
        ])->render() }}
    </div>
</div>
@endsection
