<?php

namespace App\Http\Middleware;

use App\Models\Ventana;
use Closure;
use Illuminate\Http\Request;

class VerificarPermisoRuta
{
    private array $rutasLibres = [
        'inicio',
        'inicio.dashboard',
        'home',
        'logout',
        'password.confirm',
        'seleccionar.index',
        'seleccionar.edit',
        'seleccionar-empresa-sucursal.get',
        'seleccionar-empresa-sucursal.post',
    ];

    private array $prefijos = [
        'compras.compra' => 'compra',
        'login_attempts' => 'intentos_acceso',
        'audit_logs' => 'audit_trail',
        'referenciales.estados' => 'estados_referenciales',
        'usuario' => 'usuarios',
        'venta_credito_aceptacion' => 'venta',
        'nota_remision_venta' => 'venta',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeName = $route?->getName();

        if (!$routeName || in_array($routeName, $this->rutasLibres, true)) {
            return $next($request);
        }

        $ventanaClave = $this->resolverVentana($routeName);

        if (!$ventanaClave) {
            return $next($request);
        }

        $ventana = Ventana::where('permiso_clave', $ventanaClave)->first();

        if (!$ventana) {
            return $next($request);
        }

        if (!$ventana->estado) {
            abort(403, 'La ventana se encuentra inactiva.');
        }

        $accion = $this->resolverAccion($routeName, $route?->getActionMethod());
        $user = $request->user();

        if (!$user || !$user->tienePermiso($ventanaClave, $accion)) {
            abort(403, 'No tiene permiso para realizar esta accion.');
        }

        return $next($request);
    }

    private function resolverVentana(string $routeName): ?string
    {
        foreach ($this->prefijos as $prefijo => $ventanaClave) {
            if ($routeName === $prefijo || str_starts_with($routeName, $prefijo . '.')) {
                return $ventanaClave;
            }
        }

        return explode('.', $routeName)[0] ?? null;
    }

    private function resolverAccion(string $routeName, ?string $metodo): string
    {
        $segmento = str($routeName)->afterLast('.')->toString();
        $nombre = $metodo ?: $segmento;

        return match ($nombre) {
            'create', 'store', 'insertar_pedidos', 'insertar_presupuestos', 'insertar_ordenes', 'insertar_facturas', 'cobrarfactura', 'pagar', 'finalizar' => 'crear',
            'edit', 'update', 'cierre', 'deleteFactura', 'deletePago', 'reactivar', 'inactivar' => 'editar',
            'destroy', 'destroydetalle' => 'anular',
            default => 'ver',
        };
    }
}
