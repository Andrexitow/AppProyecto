<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 16px; margin-bottom: 16px; }

    .si-topbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 14px; }
    .si-field { display: flex; flex-direction: column; gap: 4px; }
    .si-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .si-field input { border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 10px; font-size: 13px; color: #111827; background: #F9FAFB; outline: none; min-width: 200px; }
    .si-field input:focus { border-color: #1D4ED8; background: #fff; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 8px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    table.si-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.si-tbl thead th { padding: 8px 6px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; }
    table.si-tbl td { padding: 6px; vertical-align: middle; }
    table.si-tbl select, table.si-tbl input { width: 100%; border: 1px solid #D1D5DB; border-radius: 6px; padding: 6px 8px; font-size: 12.5px; background: #F9FAFB; box-sizing: border-box; }
    table.si-tbl select:focus, table.si-tbl input:focus { border-color: #1D4ED8; background: #fff; outline: none; }
    table.si-tbl input[type="number"] { text-align: right; }

    .si-totales { display: flex; gap: 24px; align-items: center; margin-top: 14px; padding: 12px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; }
    .si-totales.ok { background: #ECFDF5; color: #065F46; }
    .si-totales.bad { background: #FEF2F2; color: #991B1B; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    table.hist-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.hist-tbl thead { background: #F8FAFC; }
    table.hist-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; }
    table.hist-tbl td { padding: 9px 12px; border-top: 1px solid #F3F4F6; color: #374151; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .badge { display: inline-flex; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 30px; color: #6B7280; font-size: 13px; }
</style>

<div id="view-saldos-iniciales">
    <div class="sec-header">
        <div>
            <p class="sec-title">🚀 Saldos Iniciales</p>
            <p class="sec-subtitle">Carga el punto de partida contable del negocio (migración o apertura de año fiscal)</p>
        </div>
    </div>

    <div class="card">
        <div class="si-topbar">
            <div class="si-field">
                <label>Fecha de corte</label>
                <input type="date" id="si-fecha">
            </div>
            <div class="si-field" style="flex:1;min-width:220px;">
                <label>Descripción (opcional)</label>
                <input type="text" id="si-descripcion" placeholder="Ej: Saldos iniciales al cierre del año anterior">
            </div>
            <button class="btn-outline" onclick="agregarLineaSI()">＋ Añadir cuenta</button>
        </div>

        <div style="overflow-x:auto;">
            <table class="si-tbl">
                <thead><tr><th style="width:38%;">Cuenta</th><th style="width:20%;">Tercero (si aplica)</th><th style="width:18%;text-align:right;">Débito</th><th style="width:18%;text-align:right;">Crédito</th><th></th></tr></thead>
                <tbody id="si-lineas"></tbody>
            </table>
        </div>

        <div class="si-totales" id="si-totales">
            <span>Débitos: <span id="si-tot-debito">$ 0</span></span>
            <span>Créditos: <span id="si-tot-credito">$ 0</span></span>
            <span id="si-diferencia-label">Agregue cuentas para empezar</span>
        </div>

        <div style="margin-top:14px;display:flex;justify-content:flex-end;">
            <button class="btn-primary" id="btnGuardarSI" onclick="guardarSaldosIniciales()" disabled>💾 Contabilizar Saldos Iniciales</button>
        </div>
    </div>

    <p class="sec-subtitle" style="margin-bottom:8px;">Cargas anteriores</p>
    <div class="table-wrapper">
        <div id="si-historial"><div class="spinner-cell">Cargando historial…</div></div>
    </div>
</div>

<script>
    var SI = { cuentas: [], lineas: [] };

    function tokenSI() { return document.querySelector('meta[name="csrf-token"]')?.content; }
    function fmtMoneySI(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtFechaSI(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function notifSI(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo || 'success'); return; } window.alert(msg); }

    document.getElementById('si-fecha').value = new Date().toISOString().slice(0, 10);

    fetch('/saldos-iniciales/cuentas', { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); }).then(function (res) {
            SI.cuentas = res.data || [];
            agregarLineaSI();
            agregarLineaSI();
        });

    cargarHistorialSI();

    function cargarHistorialSI() {
        fetch('/saldos-iniciales/historial', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); }).then(function (res) {
                var filas = (res.data || []).map(function (c) {
                    var badge = c.estado === 'REGISTRADO' ? '<span class="badge badge-green">Vigente</span>' : '<span class="badge badge-red">Anulado</span>';
                    // c.numero ya incluye el prefijo (así lo guarda el motor de
                    // comprobantes) — concatenar c.prefijo de nuevo lo duplicaría.
                    return '<tr><td><span class="td-mono">' + c.numero + '</span></td><td>' + fmtFechaSI(c.fecha) + '</td>' +
                        '<td>' + (c.descripcion || '—') + '</td><td style="text-align:right;">' + fmtMoneySI(c.total_debito) + '</td><td>' + badge + '</td></tr>';
                }).join('') || '<tr><td colspan="5" style="text-align:center;color:#9CA3AF;padding:20px;">Aún no se ha cargado ningún saldo inicial.</td></tr>';

                document.getElementById('si-historial').innerHTML =
                    '<table class="hist-tbl"><thead><tr><th>Comprobante</th><th>Fecha</th><th>Descripción</th><th style="text-align:right;">Valor</th><th>Estado</th></tr></thead><tbody>' + filas + '</tbody></table>';
            });
    }

    window.agregarLineaSI = function () {
        SI.lineas.push({ cuenta_id: '', tercero_id: '', debito: 0, credito: 0 });
        renderLineasSI();
    };

    window.eliminarLineaSI = function (idx) { SI.lineas.splice(idx, 1); renderLineasSI(); };

    window.lineaCambiarSI = function (idx, campo, val) {
        SI.lineas[idx][campo] = val;
        if (campo === 'debito' && val > 0) SI.lineas[idx].credito = 0;
        if (campo === 'credito' && val > 0) SI.lineas[idx].debito = 0;
        renderLineasSI();
    };

    function renderLineasSI() {
        var tbody = document.getElementById('si-lineas');
        tbody.innerHTML = SI.lineas.map(function (l, idx) {
            var cuenta = SI.cuentas.find(function (c) { return String(c.id) === String(l.cuenta_id); });
            var optsCuenta = '<option value="">— Seleccione —</option>' + SI.cuentas.map(function (c) {
                return '<option value="' + c.id + '" ' + (String(c.id) === String(l.cuenta_id) ? 'selected' : '') + '>' + c.codigo + ' - ' + c.nombre + '</option>';
            }).join('');

            var mostrarTercero = cuenta && cuenta.requiere_tercero;

            var hintNaturaleza = cuenta ? '<div style="font-size:10px;color:#9CA3AF;margin-top:2px;">Naturaleza: ' + (cuenta.naturaleza === 'DEBITO' ? 'Débito' : 'Crédito') + '</div>' : '';

            return '<tr>' +
                '<td><select onchange="lineaCambiarSI(' + idx + ',\'cuenta_id\',this.value)">' + optsCuenta + '</select>' + hintNaturaleza + '</td>' +
                '<td>' + (mostrarTercero ? '<input type="text" placeholder="ID tercero (usar buscador de Terceros)" onchange="lineaCambiarSI(' + idx + ',\'tercero_id\',this.value)" value="' + (l.tercero_id || '') + '">' : '<span style="color:#D1D5DB;">—</span>') + '</td>' +
                '<td><input type="number" min="0" step="0.01" value="' + (l.debito || 0) + '" onchange="lineaCambiarSI(' + idx + ',\'debito\',+this.value)"></td>' +
                '<td><input type="number" min="0" step="0.01" value="' + (l.credito || 0) + '" onchange="lineaCambiarSI(' + idx + ',\'credito\',+this.value)"></td>' +
                '<td><button onclick="eliminarLineaSI(' + idx + ')" style="border:none;background:transparent;cursor:pointer;color:#DC2626;font-size:14px;">✕</button></td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="5" style="text-align:center;color:#9CA3AF;padding:14px;">Sin cuentas. Haga clic en "＋ Añadir cuenta".</td></tr>';

        calcularTotalesSI();
    }

    function calcularTotalesSI() {
        var d = SI.lineas.reduce(function (s, l) { return s + (+l.debito || 0); }, 0);
        var h = SI.lineas.reduce(function (s, l) { return s + (+l.credito || 0); }, 0);
        var dif = Math.round((d - h) * 100) / 100;
        var cuadrado = Math.abs(dif) < 0.01 && d > 0 && SI.lineas.length >= 2;

        document.getElementById('si-tot-debito').textContent = fmtMoneySI(d);
        document.getElementById('si-tot-credito').textContent = fmtMoneySI(h);
        var cont = document.getElementById('si-totales');
        cont.className = 'si-totales ' + (cuadrado ? 'ok' : 'bad');
        document.getElementById('si-diferencia-label').textContent = cuadrado ? '✓ Cuadrado, listo para contabilizar' : 'Diferencia: ' + fmtMoneySI(Math.abs(dif));
        document.getElementById('btnGuardarSI').disabled = !cuadrado;
    }

    window.guardarSaldosIniciales = function () {
        var btn = document.getElementById('btnGuardarSI');
        btn.disabled = true;

        var lineas = SI.lineas.filter(function (l) { return l.cuenta_id && ((+l.debito || 0) > 0 || (+l.credito || 0) > 0); })
            .map(function (l) { return { cuenta_id: l.cuenta_id, tercero_id: l.tercero_id || null, debito: +l.debito || 0, credito: +l.credito || 0 }; });

        fetch('/saldos-iniciales', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': tokenSI(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ fecha: document.getElementById('si-fecha').value, descripcion: document.getElementById('si-descripcion').value, lineas: lineas }),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) {
                    var msgs = res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : (res.d.message || 'No se pudo contabilizar.');
                    throw new Error(msgs);
                }
                notifSI(res.d.message, 'success');
                SI.lineas = [];
                agregarLineaSI(); agregarLineaSI();
                cargarHistorialSI();
            })
            .catch(function (e) { notifSI(e.message, 'error'); })
            .finally(function () { calcularTotalesSI(); });
    };
</script>
