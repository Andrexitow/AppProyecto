<div class="table-scroll">
    <table class="cons-tbl">
        <thead>
            <tr>
                <th>Prefijo</th>
                <th>Número</th>
                <th>Fecha</th>
                <th>Observación</th>
                <th>Estado</th>
                <th style="text-align:right;">Total</th>
                <th>Registrado por</th>
                <th>Fecha de registro</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($consumos as $consumo)
                @php
                    $prefijo = 'CONS';
                    $numero = $consumo->numero_factura;
                    if (str_contains($consumo->numero_factura, '-')) {
                        [$prefijo, $numero] = explode('-', $consumo->numero_factura, 2);
                    }
                    $pendiente = $consumo->estado === 'no_registrado';
                @endphp
                <tr>
                    <td><span class="badge badge-purple">{{ $prefijo }}</span></td>
                    <td><span class="td-mono">{{ $numero }}</span></td>
                    <td>{{ optional($consumo->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $consumo->observacion }}</td>
                    <td>
                        @if ($pendiente)
                            <span class="badge badge-amber">⚠️ No registrado</span>
                        @else
                            <span class="badge badge-green">✓ Registrado</span>
                        @endif
                    </td>
                    <td class="td-money" style="text-align:right;">${{ number_format($consumo->total, 0, ',', '.') }}</td>
                    <td>{{ $consumo->user->name ?? '—' }}</td>
                    <td>{{ $consumo->created_at->format('d/m/Y H:i') }}</td>
                    <td style="text-align:right;">
                        <button class="act-btn" title="Ver detalle" onclick="verConsumo({{ $consumo->id }})">{{ $pendiente ? '⚠️' : '👁️' }}</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="spinner-cell">📭 No hay consumos de materia prima registrados todavía.<br>
                        <span style="font-size:11.5px;">Se generan solos cuando se vende un producto marcado como "ensamblado".</span></div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($consumos instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;border-top:1px solid #F3F4F6;font-size:12px;color:#6B7280;flex-wrap:wrap;">
            <span>
                Mostrando {{ $consumos->firstItem() ?? 0 }}–{{ $consumos->lastItem() ?? 0 }}
                de {{ $consumos->total() }} consumo{{ $consumos->total() !== 1 ? 's' : '' }}
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" class="act-btn" style="width:auto;padding:4px 10px;" onclick="irAPaginaConsumo({{ $consumos->currentPage() - 1 }})" {{ $consumos->onFirstPage() ? 'disabled' : '' }}>‹ Anterior</button>
                <span>Página {{ $consumos->currentPage() }} de {{ $consumos->lastPage() }}</span>
                <button type="button" class="act-btn" style="width:auto;padding:4px 10px;" onclick="irAPaginaConsumo({{ $consumos->currentPage() + 1 }})" {{ $consumos->hasMorePages() ? '' : 'disabled' }}>Siguiente ›</button>
            </div>
        </div>
    @endif
</div>
