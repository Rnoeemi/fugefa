/**
 * Képsáv: slide váltás + lightbox (teljes méret, viewport-on belül).
 */
(function (global) {
    const SELECTOR = '.ts-gallery';
    let lightboxEl = null;
    let lightboxState = { items: [], index: 0, section: null };

    function ensureLightbox() {
        if (lightboxEl) return lightboxEl;

        const root = document.createElement('div');
        root.className = 'ts-gallery-lightbox';
        root.hidden = true;
        root.setAttribute('role', 'dialog');
        root.setAttribute('aria-modal', 'true');
        root.setAttribute('aria-label', 'Kép nagyítása');
        root.innerHTML =
            '<button type="button" class="ts-gallery-lightbox__close" aria-label="Bezárás">×</button>'
            + '<button type="button" class="ts-gallery-lightbox__prev" aria-label="Előző kép">‹</button>'
            + '<button type="button" class="ts-gallery-lightbox__next" aria-label="Következő kép">›</button>'
            + '<figure class="ts-gallery-lightbox__figure">'
            + '<img class="ts-gallery-lightbox__img" src="" alt="">'
            + '<figcaption class="ts-gallery-lightbox__caption"></figcaption>'
            + '</figure>';

        document.body.appendChild(root);
        lightboxEl = root;

        root.querySelector('.ts-gallery-lightbox__close')?.addEventListener('click', closeLightbox);
        root.querySelector('.ts-gallery-lightbox__prev')?.addEventListener('click', () => stepLightbox(-1));
        root.querySelector('.ts-gallery-lightbox__next')?.addEventListener('click', () => stepLightbox(1));
        root.addEventListener('click', (event) => {
            if (event.target === root) closeLightbox();
        });

        document.addEventListener('keydown', (event) => {
            if (! lightboxEl || lightboxEl.hidden) return;
            if (event.key === 'Escape') closeLightbox();
            if (event.key === 'ArrowLeft') stepLightbox(-1);
            if (event.key === 'ArrowRight') stepLightbox(1);
        });

        return root;
    }

    function itemsFromSection(section) {
        return [...section.querySelectorAll('.ts-gallery__trigger')].map((btn) => ({
            src: btn.getAttribute('data-ts-gallery-src')
                || btn.querySelector('img')?.getAttribute('src')
                || '',
            alt: btn.querySelector('img')?.getAttribute('alt') || '',
        })).filter((item) => item.src);
    }

    function renderLightbox() {
        const box = ensureLightbox();
        const item = lightboxState.items[lightboxState.index];
        if (! item) return;

        const img = box.querySelector('.ts-gallery-lightbox__img');
        const caption = box.querySelector('.ts-gallery-lightbox__caption');
        const prev = box.querySelector('.ts-gallery-lightbox__prev');
        const next = box.querySelector('.ts-gallery-lightbox__next');

        if (img) {
            img.src = item.src;
            img.alt = item.alt || '';
        }
        if (caption) {
            caption.textContent = item.alt || '';
            caption.hidden = ! item.alt;
        }
        if (prev) prev.hidden = lightboxState.items.length <= 1;
        if (next) next.hidden = lightboxState.items.length <= 1;
    }

    function openLightbox(section, index) {
        const items = itemsFromSection(section);
        if (! items.length) return;

        lightboxState = {
            items,
            index: Math.max(0, Math.min(index, items.length - 1)),
            section,
        };

        const box = ensureLightbox();
        renderLightbox();
        box.hidden = false;
        document.body.classList.add('ts-gallery-lightbox-open');
        box.querySelector('.ts-gallery-lightbox__close')?.focus();
    }

    function closeLightbox() {
        if (! lightboxEl) return;
        lightboxEl.hidden = true;
        document.body.classList.remove('ts-gallery-lightbox-open');
        const img = lightboxEl.querySelector('.ts-gallery-lightbox__img');
        if (img) img.removeAttribute('src');
    }

    function stepLightbox(delta) {
        if (! lightboxState.items.length) return;
        lightboxState.index = (lightboxState.index + delta + lightboxState.items.length) % lightboxState.items.length;
        renderLightbox();
    }

    function bindLightbox(section) {
        if (section._tsGalleryLightboxBound) return;
        section._tsGalleryLightboxBound = true;

        section.addEventListener('click', (event) => {
            const trigger = event.target.closest?.('.ts-gallery__trigger');
            if (! trigger || ! section.contains(trigger)) return;

            event.preventDefault();
            event.stopPropagation();

            const triggers = [...section.querySelectorAll('.ts-gallery__trigger')];
            const index = triggers.indexOf(trigger);
            if (index >= 0) openLightbox(section, index);
        });
    }

    function destroySlide(section) {
        if (section._tsGallerySlideCleanup) {
            section._tsGallerySlideCleanup();
            section._tsGallerySlideCleanup = null;
        }
    }

    function initSlide(section) {
        destroySlide(section);

        const layout = section.getAttribute('data-layout') || 'grid';
        if (layout !== 'slide') return;

        const grid = section.querySelector('.ts-gallery__grid');
        const prev = section.querySelector('[data-ts-gallery-prev]');
        const next = section.querySelector('[data-ts-gallery-next]');
        const items = grid ? [...grid.querySelectorAll('.ts-gallery__item:not(.ts-gallery__item--empty)')] : [];

        if (! grid || items.length === 0) {
            if (prev) prev.hidden = true;
            if (next) next.hidden = true;
            return;
        }

        let index = 0;
        let scrolling = false;

        const stepSize = () => {
            const first = items[0];
            if (! first) return grid.clientWidth || 1;
            const styles = global.getComputedStyle(grid);
            const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
            return first.offsetWidth + gap;
        };

        const visibleCount = () => {
            const step = stepSize();
            if (step <= 0) return 1;
            return Math.max(1, Math.floor((grid.clientWidth + (parseFloat(global.getComputedStyle(grid).gap || '0') || 0)) / step));
        };

        const syncNav = () => {
            const showNav = items.length > visibleCount();
            if (prev) prev.hidden = ! showNav;
            if (next) next.hidden = ! showNav;
        };

        const scrollToIndex = (i, behavior) => {
            const step = stepSize();
            if (step <= 0) return;
            scrolling = true;
            grid.scrollTo({ left: i * step, behavior: behavior || 'smooth' });
            global.setTimeout(() => { scrolling = false; }, behavior === 'auto' ? 0 : 450);
        };

        const syncFromScroll = () => {
            if (scrolling) return;
            const step = stepSize();
            index = Math.round(grid.scrollLeft / step);
            index = Math.max(0, Math.min(index, items.length - 1));
        };

        const go = (delta) => {
            const maxIndex = Math.max(0, items.length - 1);
            index = Math.max(0, Math.min(index + delta, maxIndex));
            scrollToIndex(index, 'smooth');
        };

        const onPrev = (event) => {
            event.preventDefault();
            event.stopPropagation();
            go(-1);
        };
        const onNext = (event) => {
            event.preventDefault();
            event.stopPropagation();
            go(1);
        };

        prev?.addEventListener('click', onPrev);
        next?.addEventListener('click', onNext);

        const onScroll = () => {
            global.requestAnimationFrame(syncFromScroll);
        };
        grid.addEventListener('scroll', onScroll, { passive: true });

        const onResize = () => syncNav();
        global.addEventListener('resize', onResize);

        syncNav();
        scrollToIndex(0, 'auto');

        section._tsGallerySlideCleanup = () => {
            prev?.removeEventListener('click', onPrev);
            next?.removeEventListener('click', onNext);
            grid.removeEventListener('scroll', onScroll);
            global.removeEventListener('resize', onResize);
        };
    }

    function initOne(section) {
        if (! (section instanceof HTMLElement)) return;
        bindLightbox(section);
        initSlide(section);
        section.setAttribute('data-ts-gallery-ready', '1');
    }

    function init(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll(SELECTOR).forEach((section) => {
            initOne(section);
        });
    }

    global.TsGallery = { init, initOne, closeLightbox };
}(window));
