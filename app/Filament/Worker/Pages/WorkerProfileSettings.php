<?php

namespace App\Filament\Worker\Pages;

use App\Models\Worker;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
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

class WorkerProfileSettings extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static ?string $navigationLabel = 'Profil és munkaidő';

    protected static string|UnitEnum|null $navigationGroup = 'Beállítások';

    protected static ?string $title = 'Profil és munkaidő';

    protected static ?string $slug = 'profile';

    protected static ?int $navigationSort = 1;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $worker = Filament::auth()->user();
        abort_unless($worker instanceof Worker, 403);

        $this->form->fill([
            'title' => $worker->title,
            'bio' => $worker->bio,
            'work_starts_at' => $worker->workStartsAtTime(),
            'work_ends_at' => $worker->workEndsAtTime(),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bemutatkozás')
                ->description('Ez jelenik meg a nyilvános időpontfoglaló oldalon.')
                ->schema([
                    FlexTextInput::make('title')
                        ->label('Beosztás / szerepkör'),
                    FlexTextareaField::make('bio')
                        ->label('Bemutatkozás')
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
            Section::make('Munkaidő')
                ->description('A foglalási skála ehhez igazodik. 15 perces lépésköz.')
                ->columns(2)
                ->schema([
                    FlexTextInput::make('work_starts_at')
                        ->label('Mettől')
                        ->type('time')
                        ->required(),
                    FlexTextInput::make('work_ends_at')
                        ->label('Meddig')
                        ->type('time')
                        ->required(),
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

            $worker->update([
                'title' => $data['title'] ?? null,
                'bio' => $data['bio'] ?? null,
                'work_starts_at' => $data['work_starts_at'],
                'work_ends_at' => $data['work_ends_at'],
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
            ->title('Profil mentve')
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
