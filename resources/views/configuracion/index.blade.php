<style>
    .co-wrap {
        max-width: 980px;
    }

    .co-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .co-header-icon {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 20px;
        background: #EFF6FF;
    }

    .co-header h1 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        letter-spacing: -.3px;
    }

    .co-header p {
        font-size: 12.5px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Franja de estado a simple vista ── */
    .co-resumen {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        margin-bottom: 22px;
    }

    .co-resumen-item {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 11px;
        padding: 12px 14px;
    }

    .co-resumen-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
        background: #D1D5DB;
    }

    .co-resumen-dot.on {
        background: #16A34A;
        box-shadow: 0 0 0 3px #DCFCE7;
    }

    .co-resumen-dot.off {
        background: #D1D5DB;
    }

    .co-resumen-label {
        font-size: 10.5px;
        font-weight: 600;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .co-resumen-valor {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        margin-top: 1px;
    }

    /* ── Tarjetas ── */
    .co-card {
        position: relative;
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 14px;
        padding: 22px 24px 24px;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .co-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--co-accent, #1D4ED8);
    }

    .co-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .co-card-head-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .co-card-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
        background: var(--co-accent-bg, #EFF6FF);
    }

    .co-card-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #111827;
    }

    .co-card-desc {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    .co-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
    }

    .co-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .co-field.full {
        grid-column: 1 / -1;
    }

    .co-field label {
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: .3px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .co-field input {
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 13px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: border-color .12s, background .12s, box-shadow .12s;
    }

    .co-field input:focus {
        border-color: #1D4ED8;
        background: #fff;
        box-shadow: 0 0 0 3px #DBEAFE;
    }

    .co-field input:disabled {
        color: #9CA3AF;
        cursor: not-allowed;
    }

    .co-field-hint {
        font-size: 11.5px;
        color: #9CA3AF;
        margin-top: 10px;
    }

    .co-subsection {
        margin: 18px 0 14px;
        padding-top: 16px;
        border-top: 1px dashed #E5E7EB;
    }

    .co-subsection-title {
        font-size: 12.5px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .co-subsection-desc {
        font-size: 12px;
        color: #6B7280;
        line-height: 1.5;
        max-width: 640px;
    }

    .co-suffix-input {
        position: relative;
    }

    .co-suffix-input input {
        padding-right: 42px;
    }

    .co-suffix-input span {
        position: absolute;
        right: 11px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 11.5px;
        color: #9CA3AF;
        font-weight: 600;
        pointer-events: none;
    }

    .co-secret-row {
        position: relative;
    }

    .co-secret-row input {
        padding-right: 38px;
    }

    .co-eye {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        cursor: pointer;
        font-size: 14px;
        padding: 4px 6px;
        line-height: 1;
        color: #9CA3AF;
    }

    .co-eye:hover {
        color: #374151;
    }

    /* ── Toggle ── */
    .co-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        background: #F9FAFB;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .co-toggle-row p {
        font-size: 13.5px;
        font-weight: 600;
        color: #111827;
    }

    .co-toggle-row span {
        font-size: 12px;
        color: #6B7280;
        display: block;
        margin-top: 1px;
    }

    .co-switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 24px;
        flex-shrink: 0;
    }

    .co-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .co-switch-track {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #D1D5DB;
        border-radius: 999px;
        transition: .15s;
    }

    .co-switch-track::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .15s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
    }

    .co-switch input:checked+.co-switch-track {
        background: #16A34A;
    }

    .co-switch input:checked+.co-switch-track::before {
        transform: translateX(18px);
    }

    /* Cuando la facturación electrónica está apagada, las credenciales se
       ven disponibles pero visualmente secundarias — siguen editables por
       si el admin quiere dejarlas listas antes de encender el interruptor. */
    .co-grid.co-atenuado {
        opacity: .55;
    }

    .co-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .co-badge.si {
        background: #ECFDF5;
        color: #065F46;
    }

    .co-badge.no {
        background: #F3F4F6;
        color: #6B7280;
    }

    .co-token-box {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 14px;
        font-size: 12.5px;
        color: #92400E;
        display: none;
    }

    .co-token-box.mostrar {
        display: block;
        animation: coFadeIn .15s ease-out;
    }

    @keyframes coFadeIn {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .co-token-valor {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }

    .co-token-valor code {
        flex: 1;
        background: #fff;
        border: 1px solid #FDE68A;
        border-radius: 7px;
        padding: 7px 9px;
        font-size: 11.5px;
        word-break: break-all;
    }

    .co-acciones-agente {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #1D4ED8;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .12s;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    .btn-primary:disabled {
        opacity: .6;
        cursor: default;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: #374151;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .12s, border-color .12s;
    }

    .btn-outline:hover {
        background: #F9FAFB;
    }

    .btn-outline.warn {
        color: #92400E;
        border-color: #FDE68A;
        background: #FFFBEB;
    }

    .btn-outline.warn:hover {
        background: #FEF3C7;
    }

    /* ── Barra de guardar, fija al fondo de la tarjeta contenedora ── */
    .co-savebar {
        position: sticky;
        bottom: 0;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        background: rgba(249, 250, 251, .92);
        backdrop-filter: blur(4px);
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 14px 16px;
        max-width: 980px;
    }

    .co-savebar span {
        font-size: 11.5px;
        color: #9CA3AF;
        margin-right: auto;
    }

    @media (max-width: 640px) {
        .co-resumen {
            grid-template-columns: 1fr;
        }

        .co-card {
            padding: 18px 16px 20px;
        }
    }
</style>

<div id="view-configuracion" class="co-wrap">
    <div class="co-header">
        <div class="co-header-icon">⚙️</div>
        <div>
            <h1>Configuración</h1>
            <p>Ajustes generales del sistema — antes solo se podían cambiar editando el .env en el servidor</p>
        </div>
    </div>

    <div class="co-resumen">
        <div class="co-resumen-item">
            <span class="co-resumen-dot" id="co-resumen-fe-dot"></span>
            <div>
                <p class="co-resumen-label">Facturación electrónica</p>
                <p class="co-resumen-valor" id="co-resumen-fe">—</p>
            </div>
        </div>
        <div class="co-resumen-item">
            <span class="co-resumen-dot on"></span>
            <div>
                <p class="co-resumen-label">Cierre por inactividad</p>
                <p class="co-resumen-valor" id="co-resumen-sesion">—</p>
            </div>
        </div>
        <div class="co-resumen-item">
            <span class="co-resumen-dot" id="co-resumen-turno-dot"></span>
            <div>
                <p class="co-resumen-label">Duración máxima de turno</p>
                <p class="co-resumen-valor" id="co-resumen-turno">—</p>
            </div>
        </div>
        <div class="co-resumen-item">
            <span class="co-resumen-dot" id="co-resumen-agente-dot"></span>
            <div>
                <p class="co-resumen-label">Agente de impresión</p>
                <p class="co-resumen-valor" id="co-resumen-agente">—</p>
            </div>
        </div>
    </div>

    <form id="formConfiguracion" onsubmit="return false;" autocomplete="off">

        {{-- ═══════ OPERACIÓN ═══════ --}}
        <div class="co-card" style="--co-accent:#1D4ED8;--co-accent-bg:#EFF6FF;">
            <div class="co-card-head">
                <div class="co-card-head-left">
                    <div class="co-card-icon">🕓</div>
                    <div>
                        <p class="co-card-title">Operación</p>
                        <p class="co-card-desc">Horarios y cierre de sesión automático</p>
                    </div>
                </div>
            </div>

            <div class="co-grid">
                <div class="co-field">
                    <label>Hora de corte operativo</label>
                    <input type="time" name="hora_corte_operativo" autocomplete="off">
                    <p class="co-field-hint">El "día operativo" del Dashboard empieza a esta hora.</p>
                </div>
                <div class="co-field">
                    <label>Inactividad — Mesero / Cajero / Cocina</label>
                    <div class="co-suffix-input">
                        <input type="number" name="inactividad_operativos_minutos" min="1" max="1440"
                            autocomplete="off">
                        <span>min</span>
                    </div>
                    <p class="co-field-hint">Minutos sin actividad antes de cerrar la sesión.</p>
                </div>
                <div class="co-field">
                    <label>Inactividad — Administrador / Contabilidad</label>
                    <div class="co-suffix-input">
                        <input type="number" name="inactividad_admin_minutos" min="1" max="1440"
                            autocomplete="off">
                        <span>min</span>
                    </div>
                    <p class="co-field-hint">Suele ser más largo, para no interrumpir trabajo contable.</p>
                </div>
            </div>

            <div class="co-subsection">
                <p class="co-subsection-title">Duración máxima de sesión</p>
                <p class="co-subsection-desc">Distinto a la inactividad: cierra la sesión al cumplirse el turno
                    <strong>aunque el usuario siga activo</strong> — pensado para que un mesero o cajero no quede
                    conectado indefinidamente solo porque la pantalla se autorefresca sola. Vuelve a iniciar sesión
                    sin problema si el turno sigue; esto no toca su contraseña ni desactiva la cuenta.
                </p>
            </div>

            <div class="co-grid">
                <div class="co-field">
                    <label>Turno máximo — Mesero / Cajero / Cocina</label>
                    <div class="co-suffix-input">
                        <input type="number" name="sesion_maxima_operativos_horas" min="0" max="72" step="0.5"
                            autocomplete="off">
                        <span>h</span>
                    </div>
                    <p class="co-field-hint">0 = sin límite. 8 h cubre un turno normal.</p>
                </div>
                <div class="co-field">
                    <label>Turno máximo — Administrador / Contabilidad</label>
                    <div class="co-suffix-input">
                        <input type="number" name="sesion_maxima_admin_horas" min="0" max="72" step="0.5"
                            autocomplete="off">
                        <span>h</span>
                    </div>
                    <p class="co-field-hint">0 = sin límite (por defecto, no trabajan por turnos fijos).</p>
                </div>
            </div>
        </div>

        {{-- ═══════ FACTURACIÓN ELECTRÓNICA ═══════ --}}
        <div class="co-card" style="--co-accent:#7C3AED;--co-accent-bg:#F5F3FF;">
            <div class="co-card-head">
                <div class="co-card-head-left">
                    <div class="co-card-icon">🧾</div>
                    <div>
                        <p class="co-card-title">Facturación electrónica (DIAN)</p>
                        <p class="co-card-desc">Transmisión vía Factus — mientras esté apagada, el sistema funciona
                            exactamente igual que hoy</p>
                    </div>
                </div>
            </div>

            <div class="co-toggle-row">
                <div>
                    <p>Habilitar transmisión electrónica</p>
                    <span>Con esto apagado, ninguna factura se encola para envío a la DIAN.</span>
                </div>
                <label class="co-switch">
                    <input type="checkbox" name="factura_electronica_habilitada" id="co-fe-toggle"
                        onchange="document.getElementById('co-fe-grid').classList.toggle('co-atenuado', !this.checked)">
                    <span class="co-switch-track"></span>
                </label>
            </div>

            <div class="co-grid" id="co-fe-grid">
                <div class="co-field">
                    <label>URL de la API</label>
                    <input type="text" name="factus_url" placeholder="https://api-sandbox.factus.com.co"
                        autocomplete="off">
                </div>
                <div class="co-field">
                    <label>Client ID</label>
                    <input type="text" name="factus_client_id" autocomplete="off">
                </div>
                <div class="co-field">
                    <label>Client Secret <span id="co-badge-secret"></span></label>
                    <div class="co-secret-row">
                        <input type="password" name="factus_client_secret" autocomplete="new-password"
                            id="co-input-secret">
                        <button type="button" class="co-eye" onclick="coAlternarClave('co-input-secret', this)"
                            tabindex="-1">👁</button>
                    </div>
                </div>
                <div class="co-field">
                    <label>Usuario</label>
                    <input type="text" name="factus_username" autocomplete="off">
                </div>
                <div class="co-field">
                    <label>Contraseña <span id="co-badge-password"></span></label>
                    <div class="co-secret-row">
                        <input type="password" name="factus_password" autocomplete="new-password"
                            id="co-input-password">
                        <button type="button" class="co-eye" onclick="coAlternarClave('co-input-password', this)"
                            tabindex="-1">👁</button>
                    </div>
                </div>
            </div>
            <p class="co-field-hint">Deja un campo vacío para conservar el valor que ya está guardado.</p>
        </div>

        {{-- ═══════ AGENTE DE IMPRESIÓN ═══════ --}}
        <div class="co-card" style="--co-accent:#0D9488;--co-accent-bg:#ECFDF5;">
            <div class="co-card-head">
                <div class="co-card-head-left">
                    <div class="co-card-icon">🖨️</div>
                    <div>
                        <p class="co-card-title">Agente de impresión</p>
                        <p class="co-card-desc">Token que el programa de impresión de cada negocio usa para
                            conectarse aquí</p>
                    </div>
                </div>
                <span class="co-badge" id="co-badge-agente"></span>
            </div>

            <div class="co-field full">
                <label>Token actual</label>
                <input autocomplete="off" type="text" id="co-agente-token-mostrar" disabled placeholder="Sin generar">
            </div>

            <div class="co-acciones-agente" style="margin-top:14px;">
                <a href="/configuracion/descargar-agente" class="btn-outline" style="text-decoration:none;">⬇️
                    Descargar agente de impresión</a>
                <button type="button" class="btn-outline warn" onclick="regenerarTokenAgente()">🔄 Generar nuevo
                    token</button>
            </div>
            <p class="co-field-hint">Para montarlo en un negocio nuevo: descarga el .zip, sigue el LEEME.txt de
                adentro, y pega el token de arriba en su <code>.env</code>.</p>

            <div class="co-token-box" id="co-token-box">
                ⚠️ Copia este token ahora — no se va a volver a mostrar. Pégalo en el <code>.env</code> del agente de
                impresión de este negocio (variable <code>AGENTE_IMPRESION_TOKEN</code>).
                <div class="co-token-valor">
                    <code id="co-token-nuevo"></code>
                    <button type="button" class="btn-outline" onclick="copiarTokenAgente()">📋 Copiar</button>
                </div>
            </div>
        </div>

        <div class="co-savebar">
            <span>Los cambios aplican de inmediato al guardar.</span>
            <button class="btn-primary" id="co-btn-guardar" onclick="guardarConfiguracion()">💾 Guardar
                cambios</button>
        </div>
    </form>
</div>

<script>
    function tokenCO() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    function notifCO(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo || 'success');
            return;
        }
        window.alert(msg);
    }

    function badgeConfigurado(configurado) {
        return configurado ?
            '<span class="co-badge si">✓ configurado</span>' :
            '<span class="co-badge no">sin configurar</span>';
    }

    window.coAlternarClave = function(id, boton) {
        var input = document.getElementById(id);
        var esPassword = input.type === 'password';
        input.type = esPassword ? 'text' : 'password';
        boton.textContent = esPassword ? '🙈' : '👁';
    };

    function cargarConfiguracion() {
        fetch('/configuracion/datos', { headers: { Accept: 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                var d = res.data || {};
                var form = document.getElementById('formConfiguracion');

                form.hora_corte_operativo.value = d.hora_corte_operativo || '00:00';
                form.inactividad_operativos_minutos.value = d.inactividad_operativos_minutos || 15;
                form.inactividad_admin_minutos.value = d.inactividad_admin_minutos || 60;
                // ?? en vez de || : 0 es un valor válido acá ("sin límite"),
                // y con || quedaría atrapado como si fuera un valor vacío.
                form.sesion_maxima_operativos_horas.value = d.sesion_maxima_operativos_horas ?? 8;
                form.sesion_maxima_admin_horas.value = d.sesion_maxima_admin_horas ?? 0;

                var feHabilitada = !!d.factura_electronica_habilitada;
                var feToggle = document.getElementById('co-fe-toggle');
                feToggle.checked = feHabilitada;
                document.getElementById('co-fe-grid').classList.toggle('co-atenuado', !feHabilitada);

                form.factus_url.value = d.factus_url || '';
                form.factus_client_id.value = d.factus_client_id || '';
                form.factus_username.value = d.factus_username || '';

                form.factus_client_secret.value = '';
                form.factus_client_secret.placeholder = d.factus_client_secret_configurado ?
                    '•••••••• (dejar vacío conserva el actual)' : 'Sin configurar';
                document.getElementById('co-badge-secret').innerHTML = badgeConfigurado(d
                    .factus_client_secret_configurado);

                form.factus_password.value = '';
                form.factus_password.placeholder = d.factus_password_configurado ?
                    '•••••••• (dejar vacío conserva el actual)' : 'Sin configurar';
                document.getElementById('co-badge-password').innerHTML = badgeConfigurado(d
                    .factus_password_configurado);

                document.getElementById('co-badge-agente').outerHTML = '<span class="co-badge ' + (d
                        .agente_impresion_token_configurado ? 'si' : 'no') + '" id="co-badge-agente">' +
                    (d.agente_impresion_token_configurado ? '✓ configurado' : 'sin generar') + '</span>';
                document.getElementById('co-agente-token-mostrar').placeholder = d
                    .agente_impresion_token_configurado ?
                    '•••••••••••••••••••••••••••••••• (oculto por seguridad)' : 'Sin generar';

                // ── Franja de estado ──
                document.getElementById('co-resumen-fe').textContent = feHabilitada ? 'Encendida' : 'Apagada';
                document.getElementById('co-resumen-fe-dot').className = 'co-resumen-dot ' + (feHabilitada ?
                    'on' : 'off');

                document.getElementById('co-resumen-sesion').textContent =
                    d.inactividad_operativos_minutos + ' / ' + d.inactividad_admin_minutos + ' min';

                var turnoOperativos = d.sesion_maxima_operativos_horas > 0 ? d.sesion_maxima_operativos_horas +
                    'h' : 'sin límite';
                var turnoAdmin = d.sesion_maxima_admin_horas > 0 ? d.sesion_maxima_admin_horas + 'h' :
                    'sin límite';
                document.getElementById('co-resumen-turno').textContent = turnoOperativos + ' / ' + turnoAdmin;
                document.getElementById('co-resumen-turno-dot').className = 'co-resumen-dot ' + (d
                    .sesion_maxima_operativos_horas > 0 ? 'on' : 'off');

                var agenteOk = !!d.agente_impresion_token_configurado;
                document.getElementById('co-resumen-agente').textContent = agenteOk ? 'Configurado' :
                    'Sin generar';
                document.getElementById('co-resumen-agente-dot').className = 'co-resumen-dot ' + (agenteOk ?
                    'on' : 'off');
            })
            .catch(function(e) { notifCO('No se pudo cargar la configuración: ' + e.message, 'error'); });
    }

    window.guardarConfiguracion = function() {
        var form = document.getElementById('formConfiguracion');
        var datos = Object.fromEntries(new FormData(form).entries());
        datos.factura_electronica_habilitada = document.getElementById('co-fe-toggle').checked ? '1' : '0';

        var btn = document.getElementById('co-btn-guardar');
        btn.disabled = true;

        fetch('/configuracion', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': tokenCO(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify(datos),
            }).then(function(r) {
                return r.json().then(function(d) { return { ok: r.ok, d: d }; });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') :
                    res.d.message);
                notifCO(res.d.message, 'success');
                cargarConfiguracion();
            }).catch(function(e) {
                notifCO(e.message, 'error');
            }).finally(function() {
                btn.disabled = false;
            });
    };

    window.regenerarTokenAgente = function() {
        if (!confirm('¿Generar un token nuevo? El agente de impresión que use el token anterior dejará de poder conectarse hasta que lo actualices allá también.')) return;

        fetch('/configuracion/regenerar-token-agente', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': tokenCO(), Accept: 'application/json' }
            })
            .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, d: d }; }); })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.message || 'No se pudo generar el token');
                document.getElementById('co-token-nuevo').textContent = res.d.token;
                document.getElementById('co-token-box').classList.add('mostrar');
                cargarConfiguracion();
                notifCO('Token regenerado. Cópialo ahora.', 'success');
            })
            .catch(function(e) { notifCO(e.message, 'error'); });
    };

    window.copiarTokenAgente = function() {
        var texto = document.getElementById('co-token-nuevo').textContent;
        navigator.clipboard.writeText(texto).then(function() {
            notifCO('Token copiado al portapapeles', 'success');
        }).catch(function() {
            notifCO('No se pudo copiar automáticamente — selecciónalo a mano', 'error');
        });
    };

    cargarConfiguracion();
</script>
