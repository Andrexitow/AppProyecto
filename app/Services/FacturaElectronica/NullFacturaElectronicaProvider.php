<?php

namespace App\Services\FacturaElectronica;

use App\Contracts\FacturaElectronicaProvider;
use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\Factura;
use App\Models\NotaFactura;

/**
 * Implementación por defecto: todavía NO hay proveedor de facturación
 * electrónica contratado. El sistema sigue funcionando exactamente igual
 * que hoy (POS + contabilidad interna) — este "no-op" solo evita que el
 * resto del código tenga que preguntar en cada punto "¿hay proveedor o no?".
 *
 * Cuando contrates uno, ver las instrucciones en
 * App\Contracts\FacturaElectronicaProvider — se reemplaza esta clase por
 * una nueva sin tocar FacturacionController ni NotaFacturaService.
 */
class NullFacturaElectronicaProvider implements FacturaElectronicaProvider
{
    private const MENSAJE = 'No hay proveedor de facturación electrónica configurado todavía.';

    public function emitirFactura(Factura $factura): ResultadoFacturaElectronica
    {
        return new ResultadoFacturaElectronica(estado: 'no_aplica', mensaje: self::MENSAJE);
    }

    public function emitirNotaCredito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        return new ResultadoFacturaElectronica(estado: 'no_aplica', mensaje: self::MENSAJE);
    }

    public function emitirNotaDebito(NotaFactura $nota): ResultadoFacturaElectronica
    {
        return new ResultadoFacturaElectronica(estado: 'no_aplica', mensaje: self::MENSAJE);
    }

    public function consultarEstado(Factura|NotaFactura $documento): ResultadoFacturaElectronica
    {
        return new ResultadoFacturaElectronica(estado: 'no_aplica', mensaje: self::MENSAJE);
    }
}
