<?php

namespace App\DataTransferObjects;

/**
 * Respuesta uniforme de CUALQUIER proveedor de facturación electrónica,
 * sin importar la forma exacta de su API. Cuando implementes un proveedor
 * real, tu clase traduce la respuesta HTTP de esa API a este mismo objeto.
 */
class ResultadoFacturaElectronica
{
    public function __construct(
        // 'no_aplica' (sin proveedor configurado) | 'pendiente' | 'enviada'
        // | 'aceptada' | 'rechazada' | 'error'
        public string $estado,
        public ?string $cufe = null,
        // El número que el PROVEEDOR asigna al documento (p. ej.
        // "SETP990001131" en Factus) — distinto de nuestro numero_factura y
        // del CUFE. Hace falta para poder emitir notas crédito/débito sobre
        // este documento después.
        public ?string $numeroProveedor = null,
        public ?string $xmlUrl = null,
        public ?string $pdfUrl = null,
        public ?string $qrTexto = null,
        public ?string $mensaje = null,
    ) {
    }

    public function exitosa(): bool
    {
        return in_array($this->estado, ['enviada', 'aceptada'], true);
    }
}
