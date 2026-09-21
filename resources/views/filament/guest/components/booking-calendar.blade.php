<div
    class="guest-booking-calendar"
    wire:ignore
    x-data="guestBookingCalendar()"
    x-on:guest-booking-accommodation-changed.window="onAccommodationChanged()"
>
    <div class="guest-booking-calendar__selection">
        <div class="guest-booking-calendar__selection-item">
            <span class="guest-booking-calendar__selection-label">Érkezés</span>
            <strong class="guest-booking-calendar__selection-value" x-text="formatDisplay(checkIn)">–</strong>
        </div>
        <div class="guest-booking-calendar__selection-item">
            <span class="guest-booking-calendar__selection-label">Távozás</span>
            <strong class="guest-booking-calendar__selection-value" x-text="formatDisplay(checkOut)">–</strong>
        </div>
        <button
            type="button"
            class="guest-booking-calendar__clear"
            x-show="checkIn || checkOut"
            x-cloak
            x-on:click="clearDates()"
        >
            Dátumok törlése
        </button>
    </div>

    <div class="guest-booking-calendar__legend">
        <span class="guest-booking-calendar__legend-item is-past">Múlt</span>
        <span class="guest-booking-calendar__legend-item is-free">Szabad</span>
        <span class="guest-booking-calendar__legend-item is-turnover-out">Foglalás első napja</span>
        <span class="guest-booking-calendar__legend-item is-turnover-in">Foglalás utolsó napja</span>
        <span class="guest-booking-calendar__legend-item is-occupied">Foglalt / zárt</span>
    </div>

    <div class="guest-booking-calendar__nav">
        <button type="button" class="guest-booking-calendar__nav-btn" x-on:click="previousMonth()" aria-label="Előző hónap">‹</button>
        <p class="guest-booking-calendar__month" x-text="monthLabel"></p>
        <button type="button" class="guest-booking-calendar__nav-btn" x-on:click="nextMonth()" aria-label="Következő hónap">›</button>
    </div>

    <div class="guest-booking-calendar__weekdays">
        <span>H</span><span>K</span><span>Sze</span><span>Cs</span><span>P</span><span>Szo</span><span>V</span>
    </div>

    <div class="guest-booking-calendar__grid">
        <template x-for="(cell, index) in cells" :key="index">
            <button
                type="button"
                class="guest-booking-calendar__day"
                x-bind:class="cell.classes"
                x-bind:disabled="cell.disabled"
                x-on:click="selectDay(cell)"
                x-text="cell.label"
            ></button>
        </template>
    </div>

    <p class="guest-booking-calendar__hint" x-show="! accommodationId">
        Válasszon szállást a naptár megjelenítéséhez.
    </p>
</div>
