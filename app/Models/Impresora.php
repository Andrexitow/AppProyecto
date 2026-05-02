<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $fillable = ['nombre', 'ip', 'puerto', 'tipo', 'activa'];
}