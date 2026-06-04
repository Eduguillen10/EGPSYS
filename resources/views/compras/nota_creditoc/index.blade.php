@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Listado de Nota de Credito de Compra
            <a href="{{ route('nota_creditoc.create') }}">
                <button class="btn btn-success">Nuevo</button>
            </a>
        </h3>

        @include('compras.nota_creditoc.search')

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Sucursal</th>
                        <th>Proveedor</th>
                        <th>RUC</th>
                        <th>Nro. Nota Credito</th>
                        <th>Timbrado</th>
                        <th>Compra</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nota_creditoc as $ntc)
                        <tr>
                            <td>{{ $ntc->idnota_creditoc }}</td>
                            <td>{{ $ntc->fecha_registro ? date('d/m/Y', strtotime($ntc->fecha_registro)) : '' }}</td>
                            <td>{{ $ntc->sucursal }}</td>
                            <td>{{ $ntc->proveedor }}</td>
                            <td>{{ $ntc->num_documento }}</td>
                            <td>{{ $ntc->nro_factura }}</td>
                            <td>{{ $ntc->timbrado }}</td>
                            <td>{{ $ntc->idcompra }} / {{ $ntc->nro_factura_compra }}</td>
                            <td>{{ $ntc->estado }}</td>
                            <td>{{ number_format((int) $ntc->montonota_credito_compra, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('nota_creditoc.show', $ntc->idnota_creditoc) }}">
                                    <button class="btn btn-primary">Detalles</button>
                                </a>
                                @if($ntc->estado !== 'Cancelado')
                                    <a href="" data-target="#modal-delete-{{ $ntc->idnota_creditoc }}" data-toggle="modal">
                                        <button class="btn btn-danger">Anular</button>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @include('compras.nota_creditoc.modal')
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No hay notas de credito registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $nota_creditoc->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5', 'searchText6']))->render() }}
    </div>
</div>
@endsection
