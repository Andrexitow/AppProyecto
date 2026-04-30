<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool tienePermiso(string $permiso)
 */

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'rol_id',
        'role',
        'caja_id',
        'activo',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'rol_id');
    }

    /**
     * Verifica si el usuario tiene un permiso por slug.
     *
     * @param string $slug
     * @return bool
     */

    public function tienePermiso($slug)
    {
        // 1. Verifica si el usuario tiene un rol asignado
        if (!$this->rol) {
            return false;
        }

        // 2. Verifica si dentro de los permisos de ese rol existe el slug buscado
        return $this->rol->permisos->contains('slug', $slug);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
