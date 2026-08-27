<?php

namespace App\Filament\Pages;

use App\Enums\SiteModule;
use App\Models\User;
use App\Services\ModuleService;
use BackedEnum;
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
use Illuminate\Contracts\Support\Htmlable;
use Throwable;
use UnitEnum;

class ManageModules extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PuzzlePiece;

    protected static ?string $navigationLabel = 'Modulok';

    protected static string|UnitEnum|null $navigationGroup = 'Adminisztráció';

    protected static ?string $title = 'Modulok';

    protected static ?string $slug = 'modules';

    protected static ?int $navigationSort = 98;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->isSystemAdmin();
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(
            app(ModuleService::class)->getFormState(),
        );
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Modulok')
                ->description('Kapcsolja be vagy ki a rendszer moduljait. A kikapcsolt modulok nem jelennek meg az adminban, és a kapcsolódó frontend felületek 404-et adnak.')
                ->columns(4)
                ->schema(
                    collect(SiteModule::cases())
                        ->map(fn (SiteModule $module): SwitchField => SwitchField::make("modules.{$module->value}")
                            ->label($module->getLabel())
                            ->inline()
                            ->hiddenLabel(false)
                            ->beforeLabel(
                                Action::make("moduleInfo_{$module->value}")
                                    ->label('Információ')
                                    ->icon(Heroicon::InformationCircle)
                                    ->iconButton()
                                    ->color('gray')
                                    ->modalHeading($module->getLabel())
                                    ->modalDescription($module->getDescription())
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Bezárás'),
                            )
                        )
                        ->all(),
                ),
        ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            app(ModuleService::class)->syncFromFormState($data);

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
            ->title('Modulok mentve')
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Modulok';
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
