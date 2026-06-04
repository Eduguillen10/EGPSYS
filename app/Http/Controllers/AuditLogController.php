<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->get('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->get('hasta'));
        }

        if ($request->filled('evento')) {
            $query->where('event', $request->get('evento'));
        }

        if ($request->filled('modelo')) {
            $query->where('auditable_type', $request->get('modelo'));
        }

        if ($request->filled('idusuario')) {
            $query->where('idusuario', $request->get('idusuario'));
        }

        if ($request->filled('registro')) {
            $query->where('auditable_id', $request->get('registro'));
        }

        if ($request->filled('ip')) {
            $query->where('ip_address', 'LIKE', '%' . trim($request->get('ip')) . '%');
        }

        $auditorias = $query->paginate(15);

        return view('acceso.auditoria.index', [
            'auditorias' => $auditorias,
            'filtros' => $request->only(['desde', 'hasta', 'evento', 'modelo', 'idusuario', 'registro', 'ip']),
            'eventos' => $this->eventos(),
            'modelos' => $this->modelos(),
            'usuarios' => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    private function eventos(): array
    {
        return [
            'CREATED' => 'Creacion',
            'UPDATED' => 'Actualizacion',
            'ANULACION' => 'Anulacion',
            'DELETED' => 'Eliminacion',
        ];
    }

    private function modelos(): array
    {
        return [
            \App\Models\Accion::class => 'Accion',
            \App\Models\Ajuste::class => 'Ajuste',
            \App\Models\AjusteDetalle::class => 'Detalle de Ajuste',
            \App\Models\Apertura::class => 'Apertura de Caja',
            \App\Models\AperturaDetalle::class => 'Detalle de Apertura',
            \App\Models\Bancos::class => 'Banco',
            \App\Models\Cajas::class => 'Caja',
            \App\Models\Cargos::class => 'Cargo',
            \App\Models\Chofer::class => 'Chofer',
            \App\Models\Ciudades::class => 'Ciudad',
            \App\Models\Clientes::class => 'Cliente',
            \App\Models\Cobro::class => 'Cobros',
            \App\Models\CobroDetalle::class => 'Detalle de Cobro',
            \App\Models\Compras::class => 'Compras',
            \App\Models\ComprasDetalle::class => 'Detalle de Compra',
            \App\Models\CuentaCobrar::class => 'Cuenta a Cobrar',
            \App\Models\Depositos::class => 'Deposito',
            \App\Models\Empleados::class => 'Empleado',
            \App\Models\Empresas::class => 'Empresa',
            \App\Models\EntidadEmisora::class => 'Entidad Emisora',
            \App\Models\FacturaCuota::class => 'Factura Cuota',
            \App\Models\FacturaCuotaDetalle::class => 'Detalle de Factura Cuota',
            \App\Models\FormaCobro::class => 'Forma de Cobro',
            \App\Models\FormaCobroDetalle::class => 'Forma de Cobro',
            \App\Models\Libroventas::class => 'Libro de Ventas',
            \App\Models\Marcas::class => 'Marca',
            \App\Models\Modulo::class => 'Modulo',
            \App\Models\Motivo::class => 'Motivo',
            \App\Models\Nacionalidades::class => 'Nacionalidad',
            \App\Models\NotaCreditoC::class => 'Nota de Credito Compra',
            \App\Models\NotaCreditocDetalle::class => 'Detalle Nota Credito Compra',
            \App\Models\NotaCreditoV::class => 'Nota de Credito Venta',
            \App\Models\NotaCreditovDetalle::class => 'Detalle Nota Credito Venta',
            \App\Models\NotaDebitoV::class => 'Nota de Debito Venta',
            \App\Models\NotaDebitovDetalle::class => 'Detalle Nota Debito Venta',
            \App\Models\OrdenCompras::class => 'Orden de Compra',
            \App\Models\OrdenComprasDetalle::class => 'Detalle Orden de Compra',
            \App\Models\PedidosCompras::class => 'Pedido de Compra',
            \App\Models\PedidosComprasDetalle::class => 'Detalle Pedido de Compra',
            \App\Models\PresupuestosCompras::class => 'Presupuesto de Compra',
            \App\Models\PresupuestosComprasDetalle::class => 'Detalle Presupuesto Compra',
            \App\Models\ProductoCliente::class => 'Producto Cliente',
            \App\Models\ProductoClienteCuota::class => 'Cuota Producto Cliente',
            \App\Models\Productos::class => 'Producto',
            \App\Models\Proveedores::class => 'Proveedor',
            \App\Models\Rubros::class => 'Rubro',
            \App\Models\Sucursales::class => 'Sucursal',
            \App\Models\Tarjeta::class => 'Tarjeta',
            \App\Models\Timbrado::class => 'Timbrado',
            \App\Models\TipoArqueo::class => 'Tipo de Arqueo',
            \App\Models\TipoImpuesto::class => 'Tipo de Impuesto',
            \App\Models\TiposClientes::class => 'Tipo de Cliente',
            \App\Models\TiposDocumentos::class => 'Tipo de Documento',
            \App\Models\User::class => 'Usuario',
            \App\Models\UsuarioSucursal::class => 'Usuario Sucursal',
            \App\Models\Vehiculos::class => 'Vehiculo',
            \App\Models\Ventana::class => 'Ventana',
            \App\Models\Ventas::class => 'Ventas',
            \App\Models\VentasDetalle::class => 'Detalle de Venta',
        ];
    }

}
