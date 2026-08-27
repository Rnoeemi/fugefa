<?php

namespace App\Filament\Worker\Pages;

use App\Models\Worker;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Throwable;
use UnitEnum;

class WorkerGoogleCalendarSettings extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    protected static ?string $navigationLabel = 'Google Naptár';

    protected static string|UnitEnum|null $navigationGroup = 'Beállítások';

    protected static ?string $title = 'Google Naptár szinkron';

    protected static ?string $slug = 'google-calendar';

    protected static ?int $navigationSort = 10;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $worker = Filament::auth()->user();
        abort_unless($worker instanceof Worker, 403);

        $this->form->fill([
            'google_calendar_enabled' => $worker->google_calendar_enabled,
            'google_calendar_id' => $worker->google_calendar_id,
            'google_credentials' => $worker->google_credentials ?? [],
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Google Naptár')
                ->description('Kapcsolja be a szinkront, és adja meg a Google OAuth adatokat. Az időpontok automatikusan megjelennek a naptárában.')
                ->columns(2)
                ->schema([
                    SwitchField::make('google_calendar_enabled')
                        ->label('Szinkron bekapcsolva')
                        ->columnSpanFull(),
                    FlexTextInput::make('google_calendar_id')
                        ->label('Naptár ID')
                        ->columnSpanFull(),
                    FlexTextInput::make('google_credentials.client_id')
                        ->label('Client ID')
                        ->password()
                        ->revealable(),
                    FlexTextInput::make('google_credentials.client_secret')
                        ->label('Client secret')
                        ->password()
                        ->revealable(),
                    FlexTextInput::make('google_credentials.refresh_token')
                        ->label('Refresh token')
                        ->password()
                        ->revealable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        $worker = Filament::auth()->user();
        abort_unless($worker instanceof Worker, 403);

        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();
            $credentials = $data['google_credentials'] ?? [];
            $existing = $worker->google_credentials ?? [];

            $worker->update([
                'google_calendar_enabled' => (bool) ($data['google_calendar_enabled'] ?? false),
                'google_calendar_id' => $data['google_calendar_id'] ?? null,
                'google_credentials' => array_filter([
                    'client_id' => $credentials['client_id'] ?? $existing['client_id'] ?? null,
                    'client_secret' => $credentials['client_secret'] ?? $existing['client_secret'] ?? null,
                    'refresh_token' => $credentials['refresh_token'] ?? $existing['refresh_token'] ?? null,
                    'access_token' => $existing['access_token'] ?? null,
                    'access_token_expires_at' => $existing['access_token_expires_at'] ?? null,
                ], fn ($value) => filled($value)),
            ]);

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->success()
            ->title('Google Naptár beállítások mentve')
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Mentés')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])
                        ->alignment($this->getFormActionsAlignment())
                        ->fullWidth($this->hasFullWidthFormActions())
                        ->sticky($this->areFormActionsSticky())
                        ->key('form-actions'),
                ]),
        ]);
    }
}
