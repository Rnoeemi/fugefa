<?php

namespace App\Filament\Pages;

use App\Enums\AdminRole;
use App\Services\FilamentResourceRegistry;
use App\Services\ResourcePermissionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;
use UnitEnum;

class ManagePermissions extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $navigationLabel = 'Jogosultságok';

    protected static string|UnitEnum|null $navigationGroup = 'Adminisztráció';

    protected static ?string $title = 'Jogosultságok';

    protected static ?string $slug = 'permissions';

    protected static ?int $navigationSort = 99;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->canManagePermissions() ?? false;
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill(
            app(ResourcePermissionService::class)->getFormState(),
        );
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $resources = app(FilamentResourceRegistry::class)->all();

        return $schema->components([
            Tabs::make('Jogkörök')
                ->tabs(
                    collect(AdminRole::assignableCases())
                        ->map(function (AdminRole $role) use ($resources): Tab {
                            $sections = collect($resources)
                                ->map(function (array $resource) use ($role): Section {
                                    $statePath = "permissions.{$role->value}.{$resource['class']}";

                                    return Section::make($resource['label'])
                                        ->schema([
                                            Grid::make(4)->schema([
                                                Checkbox::make("{$statePath}.can_view")
                                                    ->label('Megtekintheti'),
                                                Checkbox::make("{$statePath}.can_edit")
                                                    ->label('Szerkesztheti'),
                                                Checkbox::make("{$statePath}.can_add")
                                                    ->label('Létrehozhat'),
                                                Checkbox::make("{$statePath}.can_remove")
                                                    ->label('Törölheti'),
                                            ]),
                                        ])
                                        ->compact();
                                })
                                ->all();

                            return Tab::make($role->getLabel())
                                ->schema($sections);
                        })
                        ->all(),
                )
                ->persistTabInQueryString(),
        ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();

            app(ResourcePermissionService::class)->syncFromFormState($data);

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
            ->title('Jogosultságok mentve')
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Jogosultságok';
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
