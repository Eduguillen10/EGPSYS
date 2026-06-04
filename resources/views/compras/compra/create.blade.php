@extends ('layouts.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Nueva Compra</h3>
		@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif
		@if(session('error'))
			<div class="alert alert-danger">{{ session('error') }}</div>
		@endif
	</div>
</div>

@include('compras.compra.modalelegir')

<div class="row">
	<div class="col-lg-3 col-sm-4 col-md-3 col-xs-12">
		<div class="form-group">
			<label>Sucursal</label>
			<input type="text" class="form-control" value="{{ $sucursal ? $sucursal->descripcion : 'Sin sucursal' }}" readonly>
		</div>
	</div>
	<div class="col-lg-3 col-sm-4 col-md-3 col-xs-12">
		<div class="form-group">
			<label>Usuario</label>
			<input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
		</div>
	</div>
	<div class="col-lg-2 col-sm-4 col-md-2 col-xs-12">
		<div class="form-group">
			<label>Fecha</label>
			<input type="date" class="form-control" value="{{ $fecha }}" readonly>
		</div>
	</div>
	<div class="col-lg-4 col-sm-12 col-md-4 col-xs-12">
		<div class="form-group" style="padding-top: 24px;">
			<a href="" data-target="#modal-elegir" data-toggle="modal" class="btn btn-info">
				<i class="fa fa-search"></i> Seleccionar Orden
			</a>
			<a href="{{ url('compras/compra') }}" class="btn btn-default">
				<i class="fa fa-arrow-left"></i> Volver
			</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Datos de la Compra</h3>
			</div>
			<div class="panel-body">
				<p class="text-muted" style="margin-bottom: 0;">
					Seleccione una orden pendiente para cargar los datos fiscales de la factura del proveedor. El detalle se verificara en la pantalla de actualizacion antes de registrar stock, cuenta a pagar y libro de compras.
				</p>
			</div>
		</div>
	</div>
</div>
@endsection
