<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SiteThemeService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Throwable;
use UnitEnum;

class ManageSiteCustomCss extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CodeBracket;

    protected static ?string $navigationLabel = 'Egyedi CSS';

    protected static string|UnitEnum|null $navigationGroup = 'Webhely';

    protected static ?string $title = 'Egyedi CSS';

    protected static ?string $slug = 'site-custom-css';

    protected static ?int $navigationSort = 6;

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

        $this->form->fill([
            'custom_css' => SiteSetting::current()->custom_css ?? '',
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Egyedi stílusok')
                ->description('Ez a CSS a buildelt Tailwind után töltődik be külön fájlként (nem inline), így felülírhatja a generált stílusokat.')
                ->schema([
                    CodeEditor::make('custom_css')
                        ->label('CSS')
                        ->language(Language::Css)
                        ->helperText('Publikus fájl: /css/site-custom.css')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $settings = SiteSetting::current();
            $settings->update([
                'custom_css' => $this->form->getState()['custom_css'] ?? '',
            ]);
            app(SiteThemeService::class)->writeCustomCss($settings->fresh());

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->rollBackDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Egyedi CSS mentve')
            ->success()
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
                            ->label('CSS mentése')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])
                        ->alignment(Alignment::Start)
                        ->key('form-actions'),
                ]),
        ]);
    }
}
