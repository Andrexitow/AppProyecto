<?php

namespace App\Console\Commands;

use App\Models\Bodega;
use App\Models\Documento;
use App\Models\IntegracionContable;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use ZipArchive;

class ImportarInventarioGigiomania extends Command
{
    protected $signature = 'nexora:importar-gigiomania
        {catalogo : Archivo RepProductos-v2.xlsx}
        {--existencia-02= : Archivo RepExistenciaPOS-02.xlsx}
        {--existencia-03= : Archivo RepExistenciaPOS-03.xlsx}
        {--existencia-05= : Archivo RepExistenciaPOS-05.xlsx}
        {--existencia-09= : Archivo RepExistenciaPOS-09.xlsx}
        {--usuario=1 : ID del usuario que registrará los documentos de migración}
        {--ejecutar : Confirma la escritura en la base de datos}';

    protected $description = 'Importa productos V y saldos iniciales por bodega desde reportes Excel de Gigiomania.';

    /** @var array<int, string> */
    private array $sharedStrings = [];

    public function handle(DocumentService $documentService): int
    {
        $catalogo = $this->leerCatalogo($this->argument('catalogo'));
        $usuario = User::find($this->option('usuario'));

        if (!$usuario) {
            $this->error('No existe el usuario indicado para registrar la migración.');

            return self::FAILURE;
        }

        $configuracionBodegas = [
            '02' => ['nombre' => 'Restaurante', 'archivo' => $this->option('existencia-02')],
            '03' => ['nombre' => 'Discoteca', 'archivo' => $this->option('existencia-03')],
            '05' => ['nombre' => 'Karaoke', 'archivo' => $this->option('existencia-05')],
            '09' => ['nombre' => 'Bodega general', 'archivo' => $this->option('existencia-09')],
        ];

        $saldos = [];
        $resumenReportes = [];

        foreach ($configuracionBodegas as $codigo => $configuracion) {
            if (!$configuracion['archivo']) {
                $this->error("Falta el archivo de existencias para la bodega {$codigo}.");

                return self::FAILURE;
            }

            [$filas, $resumen] = $this->leerExistencias($configuracion['archivo'], $catalogo);
            $saldos[$codigo] = $filas;
            $resumenReportes[$codigo] = $resumen;
        }

        $this->newLine();
        $this->info('Resumen del archivo fuente');
        $this->table(
            ['Productos V', 'Reportes', 'Saldos aplicables', 'Filas omitidas'],
            [[count($catalogo['por_codigo']), count($configuracionBodegas), array_sum(array_map('count', $saldos)), array_sum(array_column($resumenReportes, 'omitidas'))]]
        );

        foreach ($resumenReportes as $codigo => $resumen) {
            $this->line("Bodega {$codigo}: {$resumen['aplicables']} saldos V aplicables; {$resumen['omitidas']} omitidos por no pertenecer al catálogo V o tener descripción ambigua.");
        }

        if (!$this->option('ejecutar')) {
            $this->warn('Simulación terminada. No se modificó la base de datos. Agrega --ejecutar para confirmar la importación.');

            return self::SUCCESS;
        }

        try {
            $resultado = DB::transaction(function () use ($catalogo, $configuracionBodegas, $saldos, $usuario, $documentService) {
                $integracionPredeterminada = IntegracionContable::where('codigo', 'OTROS')->where('estado', true)->value('id');

                if (!$integracionPredeterminada) {
                    throw new RuntimeException('No existe una integración contable activa con código OTROS para los productos importados.');
                }

                $productos = [];
                $creados = 0;
                $actualizados = 0;

                foreach ($catalogo['por_codigo'] as $codigo => $fila) {
                    // Tras la normalización, el código visible puede ser V1, V2,
                    // etc. El código del POS origen se conserva como código de barras
                    // para poder reconciliar futuras importaciones sin duplicar ítems.
                    $producto = Producto::where('codigo', $codigo)
                        ->orWhere('codigo_barras', $codigo)
                        ->first();
                    $valores = [
                        'codigo_barras' => $codigo,
                        'descripcion' => $fila['descripcion'],
                        'und_detal' => $fila['unidad'],
                        'precio' => $fila['precio'],
                        'afecta_inventario' => true,
                        'inactivo' => false,
                    ];

                    if ($producto) {
                        $producto->fill($valores);
                        if (!$producto->integracion_contable_id) {
                            $producto->integracion_contable_id = $integracionPredeterminada;
                        }
                        $producto->save();
                        $actualizados++;
                    } else {
                        $producto = Producto::create($valores + [
                            'codigo' => $codigo,
                            'integracion_contable_id' => $integracionPredeterminada,
                        ]);
                        $creados++;
                    }

                    $productos[$codigo] = $producto;
                }

                $bodegas = [];
                $bodegasCreadas = 0;
                foreach ($configuracionBodegas as $codigo => $configuracion) {
                    $bodega = Bodega::whereRaw('LOWER(descripcion) = ?', [mb_strtolower($configuracion['nombre'])])->first();
                    if (!$bodega) {
                        $bodega = Bodega::create(['descripcion' => $configuracion['nombre']]);
                        $bodegasCreadas++;
                    }
                    $bodegas[$codigo] = $bodega;
                }

                $tipoMigracion = TipoDocumento::firstOrCreate(
                    ['codigo' => 'MIGRACION_INVENTARIO'],
                    [
                        'nombre' => 'Migración inicial de inventario',
                        'prefijo_default' => 'MIG',
                        'afecta_inventario' => true,
                        'afecta_caja' => false,
                        'afecta_contabilidad' => false,
                        'naturaleza' => 'neutro',
                        'activo' => true,
                    ]
                );

                $documentos = 0;
                $movimientos = 0;

                foreach ($saldos as $codigoBodega => $filas) {
                    $bodega = $bodegas[$codigoBodega];
                    $detalles = [];
                    // El reporte representa el saldo completo de la bodega: un
                    // producto V ausente debe terminar con existencia cero.
                    $objetivos = array_fill_keys(array_keys($catalogo['por_codigo']), 0.0);
                    foreach ($filas as $codigoProducto => $cantidad) {
                        $objetivos[$codigoProducto] = $cantidad;
                    }

                    foreach ($objetivos as $codigoProducto => $cantidadDestino) {
                        $producto = $productos[$codigoProducto];
                        $inventario = Inventario::where([
                            'producto_id' => $producto->id,
                            'bodega_id' => $bodega->id,
                        ])->lockForUpdate()->first();
                        $cantidadActual = (float) ($inventario?->stock ?? 0);
                        $diferencia = round($cantidadDestino - $cantidadActual, 3);

                        if (abs($diferencia) < 0.0005) {
                            continue;
                        }

                        $detalles[] = [
                            'producto_id' => $producto->id,
                            'descripcion' => $producto->descripcion,
                            'cantidad' => abs($diferencia),
                            'precio_unitario' => 0,
                            'subtotal' => 0,
                            'total' => 0,
                            'tipo_movimiento_inventario' => $diferencia > 0 ? 'ENTRADA' : 'SALIDA',
                            'bodega_origen_id' => $diferencia < 0 ? $bodega->id : null,
                            'bodega_destino_id' => $diferencia > 0 ? $bodega->id : null,
                        ];
                    }

                    if (!$detalles) {
                        continue;
                    }

                    $documento = $documentService->crear([
                        'tipo_codigo' => $tipoMigracion->codigo,
                        'prefijo' => 'MIG',
                        'fecha' => now()->toDateString(),
                        'bodega_id' => $bodega->id,
                        'user_id' => $usuario->id,
                        'observaciones' => "Saldos iniciales importados desde reporte POS de bodega {$codigoBodega}.",
                        'referencia_externa' => "GIGIOMANIA-POS-{$codigoBodega}-" . now()->format('Ymd'),
                    ], $detalles);

                    $documentService->registrar($documento, $usuario->id);
                    $documentos++;
                    $movimientos += count($detalles);
                }

                return compact('creados', 'actualizados', 'bodegasCreadas', 'documentos', 'movimientos');
            });
        } catch (\Throwable $error) {
            report($error);
            $this->error('La importación fue revertida: ' . $error->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Importación completada.');
        $this->table(
            ['Productos creados', 'Productos actualizados', 'Bodegas creadas', 'Documentos de migración', 'Movimientos de inventario'],
            [array_values($resultado)]
        );
        $this->warn('Los reportes no incluyen costos ni impuestos. El inventario se cargó con costo promedio $0 y sin inventar IVA; esos datos deben parametrizarse antes de emitir facturas electrónicas.');

        return self::SUCCESS;
    }

    /** @return array{por_codigo: array<string, array{descripcion:string, unidad:string, precio:float}>, por_descripcion: array<string, array<int, string>>} */
    private function leerCatalogo(string $archivo): array
    {
        $filas = $this->leerPrimeraHoja($archivo);
        $cabecera = $this->buscarCabecera($filas, ['codigo', 'descrip', 'unidad', 'prec']);
        $porCodigo = [];
        $porDescripcion = [];

        foreach (array_slice($filas, $cabecera['fila'] + 1) as $fila) {
            $codigo = trim((string) ($fila[$cabecera['columnas']['codigo']] ?? ''));
            if (!str_starts_with($codigo, 'V')) {
                continue;
            }

            $descripcion = trim((string) ($fila[$cabecera['columnas']['descrip']] ?? ''));
            if ($descripcion === '') {
                continue;
            }

            $porCodigo[$codigo] = [
                'descripcion' => $descripcion,
                'unidad' => trim((string) ($fila[$cabecera['columnas']['unidad']] ?? 'UND')) ?: 'UND',
                'precio' => $this->numero($fila[$cabecera['columnas']['prec']] ?? 0),
            ];
            $porDescripcion[$this->clave($descripcion)][] = $codigo;
        }

        if (!$porCodigo) {
            throw new RuntimeException('El catálogo no contiene productos cuyo código inicie por V.');
        }

        return ['por_codigo' => $porCodigo, 'por_descripcion' => $porDescripcion];
    }

    /** @param array{por_codigo: array<string, mixed>, por_descripcion: array<string, array<int, string>>} $catalogo */
    private function leerExistencias(string $archivo, array $catalogo): array
    {
        $filas = $this->leerPrimeraHoja($archivo);
        $cabecera = $this->buscarCabecera($filas, ['descrip', 'cantidad']);
        $saldos = [];
        $omitidas = 0;

        foreach (array_slice($filas, $cabecera['fila'] + 1) as $fila) {
            $descripcion = trim((string) ($fila[$cabecera['columnas']['descrip']] ?? ''));
            if ($descripcion === '') {
                continue;
            }

            $codigos = $catalogo['por_descripcion'][$this->clave($descripcion)] ?? [];
            if (count($codigos) !== 1) {
                $omitidas++;
                continue;
            }

            $codigo = $codigos[0];
            $saldos[$codigo] = ($saldos[$codigo] ?? 0) + $this->numero($fila[$cabecera['columnas']['cantidad']] ?? 0);
        }

        return [$saldos, ['aplicables' => count($saldos), 'omitidas' => $omitidas]];
    }

    /** @return array<int, array<int, string|float|null>> */
    private function leerPrimeraHoja(string $archivo): array
    {
        if (!is_file($archivo)) {
            throw new RuntimeException("No se encontró el archivo: {$archivo}");
        }

        $zip = new ZipArchive();
        if ($zip->open($archivo) !== true) {
            throw new RuntimeException("No se pudo abrir el archivo Excel: {$archivo}");
        }

        try {
            $this->sharedStrings = [];
            if ($contenido = $zip->getFromName('xl/sharedStrings.xml')) {
                $xml = simplexml_load_string($contenido);
                $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($xml->xpath('//x:si') as $item) {
                    $this->sharedStrings[] = trim(implode('', $item->xpath('.//x:t')));
                }
            }

            $contenido = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($contenido === false) {
                throw new RuntimeException("El archivo {$archivo} no contiene la primera hoja de cálculo.");
            }

            $xml = simplexml_load_string($contenido);
            $xml->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $filas = [];

            foreach ($xml->xpath('//x:sheetData/x:row') as $filaXml) {
                $fila = [];
                foreach ($filaXml->c as $celda) {
                    $columna = $this->indiceColumna((string) $celda['r']);
                    $tipo = (string) $celda['t'];
                    $valor = (string) ($celda->v ?? '');

                    if ($tipo === 's') {
                        $valor = $this->sharedStrings[(int) $valor] ?? '';
                    } elseif ($tipo === 'inlineStr') {
                        $celda->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                        $valor = trim(implode('', $celda->xpath('.//x:t')));
                    }

                    $fila[$columna] = $valor;
                }
                $filas[] = $fila;
            }

            return $filas;
        } finally {
            $zip->close();
        }
    }

    /** @param array<int, array<int, string|float|null>> $filas */
    private function buscarCabecera(array $filas, array $requeridas): array
    {
        foreach ($filas as $indiceFila => $fila) {
            $columnas = [];
            foreach ($fila as $indiceColumna => $valor) {
                $texto = $this->clave((string) $valor);
                foreach ($requeridas as $requerida) {
                    if (str_contains($texto, $requerida)) {
                        $columnas[$requerida] ??= $indiceColumna;
                    }
                }
            }

            if (count($columnas) === count($requeridas)) {
                return ['fila' => $indiceFila, 'columnas' => $columnas];
            }
        }

        throw new RuntimeException('No se encontraron las columnas requeridas en el reporte Excel.');
    }

    private function indiceColumna(string $referencia): int
    {
        preg_match('/[A-Z]+/', $referencia, $coincidencia);
        $letras = $coincidencia[0] ?? 'A';
        $indice = 0;
        foreach (str_split($letras) as $letra) {
            $indice = ($indice * 26) + (ord($letra) - 64);
        }

        return $indice;
    }

    private function clave(string $valor): string
    {
        $valor = trim(preg_replace('/\s+/u', ' ', $valor) ?? '');

        return mb_strtolower($valor);
    }

    private function numero(mixed $valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        $valor = str_replace(['.', ','], ['', '.'], trim((string) $valor));

        return (float) $valor;
    }
}
