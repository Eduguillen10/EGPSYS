@extends('layouts.admin')
@section('contenido')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12">
            <h3>Editar Empleado: {{ $empleado->nombre }} {{ $empleado->apellido }}</h3>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ url('referenciales/empleados/'.$empleado->idempleado) }}" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                {{-- Campos del formulario --}}
                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label for="idciudad">Ciudad</label>
                    <select name="idciudad" class="form-control">
                        <option value="" disabled selected>Seleccione una ciudad</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->idciudad }}" {{ $ciudad->idciudad == $empleado->idciudad ? 'selected' : '' }}>
                                {{ $ciudad->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label for="idcargo">Cargo</label>
                    <select name="idcargo" class="form-control">
                        <option value="" disabled selected>Seleccione un cargo</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->idcargo }}" {{ $cargo->idcargo == $empleado->idcargo ? 'selected' : '' }}>
                                {{ $cargo->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $empleado->nombre }}" placeholder="Nombre...">
                </div>

                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="{{ $empleado->apellido }}" placeholder="Apellido...">
                </div>

                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                    <label for="ci">CI</label>
                    <input type="text" name="ci" class="form-control" value="{{ $empleado->ci }}" placeholder="CI...">
                </div>

                <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ $empleado->direccion }}" placeholder="Dirección...">
                </div>

                <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ $empleado->telefono }}" placeholder="Teléfono...">
                </div>

                <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
    			<label>Estado</label>
    			<select name="estado" class="form-control">
    				@if ($empleado->estado=='Activo')
    					<option value="Activo" selected>Activo</option>
    					<option value="Inactivo">Inactivo</option>		
    				@else ($empleado->estado=='Inactivo')  
    					<option value="Activo">Activo</option>
    					<option value="Inactivo"selected>Inactivo</option>	
    				@endif		
    			</select>
    		</div>
    	</div>

                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a href="{{ url('referenciales/empleados') }}" class="btn btn-danger">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
