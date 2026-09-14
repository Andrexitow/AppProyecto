<?php

namespace App\Providers;

use App\Contracts\FacturaElectronicaProvider;
use App\Services\FacturaElectronica\FactusFacturaElectronicaProvider;
use App\Services\FacturaElectronica\NullFacturaElectronicaProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ── Facturación electrónica ──────────────────────────────────────
        // Probando con Factus (sandbox). El sistema sigue sin transmitir
        // nada de verdad hasta que, ADEMÁS de este bind, se configure
        // FACTURA_ELECTRONICA_HABILITADA=true y las credenciales FACTUS_*
        // en el .env — ver App\Contracts\FacturaElectronicaProvider.
        $this->app->bind(FacturaElectronicaProvider::class, FactusFacturaElectronicaProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
