<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\GuestStatus;
use App\Models\Accommodation;
use App\Models\Guest;
use App\Services\BookingAvailabilityService;
use App\Services\PricingService;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make('basic')
                            ->label('Foglalás')
                            ->columns(3)
                            ->schema([
                                SelectField::make('accommodation_id')
                                    ->label('Szállás')
                                    ->relationship(
                                        name: 'accommodation',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->orderBy('sort_order')->orderBy('name'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Accommodation $record): string => sprintf(
                                        '%s (%s%s)',
                                        $record->name,
                                        $record->type->getLabel(),
                                        $record->isAdminOnlyBooking() ? ' · csak admin' : '',
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(2),
                                SelectField::make('guest_id')
                                    ->label('Vendég')
                                    ->relationship(
                                        name: 'guest',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Guest $record): string => sprintf(
                                        '%s [%s]',
                                        $record->name,
                                        $record->status->getLabel(),
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->createOptionForm([
                                        \Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput::make('name')
                                            ->label('Név')
                                            ->required(),
                                        SelectField::make('status')
                                            ->label('Státusz')
                                            ->options(GuestStatus::class)
                                            ->enum(GuestStatus::class)
                                            ->default(GuestStatus::Welcome)
                                            ->required(),
                                        \Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput::make('email')
                                            ->label('E-mail')
                                            ->email(),
                                        \Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField::make('phone')
                                            ->label('Telefon')
                                            ->defaultCountry('HU'),
                                    ])
                                    ->columnSpan(1),
                                FlexDatePicker::make('check_in')
                                    ->label('Érkezés')
                                    ->required()
                                    ->live()
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                FlexDatePicker::make('check_out')
                                    ->label('Távozás')
                                    ->required()
                                    ->live()
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                NumberStepper::make('guests_count')
                                    ->label('Vendégek száma')
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(50)
                                    ->default(1)
                                    ->suffix('fő')
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                NumberStepper::make('adults_count')
                                    ->label('Felnőttek (18+)')
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->default(1)
                                    ->helperText('IFA csak 18 év felett.')
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                NumberStepper::make('children_count')
                                    ->label('18 év alatt')
                                    ->minValue(0)
                                    ->maxValue(50)
                                    ->default(0)
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                SelectField::make('bed_id')
                                    ->label('Ágy')
                                    ->options(function ($get): array {
                                        $accommodationId = $get('accommodation_id');

                                        if (! $accommodationId) {
                                            return [];
                                        }

                                        return \App\Models\Bed::query()
                                            ->whereHas('room', fn ($q) => $q->where('accommodation_id', $accommodationId))
                                            ->with('room')
                                            ->get()
                                            ->mapWithKeys(fn ($bed) => [$bed->id => $bed->label()])
                                            ->all();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->visible(function ($get): bool {
                                        $accommodationId = $get('accommodation_id');

                                        if (! $accommodationId) {
                                            return false;
                                        }

                                        return (bool) Accommodation::query()
                                            ->find($accommodationId)
                                            ?->supportsBeds();
                                    })
                                    ->helperText('Munkásszállásnál kötelező/ajánlott. Üresen: első szabad ágy.')
                                    ->columnSpan(1),
                                SelectField::make('status')
                                    ->label('Státusz')
                                    ->options(BookingStatus::class)
                                    ->enum(BookingStatus::class)
                                    ->required()
                                    ->default(BookingStatus::Confirmed)
                                    ->columnSpan(1),
                                SelectField::make('source')
                                    ->label('Forrás')
                                    ->options(BookingSource::class)
                                    ->enum(BookingSource::class)
                                    ->required()
                                    ->default(BookingSource::Admin)
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                                FlexTextInput::make('total_price')
                                    ->label('Összeg')
                                    ->numeric()
                                    ->suffix('Ft')
                                    ->disabledOn('edit')
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ]),
                        Tab::make('notes')
                            ->label('Megjegyzés')
                            ->schema([
                                FlexTextareaField::make('notes')
                                    ->label('Belső megjegyzés')
                                    ->rows(5)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function validateAvailability(array $data, ?int $ignoreBookingId = null, bool $enforceMinNights = false): array
    {
        $accommodation = Accommodation::query()->find($data['accommodation_id'] ?? null);
        $guest = Guest::query()->find($data['guest_id'] ?? null);

        if (! $accommodation || ! $guest) {
            throw ValidationException::withMessages([
                'accommodation_id' => 'A szállás és a vendég megadása kötelező.',
            ]);
        }

        $source = $data['source'] instanceof BookingSource
            ? $data['source']
            : BookingSource::from((string) ($data['source'] ?? BookingSource::Admin->value));

        $bedId = isset($data['bed_id']) && filled($data['bed_id']) ? (int) $data['bed_id'] : null;

        if (! $bedId && $accommodation->supportsBeds()) {
            $bed = app(BookingAvailabilityService::class)->firstAvailableBed(
                $accommodation,
                $data['check_in'],
                $data['check_out'],
                $ignoreBookingId,
            );
            $bedId = $bed?->id;
            $data['bed_id'] = $bedId;
            $data['room_id'] = $bed?->room_id;
        } elseif ($bedId) {
            $bed = \App\Models\Bed::query()->find($bedId);
            $data['room_id'] = $bed?->room_id;
        }

        app(BookingAvailabilityService::class)->assertCanBook(
            accommodation: $accommodation,
            guest: $guest,
            checkIn: $data['check_in'],
            checkOut: $data['check_out'],
            guestsCount: (int) ($data['guests_count'] ?? 1),
            source: $source,
            ignoreBookingId: $ignoreBookingId,
            enforceMinNights: $enforceMinNights,
            bedId: $bedId,
            adultsCount: isset($data['adults_count']) ? (int) $data['adults_count'] : null,
        );

        $room = isset($data['room_id']) ? \App\Models\Room::query()->find($data['room_id']) : null;

        $quote = app(PricingService::class)->quote(
            $accommodation,
            $data['check_in'],
            $data['check_out'],
            (int) ($data['guests_count'] ?? 1),
            isset($data['adults_count']) ? (int) $data['adults_count'] : null,
            isset($data['children_count']) ? (int) $data['children_count'] : null,
            $room,
        );

        $data['accommodation_total'] = $quote['accommodation_total'];
        $data['ifa_total'] = $quote['ifa_total'];
        $data['price_breakdown'] = $quote;

        if (! filled($data['total_price'] ?? null)) {
            $data['total_price'] = $quote['total'];
        }

        return $data;
    }
}
