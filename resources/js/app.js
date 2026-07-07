import 'bootstrap';
import Alpine from 'alpinejs';
import 'cropperjs/dist/cropper.css';
import './product-image.js';

window.Alpine = Alpine;
Alpine.start();

// ── Mobile sidebar — vanilla JS, no Alpine in the tap path ───────────────────
// Alpine @click/@touchend on iOS Chrome/Safari can be swallowed by the
// browser's scroll-intent detection when the button is inside a position:fixed
// overflow-y:auto container. Plain addEventListener with passive:false is the
// only guaranteed solution.
document.addEventListener('DOMContentLoaded', function () {
    const sidebar  = document.getElementById('om-sidebar');
    const toggle   = document.getElementById('om-topbar-toggle');
    const closeBtn = document.getElementById('om-sidebar-close');
    const backdrop = document.getElementById('om-sidebar-backdrop');

    if (!sidebar) return;

    function isMobile() { return window.innerWidth < 768; }

    function openSidebar() {
        sidebar.classList.remove('collapsed');
        if (backdrop) backdrop.classList.add('is-open');
        if (isMobile()) document.body.classList.add('om-sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.add('collapsed');
        if (backdrop) backdrop.classList.remove('is-open');
        document.body.classList.remove('om-sidebar-open');
    }

    // Set initial state — collapsed on mobile, open on desktop
    if (isMobile()) sidebar.classList.add('collapsed');

    // Hamburger toggle
    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.contains('collapsed') ? openSidebar() : closeSidebar();
        });
    }

    // Close button — touchend fires before iOS scroll-intent can cancel it;
    // passive:false lets us call preventDefault() to suppress the follow-up click
    if (closeBtn) {
        closeBtn.addEventListener('touchend', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        }, { passive: false });
        closeBtn.addEventListener('click', closeSidebar);
    }

    // Backdrop — tap outside closes sidebar
    if (backdrop) {
        backdrop.addEventListener('touchend', function (e) {
            e.preventDefault();
            closeSidebar();
        }, { passive: false });
        backdrop.addEventListener('click', closeSidebar);
    }

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isMobile()) closeSidebar();
    });

    // Nav links close sidebar immediately on mobile before page loads
    sidebar.querySelectorAll('a.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (isMobile()) closeSidebar();
        });
    });
});
