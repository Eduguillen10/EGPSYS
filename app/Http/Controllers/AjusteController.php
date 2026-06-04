<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjusteFormRequest;
use App\Models\Ajuste;
use App\Models\AjusteDetalle;
use App\Services\MovimientoStockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AjusteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = trim((string) $request->get('searchText'));
        $queryFecha = trim((string) $request->get('searchText2'));
        $querySucursal = trim((string) $request->get('searchText3'));
        $queryTipo = trim((string) $request->get('searchText4'));
        $queryEstado = trim((string) $request->get('searchText5'));

        $builder = DB::table('ajuste as a')
            ->join('depositos as d', 'a.iddeposito', '=', 'd.iddeposito')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('motivo as m', 'a.idmotivo', '=', 'm.idmotivo')
            ->join('tipo_ajuste as ta', 'a.idtipo_ajuste', '=', 'ta.idtipo_ajuste')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.idajuste',
                'a.fecha',
                'u.name as usuario',
                'a.observacion',
                'a.estado',
                's.descripcion as sucursal',
                'd.descripcion as deposito',
                'm.descripcion as motivo',
                'ta.descripcion as tipo_ajuste'
            );

        if ($query !== '') {
            $builder->where('a.idajuste', 'LIKE', '%' . $query . '%');
        }

        if ($queryFecha !== '') {
            $builder->where('a.fecha', 'LIKE', '%' . $queryFecha . '%');
        }

        if ($querySucursal !== '') {
            $builder->where('s.descripcion', 'LIKE', '%' . $querySucursal . '%');
        }

        if ($queryTipo !== '') {
            $builder->where('ta.descripcion', 'LIKE', '%' . $queryTipo . '%');
        }

        if ($queryEstado !== '') {
            $builder->where('a.estado', 'LIKE', '%' . $queryEstado . '%');
        }

        $ajustes = $builder->orderByDesc('a.idajuste')->paginate(7);

        return view('compras.ajuste.index', [
            'ajustes' => $ajustes,
            'searchText' => $query,
            'searchText2' => $queryFecha,
            'searchText3' => $querySucursal,
            'searchText4' => $queryTipo,
            'searchText5' => $queryEstado,
        ]);
    }

    public function create()
    {
        $suc = Auth::user()->trabaja_sucursal;
        $fecha = date('Y-m-d');

        $sucursal = DB::table('sucursales')
            ->where('idsucursal', '=', $suc)
            ->first();

        $depositos = DB::table('depositos')
            ->where('idsucursal', '=', $suc)
            ->where('estado', '=', 'Activo')
            ->orderBy('descripcion')
            ->get();

        $tiposAjuste = DB::table('tipo_ajuste')
            ->where('estado', '=', 'Activo')
            ->orderBy('descripcion')
            ->get();

        $motivos = DB::table('motivo')
            ->where('estado', '=', 'Activo')
            ->orderBy('descripcion')
            ->get();

        $productos = DB::table('productos as p')
            ->select(DB::raw('CONCAT(p.codigo, " - ", p.descripcion) AS producto'), 'p.idproducto')
            ->where('p.estado', '=', 'Activo')
            ->orderBy('p.descripcion')
            ->get();

        return view('compras.ajuste.create', compact(
            'fecha',
            'sucursal',
            'depositos',
            'tiposAjuste',
            'motivos',
            'productos'
        ));
    }

    public function store(AjusteFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->validarSucursalDeposito((int) $request->input('idsucursal'), (int) $request->input('iddeposito'));

            $tipo = DB::table('tipo_ajuste')
                ->where('idtipo_ajuste', '=', $request->input('idtipo_ajuste'))
                ->where('estado', '=', 'Activo')
                ->lockForUpdate()
                ->first();

            if (! $tipo) {
                throw new Exception('El tipo de ajuste seleccionado no esta activo.');
            }

            $operacion = $this->operacionDesdeTipo((string) $tipo->descripcion);

            $ajuste = Ajuste::create([
                'idsucursal' => (int) $request->input('idsucursal'),
                'iddeposito' => (int) $request->input('iddeposito'),
                'idtipo_ajuste' => (int) $request->input('idtipo_ajuste'),
                'idmotivo' => (int) $request->input('idmotivo'),
                'idusuario' => (int) Auth::id(),
                'fecha' => $request->input('fecha'),
                'observacion' => $request->input('observacion'),
                'estado' => 'Realizado',
            ]);

            $productos = $request->input('idproducto', []);
            $cantidades = $request->input('cantidad', []);
            $items = 1;

            foreach ($productos as $index => $idproducto) {
                $idproducto = (int) $idproducto;
                $cantidad = (float) ($cantidades[$index] ?? 0);

                if ($cantidad <= 0) {
                    throw new Exception('La cantidad debe ser mayor a cero.');
                }

                AjusteDetalle::create([
                    'idajuste' => (int) $ajuste->idajuste,
                    'idproducto' => $idproducto,
                    'items' => $items,
                    'cantidad' => $cantidad,
                ]);

                $this->aplicarMovimientoStock(
                    (int) $ajuste->idsucursal,
                    (int) $ajuste->iddeposito,
                    $idproducto,
                    $cantidad,
                    $operacion,
                    (int) $ajuste->idajuste,
                    'Ajuste de stock'
                );

                $items++;
            }

            DB::commit();

            return Redirect::route('ajuste.show', $ajuste->idajuste)
                ->with('success', 'Operacion exitosa.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $datos = $this->datosAjuste((int) $id);

        if (! $datos['ajuste']) {
            abort(404);
        }

        return view('compras.ajuste.show', $datos);
    }

    public function edit($id)
    {
        return Redirect::route('ajuste.show', $id);
    }

    public function update(Request $request, $id)
    {
        return Redirect::route('ajuste.show', $id);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $ajuste = Ajuste::where('idajuste', (int) $id)->lockForUpdate()->firstOrFail();

            if ($this->estadoEsCancelado($ajuste->estado)) {
                DB::rollBack();

                return Redirect::route('ajuste.index')->with('info', 'El ajuste ya estaba cancelado.');
            }

            $tipo = DB::table('tipo_ajuste')->where('idtipo_ajuste', '=', $ajuste->idtipo_ajuste)->first();
            $operacionOriginal = $this->operacionDesdeTipo((string) $tipo->descripcion);
            $operacionInversa = $operacionOriginal === 'ENTRADA' ? 'SALIDA' : 'ENTRADA';

            $detalles = AjusteDetalle::where('idajuste', (int) $ajuste->idajuste)->lockForUpdate()->get();

            foreach ($detalles as $detalle) {
                $this->aplicarMovimientoStock(
                    (int) $ajuste->idsucursal,
                    (int) $ajuste->iddeposito,
                    (int) $detalle->idproducto,
                    (float) $detalle->cantidad,
                    $operacionInversa,
                    (int) $ajuste->idajuste,
                    'Anulacion de ajuste de stock'
                );
            }

            $ajuste->estado = 'Cancelado';
            $ajuste->save();

            DB::commit();

            return Redirect::route('ajuste.index')->with('success', 'Ajuste anulado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::route('ajuste.index')->with('error', 'Error al anular el ajuste: ' . $e->getMessage());
        }
    }

    private function datosAjuste(int $id): array
    {
        $ajuste = DB::table('ajuste as a')
            ->join('depositos as d', 'a.iddeposito', '=', 'd.iddeposito')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('motivo as m', 'a.idmotivo', '=', 'm.idmotivo')
            ->join('tipo_ajuste as ta', 'a.idtipo_ajuste', '=', 'ta.idtipo_ajuste')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.idajuste',
                'a.fecha',
                'u.name as usuario',
                'a.observacion',
                'a.estado',
                's.descripcion as sucursal',
                'd.descripcion as deposito',
                'm.descripcion as motivo',
                'ta.descripcion as tipo_ajuste'
            )
            ->where('a.idajuste', '=', $id)
            ->first();

        $detalles = DB::table('ajuste_detalle as ad')
            ->join('productos as p', 'ad.idproducto', '=', 'p.idproducto')
            ->select('ad.items', 'p.codigo', 'p.descripcion as producto', 'ad.cantidad')
            ->where('ad.idajuste', '=', $id)
            ->orderBy('ad.items')
            ->get();

        return compact('ajuste', 'detalles');
    }

    private function validarSucursalDeposito(int $idsucursal, int $iddeposito): void
    {
        $existe = DB::table('depositos')
            ->where('idsucursal', '=', $idsucursal)
            ->where('iddeposito', '=', $iddeposito)
            ->where('estado', '=', 'Activo')
            ->exists();

        if (! $existe) {
            throw new Exception('El deposito seleccionado no pertenece a la sucursal actual o esta inactivo.');
        }
    }

    private function aplicarMovimientoStock(
        int $idsucursal,
        int $iddeposito,
        int $idproducto,
        float $cantidad,
        string $operacion,
        int $idajuste,
        string $observacion
    ): void {
        if ($operacion === 'ENTRADA') {
            $updated = DB::table('stock')
                ->where('idsucursal', '=', $idsucursal)
                ->where('iddeposito', '=', $iddeposito)
                ->where('idproducto', '=', $idproducto)
                ->increment('cantidad', $cantidad);

            if ($updated === 0) {
                DB::table('stock')->insert([
                    'idsucursal' => $idsucursal,
                    'iddeposito' => $iddeposito,
                    'idproducto' => $idproducto,
                    'cantidad' => $cantidad,
                ]);
            }
        } else {
            $stock = DB::table('stock')
                ->where('idsucursal', '=', $idsucursal)
                ->where('iddeposito', '=', $iddeposito)
                ->where('idproducto', '=', $idproducto)
                ->lockForUpdate()
                ->first();

            if (! $stock || (float) $stock->cantidad < $cantidad) {
                $producto = DB::table('productos')->where('idproducto', $idproducto)->value('descripcion') ?? $idproducto;
                $disponible = $stock ? (float) $stock->cantidad : 0;

                throw new Exception("Stock insuficiente para {$producto}. Disponible: {$disponible}, requerido: {$cantidad}.");
            }

            DB::table('stock')
                ->where('idsucursal', '=', $idsucursal)
                ->where('iddeposito', '=', $iddeposito)
                ->where('idproducto', '=', $idproducto)
                ->decrement('cantidad', $cantidad);
        }

        app(MovimientoStockService::class)->registrar(
            $idproducto,
            $idsucursal,
            $iddeposito,
            'AJUSTE',
            $idajuste,
            'ajuste_detalle',
            $operacion,
            $cantidad,
            null,
            $observacion
        );
    }

    private function operacionDesdeTipo(string $descripcion): string
    {
        $tipo = $this->normalizarTexto($descripcion);

        return match ($tipo) {
            'ENTRADA' => 'ENTRADA',
            'SALIDA' => 'SALIDA',
            default => throw new Exception('El tipo de ajuste debe ser Entrada o Salida.'),
        };
    }

    private function estadoEsCancelado(?string $estado): bool
    {
        return in_array($this->normalizarTexto((string) $estado), ['CANCELADO', 'CANCELADA', 'ANULADO', 'ANULADA'], true);
    }

    private function normalizarTexto(string $texto): string
    {
        return strtr(strtoupper(trim($texto)), [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
        ]);
    }
}
