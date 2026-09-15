<style>
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

    .ce-alerta {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 500;
        margin-bottom: 16px;
    }

    .ce-alerta.incompleta {
        background: #FFFBEB;
        color: #92400E;
        border: 1px solid #FDE68A;
    }

    .ce-alerta.completa {
        background: #ECFDF5;
        color: #065F46;
        border: 1px solid #A7F3D0;
    }

    .ce-card {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 20px;
        max-width: 720px;
    }

    .ce-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .ce-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ce-field.full {
        grid-column: 1 / -1;
    }

    .ce-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .ce-field input,
    .ce-field select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .ce-field input:focus,
    .ce-field select:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .ce-section-title {
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin: 4px 0 4px;
        padding-top: 14px;
        border-top: 1px solid #EAECF0;
    }

    .ce-section-title:first-of-type {
        border-top: none;
        padding-top: 0;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #1D4ED8;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 9px 16px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    .btn-primary:disabled {
        opacity: .6;
        cursor: default;
    }
</style>

<div id="view-configuracion-emisor">
    <div class="sec-header">
        <div>
            <p class="sec-title">🏢 Datos del emisor</p>
            <p class="sec-subtitle">Tu negocio como emisor de facturas — cualquier proveedor de facturación electrónica
                que contrates va a pedir estos datos</p>
        </div>
    </div>

    <div id="ce-alerta"></div>

    <div class="ce-card">
        <form id="formEmisor" onsubmit="return false;">
            <p class="ce-section-title">Identificación</p>
            <div class="ce-grid">
                <div class="ce-field full"><label>Razón social</label><input type="text" name="razon_social"
                        placeholder="Ej: Mi Restaurante S.A.S"></div>
                <div class="ce-field"><label>NIT</label><input type="text" name="nit"
                        placeholder="Sin DV, ej: 901456789"></div>
                <div class="ce-field"><label>DV</label><input type="text" name="dv" maxlength="1"
                        placeholder="1"></div>
                <div class="ce-field">
                    <label>Tipo de persona</label>
                    <select name="tipo_persona">
                        <option value="juridica">Jurídica</option>
                        <option value="natural">Natural</option>
                    </select>
                </div>
                <div class="ce-field full">
                    <label>Régimen tributario</label>
                    <select name="regimen_tributario">
                        <option value="">Selecciona…</option>
                        @foreach (\App\Models\Tercero::REGIMENES_TRIBUTARIOS as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="ce-section-title">Ubicación y contacto</p>
            <div class="ce-grid">
                <div class="ce-field full"><label>Dirección</label><input type="text" name="direccion"
                        placeholder="Calle 10 # 20-30"></div>
                <div class="ce-field"><label>Ciudad</label><input type="text" name="ciudad"
                        placeholder="Piedecuesta"></div>
                <div class="ce-field"><label>Departamento</label><input type="text" name="departamento"
                        placeholder="Santander"></div>
                <div class="ce-field"><label>Código postal</label><input type="text" name="codigo_postal"
                        placeholder="681001"></div>
                <div class="ce-field"><label>Teléfono</label><input type="text" name="telefono"
                        placeholder="300 000 0000"></div>
                <div class="ce-field"><label>Email</label><input type="email" name="email"
                        placeholder="facturacion@tunegocio.com"></div>
                <div class="ce-field"><label>Matrícula mercantil (opcional)</label><input type="text"
                        name="matricula_mercantil"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                <button class="btn-primary" id="ce-btn-guardar" onclick="guardarEmisor()">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function tokenCE() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    function notifCE(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo || 'success');
            return;
        }
        window.alert(msg);
    }

    function cargarEmisor() {
        fetch('/configuracion-emisor/datos', {
                headers: {
                    Accept: 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                var e = res.data || {};
                var form = document.getElementById('formEmisor');
                ['razon_social', 'nit', 'dv', 'tipo_persona', 'regimen_tributario', 'direccion', 'ciudad',
                    'departamento', 'codigo_postal', 'telefono', 'email', 'matricula_mercantil'
                ].forEach(function(campo) {
                    if (form[campo]) form[campo].value = e[campo] || (campo === 'tipo_persona' ?
                        'juridica' : '');
                });

                var alerta = document.getElementById('ce-alerta');
                alerta.className = 'ce-alerta ' + (res.completa ? 'completa' : 'incompleta');
                alerta.innerHTML = res.completa ?
                    '✅ Los datos del emisor están completos.' :
                    '⚠️ Completa razón social, NIT, DV, dirección y ciudad — son los mínimos que cualquier proveedor de facturación electrónica te va a pedir.';
            });
    }

    window.guardarEmisor = function() {
        var form = document.getElementById('formEmisor');
        var datos = Object.fromEntries(new FormData(form).entries());
        var btn = document.getElementById('ce-btn-guardar');
        btn.disabled = true;

        fetch('/configuracion-emisor', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': tokenCE(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify(datos),
            }).then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') :
                    res.d.message);
                notifCE(res.d.message, 'success');
                cargarEmisor();
            }).catch(function(e) {
                notifCE(e.message, 'error');
            }).finally(function() {
                btn.disabled = false;
            });
    };

    cargarEmisor();
</script>
