<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Ajustes generales que hasta ahora solo se podían cambiar editando el
 * .env a mano en el servidor (facturación electrónica, tiempos de
 * inactividad, token del agente de impresión). Se guardan en
 * configuracion_sistema y AppServiceProvider::aplicarConfiguracionGuardada()
 * los aplica sobre config() en cada arranque — ver ese archivo.
 */
class ConfiguracionController extends Controller
{
    /**
     * Estas nunca se devuelven en texto plano al navegador: solo si están
     * configuradas o no. Dejar el campo vacío en el formulario CONSERVA el
     * valor que ya había, igual que la clave de un usuario en Cuentas y roles.
     */
    private const CLAVES_SECRETAS = ['factus_client_secret', 'factus_password', 'agente_impresion_token'];

    public function index()
    {
        return view('configuracion.index');
    }

    public function show()
    {
        $guardados = ConfiguracionSistema::pluck('valor', 'clave');

        return response()->json([
            'data' => [
                'hora_corte_operativo' => $guardados->get('hora_corte_operativo', '00:00'),
                'inactividad_operativos_minutos' => (int) $guardados->get('inactividad_operativos_minutos', config('nexora.inactividad_operativos_minutos')),
                'inactividad_admin_minutos' => (int) $guardados->get('inactividad_admin_minutos', config('nexora.inactividad_admin_minutos')),
                'sesion_maxima_operativos_horas' => (float) $guardados->get('sesion_maxima_operativos_horas', config('nexora.sesion_maxima_operativos_horas')),
                'sesion_maxima_admin_horas' => (float) $guardados->get('sesion_maxima_admin_horas', config('nexora.sesion_maxima_admin_horas')),
                'factura_electronica_habilitada' => $guardados->has('factura_electronica_habilitada')
                    ? $guardados->get('factura_electronica_habilitada') === '1'
                    : (bool) config('services.factura_electronica.habilitada'),
                'factus_url' => $guardados->get('factus_url', config('services.factus.url')),
                'factus_client_id' => $guardados->get('factus_client_id', config('services.factus.client_id')),
                'factus_username' => $guardados->get('factus_username', config('services.factus.username')),
                'factus_client_secret_configurado' => filled($guardados->get('factus_client_secret', config('services.factus.client_secret'))),
                'factus_password_configurado' => filled($guardados->get('factus_password', config('services.factus.password'))),
                // config('app.agente_impresion_token') no está declarado en
                // config/app.php, así que sin el segundo argumento SIEMPRE
                // da null — hay que replicar el mismo fallback a env() que
                // usa AutenticarAgenteImpresion, o esto nunca ve el token
                // que ya viene del .env.
                'agente_impresion_token_configurado' => filled($guardados->get('agente_impresion_token', config('app.agente_impresion_token', env('AGENTE_IMPRESION_TOKEN')))),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'hora_corte_operativo' => ['required', 'date_format:H:i'],
            'inactividad_operativos_minutos' => ['required', 'integer', 'min:1', 'max:1440'],
            'inactividad_admin_minutos' => ['required', 'integer', 'min:1', 'max:1440'],
            // 0 = sin límite. Hasta 72h para no cerrar sesión mid-turno por
            // un valor mal puesto, pero sin dejarlo verdaderamente abierto.
            'sesion_maxima_operativos_horas' => ['required', 'numeric', 'min:0', 'max:72'],
            'sesion_maxima_admin_horas' => ['required', 'numeric', 'min:0', 'max:72'],
            'factura_electronica_habilitada' => ['required', 'boolean'],
            'factus_url' => ['nullable', 'url', 'max:255'],
            'factus_client_id' => ['nullable', 'string', 'max:255'],
            'factus_client_secret' => ['nullable', 'string', 'max:255'],
            'factus_username' => ['nullable', 'string', 'max:255'],
            'factus_password' => ['nullable', 'string', 'max:255'],
            'agente_impresion_token' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (['hora_corte_operativo', 'inactividad_operativos_minutos', 'inactividad_admin_minutos', 'sesion_maxima_operativos_horas', 'sesion_maxima_admin_horas'] as $clave) {
            ConfiguracionSistema::updateOrCreate(['clave' => $clave], ['valor' => (string) $datos[$clave]]);
        }

        ConfiguracionSistema::updateOrCreate(
            ['clave' => 'factura_electronica_habilitada'],
            ['valor' => $datos['factura_electronica_habilitada'] ? '1' : '0']
        );

        foreach (['factus_url', 'factus_client_id', 'factus_username'] as $clave) {
            if ($request->filled($clave)) {
                ConfiguracionSistema::updateOrCreate(['clave' => $clave], ['valor' => $datos[$clave]]);
            }
        }

        foreach (self::CLAVES_SECRETAS as $clave) {
            if ($request->filled($clave)) {
                ConfiguracionSistema::updateOrCreate(['clave' => $clave], ['valor' => $datos[$clave]]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Configuración actualizada correctamente.']);
    }

    /**
     * Genera un token nuevo y lo devuelve en texto plano UNA vez, para que
     * el admin lo copie y lo pegue en el .env del agente de impresión de
     * cada negocio. Después de esto ya no se puede volver a ver, solo
     * regenerar de nuevo.
     */
    public function regenerarTokenAgente()
    {
        $token = Str::random(64);

        ConfiguracionSistema::updateOrCreate(
            ['clave' => 'agente_impresion_token'],
            ['valor' => $token]
        );

        return response()->json(['success' => true, 'token' => $token]);
    }

    /**
     * Empaqueta el programa de impresión (storage/app/agente-impresion/ —
     * sin node_modules ni un .env real, esos los completa cada negocio)
     * para que un admin lo pueda descargar directo desde aquí en vez de
     * tener que pasárselo por WhatsApp o USB cada vez que monta un
     * negocio nuevo.
     */
    public function descargarAgenteImpresion()
    {
        $origen = storage_path('app/agente-impresion');

        if (!is_dir($origen)) {
            abort(404, 'El paquete del agente de impresión no está disponible en este servidor.');
        }

        $zipPath = storage_path('app/agente-impresion.zip');

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $archivos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($origen, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($archivos as $archivo) {
            $rutaDentroDelZip = 'agente-impresion/' . substr($archivo->getPathname(), strlen($origen) + 1);
            $zip->addFile($archivo->getPathname(), $rutaDentroDelZip);
        }

        $zip->close();

        return response()->download($zipPath, 'agente-impresion.zip')->deleteFileAfterSend(true);
    }
}
