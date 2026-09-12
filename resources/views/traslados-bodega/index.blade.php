<style>
    .tb-page { padding: 24px; color: #172033; }
    .tb-head { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:18px; }
    .tb-title { margin:0; font-size:22px; letter-spacing:-.6px; } .tb-subtitle { margin:4px 0 0; color:#667085; font-size:12px; }
    .tb-btn { display:inline-flex; align-items:center; gap:7px; border:0; border-radius:8px; padding:10px 14px; background:#1d4ed8; color:#fff; font-size:12px; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(29,78,216,.18); }
    .tb-btn:hover { background:#1e40af; } .tb-btn:disabled { cursor:wait; opacity:.65; }
    .tb-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:16px; }
    .tb-metric { padding:14px 16px; border:1px solid #e5e7eb; border-radius:11px; background:#fff; } .tb-metric-label { color:#98a2b3; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; } .tb-metric-value { margin-top:5px; color:#1f2937; font-size:22px; font-weight:700; }
    .tb-card { overflow:hidden; border:1px solid #e5e7eb; border-radius:11px; background:#fff; box-shadow:0 2px 8px rgba(16,24,40,.025); }
    .tb-toolbar { display:flex; align-items:center; gap:10px; padding:13px; border-bottom:1px solid #eef0f3; } .tb-search { width:min(330px,100%); border:1px solid #d0d5dd; border-radius:7px; padding:8px 10px; outline:0; font-size:12px; } .tb-search:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .tb-table { width:100%; border-collapse:collapse; font-size:12px; } .tb-table th { padding:11px 15px; background:#f9fafb; color:#667085; font-size:10px; text-align:left; text-transform:uppercase; letter-spacing:.05em; } .tb-table td { padding:13px 15px; border-top:1px solid #f0f1f3; color:#475467; } .tb-table tr:hover td { background:#fafcff; } .tb-doc { color:#1d4ed8; font-weight:700; } .tb-route { display:flex; align-items:center; gap:7px; color:#344054; } .tb-route i { color:#98a2b3; font-style:normal; } .tb-badge { display:inline-block; border-radius:99px; padding:4px 8px; background:#ecfdf3; color:#027a48; font-size:10px; font-weight:700; text-transform:uppercase; } .tb-badge.draft { background:#fffaeb; color:#b54708; } .tb-row-actions { display:flex; gap:5px; } .tb-row-action { border:1px solid #d0d5dd; border-radius:6px; padding:5px 7px; background:#fff; color:#344054; font-size:10px; font-weight:700; cursor:pointer; } .tb-row-action.register { border-color:#b7e3c2; background:#f0fdf4; color:#067647; } .tb-row-action.revert { border-color:#fed7aa; background:#fff7ed; color:#c2410c; } .tb-row-action.delete { border-color:#fecaca; background:#fef2f2; color:#b42318; } .tb-empty { padding:48px 20px; color:#98a2b3; text-align:center; font-size:13px; }
    .tb-modal { position:fixed; z-index:600; inset:0; display:none; align-items:center; justify-content:center; padding:18px; background:rgba(16,24,40,.56); backdrop-filter:blur(3px); } .tb-modal.open { display:flex; } .tb-dialog { width:min(900px,100%); max-height:calc(100vh - 36px); overflow:auto; border-radius:16px; background:#fff; box-shadow:0 24px 55px rgba(16,24,40,.28); }
    .tb-modal-head { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid #eaecf0; } .tb-modal-head h2 { margin:0; font-size:18px; } .tb-close { border:0; background:transparent; color:#667085; font-size:26px; cursor:pointer; } .tb-form { padding:20px 22px; } .tb-form-grid { display:grid; grid-template-columns:1fr 1fr 1.2fr 1.2fr; gap:12px; } .tb-field { display:grid; gap:6px; } .tb-field label { color:#475467; font-size:11px; font-weight:700; } .tb-field input,.tb-field select,.tb-field textarea { width:100%; border:1px solid #d0d5dd; border-radius:7px; padding:9px 10px; color:#344054; font:inherit; font-size:12px; outline:0; } .tb-field input:focus,.tb-field select:focus,.tb-field textarea:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); } .tb-field textarea { min-height:58px; resize:vertical; }
    .tb-items-block { margin-top:20px; padding-top:18px; border-top:1px solid #eaecf0; } .tb-items-title { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; } .tb-items-title strong { font-size:14px; } .tb-items-title span { color:#667085; font-size:11px; }
    .tb-product-wrap { position:relative; } .tb-product-search { padding-left:34px !important; } .tb-product-icon { position:absolute; left:11px; top:9px; color:#98a2b3; } .tb-results { position:absolute; z-index:5; top:calc(100% + 4px); left:0; right:0; display:none; max-height:230px; overflow:auto; border:1px solid #d0d5dd; border-radius:8px; background:#fff; box-shadow:0 10px 25px rgba(16,24,40,.14); } .tb-results.open { display:block; } .tb-result { width:100%; display:flex; justify-content:space-between; gap:12px; padding:10px 12px; border:0; border-bottom:1px solid #f0f1f3; background:#fff; color:#344054; text-align:left; cursor:pointer; font-size:12px; } .tb-result:hover { background:#f5f8ff; } .tb-result small { color:#667085; } .tb-result-stock { color:#027a48; font-weight:700; white-space:nowrap; }
    .tb-lines { margin-top:11px; overflow:hidden; border:1px solid #eaecf0; border-radius:8px; } .tb-line { display:grid; grid-template-columns:1fr 115px 105px 36px; align-items:center; gap:10px; padding:10px 12px; border-bottom:1px solid #f0f1f3; } .tb-line:last-child { border:0; } .tb-line-name { color:#344054; font-size:12px; font-weight:700; } .tb-line-stock { margin-top:3px; color:#667085; font-size:10px; } .tb-qty { border:1px solid #d0d5dd; border-radius:6px; padding:7px; font-size:12px; } .tb-remove { width:30px; height:30px; border:0; border-radius:6px; background:#fef3f2; color:#d92d20; cursor:pointer; font-weight:700; }
    .tb-no-lines { padding:22px; color:#98a2b3; text-align:center; font-size:12px; } .tb-actions { display:flex; justify-content:flex-end; gap:9px; margin-top:20px; padding-top:16px; border-top:1px solid #eaecf0; } .tb-cancel { border:1px solid #d0d5dd; border-radius:8px; padding:9px 13px; background:#fff; color:#344054; font-size:12px; font-weight:700; cursor:pointer; }
    @media(max-width:720px) { .tb-page{padding:16px}.tb-head{align-items:stretch;flex-direction:column}.tb-btn{justify-content:center}.tb-metrics{grid-template-columns:1fr 1fr}.tb-table th:nth-child(3),.tb-table td:nth-child(3),.tb-table th:nth-child(5),.tb-table td:nth-child(5){display:none}.tb-form-grid{grid-template-columns:1fr 1fr}.tb-field:nth-child(3),.tb-field:nth-child(4){grid-column:1/-1}.tb-line{grid-template-columns:1fr 88px 30px}.tb-line-stock{display:none}.tb-modal{padding:8px}.tb-form,.tb-modal-head{padding:16px} }
</style>

<div class="tb-page">
    <div class="tb-head">
        <div><h1 class="tb-title">Traslados entre bodegas</h1><p class="tb-subtitle">Mueve existencias entre puntos de inventario con trazabilidad del documento.</p></div>
        <button class="tb-btn" type="button" onclick="abrirTraslado()">+ Nuevo traslado</button>
    </div>
    <section class="tb-metrics">
        <article class="tb-metric"><div class="tb-metric-label">Traslados registrados</div><div class="tb-metric-value" id="tb-total">0</div></article>
        <article class="tb-metric"><div class="tb-metric-label">Documentos visibles</div><div class="tb-metric-value" id="tb-visibles">0</div></article>
        <article class="tb-metric"><div class="tb-metric-label">Estado de inventario</div><div class="tb-metric-value" style="color:#027a48;font-size:16px;padding-top:5px;">Actualizado</div></article>
    </section>
    <section class="tb-card">
        <div class="tb-toolbar"><input id="tb-buscar" class="tb-search" type="search" placeholder="Buscar por prefijo o consecutivo"></div>
        <div style="overflow:auto"><table class="tb-table"><thead><tr><th>Documento</th><th>Fecha</th><th>Movimiento</th><th>Productos</th><th>Registrado por</th><th>Estado</th><th>Acciones</th></tr></thead><tbody id="tb-lista"></tbody></table></div>
        <div id="tb-vacio" class="tb-empty" style="display:none">No hay traslados registrados todavía.</div>
    </section>
</div>

<div class="tb-modal" id="tb-modal" aria-hidden="true">
    <div class="tb-dialog" role="dialog" aria-modal="true" aria-labelledby="tb-modal-title">
        <div class="tb-modal-head"><h2 id="tb-modal-title">Nuevo traslado de inventario</h2><button class="tb-close" type="button" onclick="cerrarTraslado()" aria-label="Cerrar">&times;</button></div>
        <form class="tb-form" id="tb-form">
            <div class="tb-form-grid">
                <div class="tb-field"><label for="tb-prefijo">Prefijo</label><input id="tb-prefijo" maxlength="10" value="TR" required></div>
                <div class="tb-field"><label for="tb-consecutivo">Consecutivo</label><input id="tb-consecutivo" type="number" min="1" required></div>
                <div class="tb-field"><label for="tb-fecha">Fecha</label><input id="tb-fecha" type="date" value="{{ now()->toDateString() }}" required></div>
                <div class="tb-field"><label for="tb-origen">Bodega de origen</label><select id="tb-origen" required><option value="">Selecciona la bodega de salida</option>@foreach($bodegas as $bodega)<option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>@endforeach</select></div>
                <div class="tb-field" style="grid-column:span 2"><label for="tb-destino">Bodega de destino</label><select id="tb-destino" required><option value="">Selecciona la bodega de llegada</option>@foreach($bodegas as $bodega)<option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>@endforeach</select></div>
                <div class="tb-field" style="grid-column:span 2"><label for="tb-observaciones">Observaciones</label><textarea id="tb-observaciones" placeholder="Motivo o nota del traslado (opcional)"></textarea></div>
            </div>
            <section class="tb-items-block">
                <div class="tb-items-title"><strong>Productos a trasladar</strong><span>Busca solo dentro de la bodega de origen</span></div>
                <div class="tb-product-wrap"><span class="tb-product-icon">⌕</span><input id="tb-producto" class="tb-product-search" type="search" autocomplete="off" placeholder="Primero selecciona la bodega de origen" disabled><div class="tb-results" id="tb-resultados"></div></div>
                <div class="tb-lines" id="tb-lineas"><div class="tb-no-lines">Aún no has agregado productos al traslado.</div></div>
            </section>
            <div class="tb-actions"><button class="tb-cancel" type="button" onclick="cerrarTraslado()">Cancelar</button><button class="tb-btn" id="tb-guardar" type="submit">Guardar borrador</button></div>
        </form>
    </div>
</div>

<script>
    (function () {
        var csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        var lineas = [], temporizador, buscando = false;
        var lista = document.getElementById('tb-lista'), vacio = document.getElementById('tb-vacio');
        var origen = document.getElementById('tb-origen'), destino = document.getElementById('tb-destino'), producto = document.getElementById('tb-producto'), resultados = document.getElementById('tb-resultados');
        function esc(v) { var d=document.createElement('div'); d.textContent=String(v || ''); return d.innerHTML; }
        function n(v) { return Number(v || 0).toLocaleString('es-CO',{maximumFractionDigits:3}); }
        function aviso(titulo, texto, tipo) { if (window.Swal) Swal.fire({title:titulo,text:texto,icon:tipo || 'info',confirmButtonColor:'#1d4ed8'}); else alert(titulo + ': ' + texto); }
        function cargarTraslados() {
            var buscar = document.getElementById('tb-buscar').value.trim();
            fetch('/traslados-bodega?buscar=' + encodeURIComponent(buscar), {headers:{Accept:'application/json'},cache:'no-store'})
                .then(function(r){if(!r.ok)throw new Error('No se pudo cargar la información.');return r.json();})
                .then(function(datos){
                    var filas=datos.data || []; document.getElementById('tb-total').textContent=n(datos.total); document.getElementById('tb-visibles').textContent=n(filas.length);
                    lista.innerHTML=filas.map(function(t){var borrador=t.estado==='borrador';var acciones=borrador?'<button class="tb-row-action register" onclick="registrarTraslado('+t.id+')">Registrar</button><button class="tb-row-action delete" onclick="eliminarTraslado('+t.id+')">Eliminar</button>':'<button class="tb-row-action revert" onclick="revertirTraslado('+t.id+')">Revertir</button>';return '<tr><td class="tb-doc">'+esc(t.prefijo)+'-'+String(t.consecutivo).padStart(5,'0')+'</td><td>'+esc(t.fecha_formateada || '')+'</td><td><div class="tb-route"><span>'+esc(t.origen?.descripcion || '---')+'</span><i>→</i><span>'+esc(t.destino?.descripcion || '---')+'</span></div></td><td>'+n(t.detalles_count)+' productos</td><td>'+esc(t.usuario?.name || '---')+'</td><td><span class="tb-badge '+(borrador?'draft':'')+'">'+esc(t.estado)+'</span></td><td><div class="tb-row-actions">'+acciones+'</div></td></tr>';}).join('');
                    vacio.style.display=filas.length ? 'none' : 'block';
                }).catch(function(error){ aviso('No fue posible cargar', error.message, 'error'); });
        }
        function renderLineas() {
            var cont=document.getElementById('tb-lineas');
            if(!lineas.length){cont.innerHTML='<div class="tb-no-lines">Aún no has agregado productos al traslado.</div>';return;}
            cont.innerHTML=lineas.map(function(item,i){return '<div class="tb-line"><div><div class="tb-line-name">'+esc(item.descripcion)+'</div><div class="tb-line-stock">Disponible en origen: '+n(item.stock)+'</div></div><input class="tb-qty" aria-label="Cantidad de '+esc(item.descripcion)+'" type="number" min="0.001" max="'+item.stock+'" step="0.001" value="'+item.cantidad+'" onchange="actualizarCantidadTraslado('+i+',this.value)"><div class="tb-line-stock">Stock: '+n(item.stock)+'</div><button class="tb-remove" type="button" onclick="quitarProductoTraslado('+i+')" title="Quitar">×</button></div>';}).join('');
        }
        window.actualizarCantidadTraslado=function(i,valor){var cantidad=Number(valor);if(!cantidad||cantidad<=0){lineas[i].cantidad=0.001;}else if(cantidad>Number(lineas[i].stock)){lineas[i].cantidad=Number(lineas[i].stock);aviso('Cantidad ajustada','No puedes trasladar más de lo disponible en la bodega de origen.','warning');}else lineas[i].cantidad=cantidad;renderLineas();};
        window.quitarProductoTraslado=function(i){lineas.splice(i,1);renderLineas();};
        function buscarProductos() {
            var termino=producto.value.trim(), bodega=origen.value;
            if(!bodega || termino.length<2){resultados.classList.remove('open');return;}
            buscando=true;
            fetch('/traslados-bodega/productos?bodega_id='+encodeURIComponent(bodega)+'&buscar='+encodeURIComponent(termino),{headers:{Accept:'application/json'},cache:'no-store'})
                .then(function(r){if(!r.ok)throw new Error();return r.json();}).then(function(datos){
                    var productos=datos.data || [];
                    resultados.innerHTML=productos.length ? productos.map(function(p){return '<button type="button" class="tb-result" data-producto="'+encodeURIComponent(JSON.stringify(p))+'"><span><b>'+esc(p.descripcion)+'</b><br><small>'+esc(p.codigo || 'Sin código')+'</small></span><span class="tb-result-stock">'+n(p.stock)+' disp.</span></button>';}).join('') : '<div class="tb-no-lines">No se encontraron productos disponibles.</div>';
                    resultados.classList.add('open');
                }).finally(function(){buscando=false;});
        }
        resultados.addEventListener('click',function(e){var boton=e.target.closest('[data-producto]');if(!boton)return;var item=JSON.parse(decodeURIComponent(boton.dataset.producto));if(lineas.some(function(l){return Number(l.producto_id)===Number(item.id);})){aviso('Producto agregado','Este producto ya está en el traslado. Ajusta su cantidad en la lista.','info');}else{var stock=Number(item.stock);lineas.push({producto_id:item.id,descripcion:item.descripcion,stock:stock,cantidad:Math.min(1,stock)});renderLineas();}producto.value='';resultados.classList.remove('open');});
        origen.addEventListener('change',function(){lineas=[];renderLineas();producto.value='';producto.disabled=!origen.value;producto.placeholder=origen.value?'Escribe al menos dos letras para buscar':'Primero selecciona la bodega de origen';});
        producto.addEventListener('input',function(){clearTimeout(temporizador);temporizador=setTimeout(buscarProductos,250);});
        document.getElementById('tb-prefijo').addEventListener('change',function(){var prefijo=this.value.trim() || 'TR';fetch('/traslados-bodega/siguiente-consecutivo?prefijo='+encodeURIComponent(prefijo),{headers:{Accept:'application/json'}}).then(function(r){return r.json();}).then(function(d){document.getElementById('tb-consecutivo').value=d.consecutivo;});});
        document.getElementById('tb-buscar').addEventListener('input',function(){clearTimeout(temporizador);temporizador=setTimeout(cargarTraslados,250);});
        window.abrirTraslado=function(){document.getElementById('tb-modal').classList.add('open');document.getElementById('tb-modal').setAttribute('aria-hidden','false');document.getElementById('tb-prefijo').dispatchEvent(new Event('change'));};
        window.cerrarTraslado=function(){document.getElementById('tb-modal').classList.remove('open');document.getElementById('tb-modal').setAttribute('aria-hidden','true');};
        function ejecutarAccion(id, accion, metodo, mensaje) {
            var continuar=function(){fetch('/traslados-bodega/'+id+(accion ? '/'+accion : ''),{method:metodo,headers:{Accept:'application/json','X-CSRF-TOKEN':csrf}}).then(function(r){return r.json().then(function(d){return {ok:r.ok,d:d};});}).then(function(res){if(!res.ok)throw new Error(res.d.message || 'No fue posible completar la acción.');aviso('Acción completada',res.d.message,'success');cargarTraslados();}).catch(function(error){aviso('No fue posible completar la acción',error.message,'error');});};
            if(window.Swal){Swal.fire({title:'¿Confirmar acción?',text:mensaje,icon:'warning',showCancelButton:true,confirmButtonText:'Sí, continuar',cancelButtonText:'Cancelar',confirmButtonColor:'#1d4ed8'}).then(function(r){if(r.isConfirmed)continuar();});}else if(confirm(mensaje))continuar();
        }
        window.registrarTraslado=function(id){ejecutarAccion(id,'registrar','POST','Se descontará el inventario de origen y se sumará al destino.');};
        window.revertirTraslado=function(id){ejecutarAccion(id,'revertir','POST','El inventario volverá al origen solo si aún existe suficiente stock en la bodega destino.');};
        window.eliminarTraslado=function(id){ejecutarAccion(id,'','DELETE','Se eliminará este borrador. Los borradores no modifican inventario.');};
        document.getElementById('tb-modal').addEventListener('click',function(e){if(e.target===this)cerrarTraslado();});
        document.getElementById('tb-form').addEventListener('submit',function(e){
            e.preventDefault();
            if(origen.value===destino.value){aviso('Bodegas inválidas','La bodega de origen y la de destino deben ser diferentes.','warning');return;}
            if(!lineas.length){aviso('Faltan productos','Agrega al menos un producto al traslado.','warning');return;}
            var boton=document.getElementById('tb-guardar');boton.disabled=true;
            var cuerpo={prefijo:document.getElementById('tb-prefijo').value,consecutivo:Number(document.getElementById('tb-consecutivo').value),fecha:document.getElementById('tb-fecha').value,bodega_origen_id:Number(origen.value),bodega_destino_id:Number(destino.value),observaciones:document.getElementById('tb-observaciones').value,items:lineas.map(function(l){return {producto_id:l.producto_id,cantidad:l.cantidad};})};
            fetch('/traslados-bodega',{method:'POST',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(cuerpo)})
                .then(function(r){return r.json().then(function(d){return {ok:r.ok,d:d};});}).then(function(res){if(!res.ok)throw new Error(res.d.message || Object.values(res.d.errors || {}).flat().join(' ') || 'No se pudo guardar el traslado.');aviso('Borrador guardado',res.d.message,'success');document.getElementById('tb-form').reset();document.getElementById('tb-fecha').value='{{ now()->toDateString() }}';lineas=[];renderLineas();producto.disabled=true;producto.placeholder='Primero selecciona la bodega de origen';cerrarTraslado();cargarTraslados();})
                .catch(function(error){aviso('No se pudo guardar',error.message,'error');}).finally(function(){boton.disabled=false;});
        });
        cargarTraslados();
    })();
</script>
