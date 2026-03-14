/**
 * Customer layout: sidebar links only update main content (no full page reload).
 * Load this only on the customer layout; do not use app.js for this.
 */
(function initCustomerLayoutNav() {
    const sidebar = document.querySelector('.lb-layout__sidebar');
    if (!sidebar) return;

    const sidebarLinks = sidebar.querySelectorAll('.lb-sidebar__link[href^="/"]');

    function setActiveLink(href) {
        const path = new URL(href, location.origin).pathname;
        sidebar.querySelectorAll('.lb-sidebar__item').forEach((el) => el.classList.remove('lb-sidebar__item--active'));
        const active = [...sidebarLinks].find((a) => {
            try {
                return new URL(a.getAttribute('href'), location.origin).pathname === path;
            } catch {
                return false;
            }
        });
        if (active) active.closest('.lb-sidebar__item')?.classList.add('lb-sidebar__item--active');
    }

    sidebar.addEventListener('click', (e) => {
        const a = e.target.closest('a.lb-sidebar__link');
        if (!a || !a.href) return;
        const url = new URL(a.href);
        if (url.origin !== location.origin || url.pathname === location.pathname) return;
        if (!url.pathname.startsWith('/customer/')) return;
        // Use normal navigation so page-specific @vite CSS/scripts and topbar title always refresh.
        // (The previous AJAX-only navigation caused missing styles + stale title when switching pages.)
    });
})();
