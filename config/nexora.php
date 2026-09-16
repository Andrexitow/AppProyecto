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

    /*
    |--------------------------------------------------------------------------
    | Cierre de sesión por inactividad
    |--------------------------------------------------------------------------
    |
    | Desde que la app quedó accesible por internet (antes solo se llegaba
    | estando en la red del local), un mesero/cajero podía dejar la sesión
    | abierta y seguir comandando desde la casa. Estos minutos son el límite
    | de inactividad (sin ninguna petición al servidor) antes de forzar el
    | cierre de sesión — más corto para roles operativos que para
    | Administrador/Contabilidad, que suelen trabajar más tiempo seguido.
    |
    */

    'inactividad_operativos_minutos' => (int) env('INACTIVIDAD_OPERATIVOS_MINUTOS', 15),
    'inactividad_admin_minutos' => (int) env('INACTIVIDAD_ADMIN_MINUTOS', 60),

];
