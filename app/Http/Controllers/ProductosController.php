<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductosFormRequest;
use App\Models\Productos;
use App\Models\Rubros;
use App\Models\Marcas;
use App\Models\TipoImpuesto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get('searchText'));
            $productos = DB::table('productos')  
                ->join('rubros', 'productos.idrubro', '=', 'rubros.idrubro')
                ->join('marcas', 'productos.idmarca', '=', 'marcas.idmarca')
                ->join('tipo_impuesto', 'productos.idtipoimpuesto', '=', 'tipo_impuesto.idtipoimpuesto')
                ->select('productos.*', 'rubros.descripcion as rubro', 'marcas.descripcion as marca', 'tipo_impuesto.descripcion as tipoimpuesto' , 'tipo_impuesto.porcentaje as porcentaje')
                ->where(function ($q) use ($query) {
                    $q->where('productos.descripcion', 'LIKE', '%' . $query . '%')
                        ->orWhere('rubros.descripcion', 'LIKE', '%' . $query . '%')
                        ->orWhere('marcas.descripcion', 'LIKE', '%' . $query . '%')
                        ->orWhere('tipo_impuesto.descripcion', 'LIKE', '%' . $query . '%');
                })
                ->orderBy('idproducto', 'desc')
                ->paginate(7);
    
            return view('referenciales.productos.index', ["productos" => $productos, "searchText" => $query]);
        }
    }

    public function create()
    {
        $rubros = Rubros::where('estado', 'Activo')->get();
        $marcas = Marcas::where('estado', 'Activo')->get();
        $tiposImpuesto = TipoImpuesto::where('estado', 'Activo')->get();
        return view("referenciales.productos.create", ["rubros" => $rubros, "marcas" => $marcas, "tiposImpuesto" => $tiposImpuesto]);
    }

    public function store(ProductosFormRequest $request)
    {
        $producto = new Productos;
        $producto->codigo = $request->get('codigo');
        $producto->idrubro = $request->get('idrubro');
        $producto->idmarca = $request->get('idmarca');
        $producto->idtipoimpuesto = $request->get('idtipoimpuesto');
        $producto->descripcion = $request->get('descripcion');
        $producto->precio_compra = $request->get('precio_compra');
        $producto->precio_venta = $request->get('precio_venta');
        $producto->tipo_producto = $request->get('tipo_producto');
        $producto->estado = 'Activo';
        $producto->save();
        return Redirect::to('referenciales/productos');
    }

    public function show($id)
    {
        return view("referenciales.productos.show", ["producto" => Productos::findOrFail($id)]);
    }

    public function edit($id)
    {
        $producto = Productos::findOrFail($id);
        $rubros = Rubros::where('estado', 'Activo')->get();
        $marcas = Marcas::where('estado', 'Activo')->get();
        $tiposImpuesto = TipoImpuesto::where('estado', 'Activo')->get();
        return view("referenciales.productos.edit", ["producto" => $producto, "rubros" => $rubros, "marcas" => $marcas, "tiposImpuesto" => $tiposImpuesto]);
    }

    public function update(ProductosFormRequest $request, $id)
    {
        $producto = Productos::findOrFail($id);
        $producto->codigo = $request->get('codigo');
        $producto->idrubro = $request->get('idrubro');
        $producto->idmarca = $request->get('idmarca');
        $producto->idtipoimpuesto = $request->get('idtipoimpuesto');
        $producto->descripcion = $request->get('descripcion');
        $producto->precio_compra = $request->get('precio_compra');
        $producto->precio_venta = $request->get('precio_venta');
        $producto->tipo_producto = $request->get('tipo_producto');
        $producto->estado = 'Activo';
        $producto->update();
        return Redirect::to('referenciales/productos');
    }

    public function destroy($id)
    {
        $producto = Productos::findOrFail($id);
        $producto->estado = 'Inactivo';
        $producto->save();

        return Redirect::to('referenciales/productos')->with('success', 'Producto inactivado correctamente.');
    }

    

    // Resto de las funciones (show, edit, update, destroy)
    // ...
}
