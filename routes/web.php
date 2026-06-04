<?php
use App\Http\Controllers\MarcasController;
use App\Http\Controllers\RubrosController;
use App\Http\Controllers\BancosController;
use App\Http\Controllers\NacionalidadesController;
use App\Http\Controllers\CiudadesController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\TipoImpuestoController;
use App\Http\Controllers\TipoAjusteController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\TiposDocumentosController;
use App\Http\Controllers\TiposClientesController;
use App\Http\Controllers\DepositosController;
use App\Http\Controllers\CajasController;
use App\Http\Controllers\TipoArqueoController;
use App\Http\Controllers\VehiculosController;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\TransportistaController;
use App\Http\Controllers\DestinatarioRemisionController;
use App\Http\Controllers\FormaCobroController;
use App\Http\Controllers\EntidadEmisoraController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\MotivoController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\SucursalesController;
use App\Http\Controllers\TimbradoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginAttemptController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\VentanaController;
use App\Http\Controllers\AccionController;
use App\Http\Controllers\ReferencialEstadoController;
use App\Http\Controllers\SeleccionarController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MovimientoStockController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\PedidosComprasController;
use App\Http\Controllers\PresupuestosComprasController;
use App\Http\Controllers\OrdenComprasController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CuentasPagarController;
use App\Http\Controllers\LibroComprasController;
use App\Http\Controllers\NotaCreditoCController;
use App\Http\Controllers\NotaDebitoCController;
use App\Http\Controllers\NotaRemisionCompraController;
use App\Http\Controllers\AjusteController;
use App\Http\Controllers\CobroController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\CuentaCobrarController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\LibroVentasController;
use App\Http\Controllers\NotaCreditoVController;
use App\Http\Controllers\NotaDebitoVController;
use App\Http\Controllers\VentaCreditoAceptacionController;
use App\Http\Controllers\NotaRemisionVentaController;

use App\Http\Controllers\AperturaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes(['register' => false]);

Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/two-factor', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])
    ->middleware('throttle:3,1')
    ->name('two-factor.resend');

