<?php

namespace App\Http\Controllers;

use App\Models\AcompanamientoGrupo;
use App\Models\Bodega;
use App\Models\GrupoMenu;
use App\Models\Impresora;
use App\Models\IntegracionContable;
use App\Models\Producto;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Productos por página en las tablas paginadas (index y búsqueda admin).
    public const POR_PAGINA = 50;

    public function index()
    {
        $productos = Producto::with('inventarios')
            ->orderBy('descripcion')
            ->paginate(self::POR_PAGINA);

        $grupos = GrupoMenu::all();
        $gruposAcompanamiento = AcompanamientoGrupo::orderBy('descripcion')->get(['id', 'descripcion', 'cantidad_maxima']);
        $integracionesContables = IntegracionContable::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);
        $bodegas = Bodega::orderBy('descripcion')->get(['id', 'descripcion']);

        // Las métricas van sobre TODO el catálogo, no solo la página visible.
        $metricas = $this->calcularMetricas();

        return view('productos.index', compact('productos', 'grupos', 'gruposAcompanamiento', 'integracionesContables', 'bodegas', 'metricas'));
    }

    /**
     * Métricas del catálogo completo (independientes de la paginación).
     */
    private function calcularMetricas(): array
    {
        $todos = Producto::with('inventarios')->get(['id', 'inactivo', 'afecta_inventario', 'precio']);

        return [
            'total' => $todos->count(),
            'activos' => $todos->where('inactivo', 0)->count(),
            'inactivos' => $todos->where('inactivo', 1)->count(),
            'sin_stock' => $todos->filter(fn ($p) => $p->afecta_inventario && $p->inventarios->sum('stock') <= 0)->count(),
            'valor_inventario' => $todos->sum(fn ($p) => $p->inventarios->sum('stock') * $p->precio),
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validarNoMezclarEnsambladoYAcompanamiento($request);

        $datos = $request->validate(array_merge([
            'codigo' => 'required|unique:productos,codigo',
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'afecta_inventario' => 'required|in:0,1',
            // Sin grupo de menú el producto simplemente no aparece en la
            // vista del mesero (ver FacturacionController::index, que
            // filtra whereNotNull('grupo_menu_id')) — así es como se marca
            // una materia prima que no se vende directo.
            'grupo_menu_id' => 'nullable|exists:grupo_menus,id',
            // Sin esto, cerrarMesa() rechaza CUALQUIER venta de este
            // producto con "no tiene una integración contable configurada"
            // — mejor exigirla al crear el producto que descubrirlo en
            // plena venta.
            'integracion_contable_id' => 'required|exists:integraciones_contables,id',
            'iva_ventas' => 'nullable|numeric|min:0|max:100',
        ], $this->reglasEnsamblado(null), $this->reglasAcompanamiento()));

        Producto::create([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'grupo_menu_id' => $request->grupo_menu_id, // Guardamos el grupo
            'integracion_contable_id' => $request->integracion_contable_id,
            'und_detal' => $request->und_detal,
            'precio' => $request->precio,
            'caracteristicas' => $request->caracteristicas,
            'afecta_inventario' => $this->resolverAfectaInventario($request),
            'iva_ventas' => $request->iva_ventas ?: null,
            'inactivo' => 0,
            ...$this->datosEnsamblado($datos),
            ...$this->datosAcompanamiento($datos),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto guardado correctamente'
        ]);
    }

    /**
     * "Producto ensamblado" (un insumo, factor fijo) y "acompañamiento"
     * (varias opciones, reparto libre al comandar) son dos formas distintas
     * de descontar inventario al vender — no tiene sentido activar las dos
     * a la vez en el mismo producto.
     */
    private function validarNoMezclarEnsambladoYAcompanamiento(Request $request): void
    {
        if ($request->boolean('es_ensamblado') && $request->boolean('tiene_acompanamiento')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tiene_acompanamiento' => 'Un producto no puede ser "ensamblado" y tener "acompañamiento" a la vez — elige solo una opción.',
            ]);
        }
    }

    /**
     * Reglas de validación para "Insertar acompañamiento": si viene
     * marcado, el grupo es obligatorio (sin él no hay de dónde repartir
     * unidades al comandar).
     */
    private function reglasAcompanamiento(): array
    {
        return [
            'tiene_acompanamiento' => 'nullable|in:0,1',
            'acompanamiento_grupo_id' => 'nullable|required_if:tiene_acompanamiento,1|exists:acompanamiento_grupos,id',
        ];
    }

    private function datosAcompanamiento(array $datos): array
    {
        $tieneAcompanamiento = (bool) ($datos['tiene_acompanamiento'] ?? false);

        return [
            'acompanamiento_grupo_id' => $tieneAcompanamiento ? $datos['acompanamiento_grupo_id'] : null,
        ];
    }

    /**
     * Reglas de validación para el apartado "Producto ensamblado". Cuando
     * es_ensamblado viene marcado, producto_base_id y factor_consumo pasan
     * a ser obligatorios — sin esto no hay forma de saber qué ni cuánto
     * descontar cuando se venda.
     */
    private function reglasEnsamblado(?int $id): array
    {
        $reglasBase = ['nullable', 'required_if:es_ensamblado,1', 'exists:productos,id'];
        if ($id) {
            // Un producto no puede ser su propio insumo base.
            $reglasBase[] = Rule::notIn([$id]);
        }

        return [
            'es_ensamblado' => 'nullable|in:0,1',
            'producto_base_id' => $reglasBase,
            'factor_consumo' => 'nullable|required_if:es_ensamblado,1|numeric|min:0.01',
            // Opcional: si el insumo real vive en una bodega distinta a la
            // de la caja que vende (ej. la carne de una hamburguesa vendida
            // en Discoteca vive en la bodega de Cocina) — ver
            // FacturacionController::resolverDescuentosInventario().
            'bodega_origen_id' => 'nullable|exists:bodegas,id',
        ];
    }

    /**
     * Normaliza los campos de ensamble ya validados para pasarlos a
     * Producto::create()/update(): si no quedó marcado como ensamblado, se
     * limpian base y factor para no dejar basura a medias.
     */
    private function datosEnsamblado(array $datos): array
    {
        $esEnsamblado = (bool) ($datos['es_ensamblado'] ?? false);

        return [
            'es_ensamblado' => $esEnsamblado,
            'producto_base_id' => $esEnsamblado ? $datos['producto_base_id'] : null,
            'factor_consumo' => $esEnsamblado ? $datos['factor_consumo'] : null,
            'bodega_origen_id' => $esEnsamblado ? ($datos['bodega_origen_id'] ?? null) : null,
        ];
    }

    public function buscar(Request $request)
    {
        $query = $request->query('query');

        $productos = Producto::query()
            ->where(function ($q) use ($query) {
                $q->where('descripcion', 'like', "%$query%")
                    ->orWhere('codigo', 'like', "%$query%");
            })
            ->select('id', 'codigo', 'descripcion', 'precio')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = Producto::with([
            'productoBase:id,codigo,descripcion',
            'acompanamientoGrupo:id,descripcion,cantidad_maxima',
            'integracionContable:id,nombre',
        ])->findOrFail($id);

        return response()->json($producto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $this->validarNoMezclarEnsambladoYAcompanamiento($request);

        $datos = $request->validate(array_merge([
            'codigo' => 'required|unique:productos,codigo,' . $id,
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'afecta_inventario' => 'required|in:0,1',
            'grupo_menu_id' => 'nullable|exists:grupo_menus,id',
            'integracion_contable_id' => 'required|exists:integraciones_contables,id',
            'iva_ventas' => 'nullable|numeric|min:0|max:100'
        ], $this->reglasEnsamblado((int) $id), $this->reglasAcompanamiento()));

        $producto->update([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'grupo_menu_id' => $request->grupo_menu_id, // Actualizamos el grupo
            'integracion_contable_id' => $request->integracion_contable_id,
            'und_detal' => $request->und_detal,
            'precio' => $request->precio,
            'caracteristicas' => $request->caracteristicas,
            'afecta_inventario' => $this->resolverAfectaInventario($request),
            'iva_ventas' => $request->iva_ventas ?: null,
            ...$this->datosEnsamblado($datos),
            ...$this->datosAcompanamiento($datos),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente'
        ]);
    }

    /**
     * Un producto ensamblado o con acompañamiento (ej. "Cubetazo Águila")
     * NUNCA lleva su propio stock: lo que de verdad se descuenta es su
     * insumo real (ver resolverDescuentosInventario() en
     * FacturacionController). Si además queda marcado "Afecta inventario",
     * el sistema le crea una fila de inventario propia y — peor — un
     * movimiento de kardex "fantasma" cada vez que se vende (esto ya
     * pasó con productos reales: CUBETAZO AGUILA y CUBETAZO MIX quedaron
     * con afecta_inventario=1 por error). Se fuerza a false acá para que
     * no vuelva a colarse, sin importar lo que llegue del formulario.
     */
    private function resolverAfectaInventario(Request $request): bool
    {
        if ($request->boolean('es_ensamblado') || $request->boolean('tiene_acompanamiento')) {
            return false;
        }

        return $request->boolean('afecta_inventario');
    }

    public function cambiarEstado($id)
    {
        $producto = Producto::findOrFail($id);

        // alternar valor
        $producto->inactivo = $producto->inactivo == 0 ? 1 : 0;

        $producto->save();

        return response()->json([
            'success' => true,
            'message' => $producto->inactivo == 1
                ? 'Producto desactivado correctamente'
                : 'Producto activado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Validar stock en inventario
        $tieneStock = DB::table('inventarios')
            ->where('producto_id', $id)
            ->where('stock', '>', 0)
            ->exists();

        if ($tieneStock) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar. El producto tiene existencias en inventario.'
            ], 422);
        }

        // // Validar movimientos
        // $tieneMovimientos = DB::table('movimientos')
        //     ->where('producto_id', $id)
        //     ->exists();

        // if ($tieneMovimientos) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No se puede eliminar. El producto ya tiene movimientos registrados.'
        //     ], 422);
        // }

        try {
            $producto->delete();
        } catch (QueryException $e) {
            // 23000: violación de llave foránea (el producto ya está en
            // facturas, pedidos, compras, kardex, etc.).
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar. El producto ya tiene movimientos o documentos asociados; desactívalo en su lugar.'
                ], 422);
            }

            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.'
        ]);
    }

    public function buscarAdmin(Request $request)
    {
        $texto = $request->texto;
        $estado = $request->estado; // '1' inactivo, '0' activo, '' todos

        $productos = Producto::with('inventarios')
            ->when($texto, fn ($q) => $q->where(function ($q2) use ($texto) {
                $q2->where('codigo', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
            }))
            ->when($estado !== null && $estado !== '', fn ($q) => $q->where('inactivo', $estado))
            ->orderBy('descripcion')
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        return view('productos.partials.tabla', compact('productos'));
    }
}
