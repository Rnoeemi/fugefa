<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\PhoneNormalizer;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
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
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Throwable;
use UnitEnum;

class ManageSiteSettings extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static ?string $navigationLabel = 'Weboldal beállítások';

    protected static string|UnitEnum|null $navigationGroup = 'Webhely';

    protected static ?string $title = 'Weboldal beállítások';

    protected static ?string $slug = 'site-settings';

    protected static ?int $navigationSort = 10;

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

        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kapcsolat')
                ->columns(2)
                ->schema([
                    FlexTextInput::make('site_name')
                        ->label('Oldal neve')
                        ->required()
                        ->columnSpanFull(),
                    PhoneField::make('phone')
                        ->label('Telefon')
                        ->defaultCountry('HU'),
                    FlexTextInput::make('email')
                        ->label('Nyilvános e-mail')
                        ->email()
                        ->required(),
                    FlexTextInput::make('notification_email')
                        ->label('Értesítési e-mail (foglalások)')
                        ->email()
                        ->helperText('Ide érkeznek az új foglalások admin értesítői.'),
                    FlexTextInput::make('contact_notification_email')
                        ->label('Értesítési e-mail (kapcsolat)')
                        ->email()
                        ->helperText('Ide érkeznek a kapcsolati űrlap üzenetei. Üresen a nyilvános e-mail címre megy.'),
                    FlexTextInput::make('address')
                        ->label('Cím')
                        ->columnSpanFull(),
                    FlexTextareaField::make('footer_text')
                        ->label('Lábléc szöveg')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $state = $this->form->getState();
            $state['phone'] = PhoneNormalizer::toString($state['phone'] ?? null);

            SiteSetting::current()->update($state);

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->rollBackDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Beállítások mentve')
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
                            ->label('Mentés')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])
                        ->alignment(Alignment::Start)
                        ->key('form-actions'),
                ]),
        ]);
    }
}
