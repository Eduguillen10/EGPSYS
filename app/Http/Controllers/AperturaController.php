<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\AperturaFormRequest;
use App\Models\Apertura;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class AperturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $apertura = DB::table('apertura as a')
            ->join('cajas as c', 'a.idcaja', '=', 'c.idcaja')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.idapertura',
                'c.descripcion as caja',
                's.descripcion as sucursal',
                'u.name as usuario',
                'a.fecha_apertura',
                'a.monto_inicial',
                'a.fecha_cierre',
                'a.monto_cierre',
                'a.estado'
            )
            ->orderBy('a.idapertura', 'desc')
            ->paginate(7);

        return view('ventas.apertura.index', ["apertura" => $apertura]);
    }

    public function listado(Request $request)
    {
        $query1 = trim($request->get('searchText1'));
        $query2 = trim($request->get('searchText2'));
        $query3 = trim($request->get('searchText3'));
        $query4 = trim($request->get('searchText4'));
        $query5 = trim($request->get('searchText5'));

        $apertura = DB::table('apertura as a')
            ->join('cajas as c', 'a.idcaja', '=', 'c.idcaja')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.idapertura',
                'c.descripcion as caja',
                's.descripcion as sucursal',
                'u.name as usuario',
                'a.fecha_apertura',
                'a.monto_inicial',
                'a.fecha_cierre',
                'a.monto_cierre',
                'a.estado'
            )
            ->where('a.idapertura', 'LIKE', '%' . $query1 . '%')
            ->where('c.descripcion', 'LIKE', '%' . $query2 . '%')
            ->where('u.name', 'LIKE', '%' . $query3 . '%')
            ->where('a.fecha_apertura', 'LIKE', '%' . $query4 . '%')
            ->where('a.monto_inicial', 'LIKE', '%' . $query5 . '%')
            ->orderBy('a.idapertura', 'desc')
            ->paginate(7);

        return view('ventas.apertura.listado', [
            "apertura" => $apertura,
            "searchText1" => $query1,
            "searchText2" => $query2,
            "searchText3" => $query3,
            "searchText4" => $query4,
            "searchText5" => $query5
        ]);
    }

    public function create()
    {
        $nextidapertura = DB::select("SHOW TABLE STATUS LIKE 'apertura'")[0]->Auto_increment;

        $suc = Auth::user()->trabaja_sucursal;

        $sucursales = DB::table('sucursales as s')
            ->select('s.idsucursal', 's.descripcion')
            ->where('s.idsucursal', '=', $suc)
            ->first();

        $caja = DB::table('cajas as c')->get();

        return view("ventas.apertura.create", [
            "caja" => $caja,
            "nextidapertura" => $nextidapertura,
            "sucursales" => $sucursales
        ]);
    }

    public function storeApertura(AperturaFormRequest $request)
    {
        // Validar si el usuario tiene alguna caja abierta
        $cajaAbierta = Apertura::where('idusuario', Auth::user()->id)
            ->where('estado', 'Abierto')
            ->first();

        if ($cajaAbierta) {
            return redirect()->route('apertura.create')
                ->with('error', 'Ya tienes una caja abierta. Por favor, ciérrala antes de abrir otra.');
        }

        $request->validate([
            'idcaja' => 'required|integer|exists:cajas,idcaja',
            'monto_inicial' => 'required|numeric|min:0',
            'fecha_apertura' => 'required|date',
            'idsucursal' => 'required|integer|exists:sucursales,idsucursal',
        ]);

        // Si tu campo idtipoarqueo NO es nullable, tomamos el ID del tipo "Parcial"
        $idTipoParcial = DB::table('tipoarqueo')
            ->where('descripcion', 'Parcial')
            ->value('idtipoarqueo'); // puede ser null si no existe

        $apertura = new Apertura();
        $apertura->idcaja = $request->get('idcaja');
        $apertura->idsucursal = $request->get('idsucursal');
        $apertura->monto_inicial = $request->get('monto_inicial');
        $apertura->fecha_apertura = $request->get('fecha_apertura');
        $apertura->estado = 'Abierto';
        $apertura->idusuario = Auth::user()->id;
        $apertura->idtipoarqueo = $idTipoParcial; // si tu columna es nullable, no pasa nada

        $apertura->save();

        return redirect()->route('apertura.index')->with('success', 'Caja abierta exitosamente.');
    }

    public function cierre(Request $request, $idapertura)
    {
        $apertura = Apertura::find($idapertura);
        if (!$apertura) {
            return Redirect::to('ventas/apertura')->with('error', 'No se encontró la caja con ese ID para cerrar.');
        }

        if ($apertura->estado === 'Cerrado') {
            return Redirect::to('ventas/apertura')->with('info', 'La caja ya estaba cerrada.');
        }

        DB::beginTransaction();
        try {
            // Totales por forma de cobro (IGNORANDO ANULADOS)
            $totalesPorForma = $this->getTotalesPorFormaCobroDesdeCobros($idapertura);

            $totalCobrado = (float) $totalesPorForma->sum('total_cobrado');

            // Evitar duplicados: limpiar snapshot anterior
            DB::table('apertura_detalle')->where('idapertura', $idapertura)->delete();

            // Insertar snapshot agrupado por forma de cobro
            $item = 1;
            foreach ($totalesPorForma as $row) {
                DB::table('apertura_detalle')->insert([
                    'idapertura'      => $idapertura,
                    'id_formacobro'   => $row->id_formacobro,
                    'items'           => $item,
                    'observacion'     => null,
                    'monto_ingresado' => (int) $row->total_cobrado,
                ]);
                $item++;
            }

            // Actualizar cabecera apertura
            $apertura->monto_cierre = (int) $totalCobrado;
            $apertura->fecha_cierre = Carbon::now('America/Asuncion');
            $apertura->estado = 'Cerrado';
            $apertura->save();

            DB::commit();

            return Redirect::to('ventas/apertura')->with('info', 'Caja cerrada exitosamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return Redirect::to('ventas/apertura')->with('error', 'Error al cerrar caja: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $apertura = DB::table('apertura as a')
            ->join('cajas as c', 'a.idcaja', '=', 'c.idcaja')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.*',
                'c.descripcion as caja',
                's.descripcion as sucursal',
                'u.name as usuario'
            )
            ->where('a.idapertura', $id)
            ->first();

        if (! $apertura) {
            abort(404, 'Apertura no encontrada.');
        }

        return view("ventas.apertura.show", ["apertura" => $apertura]);
    }

    // =========================
    // ARQUEOS
    // =========================

    public function arqueoParcial($idapertura)
    {
        // permitido siempre (idealmente caja abierta)
        return $this->renderArqueo($idapertura, 'Parcial');
    }

    public function arqueoFinal($idapertura)
    {
        $apertura = Apertura::find($idapertura);
        if (!$apertura) {
            return Redirect::to('ventas/apertura')->with('error', 'Apertura no encontrada.');
        }
        if ($apertura->estado !== 'Cerrado') {
            return Redirect::to('ventas/apertura')->with('error', 'El arqueo FINAL solo se habilita cuando la caja está Cerrada.');
        }

        return $this->renderArqueo($idapertura, 'Final');
    }

    private function renderArqueo(int $idapertura, string $tipo)
    {
        // Cabecera
        $datosArqueo = DB::table('apertura as a')
            ->join('cajas as c', 'a.idcaja', '=', 'c.idcaja')
            ->join('sucursales as s', 'a.idsucursal', '=', 's.idsucursal')
            ->join('users as u', 'a.idusuario', '=', 'u.id')
            ->select(
                'a.idapertura',
                'c.descripcion as caja',
                's.descripcion as sucursal',
                'u.name as usuario',
                'a.monto_inicial',
                'a.monto_cierre',
                'a.fecha_apertura',
                'a.fecha_cierre',
                'a.estado'
            )
            ->where('a.idapertura', $idapertura)
            ->first();

        if (!$datosArqueo) {
            return Redirect::to('ventas/apertura')->with('error', 'No se encontraron datos de apertura para arqueo.');
        }

        // Totales por forma de cobro (desde cobros + det_formacobro; ignora Anulados)
        $porForma = $this->getTotalesPorFormaCobroDesdeCobros($idapertura);
        $devoluciones = $this->getDevolucionesPorAnulacion($idapertura);

        $subtotalCobrado = (float) $porForma->sum('total_cobrado');
        $totalDevoluciones = (float) $devoluciones->sum('monto_devolucion');
        $montoApertura = (float) $datosArqueo->monto_inicial;
        $montoCierre = (float) ($datosArqueo->monto_cierre ?? 0);
        $total = $montoApertura + $subtotalCobrado;

        return view('ventas.arqueo.generado', compact(
            'tipo',
            'datosArqueo',
            'porForma',
            'devoluciones',
            'subtotalCobrado',
            'totalDevoluciones',
            'montoApertura',
            'montoCierre',
            'total'
        ));
    }

    /**
     * Totales por forma de cobro usando:
     * cobros (idapertura) -> det_formacobro (id_cobro) -> formacobro
     * Ignora cobros anulado: cobro_estado = 'Anulado'
     */
    private function getTotalesPorFormaCobroDesdeCobros(int $idapertura)
    {
        return DB::table('cobros as c')
            ->join('det_formacobro as dfc', 'c.id_cobro', '=', 'dfc.id_cobro')
            ->join('formacobro as fc', 'dfc.id_formacobro', '=', 'fc.id_formacobro')
            ->select(
                'dfc.id_formacobro as id_formacobro',
                'fc.descripcion as formacobro',
                DB::raw('SUM(dfc.monto_detformacobro) as total_cobrado')
            )
            ->where('c.idapertura', '=', $idapertura)
            ->where(function ($q) {
                $q->whereNull('c.cobro_estado')
                  ->orWhere('c.cobro_estado', '<>', 'Anulado');
            })
            ->groupBy('dfc.id_formacobro', 'fc.descripcion')
            ->orderBy('fc.descripcion')
            ->get();
    }

    private function getDevolucionesPorAnulacion(int $idapertura)
    {
        return DB::table('cobros as c')
            ->join('det_formacobro as dfc', 'c.id_cobro', '=', 'dfc.id_cobro')
            ->leftJoin('formacobro as fc', 'dfc.id_formacobro', '=', 'fc.id_formacobro')
            ->leftJoin('det_cobro as dc', 'c.id_cobro', '=', 'dc.id_cobro')
            ->leftJoin('ventas as v', 'dc.idventa', '=', 'v.idventa')
            ->leftJoin('users as u', 'c.idusuario', '=', 'u.id')
            ->select(
                'c.id_cobro',
                'c.fecha_cobro',
                'u.name as usuario',
                'c.idcliente',
                'fc.descripcion as formacobro',
                'dfc.documento',
                'dfc.monto_detformacobro as monto_devolucion',
                'v.idventa',
                'v.nro_factura'
            )
            ->where('c.idapertura', '=', $idapertura)
            ->where('c.cobro_estado', '<>', 'Anulado')
            ->where('dfc.monto_detformacobro', '<', 0)
            ->orderBy('c.fecha_cobro')
            ->orderBy('c.id_cobro')
            ->get();
    }
}
