<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Sucursal;
use App\Models\UsuarioSucursal;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\UsuarioSucursalFormRequest;
use DB;
use App\Models\User;

class UsuarioSucursalController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
    public function index(Request $request)
    {
    	
    	$iduser=Auth::user()->id;

    	if ($request)
    	{
    		$query=trim ($request->get('searchText'));
            $usuariosucursal=DB::table('usuariosucursal as us')
            ->join('sucursal as s','us.idsucursal','=','s.idsucursal')
            ->join('empresa as e','us.idempresa','=','e.idempresa')
            ->select('us.idusuariosucursal','s.idsucursal','s.descripcion','e.idempresa','e.razon_social')
            ->Where('e.razon_social','LIKE','%'.$query.'%')
    		->orderBy('us.idusuariosucursal','asc')
    		->paginate(7);
    		return view('referenciales.usuariosucursal.index', ["usuariosucursal"=>$usuariosucursal, "searchText"=>$query]);
    	}

    }
    public function edit($id)
    {
     	$iduser=Auth::user()->id;

        DB::update('update users Set work_company='.$id.' where id='.$iduser);
    	
    	return Redirect::to('/inicio/dashboard');	
    }

    public function asociarSucursalYEmpresaAlUsuario($id, $idsucursal, $idempresa)
    {
        $usuario = User::find($id);

        // Asociar sucursal y empresa al usuario
        $usuario->sucursales()->attach($idsucursal, ['idempresa' => $idempresa]);

        return redirect()->route('/inicio/dashboard')->with('success', 'Relación establecida correctamente');
    }
}
