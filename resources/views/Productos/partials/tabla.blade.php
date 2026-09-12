<div class="table-wrapper">
    <div class="table-scroll">
        <table class="prod-tbl">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th style="text-align:center;">Existencia</th>
                    <th style="text-align:right;">Precio</th>
                    <th style="text-align:center;">Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($productos as $producto)
                    @php $stock = $producto->inventarios->sum('stock'); @endphp
                    <tr class="{{ $producto->inactivo == 1 ? 'inactivo' : '' }}">

                        <td><span class="td-mono">{{ $producto->codigo }}</span></td>

                        <td>
                            <div style="font-weight:500;color:#111827;">{{ $producto->descripcion }}</div>
                            <div style="font-size:11px;color:#9CA3AF;">{{ $producto->categoria }} · {{ $producto->und_detal }}</div>
                        </td>

                        <td style="text-align:center;">
                            @if (!$producto->afecta_inventario)
                                <span class="badge badge-gray">— N/A</span>
                            @elseif ($stock <= 0)
                                <span class="badge badge-red"><span class="dot"></span>{{ $stock }}</span>
                            @else
                                <span class="badge badge-green"><span class="dot"></span>{{ $stock }}</span>
                            @endif
                        </td>

                        <td class="td-money" style="text-align:right;">${{ number_format($producto->precio, 0, ',', '.') }}</td>

                        <td style="text-align:center;">
                            @if ($producto->inactivo == 1)
                                <span class="badge badge-red"><span class="dot"></span>Inactivo</span>
                            @else
                                <span class="badge badge-green"><span class="dot"></span>Activo</span>
                            @endif
                        </td>

                        <td>
                            <div class="tbl-actions">
                                @if (auth()->user()->tienePermiso('productos.editar'))
                                    <button onclick="editarProducto({{ $producto->id }})" class="act-btn edit" title="Editar producto">✏️</button>
                                @endif

                                @if (auth()->user()->tienePermiso('productos.estado'))
                                    <button onclick="cambiarEstadoProducto({{ $producto->id }})" class="act-btn state" title="Cambiar estado">🚫</button>
                                @endif

                                @if (auth()->user()->tienePermiso('productos.eliminar'))
                                    <button onclick="eliminarrProducto({{ $producto->id }})" class="act-btn del" title="Eliminar producto">🗑️</button>
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="spinner-cell">📭 No se encontraron productos</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:10px 14px;border-top:1px solid #F3F4F6;font-size:12px;color:#6B7280;">
        Mostrando {{ $productos->count() }} producto{{ $productos->count() !== 1 ? 's' : '' }}
    </div>
</div>
