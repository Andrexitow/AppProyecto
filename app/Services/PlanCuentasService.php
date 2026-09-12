<?php

namespace App\Services;

use App\Models\CuentaContable;
use Illuminate\Validation\ValidationException;

class PlanCuentasService
{
    public function validar(array $datos, ?CuentaContable $actual = null): array
    {
        $padre = !empty($datos['cuenta_padre_id']) ? CuentaContable::findOrFail($datos['cuenta_padre_id']) : null;
        $codigo = trim($datos['codigo']);

        if ($padre) {
            if ($actual && $padre->id === $actual->id) $this->error('cuenta_padre_id', 'Una cuenta no puede ser su propia cuenta padre.');
            for ($nodo = $padre; $nodo; $nodo = $nodo->padre) {
                if ($actual && $nodo->id === $actual->id) $this->error('cuenta_padre_id', 'No se puede crear una relación circular entre cuentas.');
            }
            if (!str_starts_with($codigo, $padre->codigo) || $codigo === $padre->codigo) $this->error('codigo', 'El código de la subcuenta debe iniciar con el código de su cuenta padre.');
            if ($padre->permite_movimientos) $this->error('cuenta_padre_id', 'La cuenta padre acepta movimientos y no puede tener subcuentas.');
            if ($datos['clasificacion'] !== $padre->clasificacion || $datos['naturaleza'] !== $padre->naturaleza) $this->error('cuenta_padre_id', 'La subcuenta debe conservar la clasificación y naturaleza de su cuenta padre.');
        }
        if (($datos['tipo'] ?? '') === 'AGRUPADORA') $datos['movimientos'] = false;
        if (!($datos['movimientos'] ?? false)) {
            $datos['requiere_tercero'] = false;
            $datos['requiere_centro_costo'] = false;
        }
        $datos['nivel'] = $padre ? $padre->nivel + 1 : 1;
        return $datos;
    }

    public function cuentaOperable(int $id): CuentaContable
    {
        $cuenta = CuentaContable::findOrFail($id);
        if (!$cuenta->estado || !$cuenta->permite_movimientos) $this->error('cuenta_contable_id', 'Seleccione una cuenta activa que acepte movimientos.');
        return $cuenta;
    }

    private function error(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
