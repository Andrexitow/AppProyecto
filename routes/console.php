<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Facturación electrónica: no hace nada mientras no haya proveedor
// configurado (FACTURA_ELECTRONICA_HABILITADA=false) — ver
// FacturacionElectronicaService. Para que esto corra de verdad en
// producción, el hosting necesita el cron de Laravel apuntando a
// `php artisan schedule:run` cada minuto (revisa con tu hosting cómo
// programarlo si usan cPanel/Hostinger).
// withoutOverlapping(): si una corrida tarda más de 5 minutos (proveedor
// lento), la siguiente NO debe arrancar encima — evitaría doble intento
// sobre el mismo lote pendiente.
Schedule::command('facturacion-electronica:procesar')->everyFiveMinutes()->withoutOverlapping();
