@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Nuevo Producto Cliente </h3>
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

			{!! Form::open(array('url'=>'ventas/cuota_producto', 'method'=>'POST','autocomplete'=>'Off'))!!}

	<div class="row">		
		<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
			<div class="form-group">
				<label for="idsucursal">Sucursal</label>
				@if($sucursales)
				<input type="text" name="idsucursal" class="form-control" value="{{ $sucursales->descripcion }}" readonly>
				@else
				<input type="text" name="idsucursal" class="form-control" value="No hay sucursal seleccionada" readonly>
				@endif
				<input type="hidden" name="idsucursal" value="{{ $sucursales->idsucursal }}">
			</div>
		</div>

		<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
    		<div class="form-group">
				<label>Depósito</label>
				<select name="iddeposito" class="form-control selectpicker" id="psucursal" data-Live-search="true" autofocus>
					@foreach($depositos as $dep)
					<option value="{{$dep->iddeposito}}">{{$dep->descripcion}}</option>
					@endforeach
				</select>
    		</div>
   		</div> 

		<div class="col-lg-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="idcliente">Cliente</label><a  onclick="altarapida();"> ...<i class="fa fa-user-plus" aria-hidden="true"></i></a> <span id="buscando"> </span>
				<select name="idcliente"  id="idcliente" class="form-control selectpicker" data-live-search="true"  onchange="datosCliente();"  onkeyup="datosCliente();" >
					
				</select>
			</div>
		</div>

		<div class="col-lg-4 col-sm-4 col-xs-12">
			<div class="form-group">
				<label for="razon_social">Nombre</label>
				<input type="text" name="razon_social" id="razon_social" class="form-control" value="{{old('razon_social')}}" placeholder="Nombre ó Razón Social...">
			</div>
		</div>

		<div class="col-lg-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<label for="ruc">Ruc</label>
				<input type="text" name="ruc" id="ruc" class="form-control" value="{{old('ruc')}}" placeholder="Ruc ó C.I...">
			</div>
		</div>

		<div class="col-lg-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="idproducto">Producto</label><a  onclick="BuscarProducto('art');"> ...<i class="fa fa-search" aria-hidden="true" title="Click para buscar..."></i></a>
				<select name="idproducto"  id="idproducto" class="form-control selectpicker input-sm"  >
					<option value=""></option>
					@foreach ($productos as $art)
						<option value="{{$art->idproducto}}" @if (old('idproducto')==$art->idproducto) selected="selected" @endif >{{$art->codigo}} - {{$art->descripcion}}</option>

					@endforeach
				</select>
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="idcondicion_venta">Condición de Venta</label>
				<select name="idcondicion_venta" id="idcondicion_venta" class="form-control" onchange="calculaFechaVto();">
					@foreach ($ventacondiciones as $vcv)
						<option value="{{$vcv->idcondicion_venta}}_ {{$vcv->dias}}_ {{$vcv->genera_cta_cte}}" @if (old('idcondicion_venta')==$vcv->idcondicion_venta) selected="selected" @endif >{{$vcv->abreviatura}} - {{$vcv->descripcion}}</option>
					@endforeach
				</select>
			</div>
		</div>


		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="fecha">Fecha</label>
				<input type="date" name="fecha" id="fecha" class="form-control" value="{{date('Y-m-d')}}" onchange="calculaFechaVto();">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="fecha_vencimiento">Fecha Vencimiento</label>
				<input type="text" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="{{date('d/m/Y')}}" readonly >
			</div>
		</div>

		<div class="col-lg-6 col-sm-6 col-xs-6">
			<div class="form-group">
				<label for="concepto">Concepto</label>
				<input type="text" id="concepto" name="concepto" class="form-control" value="{{old('concepto')}}" placeholder="Concepto">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="entrega">Entrega</label>
				<input type="entrega" min="1" id="entrega" name="entrega" class="form-control" value="{{old('entrega')}}" placeholder="Entrega..." onchange="copiarMontovta();">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="total_cuotas">Cantidad de Cuotas</label>
				<input type="number" min="1" max="60" id="total_cuotas" name="total_cuotas" class="form-control" value="{{old('total_cuotas')}}" placeholder="Cantidad de cuotas...">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="porcentaje_interes">Porcentaje de Interes</label>
				<input type="number" min="0" id="porcentaje_interes" name="porcentaje_interes" class="form-control" value="{{old('porcentaje_interes')}}" placeholder="Porcentaje de Interes...">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="total_venta">Total Venta Gs.</label>
				<input type="number" min="1" id="total_venta" name="total_venta" class="form-control" value="{{old('total_venta')}}" placeholder="Total venta Gs. ..."  onchange="copiarMontovta();">
			</div>
		</div>

		<div class="col-lg-3 col-sm-3 col-xs-6">
			<div class="form-group">
				<label for="total_saldo">Saldo</label>
				<input type="number" min="1" id="total_saldo" name="total_saldo" class="form-control" value="{{old('total_saldo')}}" readonly="readOnly" >
			</div>
		</div>

		<div class="col-lg-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="observacion">Codeudor</label>
				<textarea name="observacion" class="form-control">{{old('observacion')}}</textarea>
			</div>
		</div>

	</div>

	<div class="row">
		<div class="col-lg-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<button class="btn btn-primary" type="submit" id="boton-ruc-correcto-submit">Guardar</button>
				<button class="btn btn-danger" type="reset" onclick="history.back(-1);">Cancelar</button>
			</div>
			
		</div>
	</div>


			{!!Form::Close()!!}

@include('ventas.cuota_producto.modal_altarapida')
@endsection
@section ('script')
<script src="{{asset('script/func_vehiculos_clientes.js')}}"></script>
<script src="{{asset('script/func_select_bootstrap_cliente.js')}}"></script>

@endsection