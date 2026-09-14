<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla de una sola fila: los datos del negocio como emisor de facturas
 * electrónicas. Usar ConfiguracionEmisor::actual() para leerla/crearla.
 */
class ConfiguracionEmisor extends Model
{
    protected $table = 'configuracion_emisor';

    protected $fillable = [
        'razon_social', 'nit', 'dv', 'tipo_persona', 'regimen_tributario',
        'direccion', 'ciudad', 'departamento', 'codigo_postal',
        'telefono', 'email', 'matricula_mercantil',
    ];

    public static function actual(): self
    {
        return static::firstOrCreate(['id' => 1], ['razon_social' => '', 'nit' => '']);
    }

    public function estaCompleta(): bool
    {
        return filled($this->razon_social) && filled($this->nit) && filled($this->dv) && filled($this->direccion) && filled($this->ciudad);
    }
}
