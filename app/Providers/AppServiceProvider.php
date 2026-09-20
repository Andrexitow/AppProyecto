<?php

namespace App\Providers;

use App\Contracts\FacturaElectronicaProvider;
use App\Models\ConfiguracionSistema;
use App\Services\FacturaElectronica\FactusFacturaElectronicaProvider;
use App\Services\FacturaElectronica\NullFacturaElectronicaProvider;
use Illuminate\Support\Facades\Config;
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
        $this->aplicarConfiguracionGuardada();
    }

    /**
     * La vista "Configuración" (Administrador) guarda ciertos valores en la
     * tabla configuracion_sistema en vez de en el .env, para que un admin
     * sin acceso al servidor pueda cambiarlos (facturación electrónica,
     * tiempos de inactividad, token del agente de impresión). Aquí se
     * sobrescribe el config() de arranque con lo que haya guardado — si no
     * hay nada guardado para una clave, todo sigue leyendo el .env como
     * siempre. Envuelto en try/catch porque esto corre en CADA arranque,
     * incluidos comandos como `migrate` antes de que la tabla exista.
     */
    private function aplicarConfiguracionGuardada(): void
    {
        try {
            $guardados = ConfiguracionSistema::pluck('valor', 'clave');
        } catch (\Throwable $e) {
            return;
        }

        if ($guardados->isEmpty()) {
            return;
        }

        $mapa = [
            'factura_electronica_habilitada' => ['services.factura_electronica.habilitada', 'bool'],
            'factus_url' => ['services.factus.url', 'string'],
            'factus_client_id' => ['services.factus.client_id', 'string'],
            'factus_client_secret' => ['services.factus.client_secret', 'string'],
            'factus_username' => ['services.factus.username', 'string'],
            'factus_password' => ['services.factus.password', 'string'],
            'inactividad_operativos_minutos' => ['nexora.inactividad_operativos_minutos', 'int'],
            'inactividad_admin_minutos' => ['nexora.inactividad_admin_minutos', 'int'],
            'sesion_maxima_operativos_horas' => ['nexora.sesion_maxima_operativos_horas', 'float'],
            'sesion_maxima_admin_horas' => ['nexora.sesion_maxima_admin_horas', 'float'],
            'agente_impresion_token' => ['app.agente_impresion_token', 'string'],
        ];

        foreach ($mapa as $clave => [$rutaConfig, $tipo]) {
            if (!$guardados->has($clave)) {
                continue;
            }

            $valor = $guardados->get($clave);
            Config::set($rutaConfig, match ($tipo) {
                'bool' => $valor === '1',
                'int' => (int) $valor,
                'float' => (float) $valor,
                default => $valor,
            });
        }
    }
}
