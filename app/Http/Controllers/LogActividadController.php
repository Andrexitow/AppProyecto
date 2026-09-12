<?php

namespace App\Http\Controllers;

use App\Models\LogActividad;
use App\Models\User;
use Illuminate\Http\Request;

class LogActividadController extends Controller
{
    public function index()
    {
        return view('logs.index', [
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function data(Request $request)
    {
        $logs = LogActividad::query()
            ->with('usuario:id,name')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = trim((string) $request->string('buscar'));
                $query->where(function ($subquery) use ($buscar) {
                    $subquery->where('descripcion', 'like', '%' . $buscar . '%')
                        ->orWhere('modulo', 'like', '%' . $buscar . '%')
                        ->orWhere('referencia', 'like', '%' . $buscar . '%');
                });
            })
            ->when($request->filled('usuario_id'), fn ($query) => $query->where('user_id', $request->integer('usuario_id')))
            ->when($request->filled('accion'), fn ($query) => $query->where('accion', (string) $request->string('accion')))
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('hasta')))
            ->latest('id')
            ->paginate(50);

        return response()->json($logs);
    }
}
