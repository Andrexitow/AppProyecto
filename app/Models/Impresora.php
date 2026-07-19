<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Impresora extends Model
{
    protected $fillable = ['nombre', 'ip', 'puerto', 'tipo', 'activa'];

    public function grupoMenus(): BelongsToMany
    {
        return $this->belongsToMany(GrupoMenu::class, 'grupo_menu_impresora')
            ->withPivot('punto')
            ->withTimestamps();
    }
}
