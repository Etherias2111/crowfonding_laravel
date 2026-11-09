/* ═══════════════════════════════════════════════════════
   CROWFONDING – Scripts Principales & Animaciones
   ═══════════════════════════════════════════════════════ */

'use strict';

/* ─────────────────────────────────────────
   1. NAVBAR – Scroll + Hamburger + Tema Claro/Oscuro
   ───────────────────────────────────────── */
(function initNavbar() {
    const navbar      = document.getElementById('navbar') || document.querySelector('header');
    const hamburger   = document.getElementById('menu-toggle');
    const navLinks    = document.getElementById('main-nav');
    const allNavLinks = document.querySelectorAll('header nav a');

    // Scroll shadow effect
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });
    }

    // Hamburger toggle para móviles
    if (hamburger && navLinks) {
        const menuIcon = hamburger.querySelector('.menu-toggle-icon');
        const closeMenu = () => {
            navLinks.classList.remove('open');
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.setAttribute('aria-label', 'Abrir menú');
            if (menuIcon) menuIcon.textContent = '☰';
            document.body.classList.remove('menu-open');
        };

        hamburger.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('open');
            hamburger.classList.toggle('open', isOpen);
            hamburger.setAttribute('aria-expanded', String(isOpen));
            hamburger.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
            if (menuIcon) menuIcon.textContent = isOpen ? '×' : '☰';
            document.body.classList.toggle('menu-open', isOpen);
        });

        // Cerrar menú al hacer clic en un enlace
        allNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        const themeButton = document.getElementById('theme-toggle');
        if (themeButton) themeButton.addEventListener('click', closeMenu);
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenu();
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1050) closeMenu();
        });
    }

    // Toggle de Modo Claro / Oscuro
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        // Cargar preferencia guardada
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            themeToggleBtn.textContent = '🌙 Modo Oscuro';
        }

        themeToggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('light-mode');
            const isLight = document.body.classList.contains('light-mode');
            themeToggleBtn.textContent = isLight ? '🌙 Modo Oscuro' : '☀️ Modo Claro';
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        });
    }
})();

(function initAccountMenu() {
    const menu = document.getElementById('account-menu');
    const trigger = document.getElementById('account-menu-toggle');
    if (!menu || !trigger) return;

    let pinnedOpen = false;
    const setOpen = open => {
        menu.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', String(open));
    };

    menu.addEventListener('mouseenter', () => setOpen(true));
    menu.addEventListener('mouseleave', () => { if (!pinnedOpen) setOpen(false); });
    menu.addEventListener('focusin', () => setOpen(true));
    menu.addEventListener('focusout', event => {
        if (!menu.contains(event.relatedTarget) && !pinnedOpen) setOpen(false);
    });
    trigger.addEventListener('click', () => {
        pinnedOpen = !pinnedOpen;
        setOpen(pinnedOpen);
    });
    document.addEventListener('click', event => {
        if (!menu.contains(event.target)) {
            pinnedOpen = false;
            setOpen(false);
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            pinnedOpen = false;
            trigger.focus();
            setOpen(false);
        }
    });
})();

document.addEventListener('submit', event => {
    const form = event.target.closest('form[data-confirm]');
    if (form && !window.confirm(form.dataset.confirm)) event.preventDefault();
});


/* ─────────────────────────────────────────
   2. ANIMACIONES – Scroll reveal (Efectos visuales)
   ───────────────────────────────────────── */
(function initAnimations() {

    const targets = document.querySelectorAll(
        '.ticket-card, .detalle-container, .form-container, .user-welcome, .product-card, ' +
        '.laptop-card, .dashboard-container, .profile-container, .admin-container, ' +
        '.admin-section, .panel-content.active, .success-card, .carrusel-wrapper, section'
    );
    if (!targets.length) return;

    // Si el navegador no admite IntersectionObserver, mostrar el contenido sin animarlo.
    if (!('IntersectionObserver' in window)) return;

    targets.forEach((el, i) => {
        el.classList.add('reveal');
        el.classList.add('reveal-delay-' + (i % 4));
    });

    const revealObserver = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1 }
    );
    targets.forEach(el => revealObserver.observe(el));
})();


/* ─────────────────────────────────────────
   3. CHECKBOX DIRECCIÓN (Formulario de Servicios)
   ───────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
 const checkbox=document.getElementById('con_recogida');
 const section=document.getElementById('seccion-direccion');
 if(checkbox&&section){
  const update=()=>section.classList.toggle('is-visible',checkbox.checked);
  checkbox.addEventListener('change',update); update();
 }
});


/* ─────────────────────────────────────────
   4. SMOOTH SCROLL – Navegación suave
   ───────────────────────────────────────── */
