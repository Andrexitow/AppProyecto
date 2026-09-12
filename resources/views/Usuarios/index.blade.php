<style>
    /* ═══════════════════════════════════════════════
       DISEÑO COMPARTIDO — mismo lenguaje visual que Facturas
    ═══════════════════════════════════════════════ */
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        letter-spacing: -0.3px;
    }

    .sec-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Métricas resumen ── */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--accent, #1D4ED8);
    }

    .metric-label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
        letter-spacing: -0.5px;
    }

    .metric-sub {
        font-size: 11px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Barra de filtros ── */
    .filter-bar {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
    }

    .filter-bar .fi-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 160px;
    }

    .fi-label {
        font-size: 12px;
        color: #6B7280;
        white-space: nowrap;
    }

    .fi-input {
        flex: 1;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        transition: border 0.15s;
    }

    .fi-input:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

    .fi-select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        cursor: pointer;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #1D4ED8;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 8px 16px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }

    .btn-primary:hover { background: #1e40af; }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff;
        color: #374151;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        white-space: nowrap;
    }

    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }

    .btn-link {
        background: transparent;
        border: none;
        color: #1D4ED8;
        font-weight: 600;
        font-size: 12.5px;
        cursor: pointer;
    }

    .btn-link:hover { text-decoration: underline; text-underline-offset: 2px; }

    /* ── Tabla ── */
    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll { overflow-x: auto; }

    table.data-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.data-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.data-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }

    table.data-tbl thead th:hover { color: #1D4ED8; }

    table.data-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.data-tbl thead th.sorted .sort-icon { opacity: 1; color: #1D4ED8; }

    table.data-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.data-tbl tbody tr:last-child { border-bottom: none; }
    table.data-tbl tbody tr:hover { background: #F8FAFC; }

    table.data-tbl tbody tr.inactivo { opacity: 0.55; }

    table.data-tbl td {
        padding: 9px 12px;
        color: #374151;
        vertical-align: middle;
    }

    /* ── Usuario / avatar ── */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #1D4ED8;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
    }

    .user-name { font-weight: 600; color: #111827; }
    .user-login { font-size: 11px; color: #9CA3AF; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-yellow { background: #FFFBEB; color: #92400E; }
    .badge-blue { background: #EFF6FF; color: #1e3a8a; }
    .badge-gray { background: #F3F4F6; color: #374151; }
    .badge-purple { background: #EDE9FE; color: #4C1D95; }

    .dot { width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: currentColor; }

    /* ── Acciones ── */
    .tbl-actions { display: flex; align-items: center; justify-content: center; gap: 4px; }

    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.15s;
        background: transparent;
        color: #6B7280;
        position: relative;
    }

    .act-btn:hover { background: #F3F4F6; color: #111827; transform: scale(1.05); }
    .act-btn.edit:hover { background: #FFFBEB; color: #D97706; }
    .act-btn.del:hover { background: #FEF2F2; color: #DC2626; }

    .act-btn::after {
        content: attr(data-tip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: #1F2937;
        color: #fff;
        font-size: 11px;
        padding: 3px 7px;
        border-radius: 5px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s;
        z-index: 50;
    }

    .act-btn:hover::after { opacity: 1; }

    .empty-row { text-align: center; color: #9CA3AF; padding: 30px; font-size: 12.5px; }

    /* ── Tarjetas de roles ── */
    .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 12px;
    }

    .role-card {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.15s;
    }

    .role-card:hover { border-color: #C7D2FE; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

    .role-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .role-card-title { font-size: 14px; font-weight: 700; color: #111827; }
    .role-card-desc { font-size: 11.5px; color: #9CA3AF; margin-top: 2px; }

    .role-perms-label {
        font-size: 10.5px;
        color: #9CA3AF;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .role-perms { display: flex; flex-wrap: wrap; gap: 5px; }

    .chip-perm {
        font-size: 11px;
        background: #ECFDF5;
        color: #065F46;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 500;
    }

    .chip-more { font-size: 11px; background: #F3F4F6; color: #6B7280; padding: 2px 8px; border-radius: 6px; font-weight: 500; }

    /* ── Modal ── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-panel {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        animation: popIn 0.18s ease-out;
    }

    @keyframes popIn {
        0% { opacity: 0; transform: scale(0.96); }
        100% { opacity: 1; transform: scale(1); }
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        color: #fff;
        flex-shrink: 0;
    }

    .modal-head.tone-blue { background: #1D4ED8; }
    .modal-head.tone-violet { background: #7C3AED; }

    .modal-head-title { font-size: 15px; font-weight: 600; }
    .modal-head-sub { font-size: 12px; opacity: 0.85; margin-top: 2px; }

    .modal-close {
        border: none;
        background: rgba(255,255,255,0.15);
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        transition: background 0.15s;
    }

    .modal-close:hover { background: rgba(255,255,255,0.25); }

    .modal-body { flex: 1; overflow-y: auto; padding: 20px; }

    .modal-foot {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    @media (max-width: 480px) { .field-grid { grid-template-columns: 1fr; } }

    .field { display: flex; flex-direction: column; gap: 3px; }
    .field.full { grid-column: 1/-1; }

    .field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .field input, .field select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border 0.15s;
        font-family: inherit;
    }

    .field input:focus, .field select:focus { border-color: #1D4ED8; background: #fff; }
    .field-hint { font-size: 11px; color: #9CA3AF; margin-top: 2px; }

    .perm-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        background: #F8FAFC;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 14px;
        max-height: 220px;
        overflow-y: auto;
    }

    @media (max-width: 480px) { .perm-list { grid-template-columns: 1fr; } }

    .perm-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 8px 10px;
        cursor: pointer;
        transition: border 0.15s;
    }

    .perm-item:hover { border-color: #C4B5FD; }
    .perm-item input { width: 15px; height: 15px; accent-color: #7C3AED; }
    .perm-item .perm-name { font-size: 12.5px; font-weight: 600; color: #374151; }
    .perm-item .perm-slug { font-size: 10px; color: #9CA3AF; text-transform: uppercase; }
</style>

<div id="view-usuarios">

    {{-- ═══════════════ USUARIOS ═══════════════ --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">👥 Usuarios del sistema</p>
            <p class="sec-subtitle">Controla quién puede acceder y qué funciones tienen permitidas.</p>
        </div>
        <button class="btn-primary" onclick="openModalUsuario()">+ Nuevo usuario</button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total usuarios</p>
            <p class="metric-value">{{ $usuarios->count() }}</p>
            <p class="metric-sub">Cuentas registradas</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Activos</p>
            <p class="metric-value">{{ $usuarios->where('activo', true)->count() }}</p>
            <p class="metric-sub">Con acceso habilitado</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivos</p>
            <p class="metric-value">{{ $usuarios->where('activo', false)->count() }}</p>
            <p class="metric-sub">Acceso deshabilitado</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Roles definidos</p>
            <p class="metric-value">{{ $roles->count() }}</p>
            <p class="metric-sub">Perfiles configurados</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="fu-buscar" placeholder="Nombre o usuario…" oninput="filtrarUsuarios()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fu-rol" onchange="filtrarUsuarios()">
                <option value="">Todos los roles</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->nombre }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fu-estado" onchange="filtrarUsuarios()">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosUsuarios()">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="data-tbl" id="tbl-usuarios">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol asignado</th>
                        <th>Caja</th>
                        <th style="text-align:center;">Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-usuarios">
                    @forelse ($usuarios as $u)
                        <tr class="{{ $u->activo ? '' : 'inactivo' }}"
                            data-nombre="{{ strtolower($u->name . ' ' . $u->username) }}"
                            data-rol="{{ $u->rol->nombre ?? '' }}"
                            data-estado="{{ $u->activo ? '1' : '0' }}">
                            <td>
                                <div class="user-cell">
                                    <div class="avatar-circle">{{ substr($u->name, 0, 1) }}</div>
                                    <div>
                                        <p class="user-name">{{ $u->name }}</p>
                                        <p class="user-login">{{ $u->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($u->rol && $u->rol->nombre == 'Administrador')
                                    <span class="badge badge-purple">{{ $u->rol->nombre }}</span>
                                @else
                                    <span class="badge badge-blue">{{ $u->rol->nombre ?? 'Sin rol' }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($u->caja)
                                    <span class="badge badge-gray">🏧 {{ $u->caja->nombre }}</span>
                                @else
                                    <span style="color:#D1D5DB;font-size:12px;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if ($u->activo)
                                    <span class="badge badge-green"><span class="dot"></span>Activo</span>
                                @else
                                    <span class="badge badge-gray"><span class="dot"></span>Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="tbl-actions">
                                    <button class="act-btn edit" data-tip="Editar" onclick="editarUsuario({{ $u->id }})">✏️</button>
                                    <button class="act-btn del" data-tip="Eliminar" onclick="eliminarUsuario({{ $u->id }})">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-row">📭 No hay usuarios registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════ ROLES Y PERMISOS ═══════════════ --}}
    <div style="margin-top:32px;padding-top:24px;border-top:1px solid #EAECF0;">
        <div class="sec-header">
            <div>
                <p class="sec-title">🛡️ Roles y permisos</p>
                <p class="sec-subtitle">Define perfiles (Admin, Vendedor, etc.) y qué pantallas pueden ver.</p>
            </div>
            <button class="btn-link" onclick="openModalRol()">+ Crear nuevo rol</button>
        </div>

        <div class="roles-grid">
            @foreach ($roles as $rol)
                <div class="role-card">
                    <div class="role-card-head">
                        <div>
                            <p class="role-card-title">{{ $rol->nombre }}</p>
                            @if(!empty($rol->descripcion))
                                <p class="role-card-desc">{{ $rol->descripcion }}</p>
                            @endif
                        </div>
                        <button class="btn-outline" style="padding:5px 10px;font-size:11px;" onclick="editarRol({{ $rol->id }})">Configurar</button>
                    </div>
                    <p class="role-perms-label">Permisos activos</p>
                    <div class="role-perms">
                        @foreach ($rol->permisos->take(3) as $permiso)
                            <span class="chip-perm">{{ $permiso->name }}</span>
                        @endforeach
                        @if ($rol->permisos->count() > 3)
                            <span class="chip-more">+{{ $rol->permisos->count() - 3 }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══════════════ MODAL USUARIO ═══════════════ --}}
<div id="modalUsuario" class="modal-backdrop" style="display:none;" onclick="cerrarModalUsuarioBackdrop(event)">
    <div class="modal-panel">
        <div class="modal-head tone-blue">
            <div>
                <p class="modal-head-title" id="mu-title">Nuevo acceso</p>
                <p class="modal-head-sub">Configuración de credenciales</p>
            </div>
            <button class="modal-close" onclick="closeModalUsuario()">✕</button>
        </div>

        <form id="formUsuario" class="modal-body">
            @csrf
            <div class="field full" style="margin-bottom:14px;">
                <label>Nombre del personal</label>
                <input type="text" name="name" placeholder="Ej: Juan Pérez">
            </div>

            <div class="field-grid">
                <div class="field">
                    <label>Usuario (login)</label>
                    <input type="text" name="username" placeholder="Ej: mesero_norte">
                </div>
                <div class="field">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••">
                </div>
            </div>

            <div class="field full" style="margin-bottom:14px;">
                <label>Clave para anulaciones</label>
                <input type="password" name="clave_anulacion" placeholder="Mínimo 4 caracteres">
                <p class="field-hint">Déjala vacía al editar para conservar la clave actual.</p>
            </div>

            <div class="field-grid">
                <div class="field">
                    <label>Rol asignado</label>
                    <select name="rol_id">
                        <option value="" disabled selected>Selecciona un perfil…</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Caja asignada</label>
                    <select name="caja_id">
                        <option value="">Sin caja asignada</option>
                        @foreach ($cajas as $caja)
                            <option value="{{ $caja->id }}">{{ $caja->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="modal-foot">
            <button class="btn-outline" onclick="closeModalUsuario()">Cancelar</button>
            <button class="btn-primary" id="mu-btn-save" onclick="guardarUsuario()">💾 Crear acceso</button>
        </div>
    </div>
</div>

{{-- ═══════════════ MODAL ROL ═══════════════ --}}
<div id="modalRol" class="modal-backdrop" style="display:none;" onclick="cerrarModalRolBackdrop(event)">
    <div class="modal-panel" style="max-width:680px;">
        <div class="modal-head tone-violet">
            <div>
                <p class="modal-head-title" id="mr-title">Gestión de roles</p>
                <p class="modal-head-sub">Definir jerarquías y accesos</p>
            </div>
            <button class="modal-close" onclick="closeModalRol()">✕</button>
        </div>

        <form id="formRol" class="modal-body">
            @csrf
            <div class="field-grid">
                <div class="field">
                    <label>Nombre del rol</label>
                    <input type="text" name="nombre" placeholder="Ej: Mesero">
                </div>
                <div class="field">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" placeholder="Ej: Solo pedidos y ventas">
                </div>
            </div>

            <p class="role-perms-label" style="margin-bottom:8px;">Permisos disponibles</p>
            <div class="perm-list">
                @foreach ($permisos as $permiso)
                    <label class="perm-item">
                        <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}">
                        <div>
                            <p class="perm-name">{{ $permiso->nombre }}</p>
                            <p class="perm-slug">{{ $permiso->slug }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </form>

        <div class="modal-foot">
            <button class="btn-outline" onclick="closeModalRol()">Cancelar</button>
            <button class="btn-primary" style="background:#7C3AED;" onmouseover="this.style.background='#6D28D9'" onmouseout="this.style.background='#7C3AED'" onclick="guardarRol()">Guardar configuración</button>
        </div>
    </div>
</div>

<script>
    /* ════════════════════════════════════════════════
       FILTRO DE USUARIOS (client-side, sobre filas ya renderizadas)
    ════════════════════════════════════════════════ */
    function filtrarUsuarios() {
        var buscar = (document.getElementById('fu-buscar').value || '').toLowerCase().trim();
        var rol = document.getElementById('fu-rol').value;
        var estado = document.getElementById('fu-estado').value;

        document.querySelectorAll('#tbody-usuarios tr[data-nombre]').forEach(function(tr) {
            var okBuscar = !buscar || tr.dataset.nombre.includes(buscar);
            var okRol = !rol || tr.dataset.rol === rol;
            var okEstado = !estado || tr.dataset.estado === estado;
            tr.style.display = (okBuscar && okRol && okEstado) ? '' : 'none';
        });
    }

    function limpiarFiltrosUsuarios() {
        document.getElementById('fu-buscar').value = '';
        document.getElementById('fu-rol').value = '';
        document.getElementById('fu-estado').value = '';
        filtrarUsuarios();
    }

    /* ════════════════════════════════════════════════
       MODAL USUARIO
    ════════════════════════════════════════════════ */
    function openModalUsuario() {
        document.getElementById('formUsuario').reset();
        document.getElementById('formUsuario').dataset.editId = '';
        document.getElementById('mu-title').textContent = 'Nuevo acceso';
        document.getElementById('mu-btn-save').textContent = '💾 Crear acceso';
        document.getElementById('modalUsuario').style.display = 'flex';
    }

    function closeModalUsuario() {
        document.getElementById('modalUsuario').style.display = 'none';
    }

    function cerrarModalUsuarioBackdrop(e) {
        if (e.target === document.getElementById('modalUsuario')) closeModalUsuario();
    }

    function editarUsuario(id) {
        document.getElementById('mu-title').textContent = 'Editar usuario';
        document.getElementById('mu-btn-save').textContent = '💾 Guardar cambios';
        document.getElementById('formUsuario').dataset.editId = id;
        document.getElementById('modalUsuario').style.display = 'flex';
        // TODO: precargar datos del usuario vía fetch('/usuarios/' + id) si aplica
    }

    function guardarUsuario() {
        var form = document.getElementById('formUsuario');
        var editId = form.dataset.editId;
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var fd = new FormData(form);
        var body = Object.fromEntries(fd.entries());

        var url = editId ? '/usuarios/' + editId : '/usuarios';
        var method = editId ? 'PUT' : 'POST';

        fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
            .then(function(res) {
                if (!res.ok) throw new Error(res.data.message || 'No se pudo guardar el usuario');
                closeModalUsuario();
                location.reload();
            })
            .catch(function(e) { alert(e.message); });
    }

    function eliminarUsuario(id) {
        if (!confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')) return;
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/usuarios/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
            .then(function(res) {
                if (!res.ok) throw new Error(res.data.message || 'No se pudo eliminar el usuario');
                location.reload();
            })
            .catch(function(e) { alert(e.message); });
    }

    /* ════════════════════════════════════════════════
       MODAL ROL
    ════════════════════════════════════════════════ */
    function openModalRol() {
        document.getElementById('formRol').reset();
        document.getElementById('formRol').dataset.editId = '';
        document.getElementById('mr-title').textContent = 'Gestión de roles';
        document.getElementById('modalRol').style.display = 'flex';
    }

    function closeModalRol() {
        document.getElementById('modalRol').style.display = 'none';
    }

    function cerrarModalRolBackdrop(e) {
        if (e.target === document.getElementById('modalRol')) closeModalRol();
    }

    function editarRol(id) {
        document.getElementById('mr-title').textContent = 'Editar rol';
        document.getElementById('formRol').dataset.editId = id;
        document.getElementById('modalRol').style.display = 'flex';
        // TODO: precargar datos del rol vía fetch('/roles/' + id) si aplica
    }

    function guardarRol() {
        var form = document.getElementById('formRol');
        var editId = form.dataset.editId;
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var fd = new FormData(form);

        var url = editId ? '/roles/' + editId : '/roles';

        fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: fd,
            })
            .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
            .then(function(res) {
                if (!res.ok) throw new Error(res.data.message || 'No se pudo guardar el rol');
                closeModalRol();
                location.reload();
            })
            .catch(function(e) { alert(e.message); });
    }
</script>