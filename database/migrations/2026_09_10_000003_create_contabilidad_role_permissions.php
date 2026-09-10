<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permisos = [
            ['nombre' => 'Ver Facturas', 'slug' => 'facturas.ver'],
            ['nombre' => 'Ver Compras', 'slug' => 'compras.ver'],
            ['nombre' => 'Gestionar Compras', 'slug' => 'compras.gestionar'],
            ['nombre' => 'Ver Comprobantes', 'slug' => 'comprobantes.ver'],
            ['nombre' => 'Gestionar Comprobantes', 'slug' => 'comprobantes.gestionar'],
            ['nombre' => 'Ver Cierres de Caja', 'slug' => 'cierres-caja.ver'],
            ['nombre' => 'Ver Plan de Cuentas', 'slug' => 'cuentas-contables.ver'],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->updateOrInsert(['slug' => $permiso['slug']], $permiso);
        }

        $rol = DB::table('roles')->where('nombre', 'Contabilidad')->first();
        if (!$rol) {
            $rolId = DB::table('roles')->insertGetId([
                'nombre' => 'Contabilidad',
                'descripcion' => 'Gestión y consulta contable, sin acceso operativo ni administrativo.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $rolId = $rol->id;
        }

        $adminId = DB::table('roles')->where('nombre', 'Administrador')->value('id');
        $permisosIds = DB::table('permisos')->whereIn('slug', array_column($permisos, 'slug'))->pluck('id');
        foreach (array_filter([$rolId, $adminId]) as $rolIdActual) {
            foreach ($permisosIds as $permisoId) {
                $existe = DB::table('permiso_rol')->where([
                    'rol_id' => $rolIdActual,
                    'permiso_id' => $permisoId,
                ])->exists();
                if (!$existe) {
                    DB::table('permiso_rol')->insert([
                        'rol_id' => $rolIdActual,
                        'permiso_id' => $permisoId,
                    ]);
                }
            }
        }

        $permisosContabilidad = DB::table('permisos')
            ->whereIn('slug', [
                'terceros.index',
                'facturas.ver',
                'compras.ver',
                'compras.gestionar',
                'comprobantes.ver',
                'comprobantes.gestionar',
                'cierres-caja.ver',
                'cuentas-contables.ver',
            ])
            ->pluck('id');
        foreach ($permisosContabilidad as $permisoId) {
            $existe = DB::table('permiso_rol')->where([
                'rol_id' => $rolId,
                'permiso_id' => $permisoId,
            ])->exists();
            if (!$existe) {
                DB::table('permiso_rol')->insert([
                    'rol_id' => $rolId,
                    'permiso_id' => $permisoId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $rolId = DB::table('roles')->where('nombre', 'Contabilidad')->value('id');
        if ($rolId) {
            DB::table('permiso_rol')->where('rol_id', $rolId)->delete();
            DB::table('roles')->where('id', $rolId)->delete();
        }
    }
};
