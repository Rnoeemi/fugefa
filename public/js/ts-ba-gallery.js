/**
 * Előtte-utána galéria: 3-as slider asztalin, lightbox, húzható összehasonlítás.
 */
(function (global) {
    const SELECTOR = '.ts-ba-gallery';
    let lightboxEl = null;
    let lightboxState = { items: [], index: 0, compareCleanup: null };

    function slidesOf(section) {
        const track = section.querySelector('[data-ts-ba-track], .ts-ba-gallery__track');
        const scope = track || section;
        return [...scope.querySelectorAll('[data-ts-ba-slide], .ts-ba-gallery__slide')]
            .filter((el) => ! el.classList.contains('ts-ba-gallery__slide--empty'));
    }

    function destroy(section) {
        if (section._tsBaGalleryCleanup) {
            section._tsBaGalleryCleanup();
            section._tsBaGalleryCleanup = null;
        }
        section.removeAttribute('data-ts-ba-ready');
    }

    function setSplit(compare, percent) {
        const value = Math.max(0, Math.min(100, Number(percent) || 0));
        compare.style.setProperty('--split', `${value}%`);
        const range = compare.querySelector('.ts-ba-gallery__range');
        if (range && Number(range.value) !== Math.round(value)) {
            range.value = String(Math.round(value));
        }
    }

    function percentFromPointer(compare, event) {
        const rect = compare.getBoundingClientRect();
        const point = event.touches?.[0] || event.changedTouches?.[0] || event;
        const x = (point.clientX || 0) - rect.left;
        if (rect.width <= 0) return 50;
        return (x / rect.width) * 100;
    }

    function initCompare(compare) {
        const range = compare.querySelector('.ts-ba-gallery__range');
        const initial = range ? Number(range.value) : 50;
        setSplit(compare, Number.isFinite(initial) ? initial : 50);

        const onRange = () => setSplit(compare, range.value);
        range?.addEventListener('input', onRange);
        range?.addEventListener('change', onRange);

        let dragging = false;

        const onPointerDown = (event) => {
            if (event.pointerType === 'mouse' && event.button !== 0) return;
            dragging = true;
            compare.classList.add('is-dragging');
            compare.setPointerCapture?.(event.pointerId);
            setSplit(compare, percentFromPointer(compare, event));
            event.preventDefault();
        };

        const onPointerMove = (event) => {
            if (! dragging) return;
            setSplit(compare, percentFromPointer(compare, event));
            event.preventDefault();
        };

        const onPointerUp = (event) => {
            if (! dragging) return;
            dragging = false;
            compare.classList.remove('is-dragging');
            if (event.pointerId != null) {
                compare.releasePointerCapture?.(event.pointerId);
            }
        };

        compare.addEventListener('pointerdown', onPointerDown);
        compare.addEventListener('pointermove', onPointerMove);
        compare.addEventListener('pointerup', onPointerUp);
        compare.addEventListener('pointercancel', onPointerUp);

        return () => {
            range?.removeEventListener('input', onRange);
            range?.removeEventListener('change', onRange);
            compare.removeEventListener('pointerdown', onPointerDown);
            compare.removeEventListener('pointermove', onPointerMove);
            compare.removeEventListener('pointerup', onPointerUp);
            compare.removeEventListener('pointercancel', onPointerUp);
            compare.classList.remove('is-dragging');
        };
    }

    function itemFromSlide(slide) {
        const type = slide.getAttribute('data-lightbox-type') || (slide.classList.contains('ts-ba-gallery__slide--compare') ? 'compare' : 'image');
        const alt = slide.getAttribute('data-lightbox-alt')
            || slide.querySelector('img')?.getAttribute('alt')
            || '';

        if (type === 'compare') {
            const before = slide.getAttribute('data-lightbox-before')
                || slide.querySelector('.ts-ba-gallery__layer--before img')?.getAttribute('src')
                || '';
            const after = slide.getAttribute('data-lightbox-after')
                || slide.querySelector('.ts-ba-gallery__layer--after img')?.getAttribute('src')
                || '';
            if (! before && ! after) return null;
            return { type: 'compare', before, after, alt };
        }

        const src = slide.getAttribute('data-lightbox-src')
            || slide.querySelector('.ts-ba-gallery__img')?.getAttribute('src')
            || '';
        if (! src) return null;
        return { type: 'image', src, alt };
    }

    function itemsFromSection(section) {
        return slidesOf(section).map(itemFromSlide).filter(Boolean);
    }

    function escapeAttr(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('"', '&quot;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;');
    }

    function compareInnerHtml(before, after, alt) {
        const safeAlt = escapeAttr(alt);
        return '<div class="ts-ba-gallery__layer ts-ba-gallery__layer--after">'
            + '<img src="'+escapeAttr(after)+'" alt="'+safeAlt+'">'
            + '<span class="ts-ba-gallery__tag ts-ba-gallery__tag--after">Utána</span>'
            + '</div>'
            + '<div class="ts-ba-gallery__layer ts-ba-gallery__layer--before">'
            + '<img src="'+escapeAttr(before)+'" alt="'+safeAlt+'">'
            + '<span class="ts-ba-gallery__tag ts-ba-gallery__tag--before">Előtte</span>'
            + '</div>'
            + '<div class="ts-ba-gallery__handle" aria-hidden="true"><span class="ts-ba-gallery__knob">‹ ›</span></div>'
            + '<input type="range" class="ts-ba-gallery__range" min="0" max="100" value="50" aria-label="Előtte és utána összehasonlítás">';
    }

    function ensureLightbox() {
        if (lightboxEl) return lightboxEl;

        const root = document.createElement('div');
        root.className = 'ts-ba-gallery-lightbox';
        root.hidden = true;
        root.setAttribute('role', 'dialog');
        root.setAttribute('aria-modal', 'true');
        root.setAttribute('aria-label', 'Kép nagyítása');
        root.innerHTML =
            '<button type="button" class="ts-ba-gallery-lightbox__close" aria-label="Bezárás">×</button>'
            + '<button type="button" class="ts-ba-gallery-lightbox__prev" aria-label="Előző kép">‹</button>'
            + '<button type="button" class="ts-ba-gallery-lightbox__next" aria-label="Következő kép">›</button>'
            + '<div class="ts-ba-gallery-lightbox__stage"></div>';

        document.body.appendChild(root);
        lightboxEl = root;

        root.querySelector('.ts-ba-gallery-lightbox__close')?.addEventListener('click', closeLightbox);
        root.querySelector('.ts-ba-gallery-lightbox__prev')?.addEventListener('click', () => stepLightbox(-1));
        root.querySelector('.ts-ba-gallery-lightbox__next')?.addEventListener('click', () => stepLightbox(1));
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

    function renderLightbox() {
        const box = ensureLightbox();
        const item = lightboxState.items[lightboxState.index];
        const stage = box.querySelector('.ts-ba-gallery-lightbox__stage');
        const prev = box.querySelector('.ts-ba-gallery-lightbox__prev');
        const next = box.querySelector('.ts-ba-gallery-lightbox__next');

        if (lightboxState.compareCleanup) {
            lightboxState.compareCleanup();
            lightboxState.compareCleanup = null;
        }

        if (! item || ! stage) return;

        if (item.type === 'compare') {
            const compare = document.createElement('div');
            compare.className = 'ts-ba-gallery__compare';
            compare.style.setProperty('--split', '50%');
            compare.innerHTML = compareInnerHtml(item.before, item.after, item.alt);
            stage.replaceChildren(compare);
            lightboxState.compareCleanup = initCompare(compare);
        } else {
            const frame = document.createElement('div');
            frame.className = 'ts-ba-gallery-lightbox__frame';
            const img = document.createElement('img');
            img.className = 'ts-ba-gallery-lightbox__img';
            img.src = item.src;
            img.alt = item.alt || '';
            frame.appendChild(img);
            stage.replaceChildren(frame);
        }

        if (prev) prev.hidden = lightboxState.items.length <= 1;
        if (next) next.hidden = lightboxState.items.length <= 1;
    }

    function openLightbox(section, index) {
        const items = itemsFromSection(section);
        if (! items.length) return;

        lightboxState.items = items;
        lightboxState.index = Math.max(0, Math.min(index, items.length - 1));

        const box = ensureLightbox();
        renderLightbox();
        box.hidden = false;
        document.body.classList.add('ts-ba-gallery-lightbox-open');
        box.querySelector('.ts-ba-gallery-lightbox__close')?.focus();
    }

    function closeLightbox() {
        if (! lightboxEl) return;
        lightboxEl.hidden = true;
        document.body.classList.remove('ts-ba-gallery-lightbox-open');
        if (lightboxState.compareCleanup) {
            lightboxState.compareCleanup();
            lightboxState.compareCleanup = null;
        }
        const stage = lightboxEl.querySelector('.ts-ba-gallery-lightbox__stage');
        if (stage) stage.replaceChildren();
    }

    function stepLightbox(delta) {
        if (! lightboxState.items.length) return;
        lightboxState.index = (lightboxState.index + delta + lightboxState.items.length) % lightboxState.items.length;
        renderLightbox();
    }

    function bindLightbox(section) {
        if (section._tsBaLightboxBound) return;
        section._tsBaLightboxBound = true;

        section.addEventListener('click', (event) => {
            const trigger = event.target.closest?.('[data-ts-ba-zoom]');
            if (! trigger || ! section.contains(trigger)) return;

            event.preventDefault();
            event.stopPropagation();

            const slide = trigger.closest('[data-ts-ba-slide], .ts-ba-gallery__slide');
            const slides = slidesOf(section);
            const index = slide ? slides.indexOf(slide) : -1;
            if (index >= 0) openLightbox(section, index);
        });
    }

    function initSlide(section) {
        const track = section.querySelector('[data-ts-ba-track], .ts-ba-gallery__track');
        const prev = section.querySelector('[data-ts-ba-prev]');
        const next = section.querySelector('[data-ts-ba-next]');
        const dotsWrap = section.querySelector('[data-ts-ba-dots], .ts-ba-gallery__dots');
        const slides = slidesOf(section);

        if (! track || slides.length === 0) {
            if (prev) prev.hidden = true;
            if (next) next.hidden = true;
            if (dotsWrap) {
                dotsWrap.innerHTML = '';
                dotsWrap.hidden = true;
            }
            return () => {};
        }

        let index = 0;
        let scrolling = false;

        const stepSize = () => {
            const first = slides[0];
            if (! first) return track.clientWidth || 1;
            const styles = global.getComputedStyle(track);
            const gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
            return first.offsetWidth + gap;
        };

        const visibleCount = () => {
            const step = stepSize();
            if (step <= 0) return 1;
            const gap = parseFloat(global.getComputedStyle(track).gap || '0') || 0;
            return Math.max(1, Math.round((track.clientWidth + gap) / step));
        };

        const syncNav = () => {
            const showNav = slides.length > visibleCount();
            if (prev) prev.hidden = ! showNav;
            if (next) next.hidden = ! showNav;
            if (dotsWrap) dotsWrap.hidden = ! showNav;
        };

        const syncDots = () => {
            if (! dotsWrap) return;
            [...dotsWrap.querySelectorAll('.ts-ba-gallery__dot')].forEach((dot, i) => {
                dot.setAttribute('aria-current', i === index ? 'true' : 'false');
            });
        };

        const scrollToIndex = (i, behavior) => {
            const step = stepSize();
            if (step <= 0) return;
            scrolling = true;
            track.scrollTo({ left: i * step, behavior: behavior || 'smooth' });
            global.setTimeout(() => { scrolling = false; }, behavior === 'auto' ? 0 : 450);
        };

        const syncFromScroll = () => {
            if (scrolling) return;
            const step = stepSize();
            index = Math.round(track.scrollLeft / step);
            index = Math.max(0, Math.min(index, slides.length - 1));
            syncDots();
        };

        const go = (delta) => {
            const maxIndex = Math.max(0, slides.length - visibleCount());
            index = Math.max(0, Math.min(index + delta, Math.max(maxIndex, slides.length - 1)));
            scrollToIndex(index, 'smooth');
            syncDots();
        };

        const goTo = (i) => {
            index = Math.max(0, Math.min(i, slides.length - 1));
            scrollToIndex(index, 'smooth');
            syncDots();
        };

        if (dotsWrap) {
            dotsWrap.innerHTML = '';
            slides.forEach((_, i) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'ts-ba-gallery__dot';
                dot.setAttribute('aria-label', `${i + 1}. kép`);
                dot.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    goTo(i);
                });
                dotsWrap.appendChild(dot);
            });
        }

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
        track.addEventListener('scroll', onScroll, { passive: true });

        const onResize = () => {
            syncNav();
            scrollToIndex(index, 'auto');
        };
        global.addEventListener('resize', onResize);

        syncNav();
        syncDots();
        scrollToIndex(0, 'auto');

        return () => {
            prev?.removeEventListener('click', onPrev);
            next?.removeEventListener('click', onNext);
            track.removeEventListener('scroll', onScroll);
            global.removeEventListener('resize', onResize);
        };
    }

    function initOne(section) {
        if (! (section instanceof HTMLElement)) return;
        destroy(section);

        const compares = [...section.querySelectorAll('.ts-ba-gallery__compare')];
        const compareCleanups = compares.map((node) => initCompare(node));
        const slideCleanup = initSlide(section);
        bindLightbox(section);

        section.setAttribute('data-ts-ba-ready', '1');
        section._tsBaGalleryCleanup = () => {
            slideCleanup?.();
            compareCleanups.forEach((fn) => fn?.());
        };
    }

    function init(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll(SELECTOR).forEach((section) => {
            initOne(section);
        });
    }

    global.TsBaGallery = { init, initOne, closeLightbox };
}(window));
