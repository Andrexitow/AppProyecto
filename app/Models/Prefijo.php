<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefijo extends Model
{
    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'activo', 'proximo_numero',
        // Resolución de facturación autorizada por la DIAN para este
        // prefijo — necesaria para facturación electrónica (ver hallazgo #3
        // de la auditoría DIAN: sin esto ningún proveedor tecnológico puede
        // transmitir facturas con este prefijo).
        'resolucion_numero', 'resolucion_fecha',
        'rango_desde', 'rango_hasta',
        'vigencia_desde', 'vigencia_hasta',
        'clave_tecnica',
        // Facturas, notas crédito y notas débito NO comparten el mismo
        // espacio de numbering_range_id en Factus (confirmado contra el
        // sandbox real 2026-09-14) — un solo campo no alcanza si la cuenta
        // tiene más de un rango activo para algún tipo de documento (p. ej.
        // dos rangos de Nota Crédito: Factus exige especificar cuál usar).
        // Cada uno queda vacío mientras la cuenta tenga un único rango
        // activo para ese tipo de documento (Factus lo autoselecciona).
        'numbering_range_id_factus',
        'numbering_range_id_nota_credito_factus',
        'numbering_range_id_nota_debito_factus',
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

    /**
     * Consecutivo central del prefijo (no de la caja): dos cajas pueden
     * compartir un mismo prefijo, así que el contador tiene que vivir aquí
     * para no repetir un número que otra caja con el mismo prefijo ya usó.
     * Debe llamarse dentro de una transacción abierta — lockForUpdate()
     * bloquea la fila hasta que esa transacción termine, así que dos
     * cajeros facturando al mismo tiempo con el mismo prefijo no pueden
     * llevarse el mismo número.
     */
    public static function siguienteNumero(string $codigo): int
    {
        $prefijo = static::where('codigo', $codigo)->lockForUpdate()->first();

        if (!$prefijo) {
            throw new \RuntimeException("El prefijo '{$codigo}' no existe en el catálogo de prefijos.");
        }

        $numero = $prefijo->proximo_numero;

        if ($prefijo->rangoAgotado($numero)) {
            throw new \RuntimeException("El prefijo '{$codigo}' agotó su rango autorizado por la DIAN (hasta {$prefijo->rango_hasta}). Solicita una nueva resolución.");
        }

        $prefijo->update(['proximo_numero' => $numero + 1]);

        return $numero;
    }
}
