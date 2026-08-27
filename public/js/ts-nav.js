/**
 * Publikus mobil menü. Dokumentum-szintű capture, hogy a Vite app.js
 * ne nyissa és zárja ugyanarra a kattintásra.
 */
(function (global) {
    if (global.__tsNavInstalled) {
        return;
    }
    global.__tsNavInstalled = true;

    const STYLE_NAMES = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen'];

    function menuStyle(navRoot) {
        const raw = navRoot.getAttribute('data-mobile-menu-style') || 'dropdown';

        return STYLE_NAMES.find((name) => raw.startsWith(name)) || 'dropdown';
    }

    function panelOf(navRoot) {
        return navRoot.querySelector('[data-nav-panel]')
            || document.querySelector('.ts-nav-panel--fs');
    }

    function markerOf(navRoot) {
        if (!navRoot._tsPanelMarker) {
            const panel = navRoot.querySelector('[data-nav-panel]');
            if (!panel || !panel.parentNode) {
                return null;
            }
            const marker = document.createComment('ts-nav-panel');
            panel.parentNode.insertBefore(marker, panel);
            navRoot._tsPanelMarker = marker;
        }

        return navRoot._tsPanelMarker;
    }

    function setOpen(navRoot, open) {
        const style = menuStyle(navRoot);
        const panel = panelOf(navRoot);
        const toggle = navRoot.querySelector('[data-nav-toggle]');
        const overlay = navRoot.querySelector('[data-nav-overlay]');
        const isFullscreen = style === 'fullscreen';
        const usesOverlay = style === 'drawer-left' || style === 'drawer-right';

        navRoot.classList.toggle('is-menu-open', open);
        if (toggle) {
            toggle.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Menü bezárása' : 'Menü megnyitása');
        }
        document.body.classList.toggle('ts-nav-open', open && (usesOverlay || isFullscreen));
        document.body.classList.toggle('ts-nav-open--fs', open && isFullscreen);

        if (panel) {
            panel.classList.toggle('is-open', open);
            panel.classList.toggle('ts-nav-panel--fs', open && isFullscreen);
            panel.removeAttribute('hidden');
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');

            if (isFullscreen) {
                const marker = markerOf(navRoot);
                if (open) {
                    document.body.appendChild(panel);
                } else if (marker && marker.parentNode) {
                    marker.parentNode.insertBefore(panel, marker);
                }
            }
        }

        if (overlay) {
            overlay.classList.toggle('is-open', open && usesOverlay);
            overlay.hidden = !(open && usesOverlay);
            overlay.setAttribute('aria-hidden', open && usesOverlay ? 'false' : 'true');
        }
    }

    function navFrom(el) {
        return el.closest('[data-site-nav]')
            || document.querySelector('[data-site-nav].is-menu-open')
            || document.querySelector('[data-site-nav]');
    }

    document.addEventListener('click', (event) => {
        const el = event.target instanceof Element ? event.target : null;
        if (!el) {
            return;
        }

        const toggle = el.closest('[data-nav-toggle]');
        const closeBtn = el.closest('[data-nav-close]');
        const overlay = el.closest('[data-nav-overlay]');
        const menuLink = el.closest('.ts-nav-panel a[href], .ts-nav-panel__link');

        if (!toggle && !closeBtn && !overlay && !menuLink) {
            return;
        }

        const navRoot = navFrom(toggle || closeBtn || overlay || menuLink);
        if (!navRoot) {
            return;
        }

        const isOpen = navRoot.classList.contains('is-menu-open');

        if (toggle || closeBtn) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            setOpen(navRoot, !isOpen);
            return;
        }

        if (overlay) {
            if (!isOpen) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            setOpen(navRoot, false);
            return;
        }

        if (menuLink && isOpen) {
            setOpen(navRoot, false);
        }
    }, true);

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }
        const navRoot = document.querySelector('[data-site-nav].is-menu-open');
        if (!navRoot) {
            return;
        }
        setOpen(navRoot, false);
        navRoot.querySelector('[data-nav-toggle]')?.focus();
    });

    global.TsNav = {
        init() {
            document.querySelectorAll('[data-site-nav]').forEach((navRoot) => {
                navRoot.querySelector('[data-nav-panel]')?.removeAttribute('hidden');
            });
        },
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => global.TsNav.init());
    } else {
        global.TsNav.init();
    }
})(window);
