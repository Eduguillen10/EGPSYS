{!! Form::open(['route' => 'nota_remision_compra.index', 'method' => 'GET', 'autocomplete' => 'off', 'role' => 'search']) !!}
<div class="form-group">
    <div class="row">
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12">
            <input type="text" class="form-control" name="searchText" placeholder="ID..." value="{{ $searchText }}">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
            <input type="date" class="form-control" name="searchText2" value="{{ $searchText2 }}">
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
            <input type="text" class="form-control" name="searchText3" placeholder="Proveedor..." value="{{ $searchText3 }}">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
            <input type="text" class="form-control" name="searchText4" placeholder="Nro. comprobante..." value="{{ $searchText4 }}">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
            <input type="text" class="form-control" name="searchText5" placeholder="Nro. orden..." value="{{ $searchText5 }}">
        </div>
        <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12">
            <input type="text" class="form-control" name="searchText6" placeholder="Estado..." value="{{ $searchText6 }}">
        </div>
        <div class="col-lg-1 col-md-1 col-sm-2 col-xs-12">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </div>
</div>
{{ Form::close() }}
