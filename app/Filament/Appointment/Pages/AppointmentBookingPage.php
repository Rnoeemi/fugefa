<?php

namespace App\Filament\Appointment\Pages;

use App\Filament\Concerns\UsesSiteShellBody;
use App\Models\Worker;
use App\Models\WorkerPackage;
use App\Services\AppointmentBookingService;
use App\Support\PhoneNormalizer;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class AppointmentBookingPage extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;
    use UsesSiteShellBody;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Időpontfoglalás';

    protected static ?string $title = 'Időpontfoglalás';

    protected static ?string $slug = '{worker?}';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public ?int $lockedWorkerId = null;

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function mount(Request $request, ?string $worker = null): void
    {
        $workerModel = null;

        if (filled($worker)) {
            $workerModel = Worker::query()
                ->bookable()
                ->where('slug', $worker)
                ->firstOrFail();
        } elseif ($request->filled('worker')) {
            $workerModel = Worker::query()
                ->bookable()
                ->where('slug', $request->string('worker'))
                ->first();
        }

        $this->lockedWorkerId = $workerModel?->id;
        $defaultPackageId = $workerModel
            ? $workerModel->activePackages()->value('id')
            : null;

        $today = app(AppointmentBookingService::class)->now()->toDateString();

        $this->form->fill([
            'worker_id' => $this->lockedWorkerId,
            'worker_package_id' => $defaultPackageId,
            'appointment_date' => $request->query('date', $today),
            'starts_at' => null,
            'customer_name' => null,
            'customer_email' => null,
            'customer_phone' => null,
            'notes' => null,
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
                Section::make('Foglalás')
                    ->description('Válasszon munkatársat, csomagot, majd jelöljön ki kezdő időpontot a skálán.')
                    ->schema([
                        SelectField::make('worker_id')
                            ->label('Munkatárs')
                            ->options(fn (): array => Worker::query()
                                ->bookable()
                                ->get()
                                ->mapWithKeys(fn (Worker $worker): array => [
                                    $worker->id => trim($worker->name.($worker->title ? ' – '.$worker->title : '')),
                                ])
                                ->all())
                            ->required()
                            ->disabled(fn (): bool => filled($this->lockedWorkerId))
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                $set('starts_at', null);
                                $set('worker_package_id', null);

                                if (filled($state)) {
                                    $firstPackageId = WorkerPackage::query()
                                        ->where('worker_id', $state)
                                        ->active()
                                        ->orderBy('sort_order')
                                        ->value('id');
                                    $set('worker_package_id', $firstPackageId);
                                }
                            })
                            ->columnSpanFull(),

                        Placeholder::make('worker_bio')
                            ->label('')
                            ->content(function (Get $get): HtmlString|string {
                                $workerId = $get('worker_id');
                                if (blank($workerId)) {
                                    return '';
                                }

                                $worker = Worker::query()->bookable()->find($workerId);
                                if (! $worker || (blank($worker->bio) && blank($worker->title))) {
                                    return '';
                                }

                                $title = e($worker->title ?: 'Munkatárs');
                                $bio = nl2br(e($worker->bio ?: ''));

                                return new HtmlString(
                                    '<div class="appointment-worker-bio">'
                                    .'<p class="appointment-worker-bio__role">'.$title.'</p>'
                                    .'<h3 class="appointment-worker-bio__name">'.e($worker->name).'</h3>'
                                    .($bio !== '' ? '<div class="appointment-worker-bio__text">'.$bio.'</div>' : '')
                                    .'</div>'
                                );
                            })
                            ->visible(fn (Get $get): bool => filled($get('worker_id')))
                            ->columnSpanFull(),

                        SelectField::make('worker_package_id')
                            ->label('Csomag')
                            ->options(function (Get $get): array {
                                $workerId = $get('worker_id');
                                if (blank($workerId)) {
                                    return [];
                                }

                                return WorkerPackage::query()
                                    ->where('worker_id', $workerId)
                                    ->active()
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (WorkerPackage $package): array => [
                                        $package->id => $package->name.' · '.$package->duration_minutes.' perc · '.$package->formattedPrice(),
                                    ])
                                    ->all();
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('starts_at', null);
                            })
                            ->visible(fn (Get $get): bool => filled($get('worker_id')))
                            ->helperText('Online fizetés nincs – az ár tájékoztató jellegű.')
                            ->columnSpanFull(),

                        Placeholder::make('package_details')
                            ->label('')
                            ->content(function (Get $get): HtmlString|string {
                                $packageId = $get('worker_package_id');
                                if (blank($packageId)) {
                                    return '';
                                }

                                $package = WorkerPackage::query()->find($packageId);
                                if (! $package) {
                                    return '';
                                }

                                $description = filled($package->description)
                                    ? '<p>'.nl2br(e($package->description)).'</p>'
                                    : '';

                                return new HtmlString(
                                    '<div class="appointment-package-card">'
                                    .'<div class="appointment-package-card__meta">'
                                    .'<strong>'.e($package->name).'</strong>'
                                    .'<span>'.$package->duration_minutes.' perc</span>'
                                    .'<span>'.$package->formattedPrice().'</span>'
                                    .'</div>'
                                    .$description
                                    .'</div>'
                                );
                            })
                            ->visible(fn (Get $get): bool => filled($get('worker_package_id')))
                            ->columnSpanFull(),

                        FlexDatePicker::make('appointment_date')
                            ->label('Nap')
                            ->required()
                            ->minDate(fn (): Carbon => app(AppointmentBookingService::class)->now()->startOfDay())
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('starts_at', null);
                            })
                            ->visible(fn (Get $get): bool => filled($get('worker_id')) && filled($get('worker_package_id'))),

                        Hidden::make('starts_at')->required(),

                        ViewField::make('slot_grid')
                            ->view('filament.appointment.components.slot-grid')
                            ->viewData(fn (): array => [
                                'cells' => $this->slotCells(),
                                'selected' => $this->selectedSlotKeys(),
                                'gridKey' => implode('-', [
                                    $this->data['worker_id'] ?? 'x',
                                    $this->data['worker_package_id'] ?? 'x',
                                    $this->data['appointment_date'] ?? 'x',
                                    $this->data['starts_at'] ?? 'none',
                                ]),
                            ])
                            ->dehydrated(false)
                            ->visible(fn (Get $get): bool => filled($get('worker_id')) && filled($get('worker_package_id')) && filled($get('appointment_date')))
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 7]),

                Section::make('Elérhetőség')
                    ->schema([
                        FlexTextInput::make('customer_name')
                            ->label('Név')
                            ->required(),
                        FlexTextInput::make('customer_email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                        PhoneField::make('customer_phone')
                            ->label('Telefon')
                            ->defaultCountry('HU'),
                        FlexTextareaField::make('notes')
                            ->label('Megjegyzés')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 5]),
            ]),
        ]);
    }

    /**
     * @return list<array{time: string, starts_at: string, state: string, selectable: bool, label: string}>
     */
    public function slotCells(): array
    {
        $workerId = $this->data['worker_id'] ?? null;
        $packageId = $this->data['worker_package_id'] ?? null;
        $date = $this->data['appointment_date'] ?? null;

        if (blank($workerId) || blank($packageId) || blank($date)) {
            return [];
        }

        $worker = Worker::query()->bookable()->find($workerId);
        $package = WorkerPackage::query()->where('worker_id', $workerId)->active()->find($packageId);

        if (! $worker || ! $package) {
            return [];
        }

        return app(AppointmentBookingService::class)->dayGrid(
            $worker,
            Carbon::parse($date),
            $package->duration_minutes,
        );
    }

    /**
     * @return list<string>
     */
    public function selectedSlotKeys(): array
    {
        $startsAt = $this->data['starts_at'] ?? null;
        $packageId = $this->data['worker_package_id'] ?? null;

        if (blank($startsAt) || blank($packageId)) {
            return [];
        }

        $package = WorkerPackage::query()->find($packageId);
        if (! $package) {
            return [];
        }

        return app(AppointmentBookingService::class)->selectedBlockStarts(
            Carbon::parse($startsAt),
            $package->duration_minutes,
        );
    }

    public function selectSlot(string $startsAt): void
    {
        $cells = collect($this->slotCells())->keyBy('starts_at');
        $cell = $cells->get($startsAt);

        if (! $cell || ! ($cell['selectable'] ?? false)) {
            Notification::make()
                ->title('Ez az időpont nem választható a kiválasztott csomaghoz.')
                ->warning()
                ->send();

            return;
        }

        $this->data['starts_at'] = $startsAt;
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
                            ->label('Időpont foglalása')
                            ->submit('submit'),
                    ])->alignment(Alignment::Start),
                ]),
        ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $data['customer_phone'] = PhoneNormalizer::toString($data['customer_phone'] ?? null);

        if (blank($data['starts_at'] ?? null)) {
            Notification::make()
                ->title('Válasszon kezdő időpontot a skálán.')
                ->danger()
                ->send();

            return;
        }

        try {
            $appointment = app(AppointmentBookingService::class)->create([
                'worker_id' => (int) $data['worker_id'],
                'worker_package_id' => (int) $data['worker_package_id'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'starts_at' => $data['starts_at'],
                'notes' => $data['notes'] ?? null,
            ]);
        } catch (ValidationException $e) {
            Notification::make()
                ->title(collect($e->errors())->flatten()->first() ?? 'Az időpont nem foglalható')
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Időpont rögzítve')
            ->success()
            ->send();

        $this->redirect(route('appointments.thanks', $appointment));
    }
}
