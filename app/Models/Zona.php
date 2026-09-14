<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'bodega_id'];

    // Una zona tiene muchas mesas
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }
}
