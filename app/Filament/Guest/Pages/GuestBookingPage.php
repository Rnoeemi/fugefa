<?php

namespace App\Filament\Guest\Pages;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\GuestStatus;
use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Filament\Concerns\UsesSiteShellBody;
use App\Models\Accommodation;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\PaymentImplementation;
use App\Services\BookingAvailabilityService;
use App\Services\BookingMailService;
use App\Services\BookingNotificationService;
use App\Services\DocumentImageRecognitionService;
use App\Services\GuestDocumentValidationService;
use App\Services\ModuleService;
use App\Services\PaymentGatewayService;
use App\Services\PricingService;
use App\Support\BookingCalendarDayStates;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class GuestBookingPage extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;
    use UsesSiteShellBody;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Foglalás';

    protected static ?string $title = 'Online foglalás';

    protected static ?string $slug = 'foglalas/{accommodation:slug?}';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?int $lockedAccommodationId = null;

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function mount(Request $request, ?Accommodation $accommodation = null): void
    {
        if (! $accommodation && $request->filled('accommodation')) {
            $accommodation = Accommodation::query()
                ->bookableOnline()
                ->where('slug', $request->string('accommodation'))
                ->first();
        }

        $this->lockedAccommodationId = $accommodation?->id;

        $guestsCount = max(1, (int) ($request->query('guests', $request->query('guests_count', 2)) ?: 2));
        $travelers = [];

        for ($i = 0; $i < $guestsCount; $i++) {
            $travelers[] = ['name' => null, 'birth_date' => null, 'nationality' => 'Magyar'];
        }

        $paymentProvider = app(ModuleService::class)->paymentEnabled()
            ? PaymentImplementation::defaultProviderValue()
            : PaymentProvider::Cash->value;

        $this->form->fill([
            'accommodation_id' => $this->lockedAccommodationId,
            'check_in' => $request->query('check_in'),
            'check_out' => $request->query('check_out'),
            'guests_count' => $guestsCount,
            'travelers' => $travelers,
            'payment_provider' => $paymentProvider,
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make([
                'default' => 1,
                'lg' => 12,
            ])->schema([
                Section::make('Szállás és időszak')
                    ->description('Érkezés és távozás nem lehet múltbeli. A távozásnak az érkezés után kell lennie.')
                    ->schema([
                        SelectField::make('accommodation_id')
                            ->label('Szállás')
                            ->options(fn () => Accommodation::query()->bookableOnline()->orderBy('sort_order')->pluck('name', 'id'))
                            ->required()
                            ->disabled(fn (): bool => filled($this->lockedAccommodationId))
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function (): void {
                                $this->dispatch('guest-booking-accommodation-changed');
                            })
                            ->columnSpanFull(),
                        Hidden::make('check_in')
                            ->required()
                            ->live(),
                        Hidden::make('check_out')
                            ->required()
                            ->live(),
                        ViewField::make('booking_calendar')
                            ->view('filament.guest.components.booking-calendar')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        NumberStepper::make('guests_count')
                            ->label('Vendégek száma')
                            ->minValue(1)
                            ->maxValue(20)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get): void {
                                $this->syncTravelers((int) $state, $set, $get);
                            }),
                    ])
                    ->columnSpan(['lg' => 7]),

                Group::make([
                    Section::make('Áttekintés')
                        ->schema([
                            Placeholder::make('quote')
                                ->label('Összesítés')
                                ->content(function ($get): HtmlString {
                                    if (! $get('accommodation_id') || ! $get('check_in') || ! $get('check_out')) {
                                        return new HtmlString('<div class="guest-quote-box">Válasszon szállást és dátumokat.</div>');
                                    }

                                    try {
                                        [$adults, $children] = $this->countsFromTravelers($get('travelers') ?? []);
                                        $acc = Accommodation::find($get('accommodation_id'));
                                        $quote = app(PricingService::class)->quote(
                                            $acc,
                                            $get('check_in'),
                                            $get('check_out'),
                                            (int) $get('guests_count'),
                                            $adults,
                                            $children,
                                        );

                                        return new HtmlString(sprintf(
                                            '<div class="guest-quote-box space-y-1"><p><strong>%d éj</strong> · min. %d éj</p><p>Szállás: %s Ft</p><p>IFA (18+): %s Ft</p><p class="font-semibold text-pine">Összesen: %s Ft</p></div>',
                                            $quote['nights'],
                                            $quote['min_nights'],
                                            number_format($quote['accommodation_total'], 0, ',', ' '),
                                            number_format($quote['ifa_total'], 0, ',', ' '),
                                            number_format($quote['total'], 0, ',', ' '),
                                        ));
                                    } catch (\Throwable) {
                                        return new HtmlString('<div class="guest-quote-box">Az árajánlat nem számolható.</div>');
                                    }
                                }),
                        ]),

                    Section::make('Kapcsolattartó')
                        ->schema([
                            Grid::make(1)->schema([
                                FlexTextInput::make('name')->label('Név')->required(),
                                FlexTextInput::make('email')->label('E-mail')->email()->required(),
                                PhoneField::make('phone')->label('Telefon')->defaultCountry('HU')->required(),
                            ]),
                        ]),

                    Section::make('Fizetés')
                        ->visible(fn (): bool => app(ModuleService::class)->paymentEnabled())
                        ->schema([
                            SelectField::make('payment_provider')
                                ->label('Fizetési mód')
                                ->options(fn () => PaymentImplementation::query()->where('is_enabled', true)->get()->mapWithKeys(
                                    fn ($item) => [$item->provider->value => $item->label]
                                )->all())
                                ->default(fn (): ?string => PaymentImplementation::defaultProviderValue())
                                ->helperText('Ha az időszakra előleg vagy teljes előrefizetés vonatkozik, a foglalás után átirányítunk.'),
                        ]),
                ])
                    ->columnSpan(['lg' => 5]),

                Section::make('Vendégek')
                    ->description('14 év feletti vendégeknél adja meg az igazolványszámot. Ha már járt nálunk, az adatok automatikusan kitöltődnek, és az okmányokat nem kell újra feltölteni.')
                    ->schema([
                        Repeater::make('travelers')
                            ->label('Vendéglista')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->live()
                            ->schema([
                                Grid::make(3)->schema([
                                    FlexTextInput::make('name')
                                        ->label('Vendég neve')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (mixed $state, mixed $old, Set $set, Get $get): void {
                                            $this->syncFullNameOnDocumentFromGuestName($state, $old, $set, $get);
                                        })
                                        ->columnSpan(1),
                                    FlexDatePicker::make('birth_date')
                                        ->label('Születési dátum')
                                        ->required()
                                        ->live()
                                        ->maxDate(now())
                                        ->afterStateUpdated(function (mixed $state, Set $set, Get $get): void {
                                            if (! $this->travelerRequiresDocuments($get)) {
                                                return;
                                            }

                                            $this->syncFullNameOnDocumentFromGuestName($get('name'), null, $set, $get);
                                        })
                                        ->columnSpan(1),
                                    FlexTextInput::make('nationality')
                                        ->label('Állampolgárság')
                                        ->default('Magyar')
                                        ->required(fn ($get): bool => $this->travelerRequiresDocuments($get))
                                        ->columnSpan(1),
                                ]),
                                Hidden::make('known_guest_id'),
                                Hidden::make('documents_on_file'),
                               
                                Grid::make(2)
                                    ->visible(fn ($get): bool => $this->travelerRequiresDocuments($get))
                                    ->schema([
                                        FlexTextInput::make('id_number')
                                            ->label('Igazolványszám')
                                            ->visible(fn ($get): bool => $this->travelerRequiresDocuments($get))
                                            ->required(fn ($get): bool => $this->travelerRequiresDocuments($get))
                                            ->live(onBlur: true)
                                            ->helperText('Ha már járt nálunk, az adatok automatikusan kitöltődnek.')
                                            ->afterStateUpdated(function (mixed $state, Set $set): void {
                                                $this->lookupKnownTravelerByIdNumber($state, $set);
                                            }),
                                        FlexTextInput::make('full_name_on_document')
                                            ->label('Név az igazolványon')
                                            ->required()
                                            ->helperText('Alapból a vendég neve; szükség esetén módosítható.'),
                                        FlexTextInput::make('address_on_card')
                                            ->label('Cím a lakcímkártyán')
                                            ->required()
                                            ->columnSpanFull(),
                                        Placeholder::make('known_guest_note')
                                            ->hiddenLabel()
                                            ->visible(fn ($get): bool => $this->travelerHasStoredDocuments($get))
                                            ->content(new HtmlString(
                                                '<div class="guest-quote-box" style="border-color: var(--pine, #1f6b4a);">'
                                                .'<strong>Ismert vendég.</strong> Az okmányképek már a rendszerben vannak – új feltöltés nem szükséges.'
                                                .'</div>'
                                            ))
                                            ->columnSpanFull(),
                                        Grid::make([
                                            'default' => 1,
                                            'md' => 3,
                                        ])
                                            ->visible(fn ($get): bool => ! $this->travelerHasStoredDocuments($get))
                                            ->schema([
                                                $this->documentUploadField(
                                                    'id_card_front_path',
                                                    'Személyi előlap',
                                                    'Töltsön fel éles, jól olvasható fényképet a személyi előlapjáról.',
                                                ),
                                                $this->documentUploadField(
                                                    'id_card_back_path',
                                                    'Személyi hátlap',
                                                    'Töltsön fel éles, jól olvasható fényképet a személyi hátlapjáról.',
                                                ),
                                                $this->documentUploadField(
                                                    'address_card_front_path',
                                                    'Lakcímkártya előlap',
                                                    'Töltsön fel éles, jól olvasható fényképet a lakcímkártya előlapjáról.',
                                                ),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                                Placeholder::make('under_14_note')
                                    ->hiddenLabel()
                                    ->visible(fn ($get): bool => filled($get('birth_date')) && ! $this->travelerRequiresDocuments($get))
                                    ->content('14 év alatti vendég – okmánycsatolás nem szükséges.'),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Vendég')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 12]),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('submit')
                ->footer([
                    Actions::make([
                        \Filament\Actions\Action::make('submit')
                            ->label('Foglalás véglegesítése')
                            ->submit('submit'),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $data['phone'] = $this->normalizePhone($data['phone'] ?? null);
        $travelers = array_values($data['travelers'] ?? []);

        if (! app(ModuleService::class)->paymentEnabled()) {
            $data['payment_provider'] = PaymentProvider::Cash->value;
        }

        if (count($travelers) !== (int) $data['guests_count']) {
            Notification::make()->title('A vendéglista nem egyezik a vendégszámmal.')->danger()->send();

            return;
        }

        foreach (array_values($this->data['travelers'] ?? []) as $index => $traveler) {
            if (! $this->travelerRequiresDocuments($traveler)) {
                continue;
            }

            if ($this->travelerHasStoredDocuments($traveler)) {
                continue;
            }

            foreach (['id_card_front_check', 'id_card_back_check', 'address_card_front_check'] as $checkKey) {
                $check = $traveler[$checkKey] ?? null;

                if (! is_array($check) || ($check['status'] ?? null) !== 'ok') {
                    Notification::make()
                        ->title('Okmányellenőrzés hiányos')
                        ->body('A '.(string) ($index + 1).'. vendég dokumentumai még nincsenek sikeresen felismerve.')
                        ->danger()
                        ->send();

                    return;
                }
            }
        }

        [$adults, $children] = $this->countsFromTravelers($travelers);

        $accommodation = Accommodation::query()->with(['ratePeriods', 'paymentRules'])->findOrFail($data['accommodation_id']);

        $guest = Guest::query()->firstOrNew(['email' => strtolower($data['email'])]);
        $guest->fill([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'id_number' => $travelers[0]['id_number'] ?? $guest->id_number,
            'status' => $guest->status ?? GuestStatus::Welcome,
        ]);

        if ($guest->isBlocked()) {
            Notification::make()->title('A foglalás nem fogadható')->danger()->send();

            return;
        }

        try {
            $this->assertDatesValid($data['check_in'], $data['check_out'], $accommodation);

            app(BookingAvailabilityService::class)->assertCanBook(
                $accommodation,
                $guest,
                $data['check_in'],
                $data['check_out'],
                (int) $data['guests_count'],
                BookingSource::Website,
                enforceMinNights: true,
                adultsCount: $adults,
            );
        } catch (ValidationException $e) {
            Notification::make()->title(collect($e->errors())->flatten()->first() ?? 'Érvénytelen foglalás')->danger()->send();

            return;
        }

        $quote = app(PricingService::class)->quote(
            $accommodation,
            $data['check_in'],
            $data['check_out'],
            (int) $data['guests_count'],
            $adults,
            $children,
        );

        $paymentRequirement = app(PricingService::class)->paymentRequirement(
            $accommodation,
            $quote['total'],
            $data['check_in'],
        );

        try {
            $booking = DB::transaction(function () use ($data, $guest, $accommodation, $quote, $paymentRequirement, $travelers, $adults, $children) {
                $guest->save();
                $travelersForSave = $this->travelersWithOcrFlags($travelers);

                $booking = Booking::query()->create([
                    'accommodation_id' => $accommodation->id,
                    'guest_id' => $guest->id,
                    'check_in' => $data['check_in'],
                    'check_out' => $data['check_out'],
                    'guests_count' => $data['guests_count'],
                    'adults_count' => $adults,
                    'children_count' => $children,
                    'status' => BookingStatus::Pending,
                    'source' => BookingSource::Website,
                    'payment_status' => $paymentRequirement['amount_due'] > 0 ? PaymentStatus::DepositDue : PaymentStatus::Unpaid,
                    'total_price' => $quote['total'],
                    'accommodation_total' => $quote['accommodation_total'],
                    'ifa_total' => $quote['ifa_total'],
                    'amount_paid' => 0,
                    'price_breakdown' => array_merge($quote, [
                        'payment_requirement' => $paymentRequirement,
                        'travelers' => collect($travelersForSave)->map(fn (array $t): array => [
                            'name' => $t['name'] ?? null,
                            'birth_date' => $t['birth_date'] ?? null,
                            'nationality' => $t['nationality'] ?? null,
                            'known_guest_id' => $t['known_guest_id'] ?? null,
                            'documents_on_file' => (bool) ($t['documents_on_file'] ?? false),
                            'requires_documents' => filled($t['birth_date'] ?? null) && Carbon::parse($t['birth_date'])->age >= 14,
                            'needs_manual_validation' => (bool) ($t['needs_manual_validation'] ?? false),
                            'manual_validation_reasons' => $t['manual_validation_reasons'] ?? [],
                        ])->all(),
                    ]),
                ]);

                app(GuestDocumentValidationService::class)->validateTravelers($guest, $booking, $travelersForSave);

                return $booking;
            });
        } catch (ValidationException $e) {
            Notification::make()->title(collect($e->errors())->flatten()->first() ?? 'Okmányellenőrzés sikertelen')->danger()->send();

            return;
        }

        app(BookingMailService::class)->sendReceived($booking);
        app(BookingNotificationService::class)->notifyNewBooking($booking);

        if (
            app(ModuleService::class)->paymentEnabled()
            && $paymentRequirement['amount_due'] > 0
            && filled($data['payment_provider'] ?? null)
        ) {
            try {
                $provider = PaymentProvider::from($data['payment_provider']);
                $url = app(PaymentGatewayService::class)->startCheckout($booking, $provider);

                if ($provider->requiresOnlineCheckout()) {
                    $this->redirect($url);

                    return;
                }
            } catch (\Throwable $e) {
                Notification::make()->title('Fizetés indítása sikertelen: '.$e->getMessage())->warning()->send();
            }
        }

        Notification::make()->title('Foglalási igény rögzítve')->success()->send();
        $this->redirect(route('booking.thanks', $booking));
    }

    protected function assertDatesValid(string $checkIn, string $checkOut, Accommodation $accommodation): void
    {
        $checkInDate = Carbon::parse($checkIn)->startOfDay();
        $checkOutDate = Carbon::parse($checkOut)->startOfDay();
        $today = now()->startOfDay();

        if ($checkInDate->lt($today) || $checkOutDate->lt($today)) {
            throw ValidationException::withMessages([
                'check_in' => 'Érkezés és távozás nem lehet múltbeli dátum.',
            ]);
        }

        if ($checkOutDate->lte($checkInDate)) {
            throw ValidationException::withMessages([
                'check_out' => 'A távozásnak az érkezés után kell lennie.',
            ]);
        }

        $cursor = $checkInDate->copy();

        while ($cursor->lt($checkOutDate)) {
            if ($this->isNightOccupied($accommodation->id, $cursor)) {
                throw ValidationException::withMessages([
                    'check_in' => 'A választott időszak foglalt napot tartalmaz ('.$cursor->format('Y.m.d.').').',
                ]);
            }

            $cursor->addDay();
        }
    }

    protected function isNightOccupied(mixed $accommodationId, mixed $date): bool
    {
        if (blank($accommodationId) || blank($date)) {
            return false;
        }

        $day = Carbon::parse($date)->toDateString();

        return in_array($day, $this->occupiedDateList((int) $accommodationId), true);
    }

    /**
     * @return list<string>
     */
    protected function occupiedDateList(?int $accommodationId): array
    {
        if (! $accommodationId) {
            return [];
        }

        static $cache = [];

        if (isset($cache[$accommodationId])) {
            return $cache[$accommodationId];
        }

        $accommodation = Accommodation::query()->find($accommodationId);

        if (! $accommodation) {
            return $cache[$accommodationId] = [];
        }

        $dates = app(BookingAvailabilityService::class)->occupiedDates(
            $accommodation,
            now()->startOfDay(),
            now()->addYear()->endOfDay(),
        );

        return $cache[$accommodationId] = collect($dates)->pluck('date')->values()->all();
    }

    protected function travelerRequiresDocuments(callable|array $get): bool
    {
        $birthDate = is_array($get) ? ($get['birth_date'] ?? null) : $get('birth_date');

        if (blank($birthDate)) {
            return false;
        }

        try {
            return Carbon::parse($birthDate)->age >= 14;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function travelerHasStoredDocuments(callable|array $get): bool
    {
        if (is_array($get)) {
            return filled($get['known_guest_id'] ?? null)
                || filter_var($get['documents_on_file'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return filled($get('known_guest_id'))
            || filter_var($get('documents_on_file'), FILTER_VALIDATE_BOOLEAN);
    }

    protected function lookupKnownTravelerByIdNumber(mixed $idNumber, Set $set): void
    {
        $idNumber = filled($idNumber) ? trim((string) $idNumber) : '';

        if ($idNumber === '') {
            $this->clearKnownTravelerState($set);

            return;
        }

        $guest = Guest::findByIdNumber($idNumber);

        if (! $guest) {
            $this->clearKnownTravelerState($set);

            return;
        }

        if ($guest->isBlocked()) {
            $this->clearKnownTravelerState($set);

            Notification::make()
                ->title('Ez a vendég nem foglalhat')
                ->body('A megadott igazolványszámhoz tartozó profil le van tiltva.')
                ->danger()
                ->send();

            return;
        }

        $document = $guest->latestDocumentWithImages();

        if (! $document) {
            $this->clearKnownTravelerState($set);

            return;
        }

        $set('known_guest_id', $guest->id);
        $set('documents_on_file', true);
        $set('name', $guest->name ?: $document->full_name_on_document);
        $set('full_name_on_document', $document->full_name_on_document ?: $guest->name);

        if (filled($document->birth_date)) {
            $set('birth_date', $document->birth_date->format('Y-m-d'));
        }

        if (filled($document->nationality)) {
            $nationality = (string) $document->nationality;
            $set('nationality', in_array(strtoupper($nationality), ['HU', 'HUN'], true) ? 'Magyar' : $nationality);
        }

        if (filled($document->address_on_card)) {
            $set('address_on_card', $document->address_on_card);
        }

        if (filled($document->document_number)) {
            $set('id_number', $document->document_number);
        }

        $set('id_card_front_path', null);
        $set('id_card_back_path', null);
        $set('address_card_front_path', null);
        $set('id_card_front_check', null);
        $set('id_card_back_check', null);
        $set('address_card_front_check', null);

        // Kapcsolattartó mezők: csak ha még üresek, és ez az első vendég.
        $this->maybeFillContactFromKnownGuest($guest);

        Notification::make()
            ->title('Ismert vendég')
            ->body('Az adatok kitöltve. Az okmányokat nem kell újra feltölteni.')
            ->success()
            ->send();
    }

    protected function clearKnownTravelerState(Set $set): void
    {
        $set('known_guest_id', null);
        $set('documents_on_file', false);
    }

    protected function maybeFillContactFromKnownGuest(Guest $guest): void
    {
        if (blank($this->data['name'] ?? null) && filled($guest->name)) {
            $this->data['name'] = $guest->name;
        }

        if (blank($this->data['email'] ?? null) && filled($guest->email)) {
            $this->data['email'] = $guest->email;
        }

        if (blank($this->data['phone'] ?? null) && filled($guest->phone)) {
            $this->data['phone'] = $guest->phone;
        }
    }

    protected function syncFullNameOnDocumentFromGuestName(mixed $name, mixed $previousName, Set $set, Get $get): void
    {
        if (blank($name)) {
            return;
        }

        $current = $get('full_name_on_document');
        $name = trim((string) $name);
        $previousName = filled($previousName) ? trim((string) $previousName) : null;
        $current = filled($current) ? trim((string) $current) : null;

        // Csak akkor írjuk felül, ha üres, vagy még a korábbi vendégnévvel egyezett (nem kézzel módosították).
        if (blank($current) || ($previousName !== null && $current === $previousName) || $current === $name) {
            $set('full_name_on_document', $name);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $travelers
     * @return array{0: int, 1: int}
     */
    protected function countsFromTravelers(array $travelers): array
    {
        $adults = 0;
        $children = 0;

        foreach ($travelers as $traveler) {
            if (blank($traveler['birth_date'] ?? null)) {
                continue;
            }

            if (Carbon::parse($traveler['birth_date'])->age >= 18) {
                $adults++;
            } else {
                $children++;
            }
        }

        if ($adults + $children === 0) {
            return [max(1, count($travelers)), 0];
        }

        return [$adults, $children];
    }

    protected function syncTravelers(int $count, callable $set, callable $get): void
    {
        $travelers = array_values($get('travelers') ?? []);
        $count = max(1, $count);

        while (count($travelers) < $count) {
            $travelers[] = ['name' => null, 'birth_date' => null, 'nationality' => 'Magyar'];
        }

        $set('travelers', array_slice($travelers, 0, $count));
    }

    protected function normalizePhone(mixed $phone): ?string
    {
        if (is_array($phone)) {
            return filled($phone['e164'] ?? null)
                ? (string) $phone['e164']
                : (filled($phone['national'] ?? null) ? (string) $phone['national'] : null);
        }

        return filled($phone) ? (string) $phone : null;
    }

    /**
     * @return array<string, string>
     */
    public function calendarDayStates(int $accommodationId, string $from, string $to): array
    {
        $accommodation = Accommodation::query()->find($accommodationId);

        if (! $accommodation) {
            return [];
        }

        return app(BookingCalendarDayStates::class)->statesFor(
            $accommodation,
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->startOfDay(),
        );
    }

    protected function documentUploadField(string $field, string $label, string $helper): Group
    {
        $checkField = str_replace('_path', '_check', $field);

        return Group::make([
            FileUpload::make($field)
                ->label($label)
                ->image()
                ->disk('public')
                ->directory('guest-documents')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120)
                ->required(fn (Get $get): bool => ! $this->travelerHasStoredDocuments($get))
                ->live()
                ->panelLayout(null)
                ->imagePreviewHeight(null)
                ->placeholder('Kattintson ide, vagy húzza ide a képet')
                ->helperText($helper)
                ->afterStateUpdated(function (mixed $state, Set $set, FileUpload $component) use ($field, $checkField): void {
                    $this->handleDocumentUpload($field, $checkField, $state, $set, $component);
                }),
            Hidden::make($checkField)->dehydrated(false),
            Placeholder::make($checkField.'_feedback')
                ->hiddenLabel()
                ->content(fn (Get $get): HtmlString => $this->documentCheckFeedback($get($checkField))),
        ])
            ->extraAttributes(['class' => 'guest-doc-card'])
            ->columnSpan(1);
    }

    protected function handleDocumentUpload(string $field, string $checkField, mixed $state, Set $set, FileUpload $component): void
    {
        if (blank($state)) {
            $set($checkField, null);

            return;
        }

        $recognition = app(DocumentImageRecognitionService::class);

        // Feltöltéskor még ne utasítsunk el – várjuk meg az OCR-t.
        $set($checkField, [
            'status' => 'pending',
            'message' => 'Okmány felismerése folyamatban…',
            'ok_for' => false,
        ]);

        $url = $recognition->publicUrl($state);
        $travelerUuid = $this->travelerUuidFromStatePath($component->getStatePath());

        if (! $url || ! $travelerUuid) {
            $result = $recognition->analyze($state, $field);
            $result['status'] = $result['ok_for'] ? 'ok' : 'error';
            $set($checkField, $result);

            if ($result['ok_for']) {
                // OCR-ből nem töltünk mezőket.
            } else {
                $set($field, null);
                Notification::make()
                    ->title('Okmányellenőrzés sikertelen')
                    ->body($result['message'])
                    ->danger()
                    ->send();
            }

            return;
        }

        $this->dispatch('guest-document-ocr', [
            'url' => $url,
            'dataUrl' => $recognition->ocrDataUrl($state),
            'field' => $field,
            'travelerUuid' => $travelerUuid,
            'componentId' => $this->getId(),
        ]);
    }

    public function markDocumentCheckPending(string $travelerUuid, string $field): void
    {
        $checkField = str_replace('_path', '_check', $field);
        $current = data_get($this->data, "travelers.{$travelerUuid}.{$checkField}");

        data_set($this->data, "travelers.{$travelerUuid}.{$checkField}", array_merge(
            is_array($current) ? $current : [],
            [
                'status' => 'pending',
                'message' => 'Okmány felismerése folyamatban…',
            ],
        ));
    }

    public function reportDocumentOcrClientError(string $travelerUuid, string $field, string $error): void
    {
        if (! DocumentImageRecognitionService::OCR_DEBUG_LOG) {
            return;
        }

        $path = storage_path('logs/ocr.log');
        $line = '['.now()->format('Y-m-d H:i:s')."] CLIENT_OCR_ERROR field={$field} traveler={$travelerUuid} error={$error}".PHP_EOL;

        try {
            file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable) {
            // ignore
        }
    }

    public function applyDocumentOcrResult(string $travelerUuid, string $field, string $ocrText): void
    {
        $checkField = str_replace('_path', '_check', $field);
        $upload = data_get($this->data, "travelers.{$travelerUuid}.{$field}");

        if (blank($upload)) {
            return;
        }

        $recognition = app(DocumentImageRecognitionService::class);
        $result = $recognition->analyze($upload, $field, $ocrText);
        $result['status'] = $result['ok_for'] ? 'ok' : 'error';
        $result['needs_manual_validation'] = (bool) ($result['needs_manual_validation'] ?? false);

        if ($result['ok_for'] && in_array($field, ['id_card_front_path', 'id_card_back_path', 'address_card_front_path'], true)) {
            $provided = [
                'full_name_on_document' => data_get($this->data, "travelers.{$travelerUuid}.full_name_on_document"),
                'name' => data_get($this->data, "travelers.{$travelerUuid}.name"),
                'id_number' => data_get($this->data, "travelers.{$travelerUuid}.id_number"),
                'address_on_card' => data_get($this->data, "travelers.{$travelerUuid}.address_on_card"),
            ];

            if ($field === 'id_card_front_path') {
                $hasProvided = filled($provided['full_name_on_document'] ?? null)
                    || filled($provided['name'] ?? null)
                    || filled($provided['id_number'] ?? null);

                if ($hasProvided) {
                    $comparison = $recognition->compareToProvided($result['extracted'] ?? [], $provided, $ocrText, [
                        'compare_name' => true,
                        'compare_id' => true,
                    ]);
                    $result['name_matches'] = $comparison['name_matches'];
                    $result['id_matches'] = $comparison['id_matches'];
                    $result['needs_manual_validation'] = (bool) ($comparison['needs_manual_validation'] ?? false);

                    if (! $comparison['ok']) {
                        $result['ok_for'] = false;
                        $result['status'] = 'error';
                        $result['message'] = implode(' ', $comparison['messages']) ?: 'A megadott adatok nem egyeznek az igazolvánnyal.';
                    }
                }
            } elseif ($field === 'id_card_back_path') {
                // Hátlap: csak igazolványszám összevetés.
                if (filled($provided['id_number'] ?? null)) {
                    $comparison = $recognition->compareToProvided($result['extracted'] ?? [], $provided, $ocrText, [
                        'compare_name' => false,
                        'compare_id' => true,
                    ]);
                    $result['name_matches'] = null;
                    $result['id_matches'] = $comparison['id_matches'];
                    $result['needs_manual_validation'] = (bool) ($comparison['needs_manual_validation'] ?? false);

                    if (! $comparison['ok']) {
                        $result['ok_for'] = false;
                        $result['status'] = 'error';
                        $result['message'] = implode(' ', $comparison['messages']) ?: 'A megadott igazolványszám nem egyezik az igazolvánnyal.';
                    }
                }
            } elseif ($field === 'address_card_front_path') {
                // Lakcím: eltérésnél NEM hibázunk, csak admin flag.
                $comparison = $recognition->compareToProvided($result['extracted'] ?? [], $provided, $ocrText, [
                    'compare_name' => false,
                    'compare_id' => false,
                    'compare_address' => true,
                    'soft' => true,
                ]);
                $result['address_matches'] = $comparison['address_matches'];
                $result['needs_manual_validation'] = (bool) ($comparison['needs_manual_validation'] ?? false)
                    || ($comparison['address_matches'] === false);
            }
        }

        data_set($this->data, "travelers.{$travelerUuid}.{$checkField}", $result);

        if (! $result['ok_for']) {
            data_set($this->data, "travelers.{$travelerUuid}.{$field}", null);

            Notification::make()
                ->title('Okmányellenőrzés sikertelen')
                ->body($result['message'])
                ->danger()
                ->send();

            return;
        }

        // Mezőket NEM töltünk az OCR-ből – a vendég kézzel adja meg.
    }

    /**
     * OCR check flag-ek átadása a foglalás mentéséhez (admin kézi validáció).
     *
     * @param  list<array<string, mixed>>  $travelers
     * @return list<array<string, mixed>>
     */
    protected function travelersWithOcrFlags(array $travelers): array
    {
        $rawTravelers = $this->data['travelers'] ?? [];

        // A form state UUID-kulcsos lehet – a submit array_values-szel dolgozik.
        $indexed = array_values(is_array($rawTravelers) ? $rawTravelers : []);

        foreach ($travelers as $index => $traveler) {
            $source = $indexed[$index] ?? $traveler;

            $travelers[$index]['known_guest_id'] = $source['known_guest_id'] ?? $traveler['known_guest_id'] ?? null;
            $travelers[$index]['documents_on_file'] = $this->travelerHasStoredDocuments($source)
                || $this->travelerHasStoredDocuments($traveler);

            if ($travelers[$index]['documents_on_file']) {
                $travelers[$index]['needs_manual_validation'] = false;
                $travelers[$index]['manual_validation_reasons'] = [];

                continue;
            }

            $needsManual = false;
            $reasons = [];

            foreach (['id_card_front_check', 'id_card_back_check', 'address_card_front_check'] as $checkKey) {
                $check = $source[$checkKey] ?? null;
                if (! is_array($check)) {
                    continue;
                }

                if (! empty($check['needs_manual_validation'])) {
                    $needsManual = true;
                    $label = match ($checkKey) {
                        'id_card_front_check' => 'személyi előlap',
                        'id_card_back_check' => 'személyi hátlap',
                        'address_card_front_check' => 'lakcímkártya',
                        default => $checkKey,
                    };
                    $reasons[] = "OCR eltérés ({$label})";
                }

                if (($check['address_matches'] ?? null) === false) {
                    $needsManual = true;
                    $reasons[] = 'Lakcím OCR és megadott cím eltér';
                }
            }

            $travelers[$index]['needs_manual_validation'] = $needsManual;
            $travelers[$index]['manual_validation_reasons'] = array_values(array_unique($reasons));
        }

        return $travelers;
    }

    /**
     * @param  array{full_name?: ?string, id_number?: ?string, birth_date?: ?string, nationality?: ?string, address?: ?string}  $extracted
     */
    protected function fillTravelerFromExtraction(string $travelerUuid, string $field, array $extracted): void
    {
        // Szándékosan üres: OCR-ből nem töltünk űrlapmezőket.
    }

    /**
     * @param  array{full_name?: ?string, id_number?: ?string, birth_date?: ?string, nationality?: ?string, address?: ?string}  $extracted
     */
    protected function applyExtractedDocumentFields(Set $set, array $extracted): void
    {
        if (filled($extracted['full_name'] ?? null)) {
            $set('full_name_on_document', $extracted['full_name']);
            $set('name', $extracted['full_name']);
        }

        if (filled($extracted['id_number'] ?? null)) {
            $set('id_number', $extracted['id_number']);
        }

        if (filled($extracted['birth_date'] ?? null)) {
            $set('birth_date', $extracted['birth_date']);
        }

        if (filled($extracted['nationality'] ?? null)) {
            $set('nationality', $extracted['nationality']);
        }

        if (filled($extracted['address'] ?? null)) {
            $set('address_on_card', $extracted['address']);
        }
    }

    protected function travelerUuidFromStatePath(string $statePath): ?string
    {
        if (preg_match('/travelers\.([^.]+)\./', $statePath, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    protected function documentCheckFeedback(mixed $check): HtmlString
    {
        if (! is_array($check) || blank($check['message'] ?? null)) {
            return new HtmlString('<p class="guest-doc-status is-idle">Még nincs feltöltött kép.</p>');
        }

        $status = $check['status'] ?? 'pending';
        $class = match ($status) {
            'ok' => 'is-ok',
            'error' => 'is-error',
            default => 'is-pending',
        };

        return new HtmlString(
            '<p class="guest-doc-status '.$class.'">'.e((string) $check['message']).'</p>'
        );
    }
}
