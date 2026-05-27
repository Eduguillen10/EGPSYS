<form action="{{ route('cobro.index') }}" method="GET" autocomplete="off" role="search">
    <div class="row g-3">
        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText" placeholder="ID..." value="{{ request('searchText') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="date" class="form-control" name="searchText2" value="{{ request('searchText2') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText3" placeholder="Estado..." value="{{ request('searchText3') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText4" placeholder="Sucursal..." value="{{ request('searchText4') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText5" placeholder="Razón Social..." value="{{ request('searchText5') }}">
            </div>
        </div>

        <div class="col-lg-1 col-md-4 col-sm-6 col-12">
            <div class="form-group">
                <input type="text" class="form-control" name="searchText6" placeholder="RUC/C.I..." value="{{ request('searchText6') }}">
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6 col-12">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </div>
</form>
