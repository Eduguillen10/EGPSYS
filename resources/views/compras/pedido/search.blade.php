<form action="{{url('compras/pedido')}}" method="GET" autocomplete="off" role="search">	
	<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText" placeholder="ID..." value="{{$searchText}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText2" placeholder="Fecha..." value="{{$searchText2}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText3" placeholder="Sucursal..." value="{{$searchText3}}">
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
			<div class="form-group">
				<input type="text" class="form-control" name="searchText4" placeholder="Observacion..." value="{{$searchText4}}">
			</div>
		</div>		
		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
			<div class="input-group">
				<input type="text" class="form-control" name="searchText5" placeholder="Estado..." value="{{$searchText5}}">
				<span class="input-group-btn">
					<button type="submit" class="btn btn-primary">Buscar</button>
				</span>
			</div>
		</div>	
	</div>
</form>