<?php

namespace App\Contracts;

use App\DataTransferObjects\ResultadoFacturaElectronica;
use App\Models\Factura;
use App\Models\NotaFactura;

/**
 * Punto único de enganche con un proveedor tecnológico de facturación
 * electrónica (Alegra, Siigo, Factus, World Office, etc.). El sistema nunca
 * llama a un proveedor concreto directamente — siempre pasa por esta
 * interfaz, resuelta vía el contenedor de Laravel (ver AppServiceProvider).
 *
 * CÓMO CONECTAR UN PROVEEDOR REAL:
 * 1. Crea una clase en app/Services/FacturaElectronica/ (p. ej.
 *    FactusFacturaElectronicaProvider.php) que implemente esta interfaz.
 *    Ahí traduces cada método a las llamadas HTTP de la API de ese
 *    proveedor (con Http::withToken(...)->post(...) de Laravel), y su
 *    respuesta a un ResultadoFacturaElectronica.
 * 2. En AppServiceProvider::register(), cambia el bind() de
 *    NullFacturaElectronicaProvider a tu nueva clase.
 * 3. Guarda la URL base y el API key del proveedor en el .env (nunca
 *    hardcodeados) y léelos en el constructor de tu clase con config()/env().
 * Nada más en el sistema necesita cambiar: FacturacionController y
 * NotaFacturaService ya llaman a esta interfaz después de contabilizar.
 */
interface FacturaElectronicaProvider
{
    public function emitirFactura(Factura $factura): ResultadoFacturaElectronica;

    public function emitirNotaCredito(NotaFactura $nota): ResultadoFacturaElectronica;

    public function emitirNotaDebito(NotaFactura $nota): ResultadoFacturaElectronica;

    /**
     * Reconsultar el estado ante la DIAN de un documento ya transmitido
     * (estado 'enviada': el proveedor lo recibió pero la DIAN todavía no lo
     * validaba en la respuesta original). Recibe el documento completo, no
     * solo el CUFE, porque no todos los proveedores consultan por CUFE —
     * Factus, por ejemplo, consulta por su propio número de documento
     * (Factura::numero_proveedor / NotaFactura::numero_proveedor).
     */
    public function consultarEstado(Factura|NotaFactura $documento): ResultadoFacturaElectronica;
}
