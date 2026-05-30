<form action="{{ route('nota_remision_venta.index') }}" method="GET" autocomplete="off" role="search">
    <div class="row">
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText" placeholder="ID/Nro..." value="{{ $query }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <div class="form-group">
                <input type="date" class="form-control" name="searchText3" value="{{ $query3 }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText4" placeholder="Sucursal..." value="{{ $query4 }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText2" placeholder="Razon Social..." value="{{ $query2 }}">
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText6" placeholder="Doc..." value="{{ $query6 }}">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" name="searchText5" placeholder="Factura..." value="{{ $query5 }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>
