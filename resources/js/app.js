document.addEventListener('DOMContentLoaded', () => {
    const navRoot = document.querySelector('[data-site-nav]');

    if (navRoot) {
        const onScroll = () => {
            const solid = window.scrollY > 40;
            navRoot.classList.toggle('is-solid', solid);
            navRoot.classList.toggle('is-transparent', !solid);
        };

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        const mobileToggle = navRoot.querySelector('[data-nav-toggle]');
        const mobilePanel = navRoot.querySelector('[data-nav-panel]');
        const mobileOverlay = navRoot.querySelector('[data-nav-overlay]');

        if (mobileToggle && mobilePanel) {
            const panelClose = navRoot.querySelector('[data-nav-close]');
            mobilePanel.removeAttribute('hidden');

            const validMobileStyles = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen'];
            const rawMenuStyle = navRoot.getAttribute('data-mobile-menu-style') || 'dropdown';
            const menuStyle = validMobileStyles.find((s) => rawMenuStyle.startsWith(s)) || 'dropdown';
            if (rawMenuStyle !== menuStyle) {
                navRoot.setAttribute('data-mobile-menu-style', menuStyle);
            }
            const usesOverlay = menuStyle !== 'dropdown';

            const setOpen = (open) => {
                navRoot.classList.toggle('is-menu-open', open);
                mobilePanel.classList.toggle('is-open', open);
                mobilePanel.removeAttribute('hidden');
                mobilePanel.setAttribute('aria-hidden', open ? 'false' : 'true');
                mobileToggle.classList.toggle('is-open', open);
                mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                mobileToggle.setAttribute('aria-label', open ? 'Menü bezárása' : 'Menü megnyitása');
                document.body.classList.toggle('ts-nav-open', open && usesOverlay);

                if (mobileOverlay) {
                    mobileOverlay.classList.toggle('is-open', open && usesOverlay);
                    mobileOverlay.hidden = !(open && usesOverlay);
                    mobileOverlay.setAttribute('aria-hidden', open && usesOverlay ? 'false' : 'true');
                }
            };

            setOpen(false);

            mobileToggle.addEventListener('click', () => {
                setOpen(!navRoot.classList.contains('is-menu-open'));
            });

            panelClose?.addEventListener('click', () => setOpen(false));
            mobileOverlay?.addEventListener('click', () => setOpen(false));

            mobilePanel.querySelectorAll('a[href]').forEach((link) => {
                link.addEventListener('click', () => setOpen(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && navRoot.classList.contains('is-menu-open')) {
                    setOpen(false);
                    mobileToggle.focus();
                }
            });
        }
    }

    const revealItems = document.querySelectorAll('.reveal');

    if (revealItems.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.16 },
        );

        revealItems.forEach((el) => observer.observe(el));
    } else {
        revealItems.forEach((el) => el.classList.add('is-visible'));
    }

    initBookingCalendar();
    initPriceQuote();
});

function formatHuf(value) {
    return new Intl.NumberFormat('hu-HU').format(Math.round(Number(value || 0))) + ' Ft';
}

function initPriceQuote() {
    const box = document.querySelector('[data-price-quote]');
    const accommodationSelect = document.querySelector('[data-accommodation-select]');
    const checkInInput = document.querySelector('[name="check_in"]');
    const checkOutInput = document.querySelector('[name="check_out"]');
    const guestsInput = document.querySelector('[data-guests-count], [name="guests_count"]');

    if (! box || ! accommodationSelect || ! checkInInput || ! checkOutInput || ! guestsInput) {
        return;
    }

    const placeholder = box.querySelector('[data-price-placeholder]');
    const details = box.querySelector('[data-price-details]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

    let timer = null;

    const refresh = () => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const accommodationId = accommodationSelect.value;
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;
            const guestsCount = guestsInput.value || 1;

            if (! accommodationId || ! checkIn || ! checkOut) {
                placeholder?.classList.remove('hidden');
                details?.classList.add('hidden');
                return;
            }

            try {
                const response = await fetch('/foglalas/arajanlat', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                    body: JSON.stringify({
                        accommodation_id: Number(accommodationId),
                        check_in: checkIn,
                        check_out: checkOut,
                        guests_count: Number(guestsCount),
                    }),
                });

                if (! response.ok) {
                    placeholder.textContent = 'Az árajánlat jelenleg nem számolható.';
                    placeholder?.classList.remove('hidden');
                    details?.classList.add('hidden');
                    return;
                }

                const data = await response.json();
                placeholder?.classList.add('hidden');
                details?.classList.remove('hidden');
                box.querySelector('[data-price-nights]').textContent = data.nights;
                box.querySelector('[data-price-min]').textContent = data.min_nights;
                box.querySelector('[data-price-acc]').textContent = formatHuf(data.accommodation_total);
                box.querySelector('[data-price-ifa]').textContent = formatHuf(data.ifa_total);
                box.querySelector('[data-price-total]').textContent = formatHuf(data.total);
            } catch (e) {
                placeholder.textContent = 'Az árajánlat jelenleg nem számolható.';
                placeholder?.classList.remove('hidden');
                details?.classList.add('hidden');
            }
        }, 250);
    };

    window.__refreshBookingQuote = refresh;

    [accommodationSelect, checkInInput, checkOutInput, guestsInput].forEach((el) => {
        el.addEventListener('change', refresh);
        el.addEventListener('input', refresh);
    });

    refresh();
}

