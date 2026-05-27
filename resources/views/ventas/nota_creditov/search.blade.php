<form action="{{url('ventas/nota_creditov')}}" method="GET" autocomplete="off" role="search">
	<div class="row">
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText" placeholder="ID..." value="{{$searchText}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText3" placeholder="Fecha de Registro..." value="{{$searchText3}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText4" placeholder="Sucursal..." value="{{$searchText4}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText2" placeholder="Razón Social..." value="{{$searchText2}}">
			</div>
		</div>
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText6" placeholder="Nro. Doc..." value="{{$searchText6}}">
			</div>
		</div>	
   		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" name="searchText5" placeholder="Nro. Factura..." value="{{$searchText5}}">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary">Buscar</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</form>