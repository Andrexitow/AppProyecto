<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function permisos()
    {
        // Esto conecta Roles con Permisos a través de la tabla pivote
        return $this->belongsToMany(Permisos::class, 'permiso_rol', 'rol_id', 'permiso_id');
    }
}
