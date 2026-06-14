@extends('layouts.admin')

@section('contenido')
@php
    $ventasTotal = (int) ($ventasHoy->total ?? 0);
    $ventasCantidad = (int) ($ventasHoy->cantidad ?? 0);
    $cobrosTotal = (int) ($cobrosHoy->total ?? 0);
    $cobrosCantidad = (int) ($cobrosHoy->cantidad ?? 0);
    $pendienteTotal = (int) ($cuentasPendientes->total ?? 0);
    $pendienteCantidad = (int) ($cuentasPendientes->cantidad ?? 0);
@endphp

<style>
    .inicio-page {
        color: #1f2933;
        padding: 16px 14px 24px;
    }

    .inicio-header {
        align-items: center;
        background: #fff;
        border: 1px solid #d9e2ec;
        border-left: 4px solid #3f8fc9;
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
        display: flex;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 14px 16px;
    }

    .inicio-title {
        color: #1f2933;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1.15;
        margin: 0 0 4px;
        text-transform: none;
    }

    .inicio-date {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .inicio-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-bottom: 14px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #d9e2ec;
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
        min-height: 104px;
        padding: 14px 16px;
    }

    .metric-top {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .metric-label {
        color: #52606d;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .metric-icon {
        align-items: center;
        border-radius: 6px;
        display: inline-flex;
        height: 30px;
        justify-content: center;
        width: 30px;
    }

    .metric-icon.green {
        background: #ecfdf3;
        color: #15803d;
    }

    .metric-icon.blue {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .metric-icon.amber {
        background: #fffbeb;
        color: #b45309;
    }

    .metric-icon.red {
        background: #fef2f2;
        color: #b91c1c;
    }

    .metric-value {
        color: #111827;
        font-size: 23px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 5px;
    }

    .metric-note {
        color: #6b7280;
        font-size: 12px;
        margin: 0;
    }

    .inicio-band {
        align-items: center;
        background: #fff;
        border: 1px solid #d9e2ec;
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 12px 14px;
    }

    .band-title {
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .5px;
        margin: 0 0 4px;
        text-transform: uppercase;
    }

    .band-value {
        color: #111827;
        font-size: 15px;
        font-weight: 700;
        margin: 0;
    }

    .status-pill {
        border-radius: 6px;
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 10px;
    }

    .status-pill.open {
        background: #ecfdf3;
        color: #166534;
    }

    .status-pill.closed {
        background: #fef2f2;
        color: #991b1b;
    }

    .content-grid {
        align-items: start;
        display: grid;
        gap: 14px;
        grid-template-columns: 1.35fr 1fr;
    }

    .inicio-panel {
        background: #fff;
        border: 1px solid #d9e2ec;
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, .06);
        overflow: hidden;
    }

    .panel-head {
        align-items: center;
        background: #fff;
        border-bottom: 1px solid #d9e2ec;
        display: flex;
        justify-content: space-between;
        padding: 12px 14px;
    }

    .panel-head h4 {
        color: #111827;
        font-size: 14px;
        font-weight: 800;
        margin: 0;
    }

    .panel-link {
        color: #3f8fc9;
        font-size: 12px;
        font-weight: 700;
    }

    .panel-link:hover,
    .panel-link:focus {
        color: #347fab;
        text-decoration: none;
    }

    .inicio-table {
        margin-bottom: 0;
    }

    .inicio-table thead th {
        background: #f6f8fb !important;
        border-bottom: 1px solid #d9e2ec !important;
        color: #52606d !important;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .inicio-table tbody td {
        border-top: 1px solid #f1f5f9 !important;
        color: #374151;
        font-size: 12px;
        vertical-align: middle;
    }

    .amount {
        color: #111827;
        font-variant-numeric: tabular-nums;
        font-weight: 700;
        text-align: right;
        white-space: nowrap;
    }

    .empty-row {
        color: #6b7280;
        padding: 18px 16px;
        text-align: center;
    }

    .alert-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .alert-list li {
        align-items: center;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        padding: 11px 14px;
    }

    .alert-list li:last-child {
        border-bottom: 0;
    }

    .alert-main {
        min-width: 0;
    }

    .alert-main strong {
        color: #111827;
        display: block;
        font-size: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .alert-main span {
        color: #6b7280;
        display: block;
        font-size: 11px;
        margin-top: 2px;
    }

    .alert-value {
        color: #b91c1c;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .alert-value.stock {
        color: #b45309;
    }

    @media (max-width: 1100px) {
        .inicio-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 680px) {
        .inicio-header,
        .inicio-band {
            align-items: flex-start;
            flex-direction: column;
        }

        .inicio-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="inicio-page">
    <div class="inicio-header">
        <div>
            <h3 class="inicio-title">Inicio</h3>
            <div class="inicio-date">{{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="inicio-grid">
        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-label">Ventas de hoy</div>
                <span class="metric-icon green"><i class="fa fa-line-chart"></i></span>
            </div>
            <div class="metric-value">{{ number_format($ventasTotal, 0, ',', '.') }}</div>
            <p class="metric-note">{{ $ventasCantidad }} operacion{{ $ventasCantidad === 1 ? '' : 'es' }}</p>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-label">Cobros de hoy</div>
                <span class="metric-icon blue"><i class="fa fa-credit-card"></i></span>
            </div>
            <div class="metric-value">{{ number_format($cobrosTotal, 0, ',', '.') }}</div>
            <p class="metric-note">{{ $cobrosCantidad }} cobro{{ $cobrosCantidad === 1 ? '' : 's' }} realizado{{ $cobrosCantidad === 1 ? '' : 's' }}</p>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-label">Saldo pendiente</div>
                <span class="metric-icon amber"><i class="fa fa-clock-o"></i></span>
            </div>
            <div class="metric-value">{{ number_format($pendienteTotal, 0, ',', '.') }}</div>
            <p class="metric-note">{{ $pendienteCantidad }} cuenta{{ $pendienteCantidad === 1 ? '' : 's' }} a cobrar</p>
        </div>

        <div class="metric-card">
            <div class="metric-top">
                <div class="metric-label">Stock bajo</div>
                <span class="metric-icon red"><i class="fa fa-cubes"></i></span>
            </div>
            <div class="metric-value">{{ number_format($stockBajo, 0, ',', '.') }}</div>
            <p class="metric-note">productos con 5 o menos unidades</p>
        </div>
    </div>

    <div class="inicio-band">
        <div>
            <p class="band-title">Caja</p>
            @if($cajaAbierta)
                <p class="band-value">
                    {{ $cajaAbierta->caja ?? 'Caja' }} | {{ $cajaAbierta->sucursal ?? 'Sucursal' }} | Apertura #{{ $cajaAbierta->idapertura }}
                </p>
            @else
                <p class="band-value">No hay caja abierta para la sucursal actual</p>
            @endif
        </div>
        <span class="status-pill {{ $cajaAbierta ? 'open' : 'closed' }}">
            {{ $cajaAbierta ? 'Abierta' : 'Cerrada' }}
        </span>
    </div>

    <div class="content-grid">
        <div class="inicio-panel">
            <div class="panel-head">
                <h4>Ultimas ventas</h4>
                <a class="panel-link" href="{{ url('/ventas/venta') }}">Ver ventas</a>
            </div>
            <div class="table-responsive">
                <table class="table inicio-table">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimasVentas as $venta)
                            <tr>
                                <td>{{ $venta->nro_factura }}</td>
                                <td>{{ $venta->cliente }}</td>
                                <td>{{ $venta->fecha ? date('d/m/Y', strtotime($venta->fecha)) : '-' }}</td>
                                <td class="amount">{{ number_format($venta->montoventa, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-row">Sin ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <div class="inicio-panel" style="margin-bottom:12px;">
                <div class="panel-head">
                    <h4>Cuentas vencidas</h4>
                    <a class="panel-link" href="{{ url('/ventas/cuenta_cobrar') }}">Ver cuentas</a>
                </div>
                <ul class="alert-list">
                    @forelse($cuentasVencidas as $cuenta)
                        <li>
                            <div class="alert-main">
                                <strong>{{ $cuenta->cliente }}</strong>
                                <span>Venta #{{ $cuenta->idventa }} | {{ $cuenta->fecha_vencimiento ? date('d/m/Y', strtotime($cuenta->fecha_vencimiento)) : '-' }}</span>
                            </div>
                            <div class="alert-value">{{ number_format($cuenta->saldo, 0, ',', '.') }}</div>
                        </li>
                    @empty
                        <li>
                            <div class="empty-row">Sin cuentas vencidas.</div>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="inicio-panel">
                <div class="panel-head">
                    <h4>Stock critico</h4>
                    <a class="panel-link" href="{{ url('/referenciales/stock') }}">Ver stock</a>
                </div>
                <ul class="alert-list">
                    @forelse($stockCritico as $stock)
                        <li>
                            <div class="alert-main">
                                <strong>{{ $stock->descripcion }}</strong>
                                <span>Codigo: {{ $stock->codigo ?? '-' }}</span>
                            </div>
                            <div class="alert-value stock">{{ \App\Helpers\NumberFormatter::cantidad($stock->cantidad) }}</div>
                        </li>
                    @empty
                        <li>
                            <div class="empty-row">Sin productos criticos.</div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
