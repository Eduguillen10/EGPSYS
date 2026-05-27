@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<h3>Nuevo Timbrado</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif

			<form action="{{url('referenciales/timbrado')}}" method="POST" autocomplete="off"> 
        	{{ csrf_field() }}
        <div class="row">
        	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="nro_inicial">Nro. Inicial</label>
					<input type="number" name="nro_inicial" class="form-control" placeholder="Nro. Inicial...">
				</div>
			</div>			
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="nro_actual">Nro. Actual</label>
					<input type="number" name="nro_actual" class="form-control" placeholder="Nro. Actual...">
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="nro_final">Nro. Final</label>
					<input type="number" name="nro_final" class="form-control" placeholder="Nro. Final...">
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="nro_serie">Nro. Serie</label>
					<input type="number" name="nro_serie" class="form-control" placeholder="Nro. Serie...">
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="fecha_inicial">Fecha Inicial</label>
					<input type="date" name="fecha_inicial" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Fecha Inicial...">
				</div>
			</div>			
    		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="nro_timbrado">Nro. Timbrado</label>
					<input type="number" name="nro_timbrado" class="form-control" placeholder="Nro. Timbrado...">
				</div>
			</div>	
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">			
				<div class="form-group">
					<label>Sucursal</label>
					<select name="idsucursal" class="form-control selectpicker" id="idsucursal" data-Live-search="true">
						@foreach($sucursales as $suc)
						<option value="{{$suc->idsucursal}}">{{$suc->descripcion}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
				<div class="form-group">
					<label for="fecha_vencimiento">Fecha Vencimiento</label>
					<input type="date" name="fecha_vencimiento" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Fecha vencimiento...">
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