@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Editar Producto: {{ $producto->descripcion }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ url('/referenciales/productos/'.$producto->idproducto) }}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                {{-- Campos del formulario --}}

                <div class="form-group">
                    <label for="codigo">Codigo</label>
                    <input type="text" name="codigo" class="form-control" value="{{ $producto->codigo }}" placeholder="Codigo...">
                </div>

                <div class="form-group">
                    <label for="idrubro">Rubro</label>
                    <select name="idrubro" class="form-control">
                        <option value="" disabled selected>Seleccione un rubro</option>
                        @foreach($rubros as $rubro)
                            <option value="{{ $rubro->idrubro }}" {{ $rubro->idrubro == $producto->idrubro ? 'selected' : '' }}>
                                {{ $rubro->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idmarca">Marca</label>
                    <select name="idmarca" class="form-control">
                        <option value="" disabled selected>Seleccione una marca</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->idmarca }}" {{ $marca->idmarca == $producto->idmarca ? 'selected' : '' }}>
                                {{ $marca->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idtipoimpuesto">Tipo de Impuesto</label>
                    <select name="idtipoimpuesto" class="form-control">
                        <option value="" disabled selected>Seleccione un tipo de impuesto</option>
                        @foreach($tiposImpuesto as $tiposImpuesto)
                            <option value="{{ $tiposImpuesto->idtipoimpuesto }}" {{ $tiposImpuesto->idtipoimpuesto == $producto->idtipoimpuesto ? 'selected' : '' }}>
                                {{ $tiposImpuesto->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" value="{{ $producto->descripcion }}" placeholder="Descripción...">
                </div>

                <div class="form-group">
                    <label for="precio_compra">Precio de Compra</label>
                    <input type="number" name="precio_compra" class="form-control" value="{{ $producto->precio_compra }}" placeholder="Precio de Compra...">
                </div>

                <div class="form-group">
                    <label for="precio_venta">Precio de Venta</label>
                    <input type="number" name="precio_venta" class="form-control" value="{{ $producto->precio_venta }}" placeholder="Precio de Venta...">
                </div>

                <div class="form-group">
                    <label for="tipo_producto">Tipo de Producto</label>
                    <input type="text" name="tipo_producto" class="form-control" value="{{ $producto->tipo_producto }}" placeholder="Tipo de Producto...">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/productos') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
