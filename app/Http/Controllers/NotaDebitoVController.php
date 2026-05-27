<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotaDebitoVRequest;
use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\NotaDebitoV;
use App\Models\NotaDebitovDetalle;
use App\Models\Sucursales;
use App\Models\Ventas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotaDebitoVController extends Controller
{
    private const ESTADOS_ANULADOS = ['A', 'Anulado', 'Anulada', 'Cancelado', 'Cancelada'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = $request->get('searchText');
        $query2 = $request->get('searchText2');
        $query3 = $request->get('searchText3');
        $query4 = $request->get('searchText4');
        $query5 = $request->get('searchText5');
        $query6 = $request->get('searchText6');

        $nota_debitov = DB::table('nota_debito_venta as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->select(
                'c.idnota_debitov',
                'c.idventa',
                'c.usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'dep.iddeposito',
                'dep.descripcion as deposito',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'c.fecha_registro',
                'c.totaliva10',
                'c.totaliva5',
                'c.totalgravada10',
                'c.totalgravada5',
                'c.totalexenta',
                'c.totalventa',
                'c.timbrado',
                'c.condicion',
                'c.concepto',
                'c.nro_factura',
                'c.estado',
                'c.fecha_factura',
                'c.fecha_vencimiento'
            )
            ->where('c.idnota_debitov', 'LIKE', '%' . $query . '%')
            ->where('cli.nombre', 'LIKE', '%' . $query2 . '%')
            ->where('c.fecha_registro', 'LIKE', '%' . $query3 . '%')
            ->where('s.descripcion', 'LIKE', '%' . $query4 . '%')
            ->where('c.nro_factura', 'LIKE', '%' . $query5 . '%')
            ->where('cli.num_documento', 'LIKE', '%' . $query6 . '%')
            ->orderBy('c.idnota_debitov', 'desc')
            ->paginate(7);

        return view('ventas.nota_debitov.index', [
            'nota_debitov' => $nota_debitov,
            'searchText' => $query,
            'searchText2' => $query2,
            'searchText3' => $query3,
            'searchText4' => $query4,
            'searchText5' => $query5,
            'searchText6' => $query6,
        ]);
    }

    public function create(Request $request)
    {
        $suc = Auth::user()->trabaja_sucursal;

        $venta = DB::table('ventas as v')
            ->join('sucursales as s', 'v.idsucursal', '=', 's.idsucursal')
            ->join('clientes as cli', 'v.idcliente', '=', 'cli.idcliente')
            ->join('depositos as dep', 'v.iddeposito', '=', 'dep.iddeposito')
            ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')
            ->select(
                'v.idventa',
                'v.usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'dep.iddeposito',
                'dep.descripcion as deposito',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'v.fecha',
                'v.totaliva10',
                'v.totaliva5',
                'v.totalgravada10',
                'v.totalgravada5',
                'v.totalexenta',
                'v.totalventa',
                't.idtimbrado',
                't.nro_timbrado',
                'v.condicion',
                'v.obs',
                'v.nro_factura',
                'v.estado'
            )
            ->where('v.idsucursal', '=', $suc)
            ->whereIn('v.estado', ['Realizado', 'R', 'Pendiente', 'P'])
            ->get();

        $clientes = DB::table('clientes as cli')
            ->select('cli.idcliente', 'cli.nombre', 'cli.num_documento', 'cli.direccion')
            ->get();

        $sucursales = Sucursales::find($suc);
        $depositos = $sucursales ? $sucursales->depositos()->select('iddeposito', 'descripcion')->get() : collect();

        $productos = DB::table('productos as prod')
            ->select(DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'), 'prod.idproducto')
            ->where('prod.estado', '=', 'Activo')
            ->get();

        $facturas_ya_almacenadas = NotaDebitoV::whereNotIn('estado', self::ESTADOS_ANULADOS)
            ->pluck('idventa')
            ->toArray();

        $facturaSeleccionada = null;
        $detallesSeleccionados = collect();

        if ($request->filled('idventa')) {
            $facturaSeleccionada = DB::table('ventas as v')
                ->join('clientes as cli', 'v.idcliente', '=', 'cli.idcliente')
                ->join('timbrado as t', 'v.idtimbrado', '=', 't.idtimbrado')
                ->select('v.idventa', 'v.idcliente', 'v.iddeposito', 'v.num_documento', 'v.nro_factura', 'v.condicion', 't.nro_timbrado')
                ->where('v.idventa', (int) $request->get('idventa'))
                ->first();

            $detallesSeleccionados = DB::table('venta_detalle as vd')
                ->join('productos as prod', 'vd.idproducto', '=', 'prod.idproducto')
                ->select('vd.idproducto', DB::raw('CONCAT(prod.codigo, " " ,prod.descripcion) AS producto'), 'vd.cantidad', 'vd.precio_venta')
                ->where('vd.idventa', (int) $request->get('idventa'))
                ->orderBy('vd.items')
                ->get();
        }

        return view('ventas.nota_debitov.create', [
            'clientes' => $clientes,
            'sucursales' => $sucursales,
            'depositos' => $depositos,
            'productos' => $productos,
            'venta' => $venta,
            'facturas_ya_almacenadas' => $facturas_ya_almacenadas,
            'facturaSeleccionada' => $facturaSeleccionada,
            'detallesSeleccionados' => $detallesSeleccionados,
            'fechaFacturaSeleccionada' => $request->get('fecha'),
            'conceptoSeleccionado' => $request->get('concepto'),
        ]);
    }

    public function store(NotaDebitoVRequest $request)
    {
        DB::beginTransaction();

        try {
            $nota = NotaDebitoV::create([
                'idcliente' => (int) $request->get('idcliente'),
                'num_documento' => $request->get('num_documento'),
                'idventa' => (int) $request->get('idventa'),
                'idsucursal' => (int) $request->get('idsucursal'),
                'iddeposito' => (int) $request->get('iddeposito'),
                'usuario' => $request->get('usuario'),
                'nro_factura' => $request->get('nro_factura'),
                'concepto' => $request->get('concepto'),
                'timbrado' => $request->get('timbrado'),
                'fecha_factura' => $request->get('fecha_factura'),
                'fecha_registro' => now()->toDateString(),
                'estado' => 'Realizado',
                'totaliva10' => 0,
                'totaliva5' => 0,
                'totalgravada10' => 0,
                'totalgravada5' => 0,
                'totalexenta' => 0,
                'totalventa' => 0,
            ]);

            $totales = $this->guardarDetallesNuevos($nota, $request->get('idproducto'), $request->get('cantidad'), $request->get('precio_venta'));

            $nota->fill($totales);
            $nota->save();

            $this->recalcularDeudaVenta((int) $nota->idventa);
            $this->sincronizarCobroPendientePorVenta((int) $nota->idventa, (int) $nota->idcliente);

            DB::commit();

            return redirect('ventas/nota_debitov')->with('success', 'Nota de debito registrada correctamente.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Error al registrar nota de debito: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $nota_debitov = $this->consultaCabecera((int) $id);

        if (! $nota_debitov) {
            return redirect('ventas/nota_debitov')->with('error', 'Nota de debito no encontrada.');
        }

        $detalles = DB::table('nota_debito_venta_detalle as d')
            ->join('productos as prod', 'd.idproducto', '=', 'prod.idproducto')
            ->select('d.idnota_debitov_detalle', 'prod.idproducto', 'prod.descripcion as producto', 'd.cantidad', 'd.precio_venta', 'd.iva10', 'd.iva5', 'd.gravada10', 'd.gravada5', 'd.exenta', 'd.totalitems')
            ->where('d.idnota_debitov', '=', $id)
            ->get();

        return view('ventas.nota_debitov.show', [
            'nota_debitov' => $nota_debitov,
            'detalles' => $detalles,
        ]);
    }

    public function edit($id)
    {
        $nota_debitov = $this->consultaCabecera((int) $id);

        if (! $nota_debitov) {
            return redirect('ventas/nota_debitov')->with('error', 'Nota de debito no encontrada.');
        }

        if ($this->estaAnulado((string) $nota_debitov->estado)) {
            return redirect('ventas/nota_debitov')->with('error', 'No se puede modificar una nota de debito anulada.');
        }

        $detalles = DB::table('nota_debito_venta_detalle as d')
            ->join('productos as prod', 'd.idproducto', '=', 'prod.idproducto')
            ->select('d.idnota_debitov_detalle', 'prod.idproducto', 'prod.descripcion as producto', 'd.cantidad', 'd.precio_venta', 'd.iva10', 'd.iva5', 'd.gravada10', 'd.gravada5', 'd.exenta', 'd.totalitems')
            ->where('d.idnota_debitov', '=', $id)
            ->get();

        return view('ventas.nota_debitov.edit', [
            'nota_debitov' => $nota_debitov,
            'detalles' => $detalles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idnota_debitov_detalle' => ['required', 'array', 'min:1'],
            'idnota_debitov_detalle.*' => ['required', 'integer', 'exists:nota_debito_venta_detalle,idnota_debitov_detalle'],
            'idproducto' => ['required', 'array'],
            'cantidad' => ['required', 'array'],
            'precio_venta' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        try {
            $nota = NotaDebitoV::where('idnota_debitov', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estaAnulado((string) $nota->estado)) {
                DB::rollBack();

                return redirect('ventas/nota_debitov')->with('error', 'No se puede modificar una nota de debito anulada.');
            }

            $oldTotal = (int) ($nota->totalventa ?? 0);
            $yaAplicada = $oldTotal > 0;
            $idsDetalle = $request->get('idnota_debitov_detalle');
            $productos = $request->get('idproducto');
            $cantidades = $request->get('cantidad');
            $precios = $request->get('precio_venta');

            $oldDetalles = DB::table('nota_debito_venta_detalle')
                ->where('idnota_debitov', (int) $id)
                ->whereIn('idnota_debitov_detalle', $idsDetalle)
                ->lockForUpdate()
                ->get()
                ->keyBy('idnota_debitov_detalle');

            $totales = $this->totalesVacios();
            $items = 1;

            foreach ($idsDetalle as $detalleId) {
                $detalleId = (int) $detalleId;
                $detalleAnterior = $oldDetalles->get($detalleId);

                if (! $detalleAnterior) {
                    throw new \RuntimeException("Detalle {$detalleId} no encontrado en la nota de debito.");
                }

                $idproducto = (int) ($productos[$detalleId] ?? 0);
                $cantidad = (int) ($cantidades[$detalleId] ?? 0);
                $precio = (int) ($precios[$detalleId] ?? 0);

                if ($idproducto <= 0 || $cantidad <= 0 || $precio <= 0) {
                    throw new \RuntimeException('Producto, cantidad y precio deben ser mayores a cero.');
                }

                $deltaStock = $yaAplicada ? ($cantidad - (int) $detalleAnterior->cantidad) : $cantidad;

                if ($deltaStock > 0) {
                    $this->descontarStock((int) $nota->idsucursal, (int) $nota->iddeposito, $idproducto, $deltaStock);
                } elseif ($deltaStock < 0) {
                    $this->sumarStock((int) $nota->idsucursal, (int) $nota->iddeposito, $idproducto, abs($deltaStock));
                }

                $linea = $this->calcularLinea($idproducto, $cantidad, $precio, $items);
                $detalle = NotaDebitovDetalle::where('idnota_debitov_detalle', $detalleId)->lockForUpdate()->firstOrFail();
                $detalle->fill($linea);
                $detalle->save();

                $this->acumularTotales($totales, $linea);
                $items++;
            }

            $nota->fill($totales);
            $nota->estado = 'Realizado';
            $nota->save();

            if ((int) $totales['totalventa'] !== $oldTotal) {
                $this->recalcularDeudaVenta((int) $nota->idventa);
                $this->sincronizarCobroPendientePorVenta((int) $nota->idventa, (int) $nota->idcliente);
            }

            DB::commit();

            return redirect('ventas/nota_debitov/' . $id)->with('success', 'Nota de debito actualizada correctamente.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Error al actualizar nota de debito: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $nota = NotaDebitoV::where('idnota_debitov', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estaAnulado((string) $nota->estado)) {
                DB::rollBack();

                return redirect('ventas/nota_debitov')->with('error', 'La nota de debito ya se encuentra anulada.');
            }

            $monto = (int) ($nota->totalventa ?? 0);

            if ($monto > 0) {
                $detalles = DB::table('nota_debito_venta_detalle')
                    ->where('idnota_debitov', (int) $id)
                    ->lockForUpdate()
                    ->get();

                foreach ($detalles as $detalle) {
                    $this->sumarStock((int) $nota->idsucursal, (int) $nota->iddeposito, (int) $detalle->idproducto, (int) $detalle->cantidad);
                }

            }

            $nota->estado = 'Anulado';
            $nota->concepto = trim((string) $nota->concepto . ' | ANULACION: ' . request('motivo_anulacion', 'Sin motivo informado'));
            $nota->save();

            $this->recalcularDeudaVenta((int) $nota->idventa);
            $this->sincronizarCobroPendientePorVenta((int) $nota->idventa, (int) $nota->idcliente);

            DB::commit();

            return redirect('ventas/nota_debitov')->with('success', 'Nota de debito anulada correctamente.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect('ventas/nota_debitov')->with('error', 'Error al anular nota de debito: ' . $e->getMessage());
        }
    }

    public function destroydetalle($id)
    {
        $detalle = NotaDebitovDetalle::findOrFail((int) $id);
        $idcab = $detalle->idnota_debitov;
        $nota = NotaDebitoV::find($idcab);

        if ($nota && (int) ($nota->totalventa ?? 0) > 0 && ! $this->estaAnulado((string) $nota->estado)) {
            return redirect('ventas/nota_debitov/' . $idcab . '/edit')
                ->with('error', 'No se puede eliminar un detalle de una nota ya aplicada. Ajuste cantidad/precio o anule la nota.');
        }

        $detalle->delete();

        return redirect('ventas/nota_debitov/' . $idcab . '/edit');
    }

    public function insertar_facturas(Request $request)
    {
        $request->validate([
            'idventa' => ['required', 'integer', 'exists:ventas,idventa'],
            'fecha' => ['required', 'date'],
            'concepto' => ['required', 'string', 'max:100'],
        ]);

        return redirect()->route('nota_debitov.create', [
            'idventa' => $request->get('idventa'),
            'fecha' => $request->get('fecha'),
            'concepto' => $request->get('concepto'),
        ]);
    }

    private function consultaCabecera(int $id): ?object
    {
        return DB::table('nota_debito_venta as c')
            ->join('sucursales as s', 'c.idsucursal', '=', 's.idsucursal')
            ->join('depositos as dep', 'c.iddeposito', '=', 'dep.iddeposito')
            ->join('clientes as cli', 'c.idcliente', '=', 'cli.idcliente')
            ->select(
                'c.idnota_debitov',
                'c.idventa',
                'c.usuario',
                's.idsucursal',
                's.descripcion as sucursal',
                'dep.iddeposito',
                'dep.descripcion as deposito',
                'cli.idcliente',
                'cli.nombre as cliente',
                'cli.num_documento',
                'c.fecha_registro',
                'c.totaliva10',
                'c.totaliva5',
                'c.totalgravada10',
                'c.totalgravada5',
                'c.totalexenta',
                'c.totalventa',
                'c.timbrado',
                'c.condicion',
                'c.concepto',
                'c.nro_factura',
                'c.estado',
                'c.fecha_factura',
                'c.fecha_vencimiento'
            )
            ->where('c.idnota_debitov', '=', $id)
            ->first();
    }

    private function guardarDetallesNuevos(NotaDebitoV $nota, array $productos, array $cantidades, array $precios): array
    {
        $totales = $this->totalesVacios();
        $items = 1;

        foreach ($productos as $index => $idproducto) {
            $idproducto = (int) $idproducto;
            $cantidad = (int) ($cantidades[$index] ?? 0);
            $precio = (int) ($precios[$index] ?? 0);

            if ($idproducto <= 0 || $cantidad <= 0 || $precio <= 0) {
                throw new \RuntimeException('Producto, cantidad y precio deben ser mayores a cero.');
            }

            $this->descontarStock((int) $nota->idsucursal, (int) $nota->iddeposito, $idproducto, $cantidad);

            $linea = $this->calcularLinea($idproducto, $cantidad, $precio, $items);
            $linea['idnota_debitov'] = $nota->idnota_debitov;

            NotaDebitovDetalle::create($linea);
            $this->acumularTotales($totales, $linea);
            $items++;
        }

        return $totales;
    }

    private function calcularLinea(int $idproducto, int $cantidad, int $precio, int $items): array
    {
        $producto = DB::table('productos as prod')
            ->join('tipo_impuesto as ti', 'prod.idtipoimpuesto', '=', 'ti.idtipoimpuesto')
            ->select('prod.idproducto', 'prod.descripcion', 'ti.porcentaje')
            ->where('prod.idproducto', $idproducto)
            ->first();

        if (! $producto) {
            throw new \RuntimeException("Producto {$idproducto} no encontrado.");
        }

        $porcentaje = (int) $producto->porcentaje;
        $totalitems = $cantidad * $precio;
        $iva10 = 0;
        $iva5 = 0;
        $gravada10 = 0;
        $gravada5 = 0;
        $exenta = 0;

        if ($porcentaje === 10) {
            $iva10 = (int) round($totalitems / ((100 + $porcentaje) / $porcentaje));
            $gravada10 = $totalitems - $iva10;
        } elseif ($porcentaje === 5) {
            $iva5 = (int) round($totalitems / ((100 + $porcentaje) / $porcentaje));
            $gravada5 = $totalitems - $iva5;
        } else {
            $exenta = $totalitems;
        }

        return [
            'items' => $items,
            'idproducto' => $idproducto,
            'cantidad' => $cantidad,
            'precio_venta' => $precio,
            'iva10' => $iva10,
            'iva5' => $iva5,
            'gravada10' => $gravada10,
            'gravada5' => $gravada5,
            'exenta' => $exenta,
            'totalitems' => $totalitems,
        ];
    }

    private function totalesVacios(): array
    {
        return [
            'totaliva10' => 0,
            'totaliva5' => 0,
            'totalgravada10' => 0,
            'totalgravada5' => 0,
            'totalexenta' => 0,
            'totalventa' => 0,
        ];
    }

    private function acumularTotales(array &$totales, array $linea): void
    {
        $totales['totaliva10'] += (int) $linea['iva10'];
        $totales['totaliva5'] += (int) $linea['iva5'];
        $totales['totalgravada10'] += (int) $linea['gravada10'];
        $totales['totalgravada5'] += (int) $linea['gravada5'];
        $totales['totalexenta'] += (int) $linea['exenta'];
        $totales['totalventa'] += (int) $linea['totalitems'];
    }

    private function descontarStock(int $idsucursal, int $iddeposito, int $idproducto, int $cantidad): void
    {
        $stock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->lockForUpdate()
            ->first();

        if (! $stock || (float) $stock->cantidad < $cantidad) {
            $producto = DB::table('productos')->where('idproducto', $idproducto)->value('descripcion') ?? $idproducto;
            $disponible = $stock ? (float) $stock->cantidad : 0;

            throw new \RuntimeException("Stock insuficiente para {$producto}. Disponible: {$disponible}, requerido: {$cantidad}.");
        }

        DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto)
            ->decrement('cantidad', $cantidad);
    }

    private function sumarStock(int $idsucursal, int $iddeposito, int $idproducto, int $cantidad): void
    {
        $baseStock = DB::table('stock')
            ->where('idsucursal', $idsucursal)
            ->where('iddeposito', $iddeposito)
            ->where('idproducto', $idproducto);

        $updated = (clone $baseStock)->increment('cantidad', $cantidad);

        if ($updated === 0) {
            DB::table('stock')->insert([
                'idsucursal' => $idsucursal,
                'iddeposito' => $iddeposito,
                'idproducto' => $idproducto,
                'cantidad' => $cantidad,
            ]);
        }
    }

    private function recalcularDeudaVenta(int $idventa): void
    {
        $venta = Ventas::where('idventa', $idventa)->lockForUpdate()->firstOrFail();
        $importe = $this->importeVentaAjustado($idventa, (int) $venta->totalventa);
        $pagadoReal = (int) DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->leftJoin('nota_credito_venta_cobro as ncc', 'c.id_cobro', '=', 'ncc.id_cobro')
            ->where('dc.idventa', $idventa)
            ->where('c.cobro_estado', 'Realizado')
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
            ->sum('dc.monto_detcobro');

        $pagadoReal = min($pagadoReal, $importe);
        $saldo = max(0, $importe - $pagadoReal);
        $estado = $saldo === 0 ? 'Pagado' : ($pagadoReal > 0 ? 'Parcial' : 'Generado');

        $cta = DB::table('cuenta_cobrar')
            ->where('idventa', $idventa)
            ->lockForUpdate()
            ->first();

        if (! $cta) {
            DB::table('cuenta_cobrar')->insert([
                'idcliente' => $venta->idcliente,
                'fecha' => $venta->fecha ?? now()->toDateString(),
                'obs' => 'Generado por nota de debito de venta',
                'estado' => $estado,
                'monto_pago' => $pagadoReal,
                'saldo' => $saldo,
                'idventa' => $idventa,
                'condicion' => $venta->condicion,
                'importe' => $importe,
                'fecha_vencimiento' => $venta->fecha ?? now()->toDateString(),
            ]);
        } else {
            DB::table('cuenta_cobrar')
                ->where('idcuenta_cobrar', $cta->idcuenta_cobrar)
                ->update([
                    'importe' => $importe,
                    'monto_pago' => $pagadoReal,
                    'saldo' => $saldo,
                    'estado' => $estado,
                ]);
        }

        $venta->saldo_factura = $saldo;
        $venta->estado = $saldo === 0 ? 'Realizado' : 'Pendiente';
        $venta->save();
    }

    private function importeVentaAjustado(int $idventa, int $totalVenta): int
    {
        $totalCredito = (int) DB::table('nota_credito_venta')
            ->where('idventa', $idventa)
            ->whereNotIn('estado', self::ESTADOS_ANULADOS)
            ->sum('totalventa');

        $totalDebito = (int) DB::table('nota_debito_venta')
            ->where('idventa', $idventa)
            ->whereNotIn('estado', self::ESTADOS_ANULADOS)
            ->sum('totalventa');

        return max(0, $totalVenta + $totalDebito - $totalCredito);
    }

    private function sincronizarCobroPendientePorVenta(int $idventa, int $idcliente): void
    {
        $cta = DB::table('cuenta_cobrar')
            ->where('idventa', $idventa)
            ->lockForUpdate()
            ->first();

        if (! $cta) {
            return;
        }

        $saldo = (int) $cta->saldo;
        $pendiente = DB::table('det_cobro as dc')
            ->join('cobros as c', 'dc.id_cobro', '=', 'c.id_cobro')
            ->where('dc.idventa', $idventa)
            ->where('c.cobro_estado', 'Pendiente')
            ->select('c.id_cobro', 'dc.id_detcobro')
            ->orderByDesc('c.id_cobro')
            ->first();

        if ($saldo <= 0) {
            if ($pendiente) {
                $cobroPendiente = Cobro::findOrFail($pendiente->id_cobro);
                $cobroPendiente->cobro_estado = 'Anulado';
                $cobroPendiente->usuario = Auth::user()->name;
                $cobroPendiente->save();
            }

            return;
        }

        if ($pendiente) {
            $cobroPendiente = Cobro::findOrFail($pendiente->id_cobro);
            $cobroPendiente->monto_cobro = $saldo;
            $cobroPendiente->usuario = Auth::user()->name;
            $cobroPendiente->save();

            $detallePendiente = CobroDetalle::findOrFail($pendiente->id_detcobro);
            $detallePendiente->monto_detcobro = $saldo;
            $detallePendiente->save();

            return;
        }

        $apertura = DB::table('apertura')
            ->where('idsucursal', (int) Auth::user()->trabaja_sucursal)
            ->where('estado', 'Abierto')
            ->orderByDesc('idapertura')
            ->first();

        if (! $apertura) {
            throw new \RuntimeException('La nota de debito genera un saldo pendiente. Debe existir una caja abierta para crear el cobro pendiente.');
        }

        $cobro = Cobro::create([
            'idcaja' => $apertura->idcaja,
            'idsucursal' => $apertura->idsucursal,
            'idapertura' => $apertura->idapertura,
            'fecha_cobro' => now()->toDateString(),
            'idcliente' => $idcliente,
            'monto_cobro' => $saldo,
            'cobro_estado' => 'Pendiente',
            'usuario' => Auth::user()->name,
        ]);

        CobroDetalle::create([
            'id_cobro' => $cobro->id_cobro,
            'items' => 1,
            'idventa' => $idventa,
            'monto_detcobro' => $saldo,
        ]);
    }

    private function calcularFechaVencimiento(string $fechaFactura, string $condicion): string
    {
        $dias = 0;

        if (mb_strtolower(trim($condicion)) !== 'contado') {
            preg_match_all('/\d+/', $condicion, $matches);
            $dias = array_sum($matches[0]);
        }

        return Carbon::createFromFormat('Y-m-d', $fechaFactura)
            ->copy()
            ->addDays($dias)
            ->toDateString();
    }

    private function estaAnulado(string $estado): bool
    {
        return in_array($estado, self::ESTADOS_ANULADOS, true);
    }
}
