<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * bloquearMesa() dejaba a la mesa en 'seleccionada' sin registrar quién
     * la tomó — cualquiera podía liberarla, y dos meseros podían leerla
     * como disponible al mismo tiempo y tomarla ambos. Estas columnas dejan
     * ese dato, y bloquearMesa()/liberarMesa() ahora corren dentro de una
     * transacción con lockForUpdate() para cerrar la carrera.
     */
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->foreignId('bloqueada_por')->nullable()->after('estado')->constrained('users')->nullOnDelete();
            $table->timestamp('bloqueada_at')->nullable()->after('bloqueada_por');
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bloqueada_por');
            $table->dropColumn('bloqueada_at');
        });
    }
};
