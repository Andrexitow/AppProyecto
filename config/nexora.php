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

    /*
    |--------------------------------------------------------------------------
    | Duración máxima de sesión
    |--------------------------------------------------------------------------
    |
    | Distinto al cierre por inactividad: ese mide "hace cuánto no hace nada",
    | esto mide "hace cuánto inició sesión" — sin importar si sigue activo.
    | Existe porque la pantalla de facturación se autorefresca sola cada
    | pocos segundos, así que una sesión puede quedar viva por días aunque
    | el turno del mesero/cajero ya haya terminado hace rato. A las X horas
    | se cierra la sesión SIN tocar la contraseña ni desactivar la cuenta —
    | si el turno sigue, con volver a iniciar sesión basta.
    |
    | 0 = sin límite (para Administrador/Contabilidad por defecto, que no
    | trabajan por turnos fijos).
    |
    */

    'sesion_maxima_operativos_horas' => (float) env('SESION_MAXIMA_OPERATIVOS_HORAS', 8),
    'sesion_maxima_admin_horas' => (float) env('SESION_MAXIMA_ADMIN_HORAS', 0),

    /*
    |--------------------------------------------------------------------------
    | Detalle técnico en los mensajes de error
    |--------------------------------------------------------------------------
    |
    | false: ante un error inesperado el usuario ve un mensaje genérico con una
    | referencia (ref) que se busca en storage/logs/laravel.log.
    | true: se agrega además el mensaje técnico real. Útil mientras se hacen
    | pruebas en el servidor; apagar en producción.
    |
    */

    'errores_detallados' => (bool) env('NEXORA_ERRORES_DETALLADOS', false),

];
