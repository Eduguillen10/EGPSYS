<form action="{{ route('cajas.index') }}" method="GET" autocomplete="off" role="search">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" name="searchText" placeholder="Buscar por nombre..." value="{{ request('searchText') }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <select name="estado" class="form-control" onchange="this.form.submit()">
                    <option value="Todos">Todos</option>
                    <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>
    </div>
</form>