Route::middleware(['auth', 'permiso.ruta'])->group(function () {
    Route::get('/seleccionar-empresa-sucursal', [UsuarioController::class, 'verSeleccionarEmpresaSucursal'])->name('seleccionar-empresa-sucursal.get');
    Route::get('/get-sucursales-por-empresa/{idempresa}', [UsuarioController::class, 'getSucursalesPorEmpresa']);

    Route::post('/seleccionar-empresa-sucursal', [UsuarioController::class, 'seleccionarEmpresaSucursal'])->name('seleccionar-empresa-sucursal.post');

Route::resource('referenciales/marcas', MarcasController::class);
Route::resource('referenciales/rubros', RubrosController::class);
Route::resource('referenciales/bancos', BancosController::class);
Route::resource('referenciales/nacionalidades', NacionalidadesController::class);
Route::resource('referenciales/ciudades', CiudadesController::class);
Route::resource('referenciales/clientes', ClientesController::class);
Route::resource('referenciales/proveedores', ProveedoresController::class);
Route::resource('referenciales/tipo_impuesto', TipoImpuestoController::class);
Route::resource('referenciales/tipo_ajuste', TipoAjusteController::class);
Route::resource('referenciales/productos', ProductosController::class);
Route::resource('referenciales/cargos', CargosController::class);
Route::resource('referenciales/empleados', EmpleadosController::class);
Route::resource('referenciales/tipos_documentos', TiposDocumentosController::class);
Route::resource('referenciales/tipos_clientes', TiposClientesController::class);
Route::resource('referenciales/depositos', DepositosController::class);
Route::resource('referenciales/cajas', CajasController::class);
Route::resource('referenciales/tipo_arqueo', TipoArqueoController::class);
Route::resource('referenciales/vehiculos', VehiculosController::class);
Route::resource('referenciales/choferes', ChoferController::class);
Route::resource('referenciales/transportistas', TransportistaController::class);
Route::resource('referenciales/destinatarios_remision', DestinatarioRemisionController::class);
Route::resource('referenciales/formacobro', FormaCobroController::class);
Route::resource('referenciales/entidademisora', EntidadEmisoraController::class);
Route::resource('referenciales/tarjetas', TarjetaController::class);
Route::resource('referenciales/motivo', MotivoController::class);
Route::resource('referenciales/empresas', EmpresasController::class);
Route::resource('referenciales/sucursales', SucursalesController::class);
Route::resource('referenciales/timbrado', TimbradoController::class);
Route::get('referenciales/estados', [ReferencialEstadoController::class, 'index'])->name('referenciales.estados.index');
Route::patch('referenciales/estados/{recurso}/{id}/reactivar', [ReferencialEstadoController::class, 'reactivar'])->name('referenciales.estados.reactivar');
Route::patch('referenciales/estados/{recurso}/{id}/inactivar', [ReferencialEstadoController::class, 'inactivar'])->name('referenciales.estados.inactivar');
Route::get('acceso/intentos', [LoginAttemptController::class, 'index'])->name('login_attempts.index');
Route::get('acceso/auditoria', [AuditLogController::class, 'index'])->name('audit_logs.index');
Route::resource('acceso/modulos', ModuloController::class)->except(['show']);
Route::resource('acceso/ventanas', VentanaController::class)->except(['show']);
Route::resource('acceso/acciones', AccionController::class)->except(['show']);
Route::resource('acceso/usuario', UsuarioController::class);
Route::controller(SeleccionarController::class)->group(function () {

    Route::get('seleccionsucursales/seleccionar', 'index')->name('seleccionar.index');
    Route::get('seleccionsucursales/seleccionar/{id}', 'edit')->name('seleccionar.edit');

});

Route::get('/inicio', [App\Http\Controllers\HomeController::class, 'index'])->name('inicio');
Route::get('/inicio/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('inicio.dashboard');
Route::get('/home', fn () => redirect()->route('inicio'))->name('home');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::resource('referenciales/stock', StockController::class);
Route::get('referenciales/movimiento_stock', [MovimientoStockController::class, 'index'])->name('movimiento_stock.index');
Route::resource('compras/pedido', PedidosComprasController::class)->except(['edit', 'update']);
Route::controller(PresupuestosComprasController::class)->group(function () {

    Route::get('compras/presupuesto', 'index')->name('presupuesto.index');
    Route::post('compras/presupuesto', 'store')->name('presupuesto.store');
    Route::get('compras/presupuesto/create', 'create')->name('presupuesto.create');
    Route::get('compras/presupuesto/{id}', 'show')->name('presupuesto.show');
    Route::put('compras/presupuesto/{id}', 'update')->name('presupuesto.update');
    Route::delete('compras/presupuesto/{id}', 'destroy')->name('presupuesto.destroy');
    Route::get('compras/presupuesto/{id}/edit', 'edit')->name('presupuesto.edit');
    Route::post('compras/presupuesto/insertar_pedidos', 'insertar_pedidos')->name('presupuesto.insertar_pedidos');

});

Route::controller(OrdenComprasController::class)->group(function () {

    Route::get('compras/orden', 'index')->name('orden.index');
    Route::post('compras/orden', 'store')->name('orden.store');
    Route::get('compras/orden/create', 'create')->name('orden.create');
    Route::get('compras/orden/{id}', 'show')->name('orden.show');
    Route::put('compras/orden/{id}', 'update')->name('orden.update');
    Route::delete('compras/orden/{id}', 'destroy')->name('orden.destroy');
    Route::get('compras/orden/{id}/edit', 'edit')->name('orden.edit');
    Route::post('compras/orden/insertar_presupuestos', 'insertar_presupuestos')->name('orden.insertar_presupuestos');

});

Route::controller(CompraController::class)->group(function () {

    Route::get('compras/compra', 'index')->name('compra.index');
    Route::post('compras/compra', 'store')->name('compra.store');
    Route::get('compras/compra/create', 'create')->name('compra.create');
    Route::get('compras/compra/{id}', 'show')->name('compra.show');
    Route::put('compras/compra/{id}', 'update')->name('compra.update');
    Route::delete('compras/compra/{id}', 'destroy')->name('compra.destroy');
    Route::get('compras/compra/{id}/edit', 'edit')->name('compra.edit');
    Route::post('compras/compra/insertar_ordenes', 'insertar_ordenes')->name('compra.insertar_ordenes');

});

Route::get('compras/cuentas_pagar', [CuentasPagarController::class, 'index'])->name('cuentas_pagar.index');
Route::get('compras/libro_compras', [LibroComprasController::class, 'index'])->name('libro_compras.index');
Route::post('compras/libro_compras/reporte', [LibroComprasController::class, 'reporte'])->name('libro_compras.reporte');

Route::controller(NotaCreditoCController::class)->group(function () {
    Route::get('compras/nota_creditoc', 'index')->name('nota_creditoc.index');
    Route::get('compras/nota_creditoc/create', 'create')->name('nota_creditoc.create');
    Route::post('compras/nota_creditoc', 'store')->name('nota_creditoc.store');
    Route::get('compras/nota_creditoc/{id}', 'show')->name('nota_creditoc.show');
    Route::delete('compras/nota_creditoc/{id}', 'destroy')->name('nota_creditoc.destroy');
});

Route::controller(NotaDebitoCController::class)->group(function () {
    Route::get('compras/nota_debitoc', 'index')->name('nota_debitoc.index');
    Route::get('compras/nota_debitoc/create', 'create')->name('nota_debitoc.create');
    Route::post('compras/nota_debitoc', 'store')->name('nota_debitoc.store');
    Route::get('compras/nota_debitoc/{id}', 'show')->name('nota_debitoc.show');
    Route::delete('compras/nota_debitoc/{id}', 'destroy')->name('nota_debitoc.destroy');
});

Route::controller(NotaRemisionCompraController::class)->group(function () {
    Route::get('compras/nota_remision', 'index')->name('nota_remision_compra.index');
    Route::get('compras/nota_remision/create', 'create')->name('nota_remision_compra.create');
    Route::post('compras/nota_remision', 'store')->name('nota_remision_compra.store');
    Route::get('compras/nota_remision/{idremisionc}', 'show')->name('nota_remision_compra.show');
    Route::delete('compras/nota_remision/{idremisionc}', 'destroy')->name('nota_remision_compra.destroy');
});

Route::controller(AjusteController::class)->group(function () {

    Route::get('compras/ajuste', 'index')->name('ajuste.index');
    Route::post('compras/ajuste', 'store')->name('ajuste.store');
    Route::get('compras/ajuste/create', 'create')->name('ajuste.create');
    Route::get('compras/ajuste/{id}', 'show')->name('ajuste.show');
    Route::put('compras/ajuste/{id}', 'update')->name('ajuste.update');
    Route::delete('compras/ajuste/{id}', 'destroy')->name('ajuste.destroy');
    Route::get('compras/ajuste/{id}/edit', 'edit')->name('ajuste.edit');

});

Route::controller(AperturaController::class)->group(function () {

    // Apertura
    Route::get('ventas/apertura', 'index')->name('apertura.index');
    Route::get('ventas/apertura/listado', 'listado')->name('apertura.listado');
    Route::get('ventas/apertura/create', 'create')->name('apertura.create');
    Route::post('ventas/apertura', 'storeApertura')->name('apertura.storeApertura');
    Route::get('ventas/apertura/{id}', 'show')->name('apertura.show');

    // Cierre
    Route::put('ventas/apertura/{id}', 'cierre')->name('apertura.cierre');

    // Arqueos por apertura
    Route::get('ventas/arqueo/{idapertura}/parcial', 'arqueoParcial')->name('arqueo.parcial');
    Route::get('ventas/arqueo/{idapertura}/final', 'arqueoFinal')->name('arqueo.final');

    // (Opcional) compatibilidad con tu ruta vieja: /ventas/arqueo/{idapertura} => final
    Route::get('ventas/arqueo/{idapertura}', 'arqueoFinal')->name('arqueo.show');
});

Route::controller(CobroController::class)->group(function () {
    Route::get('ventas/cobro', 'index')->name('cobro.index');
    Route::get('ventas/cobro/create', 'create')->name('cobro.create');
    Route::post('ventas/cobro', 'store')->name('cobro.store');

    // Cobrar una factura (crea cabecera cobro + detalle factura y te manda al edit)
    Route::get('ventas/cobro/cobrarfactura/{id}', 'cobrarfactura')->name('cobro.cobrarfactura');

    // Pantallas
    Route::get('ventas/cobro/{id}/edit', 'edit')->name('cobro.edit');
    Route::get('ventas/cobro/recibo/{id}', 'imprimirRecibo')->name('cobro.recibo');
    Route::get('ventas/cobro/{id}', 'show')->name('cobro.show');

    // Finalizar (guarda det_formacobro y valida totales)
    Route::post('ventas/cobro/finalizar/{id}', 'finalizar')->name('cobro.finalizar');

    // Cancelar cobro (no borra, marca ANULADO)
    Route::delete('ventas/cobro/{id}', 'destroy')->name('cobro.destroy');

    // Eliminar una factura del cobro (det_cobro)
    Route::delete('ventas/cobro/{id_cobro}/factura/{id_detcobro}', 'deleteFactura')->name('cobro.factura.delete');

    // (opcional) eliminar una forma de pago cargada (det_formacobro)
    Route::delete('ventas/cobro/{id_cobro}/pago/{id_detformacobro}', 'deletePago')->name('cobro.pago.delete');
});

Route::controller(VentasController::class)->group(function () {

    Route::get('ventas/venta', 'index')->name('venta.index');
    Route::post('ventas/venta', 'store')->name('venta.store');
    Route::get('ventas/venta/create', 'create')->name('venta.create');
    Route::get('ventas/venta/{id}', 'show')->name('venta.show');
    Route::put('ventas/venta/{id}', 'update')->name('venta.update');
    Route::delete('ventas/venta/{id}', 'destroy')->name('venta.destroy');
    Route::get('ventas/venta/{id}/edit', 'edit')->name('venta.edit');
    Route::get('ventas/venta/imprimirfactura/{id}', 'imprimirfactura')->name('venta.imprimirfactura');
});

Route::controller(VentaCreditoAceptacionController::class)->group(function () {
    Route::get('ventas/venta/{idventa}/compromiso-pago', 'plantilla')->name('venta_credito_aceptacion.plantilla');
    Route::get('ventas/venta/{idventa}/aceptacion/create', 'create')->name('venta_credito_aceptacion.create');
    Route::post('ventas/venta/{idventa}/aceptacion', 'store')->name('venta_credito_aceptacion.store');
    Route::get('ventas/venta/{idventa}/aceptacion/{idaceptacion}', 'show')->name('venta_credito_aceptacion.show');
    Route::get('ventas/venta/{idventa}/aceptacion/{idaceptacion}/comprobante', 'comprobante')->name('venta_credito_aceptacion.comprobante');
});

Route::controller(NotaRemisionVentaController::class)->group(function () {
    Route::get('ventas/nota_remision', 'index')->name('nota_remision_venta.index');
    Route::get('ventas/nota_remision/create', 'create')->name('nota_remision_venta.create');
    Route::post('ventas/nota_remision', 'store')->name('nota_remision_venta.store');
    Route::get('ventas/nota_remision/{idremision}/edit', 'edit')->name('nota_remision_venta.edit');
    Route::put('ventas/nota_remision/{idremision}', 'update')->name('nota_remision_venta.update');
    Route::get('ventas/nota_remision/{idremision}', 'show')->name('nota_remision_venta.show');
    Route::get('ventas/nota_remision/{idremision}/comprobante', 'comprobante')->name('nota_remision_venta.comprobante');
    Route::delete('ventas/nota_remision/{idremision}', 'destroy')->name('nota_remision_venta.destroy');
    Route::get('ventas/venta/{idventa}/nota-remision/create', 'create')->name('nota_remision_venta.create_from_venta');
    Route::post('ventas/venta/{idventa}/nota-remision', 'store')->name('nota_remision_venta.store_from_venta');
    Route::get('ventas/venta/{idventa}/nota-remision/{idremision}', 'showForVenta')->name('nota_remision_venta.show_from_venta');
    Route::get('ventas/venta/{idventa}/nota-remision/{idremision}/comprobante', 'comprobanteForVenta')->name('nota_remision_venta.comprobante_from_venta');
});

Route::controller(CuentaCobrarController::class)->group(function () {
    Route::get('ventas/cuenta_cobrar', 'index')->name('cuenta_cobrar.index');
});

Route::controller(CuotaController::class)->group(function () {

Route::get('ventas/producto_clientes/cuotas/{idproducto_cliente}', [CuotaController::class, 'cuotas']);
Route::post('ventas/producto_clientes/cuotas/pagar', [CuotaController::class, 'pagar']);
Route::put('ventas/producto_clientes/cuotas/{idproducto_cliente_cuota}', [CuotaController::class, 'update']);
Route::delete('ventas/producto_clientes/cuotas/eliminar/{idproducto_cliente_cuota}', [CuotaController::class, 'destroy']);

});

Route::controller(LibroVentasController::class)->group(function () {
    Route::get('ventas/libro_ventas', 'index')->name('libro_ventas.index');
    Route::post('ventas/libro_ventas', 'generado')->name('libro_ventas.generado');
});

Route::controller(NotaCreditoVController::class)->group(function () {

    Route::get('ventas/nota_creditov', 'index')->name('nota_creditov.index');
    Route::get('ventas/nota_creditov/create', 'create')->name('nota_creditov.create');
    Route::post('ventas/nota_creditov', 'store')->name('nota_creditov.store');

    Route::get('ventas/nota_creditov/{id}/comprobante', 'comprobante')->name('nota_creditov.comprobante');
    Route::get('ventas/nota_creditov/{id}', 'show')->name('nota_creditov.show');
    Route::get('ventas/nota_creditov/{id}/edit', 'edit')->name('nota_creditov.edit');
    Route::put('ventas/nota_creditov/{id}', 'update')->name('nota_creditov.update');

    // ANULAR NC (esto debe quedar SOLO UNA VEZ)
    Route::delete('ventas/nota_creditov/{id}', 'destroy')->name('nota_creditov.destroy');

    // INSERTAR FACTURA DESDE MODAL
    Route::post('ventas/nota_creditov/insertar_facturas', 'insertar_facturas')->name('nota_creditov.insertar_facturas');

    // ELIMINAR DETALLE (ruta distinta para no chocar)
    Route::delete('ventas/nota_creditov/detalle/{id}', 'destroydetalle')->name('nota_creditov.destroydetalle');
});

Route::controller(NotaDebitoVController::class)->group(function () {

    Route::get('ventas/nota_debitov', 'index')->name('nota_debitov.index');
    Route::get('ventas/nota_debitov/create', 'create')->name('nota_debitov.create');
    Route::post('ventas/nota_debitov', 'store')->name('nota_debitov.store');

    Route::get('ventas/nota_debitov/{id}/comprobante', 'comprobante')->name('nota_debitov.comprobante');
    Route::get('ventas/nota_debitov/{id}', 'show')->name('nota_debitov.show');
    Route::get('ventas/nota_debitov/{id}/edit', 'edit')->name('nota_debitov.edit');
    Route::put('ventas/nota_debitov/{id}', 'update')->name('nota_debitov.update');

    Route::delete('ventas/nota_debitov/{id}', 'destroy')->name('nota_debitov.destroy');

    Route::post('ventas/nota_debitov/insertar_facturas', 'insertar_facturas')->name('nota_debitov.insertar_facturas');

    Route::delete('ventas/nota_debitov/detalle/{id}', 'destroydetalle')->name('nota_debitov.destroydetalle');
});
});
