/**
 * Hero slider – pontok, fade / slide, autoplay.
 * A tartalom a slide-ban van (grid: média + szöveg), az overlay csak a médián.
 */
(function (global) {
    const SELECTOR = '.ts-hero-slider';
    const DEFAULT_INTERVAL = 5500;

    function slidesOf(section) {
        const track = section.querySelector('[data-ts-slider-track], .ts-hero-slider__track');
        const scope = track || section;
        return [...scope.children].filter((el) => (
            el.matches?.('.ts-hero-slider__slide, [data-ts-slide]')
        ));
    }

    function destroy(section) {
        if (section._tsSliderCleanup) {
            section._tsSliderCleanup();
            section._tsSliderCleanup = null;
        }
        section.removeAttribute('data-ts-slider-ready');
    }

    function parseInterval(section) {
        const raw = section.getAttribute('data-autoplay');
        if (raw == null || String(raw).trim() === '') return DEFAULT_INTERVAL;
        if (raw === '0' || raw === 'false' || raw === 'off') return 0;
        const n = Number(raw);
        if (Number.isFinite(n)) {
            if (n <= 0) return 0;
            return n < 100 ? Math.round(n * 1000) : Math.round(n);
        }
        return DEFAULT_INTERVAL;
    }

    function stripSurfaceOverlays(section) {
        [...section.children].forEach((child) => {
            if (! (child instanceof HTMLElement)) return;
            const isSurface = child.hasAttribute('data-ts-bg-overlay')
                || child.classList.contains('ts-surface-overlay');
            const isSlideOverlay = child.hasAttribute('data-ts-hero-overlay')
                || child.classList.contains('ts-hero-slider__overlay');
            if (isSurface && ! isSlideOverlay) {
                child.remove();
            }
        });
        // Üres legacy stage – már nem használjuk
        section.querySelectorAll('[data-ts-slider-stage], .ts-hero-slider__stage').forEach((stage) => {
            if (! stage.querySelector('.ts-hero-slider__inner')) {
                stage.remove();
            }
        });
    }

    function initOne(section) {
        destroy(section);
        stripSurfaceOverlays(section);

        const track = section.querySelector('[data-ts-slider-track], .ts-hero-slider__track');
        const dotsWrap = section.querySelector('[data-ts-slider-dots], .ts-hero-slider__dots');
        const slides = slidesOf(section);
        if (! track || slides.length === 0) {
            return;
        }

        const overlay = section.getAttribute('data-overlay');
        if (overlay != null && overlay !== '') {
            section.style.setProperty('--ts-hero-overlay-opacity', String(overlay));
        }
        const overlayColor = section.getAttribute('data-overlay-color');
        if (overlayColor) {
            const solid = String(overlayColor).replace(
                /rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*[\d.]+\s*)?\)/i,
                'rgb($1, $2, $3)'
            );
            section.style.setProperty('--ts-overlay-color', solid);
        }

        let index = 0;
        let timer = null;
        let scrolling = false;
        const showDots = section.getAttribute('data-show-dots') !== '0';
        const transition = section.getAttribute('data-transition') || 'fade';
        const interval = slides.length > 1 ? parseInterval(section) : 0;
        const isSlide = transition === 'slide';

        section.setAttribute('data-transition', transition);
        track.style.transform = '';
        track.style.willChange = 'auto';

        const syncDots = () => {
            if (! dotsWrap) return;
            [...dotsWrap.querySelectorAll('.ts-hero-slider__dot')].forEach((dot, i) => {
                dot.setAttribute('aria-current', i === index ? 'true' : 'false');
            });
        };

        const syncSlides = () => {
            slides.forEach((slide, i) => {
                const active = i === index;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            });
            syncDots();
        };

        const scrollToIndex = (i, behavior) => {
            const width = track.clientWidth || section.clientWidth || 0;
            if (width <= 0) return;
            scrolling = true;
            track.scrollTo({ left: i * width, behavior: behavior || 'smooth' });
            global.setTimeout(() => { scrolling = false; }, behavior === 'auto' ? 0 : 600);
        };

        const goTo = (next, { user, instant } = {}) => {
            if (slides.length === 0) return;
            index = ((next % slides.length) + slides.length) % slides.length;

            if (isSlide) {
                scrollToIndex(index, instant ? 'auto' : 'smooth');
            } else {
                track.scrollLeft = 0;
            }

            syncSlides();

            if (user) {
                restartTimer();
            }
        };

        const stopTimer = () => {
            if (timer) {
                global.clearInterval(timer);
                timer = null;
            }
        };

        const startTimer = () => {
            stopTimer();
            if (interval <= 0 || slides.length < 2) return;
            timer = global.setInterval(() => goTo(index + 1), interval);
        };

        const restartTimer = () => {
            stopTimer();
            startTimer();
        };

        const onScroll = () => {
            if (! isSlide || scrolling) return;
            const width = track.clientWidth || 1;
            const next = Math.round(track.scrollLeft / width);
            if (next !== index && next >= 0 && next < slides.length) {
                index = next;
                syncSlides();
                restartTimer();
            }
        };

        if (dotsWrap) {
            dotsWrap.innerHTML = '';
            const shouldShow = showDots && slides.length > 1;
            dotsWrap.hidden = ! shouldShow;
            if (shouldShow) {
                slides.forEach((_, i) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'ts-hero-slider__dot';
                    btn.setAttribute('aria-label', `${i + 1}. slide`);
                    btn.addEventListener('click', () => goTo(i, { user: true }));
                    dotsWrap.appendChild(btn);
                });
            }
        }

        goTo(0, { instant: true });
        startTimer();
        section.setAttribute('data-ts-slider-ready', '1');

        const onEnter = () => stopTimer();
        const onLeave = () => startTimer();
        section.addEventListener('mouseenter', onEnter);
        section.addEventListener('mouseleave', onLeave);
        section.addEventListener('focusin', onEnter);
        section.addEventListener('focusout', onLeave);
        if (isSlide) {
            track.addEventListener('scroll', onScroll, { passive: true });
        }

        section._tsSliderCleanup = () => {
            stopTimer();
            section.removeEventListener('mouseenter', onEnter);
            section.removeEventListener('mouseleave', onLeave);
            section.removeEventListener('focusin', onEnter);
            section.removeEventListener('focusout', onLeave);
            track.removeEventListener('scroll', onScroll);
            if (dotsWrap) dotsWrap.innerHTML = '';
            track.style.transform = '';
            track.style.willChange = '';
            track.scrollLeft = 0;
        };
    }

    function init(root) {
        const scope = root && root.querySelectorAll ? root : document;
        const nodes = scope.matches?.(SELECTOR)
            ? [scope]
            : [...scope.querySelectorAll(SELECTOR)];
        nodes.forEach(initOne);
    }

    global.TsHeroSlider = { init, destroy, initOne };
})(typeof window !== 'undefined' ? window : globalThis);
