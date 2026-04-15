window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

window.debounce = function (func, delay = 400) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

window.activeTab = null;

window.toggleTab = function (tabId) {
    const ribbon = document.getElementById('ribbon');

    if (activeTab === tabId) {
        ribbon.classList.add('hidden');
        document.getElementById(tabId).classList.add('hidden');
        activeTab = null;
        return;
    }

    ribbon.classList.remove('hidden');

    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });

    document.getElementById(tabId).classList.remove('hidden');
    activeTab = tabId;
};