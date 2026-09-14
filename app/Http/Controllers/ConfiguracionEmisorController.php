<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionEmisor;
use Illuminate\Http\Request;

/**
 * Datos del negocio como emisor de facturas electrónicas — los que
 * CUALQUIER proveedor de facturación electrónica va a pedir, sin importar
 * cuál se contrate (ver hallazgo #3 de la auditoría DIAN y
 * App\Contracts\FacturaElectronicaProvider). Tabla de una sola fila.
 */
class ConfiguracionEmisorController extends Controller
{
    public function index()
    {
        return view('configuracion-emisor.index');
    }

    public function show()
    {
        $emisor = ConfiguracionEmisor::actual();

        return response()->json([
            'data' => $emisor,
            'completa' => $emisor->estaCompleta(),
        ]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'razon_social' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:20'],
            'dv' => ['nullable', 'digits:1'],
            'tipo_persona' => ['required', 'in:natural,juridica'],
            'regimen_tributario' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'codigo_postal' => ['nullable', 'string', 'max:10'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'matricula_mercantil' => ['nullable', 'string', 'max:30'],
        ]);

        $emisor = ConfiguracionEmisor::actual();
        $emisor->update($datos);

        return response()->json([
            'success' => true,
            'message' => 'Datos del emisor actualizados correctamente.',
            'data' => $emisor->fresh(),
        ]);
    }
}
