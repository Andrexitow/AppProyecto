window.loadView = function (view) {
    fetch('/views/' + view)
        .then(function(res) { return res.text(); })
        .then(function(html) {
            document.getElementById('main-content').innerHTML = html;
            if (typeof initEventos === 'function') {
                initEventos();
            }
        })
        .catch(function(err) { console.error(err); });
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('App cargada correctamente');
});