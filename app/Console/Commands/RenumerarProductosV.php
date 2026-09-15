<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RenumerarProductosV extends Command
{
    protected $signature = 'nexora:renumerar-productos-v
        {--ejecutar : Confirma la renumeración en la base de datos}';

    protected $description = 'Renombra productos V a V1, V2, V3... y conserva el código origen para futuras importaciones.';

    public function handle(): int
    {
        $productos = Producto::query()
            ->whereRaw("codigo REGEXP '^V[0-9]+$'")
            ->get(['id', 'codigo', 'codigo_barras', 'descripcion'])
            ->sort(function (Producto $izquierda, Producto $derecha): int {
                $numeroIzquierda = (int) substr($izquierda->codigo, 1);
                $numeroDerecha = (int) substr($derecha->codigo, 1);

                return $numeroIzquierda <=> $numeroDerecha ?: $izquierda->id <=> $derecha->id;
            })
            ->values();

        if ($productos->isEmpty()) {
            $this->warn('No se encontraron productos con códigos V numéricos.');

            return self::SUCCESS;
        }

        $this->info("Se renumerarán {$productos->count()} productos desde V1 hasta V{$productos->count()}.");
        $this->table(
            ['Actual', 'Nuevo', 'Descripción'],
            $productos->take(10)->values()->map(fn (Producto $producto, int $indice) => [
                $producto->codigo,
                'V' . ($indice + 1),
                $producto->descripcion,
            ])->all()
        );

        if (!$this->option('ejecutar')) {
            $this->warn('Simulación terminada. Agrega --ejecutar para aplicar el cambio.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($productos): void {
                $ids = $productos->pluck('id');
                $bloqueados = Producto::whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');
                $destinos = $productos->keys()->map(fn (int $indice) => 'V' . ($indice + 1));

                $conflicto = Producto::whereIn('codigo', $destinos)
                    ->whereNotIn('id', $ids)
                    ->exists();

                if ($conflicto) {
                    throw new RuntimeException('Existe un código destino V1... en un producto fuera de la renumeración.');
                }

                // Primero usamos valores temporales para no violar el índice único
                // mientras se intercambian códigos dentro de la misma transacción.
                foreach ($productos as $producto) {
                    Producto::whereKey($producto->id)->update([
                        'codigo' => "__REN_V_{$producto->id}",
                        'updated_at' => now(),
                    ]);
                }

                foreach ($productos as $indice => $producto) {
                    $actual = $bloqueados->get($producto->id);

                    Producto::whereKey($producto->id)->update([
                        'codigo' => 'V' . ($indice + 1),
                        'codigo_barras' => $actual->codigo_barras ?: $actual->codigo,
                        'updated_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $error) {
            report($error);
            $this->error('La renumeración fue revertida: ' . $error->getMessage());

            return self::FAILURE;
        }

        $this->info("Renumeración completada: V1 a V{$productos->count()}.");

        return self::SUCCESS;
    }
}
