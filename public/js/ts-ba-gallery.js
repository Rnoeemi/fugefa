/**
 * Munkáink: referenciánkénti szekció, lightbox (sima kép vagy előtte–utána).
 */
(function (global) {
    const SELECTOR = '.ts-ba-gallery';
    let lightboxEl = null;
    let lightboxState = { items: [], index: 0, compareCleanup: null };

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

    function itemFromLegacySlide(slide) {
        const type = slide.getAttribute('data-lightbox-type')
            || (slide.classList.contains('ts-ba-gallery__slide--compare') ? 'compare' : 'image');
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
            || slide.querySelector('.ts-ba-gallery__photo, .ts-ba-gallery__img')?.getAttribute('src')
            || '';
        if (! src) return null;
        return { type: 'image', src, alt };
    }

    function itemsFromWork(work) {
        const raw = work.getAttribute('data-lightbox-items');
        if (raw) {
            try {
                let text = String(raw).trim();
                if (text.includes('&quot;') || text.includes('&#39;') || text.includes('&amp;')) {
                    const ta = document.createElement('textarea');
                    ta.innerHTML = text;
                    text = ta.value;
                }
                const parsed = JSON.parse(text);
                if (Array.isArray(parsed) && parsed.length) {
                    return parsed.filter(Boolean);
                }
            } catch {
                // fall through to legacy attrs
            }
        }

        const one = itemFromLegacySlide(work);
        return one ? [one] : [];
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

    function openLightbox(items) {
        if (! items.length) return;

        lightboxState.items = items;
        lightboxState.index = 0;

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

            const work = trigger.closest('[data-ts-ba-slide], .ts-ba-gallery__work, .ts-ba-gallery__slide');
            if (! work) return;
            openLightbox(itemsFromWork(work));
        });
    }

    function initOne(section) {
        if (! (section instanceof HTMLElement)) return;
        destroy(section);
        bindLightbox(section);
        section.setAttribute('data-ts-ba-ready', '1');
        section._tsBaGalleryCleanup = () => {};
    }

    function init(root) {
        const scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll(SELECTOR).forEach((section) => {
            initOne(section);
        });
    }

    global.TsBaGallery = { init, initOne, closeLightbox };
}(window));
