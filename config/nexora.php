<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plan contratado
    |--------------------------------------------------------------------------
    |
    | Cada instalación de Nexora es de un solo cliente (no es multi-tenant),
    | así que el plan es una propiedad de LA INSTALACIÓN, no del usuario.
    | Se define en el .env de cada restaurante:
    |
    |   NEXORA_PLAN=basico   -> POS, mesas, comandas, inventario, cierres de caja
    |   NEXORA_PLAN=pro      -> todo lo de básico + contabilidad, nómina,
    |                           activos fijos, tesorería, compras, cuentas por
    |                           cobrar/pagar y facturación electrónica DIAN
    |
    | Subir a un cliente de básico a pro es cambiar esta variable y reiniciar
    | (php artisan config:clear si el config está cacheado) — no borra nada
    | de lo que ya tenía configurado ni de su historial.
    |
    */

    'plan' => env('NEXORA_PLAN', 'pro'),

];
