@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Editar Timbrado: {{ $timbrado->nro_timbrado}}</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif

			<form action="{{url('/referenciales/timbrado/'.$timbrado->idtimbrado)}}" method="POST" accept-charset="UTF-8">
				<input type="hidden" name="_method" value="PUT">
				{{csrf_field()}}
		<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="nro_inicial">Nro. Inicial</label>
					<input type="number" name="nro_inicial" class="form-control" required value="{{$timbrado->nro_inicial}}" placeholder="Nro. Inicial...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="nro_actual">Nro. Actual</label>
					<input type="number" name="nro_actual" class="form-control" required value="{{$timbrado->nro_actual}}" placeholder="Nro. Actual...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="nro_final">Nro. Final</label>
					<input type="number" name="nro_final" class="form-control" required value="{{$timbrado->nro_final}}" placeholder="Nro. Final...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="nro_serie">Nro. Serie</label>
					<input type="text" name="nro_serie" class="form-control" required value="{{$timbrado->nro_serie}}" placeholder="Nro. serie...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="fecha_inicial">Fecha Inicial</label>
					<input type="date" name="fecha_inicial" class="form-control" required value="{{$timbrado->fecha_inicial}}" placeholder="Fecha Incial...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="fecha_inicial">Fecha Inicial</label>
					<input type="date" name="fecha_inicial" class="form-control" required value="{{$timbrado->fecha_inicial}}" placeholder="Fecha Incial...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-12">
				<div class="form-group">
					<label>Estado</label>
					<select name="estado" class="form-control">
						@if ($timbrado->estado=='Activo')
							<option value="Activo" selected>Activo</option>
							<option value="Inactivo">Inactivo</option>		
						@else ($timbrado->estado=='Inactivo')  
							<option value="Activo">Activo</option>
							<option value="Inactivo"selected>Inactivo</option>	
						@endif		
					</select>
				</div>
			</div>
    		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="nro_timbrado">Nro. Timbrado</label>
					<input type="number" name="nro_timbrado" class="form-control" required value="{{$timbrado->nro_timbrado}}" placeholder="Nro. Timbrado...">
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
	       			<label for="sucursal" class="control-label">Sucursal</label>
	        		<select name="idsucursal" id="sucursal" class="form-control"> 
	                @foreach ($sucursales as $suc)
	                    <option value="{{$suc->idsucursal}}" @if ($suc->idsucursal == $timbrado->idsucursal) selected="selected" @endif /> {{$suc->idsucursal}} - {{$suc->descripcion}} </option>
	                @endforeach
	        		</select>
	    		</div>
	    	</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="fecha_vencimiento">Fecha Vencimiento</label>
					<input type="date" name="fecha_vencimiento" class="form-control" required value="{{$timbrado->fecha_vencimiento}}" placeholder="Fecha vencimiento...">
				</div>
			</div>		 	
    	</div>
			<div class="form-group">
				<button class="btn btn-primary" type="submit">Guardar</button>
				<button class="btn btn-danger" type="reset">Cancelar</button>
			</div>

			</form>
		</div>
	</div>
@endsection