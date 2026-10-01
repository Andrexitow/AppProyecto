(() => {
    const button = document.getElementById('burger');
    const menu = document.getElementById('links');
    if (!button || !menu) return;
    const setOpen = (open) => {
        menu.classList.toggle('open', open);
        button.setAttribute('aria-expanded', String(open));
        button.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        button.textContent = open ? '×' : '☰';
    };
    button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            button.focus();
        }
    });
    document.addEventListener('click', event => {
        if (!menu.contains(event.target) && !button.contains(event.target)) setOpen(false);
    });
    window.matchMedia('(min-width: 761px)').addEventListener('change', () => setOpen(false));
})();

// Animaciones: progreso de scroll, nav, aparición escalonada, brillo en tarjetas y mesas en vivo.
(() => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const nav = document.getElementById('nav');
    const bar = document.getElementById('progress');
    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const max = document.documentElement.scrollHeight - innerHeight;
            if (bar) bar.style.setProperty('--p', max > 0 ? (scrollY / max).toFixed(4) : 0);
            if (nav) nav.classList.toggle('scrolled', scrollY > 24);
            ticking = false;
        });
    };
    onScroll();
    addEventListener('scroll', onScroll, { passive: true });

    const items = document.querySelectorAll('.rv');
    if (!('IntersectionObserver' in window) || reduce) {
        items.forEach(el => el.classList.add('in'));
    } else {
        const io = new IntersectionObserver(entries => entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const siblings = [...el.parentElement.children].filter(c => c.classList.contains('rv'));
            el.style.transitionDelay = Math.min(siblings.indexOf(el), 6) * 90 + 'ms';
            el.classList.add('in');
            setTimeout(() => { el.style.transitionDelay = ''; }, 1200);
            io.unobserve(el);
        }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
        items.forEach(el => io.observe(el));
    }

    document.querySelectorAll('.feat').forEach(card => card.addEventListener('pointermove', e => {
        const r = card.getBoundingClientRect();
        card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        card.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }));

    // Mesas en vivo en la maqueta del hero
    if (reduce) return;
    const tables = [...document.querySelectorAll('.tb')];
    const amounts = ['$38.400', '$52.900', '$76.300', '$29.700', '$94.100'];
    setInterval(() => {
        const t = tables[Math.floor(Math.random() * tables.length)];
        const n = t.querySelector('b').outerHTML;
        if (t.classList.contains('free')) {
            t.className = 'tb busy flash';
            t.innerHTML = n + amounts[Math.floor(Math.random() * amounts.length)];
        } else if (t.classList.contains('busy')) {
            t.className = 'tb pay flash';
            t.innerHTML = n + 'Por cobrar';
        } else {
            t.className = 'tb free flash';
            t.innerHTML = n + 'Nueva';
        }
        setTimeout(() => t.classList.remove('flash'), 1000);
    }, 2400);
})();
