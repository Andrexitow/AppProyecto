<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\TipoDocumentoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Carga de saldos iniciales para poner en marcha el sistema con el histórico
 * de un negocio que ya venía operando (o para abrir un nuevo año fiscal sin
 * volver a digitar todo el año anterior). Es, en el fondo, un comprobante
 * manual más — mismo motor (AccountingService) y mismo ciclo de vida
 * REGISTRADO/ANULADO — pero con su propio tipo de documento ('SI') para que
 * quede identificado en los libros y con la validación de cuadre al frente,
 * porque aquí SIEMPRE se cargan todas las cuentas de un solo golpe.
 */
class SaldoInicialService
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function registrar(array $datos, int $usuarioId): ComprobanteContable
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            $lineas = $datos['lineas'] ?? [];
            if (count($lineas) < 2) {
                throw ValidationException::withMessages(['lineas' => 'Debe incluir al menos dos cuentas para poder cuadrar los saldos iniciales.']);
            }

            $totalDebito = round(array_sum(array_map(fn ($l) => (float) ($l['debito'] ?? 0), $lineas)), 2);
            $totalCredito = round(array_sum(array_map(fn ($l) => (float) ($l['credito'] ?? 0), $lineas)), 2);
            if (abs($totalDebito - $totalCredito) > 0.01) {
                throw ValidationException::withMessages(['lineas' => 'Los saldos iniciales no cuadran: débitos ' . number_format($totalDebito, 2) . ' vs. créditos ' . number_format($totalCredito, 2) . '.']);
            }

            $tipo = TipoDocumentoContable::where('codigo', 'SI')->firstOrFail();

            $comprobante = $this->accounting->crearBorrador([
                'tipo_documento_contable_id' => $tipo->id,
                'fecha' => $datos['fecha'],
                'descripcion' => $datos['descripcion'] ?? 'Carga de saldos iniciales',
            ], $usuarioId);

            return $this->accounting->registrar($comprobante, $lineas, $usuarioId);
        });
    }
}
