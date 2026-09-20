<?php

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Convierte cualquier excepción en un JSON con un motivo legible para el
 * usuario. El frontend muestra `message` tal cual en la notificación, así que
 * nunca debe llegar un "Server Error" pelado.
 */
class MensajeError
{
    /** Códigos MySQL que indican que la BD no está disponible (503). */
    private const CODIGOS_CONEXION = [1213, 1205, 2002, 2006, 2013, 1045, 1044, 1049];

    public static function responder(Throwable $e, Request $request): ?JsonResponse
    {
        if (! ($request->expectsJson() || $request->ajax())) {
            return null;
        }

        if ($e instanceof ValidationException) {
            $errores = $e->errors();
            $mensajes = collect($errores)->flatten()->unique()->values();

            return self::json(
                $mensajes->isEmpty() ? 'Hay datos inválidos en el formulario.' : $mensajes->implode('<br>'),
                $e->status,
                ['errors' => $errores]
            );
        }

        if ($e instanceof AuthenticationException) {
            return self::json('Tu sesión expiró o fue cerrada. Vuelve a iniciar sesión.', 401);
        }

        if ($e instanceof QueryException) {
            [$mensaje, $status] = self::deBaseDeDatos($e);

            return self::json($mensaje, $status);
        }

        if ($e instanceof PostTooLargeException) {
            return self::json('El archivo o los datos enviados son demasiado grandes.', 413);
        }

        if ($e instanceof ThrottleRequestsException) {
            return self::json('Demasiadas solicitudes seguidas. Espera un momento e inténtalo de nuevo.', 429);
        }

        if ($e instanceof HttpExceptionInterface) {
            return self::json(self::deHttp($e), $e->getStatusCode());
        }

        // Cualquier otro error es un fallo inesperado del servidor.
        $ref = Str::upper(Str::random(6));
        Log::error("[ref {$ref}] ".get_class($e).': '.$e->getMessage(), [
            'url' => $request->fullUrl(),
            'archivo' => $e->getFile().':'.$e->getLine(),
        ]);

        $mensaje = "Error inesperado del servidor (ref: {$ref}).";
        if (config('nexora.errores_detallados')) {
            $mensaje .= ' '.class_basename($e).': '.$e->getMessage();
        } else {
            $mensaje .= ' Si se repite, informa esta referencia al administrador.';
        }

        return self::json($mensaje, 500);
    }

    /** @return array{0:string,1:int} */
    private static function deBaseDeDatos(QueryException $e): array
    {
        $codigo = (int) ($e->errorInfo[1] ?? 0);
        $detalle = (string) ($e->errorInfo[2] ?? $e->getMessage());

        $mensaje = match ($codigo) {
            1451 => 'No se puede eliminar ni modificar: el registro está en uso por otros datos (facturas, pedidos, compras, movimientos, etc.).',
            1452 => 'Uno de los datos relacionados ya no existe o no es válido (referencia inexistente).',
            1062 => self::duplicado($detalle),
            1048 => 'Falta un dato obligatorio'.(preg_match("/Column '([^']+)'/", $detalle, $m) ? ": {$m[1]}" : '').'.',
            1406 => 'Un dato es demasiado largo'.(preg_match("/column '([^']+)'/i", $detalle, $m) ? " para el campo {$m[1]}" : '').'.',
            1264 => 'Un valor numérico está fuera del rango permitido'.(preg_match("/column '([^']+)'/i", $detalle, $m) ? " en el campo {$m[1]}" : '').'.',
            1366, 1292 => 'Un dato tiene un formato inválido'.(preg_match("/column '([^']+)'/i", $detalle, $m) ? " en el campo {$m[1]}" : '').'.',
            1054 => 'La base de datos no está actualizada (falta la columna'.(preg_match("/Unknown column '([^']+)'/", $detalle, $m) ? " {$m[1]}" : '').'). Ejecuta las migraciones pendientes.',
            1146 => 'La base de datos no está actualizada (falta la tabla'.(preg_match("/Table '([^']+)'/", $detalle, $m) ? " {$m[1]}" : '').'). Ejecuta las migraciones pendientes.',
            1213, 1205 => 'La base de datos está ocupada procesando otra operación. Inténtalo de nuevo en unos segundos.',
            2002, 2006, 2013, 1045, 1044, 1049 => 'No se pudo conectar con la base de datos. Revisa la configuración o inténtalo más tarde.',
            default => null,
        };

        if ($mensaje !== null) {
            return [$mensaje, in_array($codigo, self::CODIGOS_CONEXION, true) ? 503 : 422];
        }

        // SQLSTATE 23000 sin código MySQL conocido (otros motores de BD).
        if ((string) $e->getCode() === '23000') {
            return ['No se pudo guardar: el dato viola una restricción de la base de datos (duplicado o registro en uso).', 422];
        }

        Log::error('Error de base de datos sin mapear: '.$e->getMessage());

        $mensaje = 'Error de base de datos'.($codigo ? " (código {$codigo})" : '').'.';
        if (config('nexora.errores_detallados')) {
            $mensaje .= ' '.$detalle;
        }

        return [$mensaje, 500];
    }

    private static function duplicado(string $detalle): string
    {
        if (preg_match("/Duplicate entry '(.*)' for key '([^']+)'/", $detalle, $m)) {
            $campo = Str::afterLast($m[2], '.');

            return "Ya existe un registro con el valor \"{$m[1]}\" (índice {$campo}).";
        }

        return 'Ya existe un registro con ese valor (dato duplicado).';
    }

    private static function deHttp(HttpExceptionInterface $e): string
    {
        $status = $e->getStatusCode();

        if ($e->getPrevious() instanceof ModelNotFoundException) {
            return 'El registro solicitado no existe o ya fue eliminado.';
        }

        // Mensajes escritos a propósito con abort(código, 'texto') se respetan.
        $propio = trim($e->getMessage());
        if ($propio !== '' && $status !== 419 && ! Str::startsWith($propio, ['No query results', 'The route', 'This action is unauthorized'])) {
            return $propio;
        }

        return match ($status) {
            400 => 'La solicitud no es válida.',
            401 => 'Tu sesión expiró o fue cerrada. Vuelve a iniciar sesión.',
            403 => 'No tienes permiso para realizar esta acción.',
            404 => 'No se encontró lo solicitado (ruta o registro inexistente).',
            405 => 'Método no permitido para esta acción.',
            419 => 'La sesión de seguridad expiró. Recarga la página (F5) e inténtalo de nuevo.',
            429 => 'Demasiadas solicitudes seguidas. Espera un momento e inténtalo de nuevo.',
            503 => 'El sistema está en mantenimiento o no disponible. Inténtalo más tarde.',
            default => "El servidor respondió con un error ({$status}).",
        };
    }

    private static function json(string $mensaje, int $status, array $extra = []): JsonResponse
    {
        // `error` se incluye porque parte del frontend lee data.error.
        return response()->json(array_merge([
            'success' => false,
            'message' => $mensaje,
            'error' => $mensaje,
        ], $extra), $status);
    }
}
