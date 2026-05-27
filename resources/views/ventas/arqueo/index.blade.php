@extends ('layouts.admin')
@section ('contenido')

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Parámetros: Apertura y Cierre de Caja</h3>
			@if (count($errors)>0)
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{$error}}</li>
						@endforeach
					</ul>
				</div>
			@endif
		</div>
	</div>

	<form action="{{url('ventas/arqueo')}}" method="POST" autocomplete="off" target="_blank" > 
        	{{ csrf_field() }}

		<div class="row">			
			<div class="col-lg-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="fecha_desde">Fecha Desde</label>
					<input type="date" name="fecha_desde" required value="{{date('Y-m-d')}}" class="form-control" >
				</div>
			</div>

			<div class="col-lg-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="fecha_hasta">Fecha Hasta</label>
					<input type="date" name="fecha_hasta" required value="{{date('Y-m-d')}}" class="form-control" >
				</div>
			</div>
		</div>
			
		<div class="row">
			<div class="col-lg-6 col-sm-6 col-xs-12">
				<div class="form-group">		
					<button class="btn btn-primary" type="submit" id="evento_enviar">Generar</button>
				</div>
			</div>
		</div>

	</form>

@endsection