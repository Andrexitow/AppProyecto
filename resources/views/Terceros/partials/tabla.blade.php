<div class="table-scroll">
    <table class="tc-tbl">
        <thead>
            <tr>
                <th>Nombre / Razón Social</th>
                <th style="text-align:center;">Tipo</th>
                <th>Documento</th>
                <th>Contacto</th>
                <th style="text-align:center;">Estado</th>
                <th style="text-align:right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($terceros as $t)
                @php
                    $nombreCompleto = $t->tipo === 'persona' ? trim($t->nombre . ' ' . $t->apellido) : $t->razon_social;
                    $documento = $t->tipo === 'persona' ? $t->cedula : $t->nit;
                    $inicial = strtoupper(substr($t->tipo === 'persona' ? $t->nombre : $t->razon_social, 0, 1));
                @endphp
                <tr class="{{ $t->estado ? '' : 'inactivo' }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="tc-avatar">{{ $inicial }}</span>
                            <div>
                                <div style="font-weight:500;color:#111827;">{{ $nombreCompleto ?: '—' }}</div>
                                <div style="font-size:11px;color:#9CA3AF;">{{ $t->email ?: 'Sin correo' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        @if ($t->tipo === 'persona')
                            <span class="badge badge-purple">Persona</span>
                        @else
                            <span class="badge badge-blue">Empresa</span>
                        @endif
                    </td>
                    <td><span class="td-mono">{{ $documento ?: '—' }}</span></td>
                    <td>{{ $t->celular ?: '—' }}</td>
                    <td style="text-align:center;">
                        @if ($t->estado)
                            <span class="badge badge-green"><span class="dot"></span>Activo</span>
                        @else
                            <span class="badge badge-red"><span class="dot"></span>Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="tbl-actions">
                            <button title="Ver detalle" class="act-btn view" onclick="verTercero({{ $t->id }})">👁️</button>
                            <button title="Editar" class="act-btn edit" onclick="editarTercero({{ $t->id }})">✏️</button>
                            <button title="{{ $t->estado ? 'Desactivar' : 'Activar' }}" class="act-btn state" onclick="cambiarEstadoTercero({{ $t->id }})">{{ $t->estado ? '🚫' : '✅' }}</button>
                            <button title="Eliminar" class="act-btn del" onclick="eliminarTercero({{ $t->id }})">🗑️</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"><div class="spinner-cell">📭 No hay terceros registrados</div></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
