@extends ('layouts.admin')

@section ('contenido')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3>Listado de Pedidos 
                <a href="{{ route('pedido.create') }}"><button class="btn btn-success">Nuevo</button></a>
                <span>Total de Registros: {{ $total }}</span> 
            </h3>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            @include('compras/pedido.search')
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                        <th>ID</th>
                        <th>Fecha</th>
						<th>Usuario</th>
                        <th>Sucursal</th>
                        <th>Observación</th>
                        <th>Estado</th>
                        <th>Opciones</th>
                    </thead>
                    @foreach ($pedidos_compras as $ped)
                        <tr>
                            <td>{{ $ped->idpedidocompra }}</td>
                            <td>{{ date('d/m/Y', strtotime($ped->fecha)) }}</td>
							<td>{{ $ped->usuario }}</td>
                            <td>{{ $ped->sucursal_descripcion }}</td>
                            <td>{{ $ped->observacion }}</td>
                            <td>{{ $ped->estado }}</td>
                            <td>
                                <a href="{{ route('pedido.show', $ped->idpedidocompra) }}"><button class="btn btn-primary">Detalles</button></a>
                                @if ($ped->estado !== 'Cancelado')
                                    <a href="" data-target="#modal-delete-{{$ped->idpedidocompra}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
                                @endif
                            </td>
                        </tr>
                        @include('compras.pedido.modal') 
                    @endforeach
                </table>
            </div>
            {{ $pedidos_compras->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5', 'searchText6']))->render() }}
        </div>
    </div>
@endsection
