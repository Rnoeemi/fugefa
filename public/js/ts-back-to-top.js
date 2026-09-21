/**
 * Vissza az oldal tetejére – görgetés után megjelenő gomb.
 */
(function (global) {
    const SHOW_AFTER_PX = 420;
    const SELECTOR = '[data-back-to-top]';

    function sync(btn) {
        const y = window.scrollY || document.documentElement.scrollTop || 0;
        btn.classList.toggle('is-visible', y > SHOW_AFTER_PX);
    }

    function init(root) {
        const scope = root || document;
        const btn = scope.querySelector(SELECTOR);
        if (! btn || btn.dataset.tsBackToTopReady === '1') {
            return;
        }
        btn.dataset.tsBackToTopReady = '1';

        const onScroll = () => sync(btn);
        window.addEventListener('scroll', onScroll, { passive: true });
        sync(btn);

        btn.addEventListener('click', (event) => {
            event.preventDefault();
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
        });
    }

    global.TsBackToTop = { init };
})(typeof window !== 'undefined' ? window : globalThis);
