import './utils.js';
import './modales.js';
import './terceros.js';
import './productos.js';
import './ajustes.js';

window.loadView = function (view) {
    fetch(`/views/${view}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('main-content').innerHTML = html;

            if (typeof initEventos === 'function') {
                initEventos();
            }
        })
        .catch(err => console.error(err));
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('App cargada correctamente');
});