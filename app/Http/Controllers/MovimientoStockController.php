<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = DB::table('movimiento_stock as ms')
            ->join('productos as p', 'ms.idproducto', '=', 'p.idproducto')
            ->join('sucursales as s', 'ms.idsucursal', '=', 's.idsucursal')
            ->join('depositos as d', 'ms.iddeposito', '=', 'd.iddeposito')
            ->join('users as u', 'ms.idusuario', '=', 'u.id')
            ->select(
                'ms.idmovimiento_stock',
                'ms.fecha',
                'ms.tipo_origen',
                'ms.id_origen',
                'ms.detalle_origen',
                'ms.operacion',
                'ms.cantidad',
                'ms.costo_unitario',
                'ms.observacion',
                'u.name as usuario',
                'ms.estado',
                'p.codigo as producto_codigo',
                'p.descripcion as producto',
                's.descripcion as sucursal',
                'd.descripcion as deposito'
            );

        if ($request->filled('producto')) {
            $producto = trim((string) $request->get('producto'));
            $query->where(function ($q) use ($producto): void {
                $q->where('p.descripcion', 'LIKE', '%' . $producto . '%')
                    ->orWhere('p.codigo', 'LIKE', '%' . $producto . '%')
                    ->orWhere('ms.idproducto', $producto);
            });
        }

        if ($request->filled('sucursal')) {
            $query->where('s.descripcion', 'LIKE', '%' . trim((string) $request->get('sucursal')) . '%');
        }

        if ($request->filled('deposito')) {
            $query->where('d.descripcion', 'LIKE', '%' . trim((string) $request->get('deposito')) . '%');
        }

        if ($request->filled('tipo_origen')) {
            $query->where('ms.tipo_origen', trim((string) $request->get('tipo_origen')));
        }

        if ($request->filled('operacion')) {
            $query->where('ms.operacion', trim((string) $request->get('operacion')));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('ms.fecha', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('ms.fecha', '<=', $request->get('fecha_hasta'));
        }

        $movimientos = $query
            ->orderByDesc('ms.fecha')
            ->orderByDesc('ms.idmovimiento_stock')
            ->paginate(15);

        return view('referenciales.movimiento_stock.index', [
            'movimientos' => $movimientos,
            'filtros' => $request->only([
                'producto',
                'sucursal',
                'deposito',
                'tipo_origen',
                'operacion',
                'fecha_desde',
                'fecha_hasta',
            ]),
        ]);
    }
}
