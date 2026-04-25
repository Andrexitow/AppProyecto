<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permisos extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla en tu base de datos
    protected $table = 'permisos';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'nombre', // Ej: "Crear Usuarios"
        'slug'    // Ej: "usuarios.crear" (este es el que usamos en el código)
    ];

    /**
     * Relación: Un permiso puede pertenecer a muchos roles.
     * Esto conecta con la tabla pivote 'permiso_rol'.
     */
    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'permiso_rol', 'permiso_id', 'rol_id');
    }
}