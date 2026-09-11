/**
 * Flipcard sor: hover (desktop) + tap/click (touch) fordítás.
 */
(function (global) {
    const SELECTOR = '.ts-flipcards';

    function prefersFineHover() {
        return global.matchMedia && global.matchMedia('(hover: hover) and (pointer: fine)').matches;
    }

    function bindCard(card) {
        if (! card || card.dataset.tsFlipBound === '1') return;
        if (card.getAttribute('data-flip') !== '1') return;

        card.dataset.tsFlipBound = '1';
        if (! card.hasAttribute('tabindex')) {
            card.setAttribute('tabindex', '0');
        }
        card.setAttribute('role', 'button');
        card.setAttribute('aria-pressed', card.classList.contains('is-flipped') ? 'true' : 'false');

        const toggle = () => {
            if (prefersFineHover()) return;
            card.classList.toggle('is-flipped');
            card.setAttribute('aria-pressed', card.classList.contains('is-flipped') ? 'true' : 'false');
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('a, button')) return;
            toggle();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            if (prefersFineHover()) {
                card.classList.toggle('is-flipped');
            } else {
                toggle();
            }
            card.setAttribute('aria-pressed', card.classList.contains('is-flipped') ? 'true' : 'false');
        });
    }

    function init(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll(`${SELECTOR} .ts-flipcard[data-flip="1"]`).forEach(bindCard);
    }

    global.TsFlipcards = { init };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => init(document));
    } else {
        init(document);
    }
})(window);
