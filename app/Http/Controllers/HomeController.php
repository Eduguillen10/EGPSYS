<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $hoy = Carbon::today('America/Asuncion')->toDateString();
        $idsucursal = (int) (Auth::user()->trabaja_sucursal ?? 0);

        $ventasHoy = DB::table('ventas')
            ->whereDate('fecha', $hoy)
            ->whereNotIn('estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(montoventa), 0) as total')
            ->first();

        $cobrosHoy = DB::table('cobros as c')
            ->leftJoin('nota_credito_venta_cobro as ncc', 'c.id_cobro', '=', 'ncc.id_cobro')
            ->whereDate('c.fecha_cobro', $hoy)
            ->whereIn('c.cobro_estado', ['Realizado', 'R'])
            ->where('c.monto_cobro', '>', 0)
            ->whereNull('ncc.id_cobro')
            ->whereNotExists(function ($sub) {
                $sub->selectRaw('1')
                    ->from('det_formacobro as df_nc')
                    ->whereColumn('df_nc.id_cobro', 'c.id_cobro')
                    ->where(function ($q) {
                        $q->where('df_nc.documento', 'LIKE', 'NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REING AJUSTE NC %')
                            ->orWhere('df_nc.documento', 'LIKE', 'REINGRESO AJUSTE NC %');
                    });
            })
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(c.monto_cobro), 0) as total')
            ->first();

        $cuentasPendientes = DB::table('cuenta_cobrar')
            ->where('saldo', '>', 0)
            ->whereNotIn('estado', ['Pagado', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->selectRaw('COUNT(*) as cantidad, COALESCE(SUM(saldo), 0) as total')
            ->first();

        $stockBajo = DB::table('stock as st')
            ->join('productos as p', 'st.idproducto', '=', 'p.idproducto')
            ->where('st.cantidad', '>', 0)
            ->where('st.cantidad', '<=', 5)
            ->where('p.estado', 'Activo')
            ->count();

        $cajaAbierta = DB::table('apertura as a')
            ->leftJoin('cajas as c', 'a.idcaja', '=', 'c.idcaja')
            ->leftJoin('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->where('a.estado', 'Abierto')
            ->when($idsucursal > 0, fn ($query) => $query->where('a.idsucursal', $idsucursal))
            ->select(
                'a.idapertura',
                'a.fecha_apertura',
                'a.monto_inicial',
                'c.descripcion as caja',
                's.descripcion as sucursal'
            )
            ->orderByDesc('a.idapertura')
            ->first();

        $ultimasVentas = DB::table('ventas as v')
            ->join('clientes as c', 'v.idcliente', '=', 'c.idcliente')
            ->select('v.idventa', 'v.fecha', 'v.nro_factura', 'v.montoventa', 'v.estado', 'c.nombre as cliente')
            ->whereNotIn('v.estado', ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->orderByDesc('v.idventa')
            ->limit(5)
            ->get();

        $cuentasVencidas = DB::table('cuenta_cobrar as cc')
            ->join('clientes as c', 'cc.idcliente', '=', 'c.idcliente')
            ->select('cc.idventa', 'cc.fecha_vencimiento', 'cc.saldo', 'c.nombre as cliente')
            ->where('cc.saldo', '>', 0)
            ->whereDate('cc.fecha_vencimiento', '<', $hoy)
            ->whereNotIn('cc.estado', ['Pagado', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'])
            ->orderBy('cc.fecha_vencimiento')
            ->limit(5)
            ->get();

        $stockCritico = DB::table('stock as st')
            ->join('productos as p', 'st.idproducto', '=', 'p.idproducto')
            ->select('p.codigo', 'p.descripcion', 'st.cantidad')
            ->where('st.cantidad', '>', 0)
            ->where('st.cantidad', '<=', 5)
            ->where('p.estado', 'Activo')
            ->orderBy('st.cantidad')
            ->limit(5)
            ->get();

        return view('inicio.index', [
            'hoy' => $hoy,
            'ventasHoy' => $ventasHoy,
            'cobrosHoy' => $cobrosHoy,
            'cuentasPendientes' => $cuentasPendientes,
            'stockBajo' => $stockBajo,
            'cajaAbierta' => $cajaAbierta,
            'ultimasVentas' => $ultimasVentas,
            'cuentasVencidas' => $cuentasVencidas,
            'stockCritico' => $stockCritico,
        ]);
    }
}
