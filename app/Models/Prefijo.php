<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefijo extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'activo',
        // Resolución de facturación autorizada por la DIAN para este
        // prefijo — necesaria para facturación electrónica (ver hallazgo #3
        // de la auditoría DIAN: sin esto ningún proveedor tecnológico puede
        // transmitir facturas con este prefijo).
        'resolucion_numero', 'resolucion_fecha',
        'rango_desde', 'rango_hasta',
        'vigencia_desde', 'vigencia_hasta',
        'clave_tecnica',
        // Solo necesario si el proveedor de facturación electrónica tiene
        // más de un rango activo (p. ej. Factus: numbering_range_id).
        'numbering_range_id_factus',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'resolucion_fecha' => 'date',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];

    public function tieneResolucionDian(): bool
    {
        return !empty($this->resolucion_numero) && !empty($this->rango_desde) && !empty($this->rango_hasta);
    }

    public function resolucionVigente(): bool
    {
        if (!$this->vigencia_desde || !$this->vigencia_hasta) {
            return false;
        }

        return now()->betweenIncluded($this->vigencia_desde, $this->vigencia_hasta);
    }

    /** Consecutivo agotado: ya no quedan números autorizados en el rango. */
    public function rangoAgotado(int $proximoNumero): bool
    {
        return $this->rango_hasta !== null && $proximoNumero > $this->rango_hasta;
    }
}
