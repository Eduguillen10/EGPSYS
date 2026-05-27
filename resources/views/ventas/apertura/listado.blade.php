@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Listado de Cajas</h3>
        @include('ventas/apertura.search')

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('info')) <div class="alert alert-warning">{{ session('info') }}</div> @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Caja</th>
                    <th>Sucursal</th>
                    <th>Usuario</th>
                    <th>Fecha Apertura</th>
                    <th>Monto Apertura</th>
                    <th>Fecha Cierre</th>
                    <th>Monto Cierre</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>

                @foreach ($apertura as $aper)
                <tr>
                    <td>{{ $aper->idapertura }}</td>
                    <td>{{ $aper->caja }}</td>
                    <td>{{ $aper->sucursal }}</td>
                    <td>{{ $aper->usuario }}</td>
                    <td>{{ date('d/m/Y', strtotime($aper->fecha_apertura)) }}</td>
                    <td>{{ number_format($aper->monto_inicial, 0, ',', '.') }}</td>

                    <td>
                        @if ($aper->fecha_cierre)
                            {{ date('d/m/Y', strtotime($aper->fecha_cierre)) }}
                        @else
                            Esperando Cierre
                        @endif
                    </td>

                    <td>
                        @if (!is_null($aper->monto_cierre))
                            {{ number_format($aper->monto_cierre, 0, ',', '.') }}
                        @else
                            Esperando Cierre
                        @endif
                    </td>

                    <td style="background-color: {{ ($aper->estado == 'Abierto') ? 'green' : 'red' }}; color: white;">
                        {{ $aper->estado }}
                    </td>

                    <td>
                        @if($aper->estado == 'Abierto')
                            <a href="{{ url('ventas/arqueo/'.$aper->idapertura.'/parcial') }}" target="_blank" class="btn btn-warning btn-sm">
                                <i class="fa fa-calculator"></i> Arqueo Parcial
                            </a>

                            <a href="" data-toggle="modal" data-target="#modal-cerrar-{{ $aper->idapertura }}">
                                <button class="btn btn-danger btn-sm">
                                    <i class="fa fa-close"></i> Cierre
                                </button>
                            </a>
                        @endif

                        @if($aper->estado == 'Cerrado')
                            <a href="{{ url('ventas/arqueo/'.$aper->idapertura.'/final') }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fa fa-calculator"></i> Arqueo Final
                            </a>
                        @endif
                    </td>
                </tr>

                @include('ventas.apertura.modal')
                @endforeach
            </table>
        </div>

        {{ $apertura->appends(Request::only(['searchText1','searchText2','searchText3','searchText4','searchText5']))->render() }}
    </div>
</div>
@endsection
