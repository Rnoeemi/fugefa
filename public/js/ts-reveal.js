/**
 * Szekció szintű belépési animáció: gyerek elemek (cím, kártya stb.) egymás után jelennek meg.
 */
(function (global) {
    const STAGGER_MS = 80;
    const ROOT_MARGIN = '0px 0px -8% 0px';
    const THRESHOLD = 0.12;

    const TARGET_SELECTOR = [
        '[class*="__eyebrow"]',
        '[class*="__title"]:not([class*="__subtitle"])',
        'h1',
        'h2',
        'h3',
        '[class*="__lead"]',
        '[class*="__text"]:not([class*="__context"])',
        '[class*="__body"]',
        '[class*="__note"]',
        '[class*="__actions"]',
        '[class*="__row"]:not([class*="__arrow"])',
        '.ts-feature',
        '.ts-icon-item',
        '.ts-faq__item',
        '.ts-gallery__item:not(.ts-gallery__item--empty)',
        '.ts-quote__card',
        '.ts-checkin__grid > article',
        '.ts-steps__grid > li',
        '.ts-amenities__row > *',
        '.ts-nearby__grid > *',
        '.ts-pricing__grid > *',
        '.ts-pricing__card',
        '.ts-dyn-card',
        '.ts-dyn-list__row',
        '.ts-dyn-appointment__card',
        '.ts-split__media',
        '.ts-split__content',
        '.ts-map__embed',
        '.ts-video__embed',
        '.ts-contact__row',
        '.ts-buttons__grid > *',
        '.ts-stats__item',
        '.ts-stats__grid > *',
        '.ts-logo-row__item',
        '.ts-layout__col > section',
        '.ts-layout__col > article',
        '.ts-layout__col > div:not(.ts-layout__placeholder)',
    ].join(',');

    const SKIP_SELECTOR = [
        '.ts-hero__media',
        '.ts-hero__overlay',
        '.ts-hero__video',
        '.ts-hero__img',
        '.ts-surface-overlay',
        '[data-ts-bg-overlay]',
        '[data-ts-hero-overlay]',
        '.ts-gallery__nav',
        '.ts-layout__placeholder',
        '.ts-hero-slider__dots',
        '.ts-hero-slider__media',
        '.ts-hero-slider__track',
    ].join(',');

    function shouldSkip(el) {
        if (! el || ! (el instanceof Element)) return true;
        if (el.matches(SKIP_SELECTOR)) return true;
        return !! el.closest(SKIP_SELECTOR);
    }

    function viewFor(el) {
        return el?.ownerDocument?.defaultView || global;
    }

    function scopeView(scope) {
        return scope?.defaultView || global;
    }

    function isVisible(el) {
        if (! el || el.closest('[hidden]')) return false;
        if (shouldSkip(el)) return false;
        if (el.getAttribute('aria-hidden') === 'true') return false;
        const style = viewFor(el).getComputedStyle(el);
        if (style.display === 'none' || style.visibility === 'hidden') return false;
        return true;
    }

    function collectTargets(section) {
        const nodes = [...section.querySelectorAll(TARGET_SELECTOR)].filter(isVisible);

        return nodes.filter((node) => {
            let parent = node.parentElement;
            while (parent && parent !== section) {
                if (node !== parent && parent.matches(TARGET_SELECTOR)) {
                    return false;
                }
                parent = parent.parentElement;
            }
            return true;
        });
    }

    function clearTargets(section) {
        section.querySelectorAll('.ts-reveal-item').forEach((el) => {
            el.classList.remove('ts-reveal-item');
            el.style.removeProperty('--ts-reveal-i');
        });
    }

    function prepareSection(section, { forceVisible = false } = {}) {
        if (! section || ! (section instanceof Element)) return;

        if (section.getAttribute('data-reveal-children') !== '1') {
            section.classList.remove('is-ts-revealed');
            clearTargets(section);
            return;
        }

        clearTargets(section);
        const targets = collectTargets(section);
        targets.forEach((el, index) => {
            el.classList.add('ts-reveal-item');
            el.style.setProperty('--ts-reveal-i', String(index));
        });

        if (forceVisible) {
            section.classList.add('is-ts-revealed');
        }
    }

    function init(root, { forceVisible = false } = {}) {
        const scope = root && root.querySelectorAll ? root : global.document;
        const view = scopeView(scope);
        const sections = scope.querySelectorAll('[data-reveal-children="1"]');
        if (! sections.length) return;

        sections.forEach((section) => prepareSection(section, { forceVisible }));

        if (forceVisible || ! ('IntersectionObserver' in view)) {
            sections.forEach((section) => section.classList.add('is-ts-revealed'));
            return;
        }

        const observer = new view.IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) return;
                entry.target.classList.add('is-ts-revealed');
                observer.unobserve(entry.target);
            });
        }, { rootMargin: ROOT_MARGIN, threshold: THRESHOLD });

        sections.forEach((section) => {
            if (! section.classList.contains('is-ts-revealed')) {
                observer.observe(section);
            }
        });
    }

    function initOne(el, options = {}) {
        if (! el) return;
        const view = viewFor(el);
        if (el.getAttribute('data-reveal-children') === '1') {
            prepareSection(el, options);
            if (! options.forceVisible && 'IntersectionObserver' in view) {
                const observer = new view.IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (! entry.isIntersecting) return;
                        entry.target.classList.add('is-ts-revealed');
                        observer.unobserve(entry.target);
                    });
                }, { rootMargin: ROOT_MARGIN, threshold: THRESHOLD });
                if (! el.classList.contains('is-ts-revealed')) {
                    observer.observe(el);
                }
            } else {
                el.classList.add('is-ts-revealed');
            }
            return;
        }
        init(el, options);
    }

    global.TsReveal = {
        init,
        initOne,
        prepareSection,
        staggerMs: STAGGER_MS,
    };
})(typeof window !== 'undefined' ? window : globalThis);
