<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use DB;

class CuotaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * VER CUOTAS de un producto_cliente
     * GET: ventas/producto_clientes/cuotas/{idproducto_cliente}
     */
    public function cuotas($idproducto_cliente)
    {
        $pc = DB::table('producto_cliente as pc')
            ->join('clientes as c', 'pc.idcliente', '=', 'c.idcliente')
            ->join('productos as p', 'pc.idproducto', '=', 'p.idproducto')
            ->join('condicion as con', 'pc.idcondicion', '=', 'con.idcondicion')
            ->join('sucursales as s', 'pc.idsucursal', '=', 's.idsucursal')
            ->join('depositos as d', 'pc.iddeposito', '=', 'd.iddeposito')
            ->select(
                'pc.*',
                'c.nombre as cliente',
                'c.num_documento',
                'p.codigo',
                'p.descripcion as producto',
                'con.descripcion as condicion_desc',
                'con.cantidad_cuota',
                'con.intervalo',
                's.descripcion as sucursal',
                'd.descripcion as deposito'
            )
            ->where('pc.idproducto_cliente', $idproducto_cliente)
            ->first();

        if (!$pc) {
            abort(404, 'producto_cliente no encontrado');
        }

        $cuotas = DB::table('producto_cliente_cuota')
            ->where('idproducto_cliente', $idproducto_cliente)
            ->orderBy('cuota', 'asc')
            ->get();

        // Usá TU vista real. Si tu vista es otra, cambia este string.
        return view('ventas.cuota_producto.cuota_detalle', [
            'productocliente' => $pc,
            'productoclientecuota' => $cuotas,
        ]);
    }

    /**
     * PAGAR una cuota (resta saldo_cuota y marca estado)
     * POST: ventas/producto_clientes/cuotas/pagar
     * Body: idproducto_cliente_cuota, monto_pago
     */
    public function pagar(Request $request)
    {
        $request->validate([
            'idproducto_cliente_cuota' => 'required|integer',
            'monto_pago' => 'required|numeric|min:1',
        ]);

        $idcuota = (int)$request->idproducto_cliente_cuota;
        $monto   = (float)$request->monto_pago;

        DB::beginTransaction();
        try {
            // Lock cuota para evitar doble pago concurrente
            $cuota = DB::table('producto_cliente_cuota')
                ->where('idproducto_cliente_cuota', $idcuota)
                ->lockForUpdate()
                ->first();

            if (!$cuota) {
                DB::rollBack();
                return back()->with('error', 'Cuota no encontrada.');
            }

            $saldoActual = (float)$cuota->saldo_cuota;

            if ($saldoActual <= 0) {
                DB::rollBack();
                return back()->with('error', 'La cuota ya está pagada (saldo 0).');
            }

            if ($monto > $saldoActual) {
                DB::rollBack();
                return back()->with('error', 'El pago no puede superar el saldo de la cuota.');
            }

            $nuevoSaldo = $saldoActual - $monto;

            DB::table('producto_cliente_cuota')
                ->where('idproducto_cliente_cuota', $idcuota)
                ->update([
                    'saldo_cuota' => $nuevoSaldo,
                    'estado' => ($nuevoSaldo <= 0 ? 'PAGADO' : 'PARCIAL'),
                ]);

            // Recalcular saldo total del crédito
            $idpc = (int)$cuota->idproducto_cliente;

            $saldoTotal = (float) DB::table('producto_cliente_cuota')
                ->where('idproducto_cliente', $idpc)
                ->sum('saldo_cuota');

            DB::table('producto_cliente')
                ->where('idproducto_cliente', $idpc)
                ->update(['total_saldo' => (int)$saldoTotal]);

            // Si tu producto_cliente tiene columna "estado", la actualizo si queda saldo 0
            if (Schema::hasColumn('producto_cliente', 'estado')) {
                DB::table('producto_cliente')
                    ->where('idproducto_cliente', $idpc)
                    ->update(['estado' => ($saldoTotal <= 0 ? 'P' : 'A')]); // ajustá tus códigos
            }

            DB::commit();

            return Redirect::to('ventas/producto_clientes/cuotas/' . $idpc)
                ->with('success', 'Pago registrado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al pagar: ' . $e->getMessage());
        }
    }

    /**
     * EDITAR datos de una cuota (monto, fecha, saldo, refuerzo)
     * PUT/PATCH: ventas/producto_clientes/cuotas/{idproducto_cliente_cuota}
     */
    public function update(Request $request, $idproducto_cliente_cuota)
    {
        $request->validate([
            'monto_cuota' => 'required|numeric|min:0',
            'saldo_cuota' => 'required|numeric|min:0',
            'fecha_vto_cuota' => 'required|date',
            'refuerzo' => 'nullable|string|max:1',
        ]);

        $idcuota = (int)$idproducto_cliente_cuota;

        $cuota = DB::table('producto_cliente_cuota')
            ->where('idproducto_cliente_cuota', $idcuota)
            ->first();

        if (!$cuota) {
            return back()->with('error', 'Cuota no encontrada.');
        }

        DB::table('producto_cliente_cuota')
            ->where('idproducto_cliente_cuota', $idcuota)
            ->update([
                'monto_cuota' => (int)$request->monto_cuota,
                'saldo_cuota' => (int)$request->saldo_cuota,
                'fecha_vto_cuota' => $request->fecha_vto_cuota,
                'refuerzo' => $request->refuerzo ?? $cuota->refuerzo,
            ]);

        // Recalcular saldo total del crédito
        $idpc = (int)$cuota->idproducto_cliente;

        $saldoTotal = (float) DB::table('producto_cliente_cuota')
            ->where('idproducto_cliente', $idpc)
            ->sum('saldo_cuota');

        DB::table('producto_cliente')
            ->where('idproducto_cliente', $idpc)
            ->update(['total_saldo' => (int)$saldoTotal]);

        return Redirect::to('ventas/producto_clientes/cuotas/' . $idpc)
            ->with('success', 'Cuota actualizada.');
    }

    /**
     * ELIMINAR cuota (solo si NO tiene pagos: saldo_cuota == monto_cuota o según tu regla)
     * DELETE: ventas/producto_clientes/cuotas/eliminar/{idproducto_cliente_cuota}
     */
    public function destroy($idproducto_cliente_cuota)
    {
        $idcuota = (int)$idproducto_cliente_cuota;

        $cuota = DB::table('producto_cliente_cuota')
            ->where('idproducto_cliente_cuota', $idcuota)
            ->first();

        if (!$cuota) {
            return back()->with('error', 'Cuota no encontrada.');
        }

        $idpc = (int)$cuota->idproducto_cliente;

        // Regla simple: si saldo_cuota < monto_cuota => hubo pago (parcial o total)
        if ((float)$cuota->saldo_cuota < (float)$cuota->monto_cuota) {
            return Redirect::to('ventas/producto_clientes/cuotas/' . $idpc)
                ->with('error', 'No se puede eliminar: la cuota ya tiene pagos.');
        }

        DB::beginTransaction();
        try {
            DB::table('producto_cliente_cuota')
                ->where('idproducto_cliente_cuota', $idcuota)
                ->delete();

            $saldoTotal = (float) DB::table('producto_cliente_cuota')
                ->where('idproducto_cliente', $idpc)
                ->sum('saldo_cuota');

            DB::table('producto_cliente')
                ->where('idproducto_cliente', $idpc)
                ->update(['total_saldo' => (int)$saldoTotal]);

            DB::commit();

            return Redirect::to('ventas/producto_clientes/cuotas/' . $idpc)
                ->with('success', 'Cuota eliminada.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::to('ventas/producto_clientes/cuotas/' . $idpc)
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
