const DAY_STATES = {
    past: 'is-past',
    free: 'is-free',
    occupied: 'is-occupied',
    'turnover-out': 'is-turnover-out',
    'turnover-in': 'is-turnover-in',
    collision: 'is-collision',
};

const pad = (value) => String(value).padStart(2, '0');

const toIso = (date) => `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;

const parseIso = (value) => {
    if (! value) {
        return null;
    }

    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day);
};

const addDays = (iso, count) => {
    const date = parseIso(iso);

    date.setDate(date.getDate() + count);

    return toIso(date);
};

const compareIso = (left, right) => {
    if (! left || ! right) {
        return 0;
    }

    return left.localeCompare(right);
};

const isRangeValid = (checkIn, checkOut, dayStates) => {
    let cursor = addDays(checkIn, 1);

    while (compareIso(cursor, checkOut) < 0) {
        const state = dayStates[cursor];

        if (state === 'occupied' || state === 'collision') {
            return false;
        }

        cursor = addDays(cursor, 1);
    }

    return true;
};

const canSelectDay = (iso, state, mode, checkIn, dayStates) => {
    if (! state || state === 'past' || state === 'occupied' || state === 'collision') {
        return false;
    }

    if (mode === 'in') {
        if (state === 'turnover-out') {
            return false;
        }

        return state === 'free' || state === 'turnover-in';
    }

    // Érkezés után: korábbi / ugyanaz a nap újraérkezésként választható (hiba javításához).
    if (checkIn && compareIso(iso, checkIn) <= 0) {
        if (state === 'turnover-out') {
            return false;
        }

        return state === 'free' || state === 'turnover-in';
    }

    if (state === 'turnover-in') {
        return false;
    }

    if (state !== 'free' && state !== 'turnover-out') {
        return false;
    }

    return isRangeValid(checkIn, iso, dayStates);
};

document.addEventListener('alpine:init', () => {
    Alpine.data('guestBookingCalendar', () => ({
        accommodationId: null,
        checkIn: null,
        checkOut: null,
        selecting: 'in',
        visibleMonth: new Date(),
        dayStates: {},
        cells: [],
        monthLabel: '',
        loading: false,

        init() {
            this.syncFromWire();
            this.render();
            this.loadDayStates();

            this.$wire.$watch('data.accommodation_id', (value) => {
                this.accommodationId = value || null;
                this.loadDayStates();
            });

            this.$wire.$watch('data.check_in', (value) => {
                this.checkIn = value || null;
                this.selecting = this.checkIn && ! this.checkOut ? 'out' : 'in';
                this.render();
            });

            this.$wire.$watch('data.check_out', (value) => {
                this.checkOut = value || null;
                this.selecting = this.checkIn && ! this.checkOut ? 'out' : 'in';
                this.render();
            });
        },

        syncFromWire() {
            this.accommodationId = this.$wire.data?.accommodation_id || null;
            this.checkIn = this.$wire.data?.check_in || null;
            this.checkOut = this.$wire.data?.check_out || null;
            this.selecting = this.checkIn && ! this.checkOut ? 'out' : 'in';
        },

        onAccommodationChanged() {
            this.clearDates();
            this.loadDayStates();
        },

        clearDates() {
            this.checkIn = null;
            this.checkOut = null;
            this.selecting = 'in';
            this.$wire.set('data.check_in', null);
            this.$wire.set('data.check_out', null);
            this.render();
        },

        async loadDayStates() {
            if (! this.accommodationId) {
                this.dayStates = {};
                this.render();

                return;
            }

            const from = new Date(this.visibleMonth.getFullYear(), this.visibleMonth.getMonth(), 1);
            const to = new Date(this.visibleMonth.getFullYear(), this.visibleMonth.getMonth() + 2, 0);

            this.loading = true;

            try {
                this.dayStates = await this.$wire.calendarDayStates(
                    Number(this.accommodationId),
                    toIso(from),
                    toIso(to),
                );
            } catch (error) {
                this.dayStates = {};
            }

            this.loading = false;
            this.render();
        },

        previousMonth() {
            this.visibleMonth = new Date(this.visibleMonth.getFullYear(), this.visibleMonth.getMonth() - 1, 1);
            this.loadDayStates();
        },

        nextMonth() {
            this.visibleMonth = new Date(this.visibleMonth.getFullYear(), this.visibleMonth.getMonth() + 1, 1);
            this.loadDayStates();
        },

        formatDisplay(value) {
            if (! value) {
                return '–';
            }

            const date = parseIso(value);

            return date.toLocaleDateString('hu-HU', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            });
        },

        selectDay(cell) {
            if (! cell.iso || cell.disabled) {
                return;
            }

            if (this.selecting === 'in' || ! this.checkIn || compareIso(cell.iso, this.checkIn) <= 0) {
                this.checkIn = cell.iso;
                this.checkOut = null;
                this.selecting = 'out';
                this.$wire.set('data.check_in', cell.iso);
                this.$wire.set('data.check_out', null);
            } else {
                this.checkOut = cell.iso;
                this.selecting = 'in';
                this.$wire.set('data.check_out', cell.iso);
            }

            this.render();
        },

        render() {
            const year = this.visibleMonth.getFullYear();
            const month = this.visibleMonth.getMonth();

            this.monthLabel = this.visibleMonth.toLocaleDateString('hu-HU', {
                year: 'numeric',
                month: 'long',
            });

            const firstDay = new Date(year, month, 1);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const cells = [];

            for (let index = 0; index < startOffset; index++) {
                cells.push({
                    iso: null,
                    label: '',
                    classes: 'is-empty',
                    disabled: true,
                });
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const iso = toIso(date);
                const state = this.dayStates[iso] || 'free';
                const mode = this.selecting;
                const selectable = canSelectDay(iso, state, mode, this.checkIn, this.dayStates);
                const classes = [DAY_STATES[state] || 'is-free'];

                if (this.checkIn && this.checkIn === iso) {
                    classes.push('is-selected');
                }

                if (this.checkOut && this.checkOut === iso) {
                    classes.push('is-selected');
                }

                if (this.checkIn && this.checkOut && compareIso(iso, this.checkIn) > 0 && compareIso(iso, this.checkOut) < 0) {
                    classes.push('is-in-range');
                }

                cells.push({
                    iso,
                    label: String(day),
                    classes: classes.join(' '),
                    disabled: ! selectable,
                });
            }

            this.cells = cells;
        },
    }));
});
