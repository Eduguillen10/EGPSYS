<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;

class LoginAttemptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = LoginAttempt::query()->with('user')->orderByDesc('attempted_at');

        if ($request->filled('desde')) {
            $query->whereDate('attempted_at', '>=', $request->get('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('attempted_at', '<=', $request->get('hasta'));
        }

        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . trim($request->get('email')) . '%');
        }

        if ($request->filled('estado')) {
            $query->where('successful', $request->get('estado') === 'correcto');
        }

        $intentos = $query->paginate(15);

        return view('acceso.intentos.index', [
            'intentos' => $intentos,
            'filtros' => $request->only(['desde', 'hasta', 'email', 'estado']),
        ]);
    }
}
