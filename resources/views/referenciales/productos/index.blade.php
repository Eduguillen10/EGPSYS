@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h3>Listado de Productos <a href="{{ url('referenciales/productos/create') }}"><button class="btn btn-success">Nuevo</button></a></h3>
            @include('referenciales.productos.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    {{-- Encabezados de la tabla --}}
                    <thead>
                        <th>ID</th>
                        <th>Codigo</th>
                        <th>Rubro</th>
                        <th>Marca</th>
                        <th>Tipo de Impuesto</th>
                        <th>Descripción</th>
                        <th>Precio de Compra</th>
                        <th>Precio de Venta</th>
                        <th>Tipo de Producto</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>

                    {{-- Datos de la tabla --}}
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{ $producto->idproducto }}</td>
                            <td>{{ $producto->codigo }}</td>
                            <td>{{ $producto->rubro }}</td>
                            <td>{{ $producto->marca }}</td>
                            <td>{{ $producto->tipoimpuesto }} {{ $producto->porcentaje }}%</td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>{{ $producto->precio_compra }}</td>
                            <td>{{ $producto->precio_venta }}</td>
                            <td>{{ $producto->tipo_producto }}</td>
                            <td>@include('referenciales.partials.estado', ['estado' => $producto->estado ?? 'Activo'])</td>
                            <td>
                                <a href="{{ route('productos.edit', $producto->idproducto) }}"><button class="btn btn-info">Editar</button></a>
                                <a href="" data-target="#modal-delete-{{ $producto->idproducto }}" data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
                            </td>
                        </tr>
                        @include('referenciales.productos.modal')
                    @endforeach
                </table>
            </div>
            {{ $productos->appends(Request::only(['searchText']))->render() }}
        </div>
    </div>
@endsection
