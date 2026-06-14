@extends ('layouts.admin')
@section ('contenido')

<div class="row">
    <div class="col-lg-12">
        <h3>Listado de Cobros</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-warning">{{ session('info') }}</div>
        @endif

        @include('ventas.cobro.search')
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>ID</th>
                    <th>Recibo</th>
                    <th>Fecha</th>
                    <th>Sucursal</th>
                    <th>Apertura</th>
                    <th>Cliente</th>
                    <th>Doc</th>
                    <th>Estado</th>
                    <th>Situación</th>
                    <th>Total</th>
                    <th>Opciones</th>
                </thead>

                @foreach ($cobros as $cob)
                <tr>
                    <td>{{ $cob->id_cobro }}</td>
                    <td>{{ $cob->nro_recibo ?: '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($cob->fecha_recibo ?: $cob->fecha_cobro)->format('d/m/Y') }}</td>
                    <td>{{ $cob->sucursal }}</td>
                    <td>{{ $cob->idapertura }}</td>
                    <td>{{ $cob->cliente }}</td>
                    <td>{{ $cob->num_documento }}</td>
                    <td>@include('ventas.partials.estado', ['estado' => $cob->cobro_estado])</td>
                    <td>
                        @php
                            $saldoCuenta = (int)($cob->saldo_cuenta ?? 0);
                            $fechaVencimiento = $cob->fecha_vencimiento_cuenta ?? null;

                            $vencido = false;

                            if ($saldoCuenta > 0 && $fechaVencimiento) {
                                $vencido = \Carbon\Carbon::parse($fechaVencimiento)->lt(\Carbon\Carbon::today());
                            }
                        @endphp

                        @if($saldoCuenta <= 0)
                            <span class="label label-success">Saldado</span>
                        @elseif($vencido)
                            <span class="label label-danger">Vencido / Moroso</span>
                        @else
                            <span class="label label-warning">Vigente</span>
                        @endif
                    </td>
                    <td style="text-align:right;">{{ number_format($cob->monto_cobro, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('cobro.show', $cob->id_cobro) }}" class="btn btn-primary btn-sm">
                            Detalles
                        </a>

                        @if($cob->cobro_estado == 'Pendiente' && (int)($cob->credito_pendiente_aceptacion ?? 0) === 0)
                            <a href="{{ route('cobro.edit', $cob->id_cobro) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-credit-card"></i>Cobrar
                            </a>
                        @elseif($cob->cobro_estado == 'Pendiente')
                            <button type="button" class="btn btn-success btn-sm" disabled title="Debe registrar firma fisica en la venta">
                                <i class="fa fa-credit-card"></i>Cobrar
                            </button>
                            @if($cob->idventa)
                                <a href="{{ route('venta.show', $cob->idventa) }}" class="btn btn-warning btn-sm">
                                    Firma pendiente
                                </a>
                            @endif
                        @endif

                         @if($cob->cobro_estado == 'Realizado')
                        <a href="{{ route('cobro.recibo', $cob->id_cobro) }}" target="_blank" class="btn btn-warning btn-sm">
                             <i class="fa fa-ligth fa-print"></i>Imprimir Recibo
                        </a>

                        @if($cob->idventa && (int)($cob->saldo_cuenta ?? 0) === 0)
                            <a href="{{ route('venta.imprimirfactura', $cob->idventa) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-ligth fa-print"></i>Imprimir Factura
                            </a>
                        @endif
                        
                            <a href="" data-target="#modal-delete-{{$cob->id_cobro}}" data-toggle="modal" class="btn btn-danger btn-sm">
                                Anular
                            </a>
                        @endif
                    </td>
                </tr>
                @include('ventas.cobro.modal', ['cob' => $cob])
                @endforeach
            </table>
        </div>

        {{ $cobros->appends(request()->all())->links() }}
    </div>
</div>

@endsection
