<style>
    .logs-page { padding:24px; color:#172033; } .logs-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px; } .logs-title { margin:0; font-size:22px; letter-spacing:-.6px; } .logs-sub { margin:4px 0 0; color:#667085; font-size:12px; }
    .logs-info { display:flex; align-items:center; gap:8px; padding:9px 12px; border:1px solid #b7d7f5; border-radius:8px; background:#eff8ff; color:#175cd3; font-size:11px; }
    .logs-filters { display:grid; grid-template-columns:1.5fr 1fr 1fr 1fr 1fr auto; gap:9px; margin-bottom:13px; padding:13px; border:1px solid #e4e7ec; border-radius:11px; background:#fff; } .logs-input,.logs-select { min-width:0; border:1px solid #d0d5dd; border-radius:7px; padding:8px 9px; color:#344054; font-size:12px; outline:0; background:#fff; } .logs-input:focus,.logs-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); } .logs-filter-btn { border:0; border-radius:7px; padding:8px 12px; background:#1d4ed8; color:#fff; font-size:12px; font-weight:700; cursor:pointer; }
    .logs-card { overflow:hidden; border:1px solid #e4e7ec; border-radius:11px; background:#fff; box-shadow:0 2px 8px rgba(16,24,40,.025); } .logs-table { width:100%; border-collapse:collapse; font-size:12px; } .logs-table th { padding:11px 14px; background:#f9fafb; color:#667085; font-size:10px; text-align:left; letter-spacing:.05em; text-transform:uppercase; } .logs-table td { padding:12px 14px; border-top:1px solid #eef0f3; vertical-align:top; color:#475467; } .logs-table tr:hover td { background:#fafcff; } .logs-table tbody.is-refreshing { animation:logsRefresh .38s ease both; } @keyframes logsRefresh { from { opacity:.35; transform:translateY(3px); } to { opacity:1; transform:translateY(0); } }
    .logs-user { color:#344054; font-weight:700; } .logs-role { margin-top:3px; color:#98a2b3; font-size:10px; } .logs-description { color:#344054; font-weight:600; } .logs-detail { margin-top:3px; color:#98a2b3; font-size:10px; } .logs-badge { display:inline-block; border-radius:99px; padding:4px 8px; background:#eff8ff; color:#175cd3; font-size:10px; font-weight:700; white-space:nowrap; } .logs-badge.danger { background:#fef3f2; color:#b42318; } .logs-badge.warning { background:#fffaeb; color:#b54708; } .logs-badge.success { background:#ecfdf3; color:#027a48; } .logs-time { color:#667085; font-size:11px; white-space:nowrap; } .logs-empty { padding:52px 20px; color:#98a2b3; text-align:center; font-size:13px; }
    .logs-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border-top:1px solid #eef0f3; color:#667085; font-size:11px; } .logs-pages { display:flex; gap:6px; } .logs-page-btn { border:1px solid #d0d5dd; border-radius:6px; padding:6px 9px; background:#fff; color:#344054; font-size:11px; font-weight:700; cursor:pointer; } .logs-page-btn:disabled { opacity:.45; cursor:not-allowed; }
    @media(max-width:900px) { .logs-filters{grid-template-columns:1fr 1fr 1fr}.logs-filter-btn{grid-column:span 1}.logs-table th:nth-child(4),.logs-table td:nth-child(4){display:none} } @media(max-width:560px) { .logs-page{padding:16px}.logs-head{flex-direction:column}.logs-filters{grid-template-columns:1fr 1fr}.logs-filters .logs-input:first-child{grid-column:1/-1}.logs-table th:nth-child(3),.logs-table td:nth-child(3){display:none}.logs-footer{align-items:flex-start;flex-direction:column} }
</style>

<div class="logs-page">
    <header class="logs-head">
        <div><h1 class="logs-title">Logs del sistema</h1><p class="logs-sub">Bitácora de operaciones y accesos realizados desde que se activó la auditoría.</p></div>
        <div class="logs-info">Solo administradores pueden consultar este historial. <span id="logs-actualizacion">Actualizado ahora</span></div>
    </header>
    <form class="logs-filters" id="logs-filtros">
        <input autocomplete="off" class="logs-input" id="logs-buscar" type="search" placeholder="Buscar por módulo, acción o referencia">
        <select class="logs-select" id="logs-usuario"><option value="">Todos los usuarios</option>@foreach($usuarios as $usuario)<option value="{{ $usuario->id }}">{{ $usuario->name }}</option>@endforeach</select>
        <select class="logs-select" id="logs-accion"><option value="">Todas las acciones</option><option>Inicio de sesión</option><option>Inicio de sesión fallido</option><option>Cierre de sesión</option><option>Creación</option><option>Actualización</option><option>Eliminación</option><option>Anulación</option><option>Reversión</option><option>Registro</option></select>
        <input autocomplete="off" class="logs-input" id="logs-desde" type="date" title="Desde">
        <input autocomplete="off" class="logs-input" id="logs-hasta" type="date" title="Hasta">
        <button class="logs-filter-btn" type="submit">Filtrar</button>
    </form>
    <section class="logs-card">
        <div style="overflow:auto"><table class="logs-table"><thead><tr><th>Fecha y hora</th><th>Usuario</th><th>Actividad</th><th>Referencia técnica</th><th>Acción</th></tr></thead><tbody id="logs-lista"></tbody></table></div>
        <div id="logs-vacio" class="logs-empty" style="display:none">Aún no hay eventos registrados. Las nuevas operaciones aparecerán aquí automáticamente.</div>
        <footer class="logs-footer"><span id="logs-resumen">Cargando historial...</span><div class="logs-pages"><button class="logs-page-btn" id="logs-anterior" type="button">Anterior</button><button class="logs-page-btn" id="logs-siguiente" type="button">Siguiente</button></div></footer>
    </section>
</div>

<script>
    (function () {
        var pagina=1, ultimaPagina=1, firmaActual='', cargando=false;
        var lista=document.getElementById('logs-lista'), vacio=document.getElementById('logs-vacio');
        function esc(valor){var d=document.createElement('div');d.textContent=String(valor || '');return d.innerHTML;}
        function fecha(valor){if(!valor)return '---';return new Intl.DateTimeFormat('es-CO',{dateStyle:'short',timeStyle:'short'}).format(new Date(valor));}
        function clase(accion){if(['Eliminación','Anulación','Inicio de sesión fallido'].indexOf(accion)>=0)return 'danger';if(['Reversión'].indexOf(accion)>=0)return 'warning';if(['Registro','Inicio de sesión','Creación'].indexOf(accion)>=0)return 'success';return '';}
        function cargar(page, silencioso){
            if(cargando)return;
            pagina=page || 1;
            cargando=true;
            var p=new URLSearchParams({page:pagina,buscar:document.getElementById('logs-buscar').value,usuario_id:document.getElementById('logs-usuario').value,accion:document.getElementById('logs-accion').value,desde:document.getElementById('logs-desde').value,hasta:document.getElementById('logs-hasta').value});
            fetch('/logs/data?'+p.toString(),{headers:{Accept:'application/json'},cache:'no-store'}).then(function(r){if(!r.ok)throw new Error('No fue posible cargar los logs.');return r.json();}).then(function(datos){
                var filas=datos.data || []; ultimaPagina=datos.last_page || 1;
                var firma=filas.map(function(log){return log.id+':'+log.created_at;}).join('|');
                if(!silencioso || firma!==firmaActual){
                    lista.innerHTML=filas.map(function(log){return '<tr><td class="logs-time">'+fecha(log.created_at)+'</td><td><div class="logs-user">'+esc(log.usuario?.name || 'Sistema / no identificado')+'</div><div class="logs-role">'+esc(log.rol || 'Sin rol')+'</div></td><td><div class="logs-description">'+esc(log.descripcion)+'</div><div class="logs-detail">'+esc(log.modulo)+(log.referencia ? ' · Ref. '+esc(log.referencia) : '')+'</div></td><td><div class="logs-detail">'+esc(log.metodo || '---')+' '+esc(log.ruta || '---')+'<br>IP: '+esc(log.ip || '---')+'</div></td><td><span class="logs-badge '+clase(log.accion)+'">'+esc(log.accion)+'</span></td></tr>';}).join('');
                    lista.classList.remove('is-refreshing'); void lista.offsetWidth; lista.classList.add('is-refreshing');
                    firmaActual=firma;
                }
                vacio.style.display=filas.length?'none':'block';
                document.getElementById('logs-resumen').textContent='Mostrando '+filas.length+' de '+(datos.total || 0)+' eventos';
                document.getElementById('logs-anterior').disabled=pagina<=1;
                document.getElementById('logs-siguiente').disabled=pagina>=ultimaPagina;
                document.getElementById('logs-actualizacion').textContent='Actualizado '+new Intl.DateTimeFormat('es-CO',{hour:'numeric',minute:'2-digit'}).format(new Date());
            }).catch(function(error){if(!silencioso){lista.innerHTML='';vacio.style.display='block';vacio.textContent=error.message;}}).finally(function(){cargando=false;});
        }
        document.getElementById('logs-filtros').addEventListener('submit',function(e){e.preventDefault();firmaActual='';cargar(1);});
        document.getElementById('logs-anterior').addEventListener('click',function(){if(pagina>1)cargar(pagina-1);});
        document.getElementById('logs-siguiente').addEventListener('click',function(){if(pagina<ultimaPagina)cargar(pagina+1);});
        cargar(1);
        setInterval(function(){if(!document.hidden)cargar(pagina,true);},15000);
    })();
</script>
