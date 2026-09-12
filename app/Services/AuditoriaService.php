<?php

namespace App\Services;

use App\Models\LogActividad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditoriaService
{
    public static function registrar(
        ?User $usuario,
        string $modulo,
        string $accion,
        string $descripcion,
        ?Request $request = null,
        ?string $referencia = null,
        array $metadata = []
    ): void {
        LogActividad::create([
            'user_id' => $usuario?->id,
            'rol' => $usuario?->rol?->nombre,
            'modulo' => $modulo,
            'accion' => $accion,
            'descripcion' => Str::limit($descripcion, 500, ''),
            'metodo' => $request?->method(),
            'ruta' => $request?->path(),
            'referencia' => $referencia ? Str::limit($referencia, 255, '') : null,
            'ip' => $request?->ip(),
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }
}
