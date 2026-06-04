@extends('layouts.admin')

@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>Listado de Nota de Debito de Compra
            <a href="{{ route('nota_debitoc.create') }}">
                <button class="btn btn-success">Nuevo</button>
            </a>
        </h3>

        @include('compras.nota_debitoc.search')

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
                        <th>Nro. Nota Debito</th>
                        <th>Timbrado</th>
                        <th>Compra</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nota_debitoc as $ntd)
                        <tr>
                            <td>{{ $ntd->idnota_debitoc }}</td>
                            <td>{{ $ntd->fecha_registro ? date('d/m/Y', strtotime($ntd->fecha_registro)) : '' }}</td>
                            <td>{{ $ntd->sucursal }}</td>
                            <td>{{ $ntd->proveedor }}</td>
                            <td>{{ $ntd->num_documento }}</td>
                            <td>{{ $ntd->nro_nota_debito }}</td>
                            <td>{{ $ntd->timbrado }}</td>
                            <td>{{ $ntd->idcompra }} / {{ $ntd->nro_factura_compra }}</td>
                            <td>{{ $ntd->mueve_stock ? 'Entrada' : 'No mueve' }}</td>
                            <td>{{ $ntd->estado }}</td>
                            <td>{{ number_format((int) $ntd->montonota_debito_compra, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('nota_debitoc.show', $ntd->idnota_debitoc) }}">
                                    <button class="btn btn-primary">Detalles</button>
                                </a>
                                @if($ntd->estado !== 'Cancelado')
                                    <a href="" data-target="#modal-delete-{{ $ntd->idnota_debitoc }}" data-toggle="modal">
                                        <button class="btn btn-danger">Anular</button>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @include('compras.nota_debitoc.modal')
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No hay notas de debito registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $nota_debitoc->appends(Request::only(['searchText', 'searchText2', 'searchText3', 'searchText4', 'searchText5', 'searchText6']))->render() }}
    </div>
</div>
@endsection
