<div class="table-scroll">
    <table class="acomp-tbl">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th style="text-align:center;">Máximo a repartir</th>
                <th style="text-align:center;">Productos elegibles</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($grupos as $grupo)
                <tr>
                    <td><span class="td-mono">{{ $grupo->codigo }}</span></td>
                    <td>{{ $grupo->descripcion }}</td>
                    <td style="text-align:center;"><span class="badge badge-blue">{{ $grupo->cantidad_maxima }} und.</span></td>
                    <td style="text-align:center;">{{ $grupo->opciones_count }}</td>
                    <td style="text-align:right;">
                        <button class="act-btn" title="Productos del grupo" onclick="gestionarOpciones({{ $grupo->id }}, '{{ addslashes($grupo->descripcion) }}')">🍺</button>
                        <button class="act-btn" title="Editar" onclick="editarAcompanamiento({{ $grupo->id }}, '{{ addslashes($grupo->codigo) }}', '{{ addslashes($grupo->descripcion) }}', {{ $grupo->cantidad_maxima }})">✏️</button>
                        <button class="act-btn" title="Eliminar" onclick="eliminarAcompanamiento({{ $grupo->id }})">🗑️</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="spinner-cell">📭 Todavía no hay grupos de acompañamiento.<br>
                        <span style="font-size:11.5px;">Crea uno, ej. "Servicio de Cubetazo", y luego agrégale los productos elegibles.</span></div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($grupos instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;border-top:1px solid #F3F4F6;font-size:12px;color:#6B7280;flex-wrap:wrap;">
            <span>
                Mostrando {{ $grupos->firstItem() ?? 0 }}–{{ $grupos->lastItem() ?? 0 }}
                de {{ $grupos->total() }} grupo{{ $grupos->total() !== 1 ? 's' : '' }}
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="act-btn" style="width:auto;padding:4px 10px;" onclick="irAPaginaAcompanamiento({{ $grupos->currentPage() - 1 }})" {{ $grupos->onFirstPage() ? 'disabled' : '' }}>‹ Anterior</button>
                <span>Página {{ $grupos->currentPage() }} de {{ $grupos->lastPage() }}</span>
                <button type="button" class="act-btn" style="width:auto;padding:4px 10px;" onclick="irAPaginaAcompanamiento({{ $grupos->currentPage() + 1 }})" {{ $grupos->hasMorePages() ? '' : 'disabled' }}>Siguiente ›</button>
            </div>
        </div>
    @endif
</div>