function initBookingCalendar() {
    const root = document.querySelector('[data-booking-calendar]');

    if (! root) {
        return;
    }

    const accommodationSelect = document.querySelector('[data-accommodation-select]');
    const checkInInput = document.querySelector('[name="check_in"]');
    const checkOutInput = document.querySelector('[name="check_out"]');
    const monthLabel = root.querySelector('[data-month-label]');
    const grid = root.querySelector('[data-cal-grid]');
    const prevBtn = root.querySelector('[data-cal-prev]');
    const nextBtn = root.querySelector('[data-cal-next]');

    if (! accommodationSelect || ! checkInInput || ! checkOutInput || ! grid || ! monthLabel) {
        return;
    }

    let cursor = new Date();
    cursor.setDate(1);
    let occupied = new Set();
    let selecting = 'in';

    const pad = (n) => String(n).padStart(2, '0');
    const toIso = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

    const parseIso = (value) => {
        if (! value) {
            return null;
        }

        const [y, m, d] = value.split('-').map(Number);

        return new Date(y, m - 1, d);
    };

    async function loadOccupied() {
        const slug = accommodationSelect.selectedOptions[0]?.dataset?.slug;

        occupied = new Set();

        if (! slug) {
            render();
            return;
        }

        const from = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
        const to = new Date(cursor.getFullYear(), cursor.getMonth() + 2, 0);
        const url = `/szallasok/${slug}/availability?from=${toIso(from)}&to=${toIso(to)}`;

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (! response.ok) {
                render();
                return;
            }

            const data = await response.json();
            occupied = new Set((data.occupied || []).map((item) => item.date));
        } catch (e) {
            // silent
        }

        render();
    }

    function render() {
        const year = cursor.getFullYear();
        const month = cursor.getMonth();
        monthLabel.textContent = cursor.toLocaleDateString('hu-HU', { year: 'numeric', month: 'long' });

        const firstDay = new Date(year, month, 1);
        const startOffset = (firstDay.getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const checkIn = parseIso(checkInInput.value);
        const checkOut = parseIso(checkOutInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        grid.innerHTML = '';

        for (let i = 0; i < startOffset; i++) {
            const cell = document.createElement('div');
            cell.className = 'cal-day is-muted';
            grid.appendChild(cell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const iso = toIso(date);
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.textContent = String(day);
            cell.className = 'cal-day';

            const isPast = date < today;
            const isOccupied = occupied.has(iso);

            if (isPast || isOccupied) {
                cell.classList.add(isOccupied ? 'is-occupied' : 'is-muted');
                cell.disabled = true;
            } else {
                cell.classList.add('is-available');
            }

            if (checkIn && toIso(checkIn) === iso) {
                cell.classList.add('is-selected');
            }

            if (checkOut && toIso(checkOut) === iso) {
                cell.classList.add('is-selected');
            }

            if (checkIn && checkOut && date > checkIn && date < checkOut) {
                cell.classList.add('is-in-range');
            }

            cell.addEventListener('click', () => {
                if (cell.disabled) {
                    return;
                }

                if (selecting === 'in' || ! checkIn || date <= checkIn) {
                    checkInInput.value = iso;
                    checkOutInput.value = '';
                    selecting = 'out';
                } else {
                    checkOutInput.value = iso;
                    selecting = 'in';
                }

                render();
                window.__refreshBookingQuote?.();
            });

            grid.appendChild(cell);
        }
    }

    prevBtn?.addEventListener('click', () => {
        cursor.setMonth(cursor.getMonth() - 1);
        loadOccupied();
    });

    nextBtn?.addEventListener('click', () => {
        cursor.setMonth(cursor.getMonth() + 1);
        loadOccupied();
    });

    accommodationSelect.addEventListener('change', () => {
        loadOccupied();
        window.__refreshBookingQuote?.();
    });

    loadOccupied();
}
