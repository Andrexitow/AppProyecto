<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Defensa a nivel de base de datos, además de la validación de aplicación que
 * ya existe en cada servicio (AccountingService::guardarLineas, y el mismo
 * patrón repetido en CompraContableService/ClienteContableService/etc.): sin
 * esto, cualquier inserción directa (una migración de datos, un script suelto,
 * un bug futuro) podía dejar una línea con débito y crédito simultáneos, negativos,
 * o ambos en cero, sin que nada a nivel de esquema lo impidiera.
 *
 * No se usa Blueprint::check() (no existe en este Laravel) sino SQL crudo, y
 * solo corre en MySQL/MariaDB — SQLite (los tests) ya rechaza estos casos vía
 * la capa de aplicación, y su soporte de CHECK en ALTER TABLE es limitado.
 */
return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE movimientos_contables ADD CONSTRAINT chk_movcont_partida_doble CHECK (
                debito >= 0 AND credito >= 0
                AND NOT (debito > 0 AND credito > 0)
                AND (debito > 0 OR credito > 0)
            )');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE movimientos_contables DROP CONSTRAINT chk_movcont_partida_doble');
        }
    }
};