(function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            const offset = 80;
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
})();


/* ─────────────────────────────────────────
   5. STATS COUNTER – Animación de números dinámicos
   ───────────────────────────────────────── */
(function initCounters() {
    const counters = document.querySelectorAll('.stat-num');
    if (counters.length === 0) return;

    const counterObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const raw = el.textContent.trim();
            const hasPrefix = raw.startsWith('+');
            const numStr = raw.replace(/[^0-9]/g, '');
            const target = parseInt(numStr, 10);
            if (isNaN(target) || numStr.length === 0) return;

            let current = 0;
            const increment = Math.max(1, Math.floor(target / 40));
            const interval = setInterval(() => {
                current = Math.min(current + increment, target);
                const formatted = current.toLocaleString('es-CO');
                el.textContent = (hasPrefix ? '+' : '') + formatted;
                if (current >= target) clearInterval(interval);
            }, 40);

            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(c => counterObserver.observe(c));
})();


/* Carrusel de equipos de la portada */
(function initProductCarousel() {
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
        const track = carousel.querySelector('.carrusel-track');
        const previousButton = carousel.querySelector('.carrusel-btn.prev');
        const nextButton = carousel.querySelector('.carrusel-btn.next');
        if (!track || !previousButton || !nextButton) return;

        const cards = Array.from(track.querySelectorAll('.laptop-card'));
        if (cards.length === 0) {
            previousButton.hidden = true;
            nextButton.hidden = true;
            return;
        }

        let currentIndex = 0;
        const visibleCount = () => window.matchMedia('(max-width: 640px)').matches
            ? 1
            : window.matchMedia('(max-width: 1023px)').matches ? 2 : 3;

        const updateCarousel = () => {
            const visible = visibleCount();
            carousel.dataset.visibleItems = String(Math.min(cards.length, visible));
            const lastIndex = Math.max(0, cards.length - visible);
            currentIndex = Math.min(currentIndex, lastIndex);
            const offset = cards[currentIndex].offsetLeft - cards[0].offsetLeft;
            track.style.transform = `translateX(-${offset}px)`;

            const hasOverflow = cards.length > visible;
            previousButton.hidden = !hasOverflow;
            nextButton.hidden = !hasOverflow;
            previousButton.disabled = currentIndex === 0;
            nextButton.disabled = currentIndex >= lastIndex;
        };

        previousButton.addEventListener('click', () => {
            currentIndex = Math.max(0, currentIndex - 1);
            updateCarousel();
        });
        nextButton.addEventListener('click', () => {
            currentIndex = Math.min(cards.length - visibleCount(), currentIndex + 1);
            updateCarousel();
        });
        window.addEventListener('resize', updateCarousel, { passive: true });
        updateCarousel();
    });
})();

/* Dirección de recogida, geolocalización opcional y búsqueda en mapa */
(function initPickupAddress() {
    const pickup = document.getElementById('pickup');
    const fields = document.getElementById('pickup-address-fields');
    const address = document.getElementById('address');
    const locateButton = document.getElementById('use-current-location');
    const mapLink = document.getElementById('search-address-map');
    const message = document.getElementById('location-message');
    if (!pickup || !fields || !address) return;

    const syncVisibility = () => {
        fields.hidden = !pickup.checked;
        address.required = pickup.checked;
    };
    const updateMapLink = () => {
        const query = address.value.trim();
        mapLink.href = query
            ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`
            : 'https://www.google.com/maps';
    };

    pickup.addEventListener('change', syncVisibility);
    address.addEventListener('input', updateMapLink);
    syncVisibility();
    updateMapLink();

    if (locateButton) locateButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            message.textContent = 'Este navegador no permite obtener la ubicación. Escribe la dirección manualmente.';
            return;
        }
        locateButton.disabled = true;
        message.textContent = 'Solicitando permiso para consultar tu ubicación…';
        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            address.value = `Ubicación GPS: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
            address.dispatchEvent(new Event('input', { bubbles: true }));
            message.textContent = 'Ubicación agregada como coordenadas. Puedes editarla o buscarla en el mapa.';
            locateButton.disabled = false;
        }, error => {
            message.textContent = error.code === error.PERMISSION_DENIED
                ? 'No se concedió permiso de ubicación. Escribe la dirección o búscala en el mapa.'
                : 'No se pudo obtener la ubicación. Escribe la dirección manualmente.';
            locateButton.disabled = false;
        }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 });
    });
})();
