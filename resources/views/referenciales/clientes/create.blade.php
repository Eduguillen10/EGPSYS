@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <h3>Nuevo Cliente</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('referenciales/clientes') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                {{ csrf_field() }}
                
                <div class="form-group">
                    <label for="idciudad">Ciudad</label>
                    <select name="idciudad" class="form-control">
                        <option value="" disabled selected>Seleccione una ciudad</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->idciudad }}">{{ $ciudad->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clasificación del Cliente (Editable y Automática) -->
                <div class="form-group">
                    <label for="tipo_cliente">Clasificación del Cliente</label>
                    <select name="idtipo_cliente" id="idtipo_cliente" class="form-control" {{ $cliente->is_manual ? '' : 'disabled' }}>
                        @foreach($tiposClientes as $tipocliente)
                            <option value="{{ $tipocliente->idtipo_cliente }}">{{ $tipocliente->descripcion }}
                            {{ $tipocliente->idtipo_cliente == $cliente->idtipo_cliente ? 'selected' : '' }}>
                            {{ $tipocliente->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Checkbox para activar la edición manual -->
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_manual" id="is_manual" {{ $cliente->is_manual ? 'checked' : '' }} 
                               onclick="document.getElementById('idtipo_cliente').disabled = !this.checked;">
                        Asignar clasificación manualmente
                    </label>
                </div>

                <div class="form-group">
                    <label for="idtipodocumento">Tipo Documento</label>
                    <select name="idtipodocumento" class="form-control">
                        <option value="" disabled selected>Seleccione el Tipo de Documento</option>
                        @foreach($tiposDocumentos as $tipodocumento)
                            <option value="{{ $tipodocumento->idtipodocumento }}">{{ $tipodocumento->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="idnacionalidad">Nacionalidad</label>
                    <select name="idnacionalidad" class="form-control">
                        <option value="" disabled selected>Seleccione la Nacionalidad</option>
                        @foreach($nacionalidades as $nacionalidad)
                            <option value="{{ $nacionalidad->idnacionalidad }}">{{ $nacionalidad->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre..." required>
                </div>

                <div class="form-group">
                    <label for="num_documento">Número de Documento</label>
                    <input type="text" name="num_documento" id="num_documento" class="form-control" placeholder="Número Documento..." required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Dirección...">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono...">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Email...">
                </div>

                <div class="form-group">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/clientes') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script para actualizar automáticamente la clasificación del cliente según ventas -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elementos del formulario
            const tipoClienteSelect = document.getElementById('idtipo_cliente');

            // Simulación de datos de ventas (esto se calculará en el backend en realidad)
            let totalVentas = 0;

            function actualizarClasificacion() {
                if (totalVentas > 100000000) {
                    tipoClienteSelect.value = 1; // Platino
                } else if (totalVentas >= 50000000) {
                    tipoClienteSelect.value = 3; // Oro
                } else if (totalVentas >= 10000000) {
                    tipoClienteSelect.value = 4; // Plata
                } else {
                    tipoClienteSelect.value = 5; // Bronce
                }
            }

            actualizarClasificacion(); // Se ejecuta al cargar el formulario
        });
    </script>
@endsection
