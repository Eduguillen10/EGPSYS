<form action="{{url('referenciales/stock')}}" method="GET" autocomplete="off" role="search">
	<div class="row">
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText2" placeholder="Sucursal..." value="{{$searchText2}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText3" placeholder="Depósito..." value="{{$searchText3}}">
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText4" placeholder="Producto..." value="{{$searchText4}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" name="searchText6" placeholder="Cantidad.." value="{{$searchText5}}">
					<span class="input-group-btn">
						<button type="submit" class="btn btn-primary">Buscar</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</form>