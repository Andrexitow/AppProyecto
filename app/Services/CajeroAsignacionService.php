<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\User;

/**
 * Mantiene sincronizada la relación 1 a 1 entre Caja y Cajero, sin importar
 * desde qué pantalla se edite:
 * - Cajas → "Cajero asignado" (columna cajas.user_id)
 * - Cuentas/Usuarios → "Caja asignada" (columna users.caja_id)
 *
 * Sin este servicio, cada pantalla solo actualizaba su propia columna y las
 * dos quedaban desincronizadas (ver caso reportado: asignar la caja desde
 * Cuentas dejaba a la Caja sin cajero visible, permitiendo asignar otro ahí).
 */
class CajeroAsignacionService
{
    /** Llamar después de guardar una Caja cuyo `user_id` cambió. */
    public function asignarUsuarioACaja(Caja $caja, ?int $nuevoCajeroId, ?int $cajeroAnteriorId): void
    {
        if ($cajeroAnteriorId && $cajeroAnteriorId !== $nuevoCajeroId) {
            User::where('id', $cajeroAnteriorId)->where('caja_id', $caja->id)->update(['caja_id' => null]);
        }
        if (!$nuevoCajeroId) {
            return;
        }

        // Un cajero solo puede tener una caja a la vez: si ya era cajero de otra, se libera.
        Caja::where('user_id', $nuevoCajeroId)->where('id', '!=', $caja->id)->update(['user_id' => null]);
        User::whereKey($nuevoCajeroId)->update(['caja_id' => $caja->id]);
    }

    /**
     * Llamar después de guardar un User cuyo `caja_id` cambió.
     *
     * OJO: a diferencia de `cajas.user_id` (que es un único "cajero asignado"
     * por caja), `users.caja_id` NO es exclusivo — varios usuarios (p. ej. un
     * mesero y un cajero) pueden compartir la misma caja para facturar. Por
     * eso aquí nunca se toca el `caja_id` de otro usuario.
     */
    public function asignarCajaAUsuario(User $usuario, ?int $nuevaCajaId, ?int $cajaAnteriorId): void
    {
        // Si este usuario era el "cajero asignado" oficial de su caja anterior
        // y se cambió de caja, esa designación queda huérfana: se libera.
        if ($cajaAnteriorId && $cajaAnteriorId !== $nuevaCajaId) {
            Caja::where('id', $cajaAnteriorId)->where('user_id', $usuario->id)->update(['user_id' => null]);
        }
        if (!$nuevaCajaId) {
            return;
        }

        // Solo lo marcamos como "cajero asignado" de la nueva caja si esta
        // todavía no tiene uno distinto — no le quitamos el cargo a nadie
        // silenciosamente por asignar la caja desde Cuentas.
        Caja::whereKey($nuevaCajaId)->whereNull('user_id')->update(['user_id' => $usuario->id]);
    }
}
