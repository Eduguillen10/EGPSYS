<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>EGPSYS</title>

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/zabuto_calendar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('js/gritter/css/jquery.gritter.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('lineicons/style.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style-responsive.css') }}" rel="stylesheet">

    <style>
        .menu-search-box {
            padding: 8px 14px 12px;
        }

        .menu-search-control {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            padding: 0 8px;
        }

        .menu-search-control i {
            color: #777;
            font-size: 13px;
        }

        .menu-search-control input {
            width: 100%;
            height: 32px;
            border: 0;
            outline: 0;
            color: #333;
            background: transparent;
            font-size: 12px;
        }

        .menu-search-control button {
            border: 0;
            background: transparent;
            color: #777;
            padding: 0;
            line-height: 1;
        }

        .menu-search-empty {
            display: none;
            margin: 8px 0 0;
            color: #cfd8dc;
            font-size: 12px;
        }
    </style>
</head>

<body>
<section id="container">

    <header class="header black-bg">
        <div class="sidebar-toggle-box">
            <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
        </div>

        <a href="{{ url('/') }}" class="logo"><b>EGPSYS</b></a>

        <div class="nav notify-row" id="top_menu">
            <ul class="nav top-menu">
                <div class="top-menu">
                    <ul class="nav pull-right top-menu">
                        @auth
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-logout">Cerrar Sesion</button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </div>
            </ul>
        </div>
    </header>

    <aside>
        <div id="sidebar" class="nav-collapse ">
            <ul class="sidebar-menu" id="nav-accordion">
                <p class="centered">
                    <a href="{{ url('profile') }}">
                        <img src="{{ asset('img/TM Soluciones.jpg') }}" class="rounded-circle" width="100">
                    </a>
                </p>
                <h5 class="centered"></h5>

                @php
                    // Array principal del menu: objetos con subobjetos en children.
                    $menuItems = [
                        (object) [
                            'id' => 'menu-inicio',
                            'label' => 'Inicio',
                            'icon' => 'fa fa-home',
                            'href' => url('/inicio'),
                            'permission' => null,
                            'keywords' => 'Inicio Dashboard Panel principal /inicio',
                        ],
                        (object) [
                            'id' => 'menu-productos',
                            'label' => 'Productos',
                            'icon' => 'fa fa-laptop',
                            'href' => 'javascript:;',
                            'keywords' => 'Productos',
                            'children' => [
                                (object) ['id' => 'menu-productos-productos', 'label' => 'Productos', 'href' => url('/referenciales/productos'), 'permission' => 'productos', 'keywords' => 'Productos /referenciales/productos productos'],
                                (object) ['id' => 'menu-productos-stock', 'label' => 'Stock', 'href' => url('/referenciales/stock'), 'permission' => 'stock', 'keywords' => 'Productos Stock /referenciales/stock stock'],
                                (object) ['id' => 'menu-productos-movimiento-stock', 'label' => 'Historico de Stock', 'href' => url('/referenciales/movimiento_stock'), 'permission' => 'stock', 'keywords' => 'Productos Historico de Stock /referenciales/movimiento_stock movimiento_stock trazabilidad stock kardex'],
                            ],
                        ],
                        (object) [
                            'id' => 'menu-compras',
                            'label' => 'Compras',
                            'icon' => 'fa fa-shopping-cart',
                            'href' => 'javascript:;',
                            'keywords' => 'Compras',
                            'children' => [
                                (object) ['id' => 'menu-compras-pedidos', 'label' => 'Pedidos de Compra', 'href' => url('/compras/pedido'), 'permission' => 'pedido', 'keywords' => 'Compras Pedidos de Compra /compras/pedido pedido'],
                                (object) ['id' => 'menu-compras-presupuestos', 'label' => 'Presupuestos de Compras', 'href' => url('/compras/presupuesto'), 'permission' => 'presupuesto', 'keywords' => 'Compras Presupuestos de Compras /compras/presupuesto presupuesto'],
                                (object) ['id' => 'menu-compras-orden', 'label' => 'Orden de Compras', 'href' => url('/compras/orden'), 'permission' => 'orden', 'keywords' => 'Compras Orden de Compras /compras/orden orden'],
                                (object) ['id' => 'menu-compras-nota-remision', 'label' => 'Nota de Remision', 'href' => url('/compras/nota_remision'), 'permission' => 'compra', 'keywords' => 'Compras Nota de Remision /compras/nota_remision nota_remision remision traslado recepcion mercaderia'],
                                (object) ['id' => 'menu-compras-compras', 'label' => 'Compras', 'href' => url('/compras/compra'), 'permission' => 'compra', 'keywords' => 'Compras /compras/compra compra'],
                                (object) ['id' => 'menu-compras-nota-credito', 'label' => 'Nota de Credito', 'href' => url('/compras/nota_creditoc'), 'permission' => 'compra', 'keywords' => 'Compras Nota de Credito /compras/nota_creditoc nota_creditoc devolucion proveedor'],
                                (object) ['id' => 'menu-compras-nota-debito', 'label' => 'Nota de Debito', 'href' => url('/compras/nota_debitoc'), 'permission' => 'compra', 'keywords' => 'Compras Nota de Debito /compras/nota_debitoc nota_debitoc recargo proveedor'],
                                (object) ['id' => 'menu-compras-cuentas-pagar', 'label' => 'Cuentas a Pagar', 'href' => url('/compras/cuentas_pagar'), 'permission' => 'compra', 'keywords' => 'Compras Cuentas a Pagar /compras/cuentas_pagar cuentas_pagar proveedores deuda'],
                                (object) ['id' => 'menu-compras-libro', 'label' => 'Libro Compras', 'href' => url('/compras/libro_compras'), 'permission' => 'compra', 'keywords' => 'Compras Libro Compras /compras/libro_compras libro_compras fiscal iva'],
                                (object) ['id' => 'menu-compras-ajustes', 'label' => 'Ajustes', 'href' => url('/compras/ajuste'), 'permission' => 'ajuste', 'keywords' => 'Compras Ajustes /compras/ajuste ajuste'],
                            ],
                        ],
                        (object) [
                            'id' => 'menu-ventas',
                            'label' => 'Ventas',
                            'icon' => 'fa fa-money',
                            'href' => 'javascript:;',
                            'keywords' => 'Ventas',
                            'children' => [
                                (object) ['id' => 'menu-ventas-apertura', 'label' => 'Apertura y Cierre', 'href' => url('/ventas/apertura'), 'permission' => 'apertura', 'keywords' => 'Ventas Apertura y Cierre /ventas/apertura apertura'],
                                (object) ['id' => 'menu-ventas-ventas', 'label' => 'Registrar Ventas', 'href' => url('/ventas/venta'), 'permission' => 'venta', 'keywords' => 'Ventas /ventas/venta venta'],
                                (object) ['id' => 'menu-ventas-cuentas-cobrar', 'label' => 'Cuentas a Cobrar', 'href' => url('/ventas/cuenta_cobrar'), 'permission' => 'cuenta_cobrar', 'keywords' => 'Ventas Cuentas a Cobrar /ventas/cuenta_cobrar cuenta_cobrar'],
                                (object) ['id' => 'menu-ventas-cobros', 'label' => 'Cobros', 'href' => url('/ventas/cobro'), 'permission' => 'cobro', 'keywords' => 'Ventas Cobros /ventas/cobro cobro'],
                                (object) ['id' => 'menu-ventas-libro', 'label' => 'Libro Ventas', 'href' => url('/ventas/libro_ventas'), 'permission' => 'libro_ventas', 'keywords' => 'Ventas Libro Ventas /ventas/libro_ventas libro_ventas'],
                                (object) ['id' => 'menu-ventas-nota-credito', 'label' => 'Nota de Credito', 'href' => url('/ventas/nota_creditov'), 'permission' => 'nota_creditov', 'keywords' => 'Ventas Nota de Credito /ventas/nota_creditov nota_creditov'],
                                (object) ['id' => 'menu-ventas-nota-debito', 'label' => 'Nota de Debito', 'href' => url('/ventas/nota_debitov'), 'permission' => 'nota_debitov', 'keywords' => 'Ventas Nota de Debito /ventas/nota_debitov nota_debitov'],
                                (object) ['id' => 'menu-ventas-nota-remision', 'label' => 'Nota de Remision', 'href' => url('/ventas/nota_remision'), 'permission' => 'venta', 'keywords' => 'Ventas Nota de Remision /ventas/nota_remision nota_remision remision traslado mercaderia'],
                            ],
                        ],
                        (object) [
                            'id' => 'menu-referenciales',
                            'label' => 'Referenciales',
                            'icon' => 'fa fa-list',
                            'href' => 'javascript:;',
                            'keywords' => 'Referenciales',
                            'children' => [
                                (object) ['id' => 'menu-referenciales-marcas', 'label' => 'Marcas', 'href' => url('/referenciales/marcas'), 'permission' => 'marcas', 'keywords' => 'Referenciales Marcas /referenciales/marcas marcas'],
                                (object) ['id' => 'menu-referenciales-rubros', 'label' => 'Rubros', 'href' => url('/referenciales/rubros'), 'permission' => 'rubros', 'keywords' => 'Referenciales Rubros /referenciales/rubros rubros'],
                                (object) ['id' => 'menu-referenciales-bancos', 'label' => 'Bancos', 'href' => url('/referenciales/bancos'), 'permission' => 'bancos', 'keywords' => 'Referenciales Bancos /referenciales/bancos bancos'],
                                (object) ['id' => 'menu-referenciales-ciudades', 'label' => 'Ciudades', 'href' => url('/referenciales/ciudades'), 'permission' => 'ciudades', 'keywords' => 'Referenciales Ciudades /referenciales/ciudades ciudades'],
                                (object) ['id' => 'menu-referenciales-nacionalidades', 'label' => 'Nacionalidades', 'href' => url('/referenciales/nacionalidades'), 'permission' => 'nacionalidades', 'keywords' => 'Referenciales Nacionalidades /referenciales/nacionalidades nacionalidades'],
                                (object) ['id' => 'menu-referenciales-clientes', 'label' => 'Clientes', 'href' => url('/referenciales/clientes'), 'permission' => 'clientes', 'keywords' => 'Referenciales Clientes /referenciales/clientes clientes'],
                                (object) ['id' => 'menu-referenciales-proveedores', 'label' => 'Proveedores', 'href' => url('/referenciales/proveedores'), 'permission' => 'proveedores', 'keywords' => 'Referenciales Proveedores /referenciales/proveedores proveedores'],
                                (object) ['id' => 'menu-referenciales-tipo-impuesto', 'label' => 'Tipo Impuesto', 'href' => url('/referenciales/tipo_impuesto'), 'permission' => 'tipo_impuesto', 'keywords' => 'Referenciales Tipo Impuesto /referenciales/tipo_impuesto tipo_impuesto'],
                                (object) ['id' => 'menu-referenciales-tipo-ajuste', 'label' => 'Tipo Ajuste', 'href' => url('/referenciales/tipo_ajuste'), 'permission' => 'tipo_ajuste', 'keywords' => 'Referenciales Tipo Ajuste /referenciales/tipo_ajuste tipo_ajuste entrada salida stock ajuste'],
                                (object) ['id' => 'menu-referenciales-cargos', 'label' => 'Cargos', 'href' => url('/referenciales/cargos'), 'permission' => 'cargos', 'keywords' => 'Referenciales Cargos /referenciales/cargos cargos'],
                                (object) ['id' => 'menu-referenciales-empleados', 'label' => 'Empleados', 'href' => url('/referenciales/empleados'), 'permission' => 'empleados', 'keywords' => 'Referenciales Empleados /referenciales/empleados empleados'],
                                (object) ['id' => 'menu-referenciales-tipos-documentos', 'label' => 'Tipo Documento', 'href' => url('/referenciales/tipos_documentos'), 'permission' => 'tipos_documentos', 'keywords' => 'Referenciales Tipo Documento /referenciales/tipos_documentos tipos_documentos'],
                                (object) ['id' => 'menu-referenciales-tipos-clientes', 'label' => 'Tipo Cliente', 'href' => url('/referenciales/tipos_clientes'), 'permission' => 'tipos_clientes', 'keywords' => 'Referenciales Tipo Cliente /referenciales/tipos_clientes tipos_clientes'],
                                (object) ['id' => 'menu-referenciales-depositos', 'label' => 'Depositos', 'href' => url('/referenciales/depositos'), 'permission' => 'depositos', 'keywords' => 'Referenciales Depositos /referenciales/depositos depositos'],
                                (object) ['id' => 'menu-referenciales-cajas', 'label' => 'Cajas', 'href' => url('/referenciales/cajas'), 'permission' => 'cajas', 'keywords' => 'Referenciales Cajas /referenciales/cajas cajas'],
                                (object) ['id' => 'menu-referenciales-tipo-arqueo', 'label' => 'Tipo Arqueo', 'href' => url('/referenciales/tipo_arqueo'), 'permission' => 'tipo_arqueo', 'keywords' => 'Referenciales Tipo Arqueo /referenciales/tipo_arqueo tipo_arqueo'],
                                (object) ['id' => 'menu-referenciales-vehiculos', 'label' => 'Vehiculos', 'href' => url('/referenciales/vehiculos'), 'permission' => 'vehiculos', 'keywords' => 'Referenciales Vehiculos /referenciales/vehiculos vehiculos'],
                                (object) ['id' => 'menu-referenciales-choferes', 'label' => 'Choferes', 'href' => url('/referenciales/choferes'), 'permission' => 'choferes', 'keywords' => 'Referenciales Choferes /referenciales/choferes choferes'],
                                (object) ['id' => 'menu-referenciales-transportistas', 'label' => 'Transportistas', 'href' => url('/referenciales/transportistas'), 'permission' => 'transportistas', 'keywords' => 'Referenciales Transportistas /referenciales/transportistas transportistas traslado remision'],
                                (object) ['id' => 'menu-referenciales-destinatarios-remision', 'label' => 'Destinatarios Remision', 'href' => url('/referenciales/destinatarios_remision'), 'permission' => 'destinatarios_remision', 'keywords' => 'Referenciales Destinatarios Remision /referenciales/destinatarios_remision destinatarios remision'],
                                (object) ['id' => 'menu-referenciales-formacobro', 'label' => 'Forma Cobro', 'href' => url('/referenciales/formacobro'), 'permission' => 'formacobro', 'keywords' => 'Referenciales Forma Cobro /referenciales/formacobro formacobro'],
                                (object) ['id' => 'menu-referenciales-tarjetas', 'label' => 'Tarjetas', 'href' => url('/referenciales/tarjetas'), 'permission' => 'tarjetas', 'keywords' => 'Referenciales Tarjetas /referenciales/tarjetas tarjetas'],
                                (object) ['id' => 'menu-referenciales-entidad-emisora', 'label' => 'Entidad Emisora', 'href' => url('/referenciales/entidademisora'), 'permission' => 'entidademisora', 'keywords' => 'Referenciales Entidad Emisora /referenciales/entidademisora entidademisora'],
                                (object) ['id' => 'menu-referenciales-motivos', 'label' => 'Motivos', 'href' => url('/referenciales/motivo'), 'permission' => 'motivo', 'keywords' => 'Referenciales Motivos /referenciales/motivo motivo'],
                                (object) ['id' => 'menu-referenciales-empresas', 'label' => 'Empresas', 'href' => url('/referenciales/empresas'), 'permission' => 'empresas', 'keywords' => 'Referenciales Empresas /referenciales/empresas empresas'],
                                (object) ['id' => 'menu-referenciales-sucursales', 'label' => 'Sucursales', 'href' => url('/referenciales/sucursales'), 'permission' => 'sucursales', 'keywords' => 'Referenciales Sucursales /referenciales/sucursales sucursales'],
                                (object) ['id' => 'menu-referenciales-timbrado', 'label' => 'Timbrado', 'href' => url('/referenciales/timbrado'), 'permission' => 'timbrado', 'keywords' => 'Referenciales Timbrado /referenciales/timbrado timbrado'],
                                (object) ['id' => 'menu-referenciales-estados', 'label' => 'Estados Referenciales', 'href' => url('/referenciales/estados'), 'permission' => 'estados_referenciales', 'keywords' => 'Referenciales Estados Referenciales /referenciales/estados estados_referenciales'],
                            ],
                        ],
                        (object) [
                            'id' => 'menu-acceso',
                            'label' => 'Acceso',
                            'icon' => 'fa fa-user',
                            'href' => 'javascript:;',
                            'keywords' => 'Acceso',
                            'children' => [
                                (object) ['id' => 'menu-acceso-usuarios', 'label' => 'Usuarios', 'href' => url('/acceso/usuario'), 'permission' => 'usuarios', 'keywords' => 'Acceso Usuarios /acceso/usuario usuarios'],
                                (object) ['id' => 'menu-acceso-intentos', 'label' => 'Intentos de Acceso', 'href' => url('/acceso/intentos'), 'permission' => 'intentos_acceso', 'keywords' => 'Acceso Intentos de Acceso /acceso/intentos intentos_acceso'],
                                (object) ['id' => 'menu-acceso-modulos', 'label' => 'Modulos', 'href' => url('/acceso/modulos'), 'permission' => 'modulos', 'keywords' => 'Acceso Modulos /acceso/modulos modulos'],
                                (object) ['id' => 'menu-acceso-ventanas', 'label' => 'Ventanas', 'href' => url('/acceso/ventanas'), 'permission' => 'ventanas', 'keywords' => 'Acceso Ventanas /acceso/ventanas ventanas'],
                                (object) ['id' => 'menu-acceso-acciones', 'label' => 'Acciones', 'href' => url('/acceso/acciones'), 'permission' => 'acciones', 'keywords' => 'Acceso Acciones /acceso/acciones acciones'],
                                (object) ['id' => 'menu-acceso-auditoria', 'label' => 'Audit Trail', 'href' => url('/acceso/auditoria'), 'permission' => 'audit_trail', 'keywords' => 'Acceso Audit Trail /acceso/auditoria audit_trail auditoria'],
                            ],
                        ],
                        (object) [
                            'id' => 'menu-seleccionar-sucursal',
                            'label' => 'Seleccionar Sucursal',
                            'icon' => 'fa fa-building',
                            'href' => 'javascript:;',
                            'keywords' => 'Seleccionar Sucursal',
                            'children' => [
                                (object) ['id' => 'menu-seleccionar-sucursal-sucursal', 'label' => 'Sucursal', 'href' => url('/seleccionsucursales/seleccionar'), 'permission' => null, 'keywords' => 'Seleccionar Sucursal Sucursal /seleccionsucursales/seleccionar'],
                            ],
                        ],
                    ];

                    $menuItems = collect($menuItems)->map(function ($item) {
                        $item->children = collect($item->children ?? [])->filter(function ($child) {
                            return !Auth::check()
                                ? is_null($child->permission ?? null)
                                : (is_null($child->permission ?? null) || Auth::user()->tienePermiso($child->permission, 'ver'));
                        })->values()->all();

                        return $item;
                    })->filter(function ($item) {
                        $hasChildren = count($item->children ?? []) > 0;
                        $permission = $item->permission ?? null;
                        $canSeeDirectItem = is_null($permission)
                            || (Auth::check() && Auth::user()->tienePermiso($permission, 'ver'));

                        return $hasChildren || $canSeeDirectItem;
                    })->values()->all();

                    $menuItemsForSearch = collect($menuItems)->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'label' => $item->label,
                            'href' => $item->href,
                            'keywords' => $item->keywords,
                            'children' => collect($item->children ?? [])->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'label' => $child->label,
                                    'href' => $child->href,
                                    'keywords' => $child->keywords,
                                ];
                            })->values(),
                        ];
                    })->values();
                @endphp

                <li class="menu-search-box">
                    <div class="menu-search-control">
                        <i class="fa fa-search"></i>
                        <input type="search" id="sidebar-menu-search" placeholder="Buscar pantalla..." autocomplete="off">
                        <button type="button" id="sidebar-menu-search-clear" aria-label="Limpiar busqueda">x</button>
                    </div>
                    <p class="menu-search-empty" id="sidebar-menu-empty">No se encontraron opciones.</p>
                </li>

                @foreach ($menuItems as $menuItem)
                    @if(count($menuItem->children ?? []) > 0)
                        <li class="sub-menu js-menu-module" data-menu-id="{{ $menuItem->id }}">
                            <a href="{{ $menuItem->href }}">
                                <i class="{{ $menuItem->icon }}"></i>
                                <span>{{ $menuItem->label }}</span>
                            </a>
                            <ul class="sub">
                                @foreach ($menuItem->children as $child)
                                    <li class="js-menu-item" data-menu-id="{{ $child->id }}">
                                        <a href="{{ $child->href }}">{{ $child->label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="js-menu-module" data-menu-id="{{ $menuItem->id }}">
                            <a href="{{ $menuItem->href }}">
                                <i class="{{ $menuItem->icon }}"></i>
                                <span>{{ $menuItem->label }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </aside>

    <section id="main-content">
        <section class="wrapper">
            @yield('contenido')
        </section>
    </section>

    <footer class="site-footer">
        <div class="text-center">
            2026 - EGPSYS Informatica
            <a href="#" class="go-top"><i class="fa fa-angle-up"></i></a>
        </div>
    </footer>

</section>

<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
<script class="include" type="text/javascript" src="{{ asset('js/jquery.dcjqaccordion.2.7.js') }}"></script>
<script src="{{ asset('js/jquery.scrollTo.min.js') }}"></script>
<script src="{{ asset('js/jquery.nicescroll.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/common-scripts.js') }}"></script>

@stack('scripts')

<script>
    window.EGPSYS_MENU = @json($menuItemsForSearch ?? []);

    $(function(){
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        if ($(window).width() > 768 && $('#sidebar > ul').is(':visible')) {
            $('#main-content').css('margin-left', '210px');
            $('#sidebar').css('margin-left', '0');
            $('#container').removeClass('sidebar-closed');
        }

        var menuItems = window.EGPSYS_MENU || [];
        var $search = $('#sidebar-menu-search');
        var $clear = $('#sidebar-menu-search-clear');
        var $empty = $('#sidebar-menu-empty');

        function normalizeMenuText(value) {
            return (value || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function filterSidebarMenu() {
            var query = normalizeMenuText($search.val());
            var visibleModules = 0;

            menuItems.forEach(function (moduleItem) {
                var $module = $('[data-menu-id="' + moduleItem.id + '"]');
                var $sub = $module.children('ul.sub');
                var moduleMatches = normalizeMenuText(moduleItem.keywords).indexOf(query) !== -1;
                var visibleChildren = 0;

                (moduleItem.children || []).forEach(function (childItem) {
                    var $child = $('[data-menu-id="' + childItem.id + '"]');
                    var childMatches = normalizeMenuText(childItem.keywords).indexOf(query) !== -1;
                    var showChild = !query || moduleMatches || childMatches;

                    $child.toggle(showChild);

                    if (showChild) {
                        visibleChildren++;
                    }
                });

                var showModule = !query || moduleMatches || visibleChildren > 0;

                $module.toggle(showModule);
                $sub.toggle(query !== '' && showModule);

                if (showModule) {
                    visibleModules++;
                }
            });

            $empty.toggle(query !== '' && visibleModules === 0);
        }

        $search.on('input', filterSidebarMenu);
        $clear.on('click', function () {
            $search.val('');
            filterSidebarMenu();
            $search.trigger('focus');
        });
    });
</script>

</body>
</html>
