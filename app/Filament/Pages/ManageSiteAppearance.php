<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\SiteThemeService;
use App\Support\SiteButtonStyles;
use App\Support\SiteColors;
use App\Support\SiteFonts;
use App\Support\SiteStylePresets;
use App\Support\SiteTypography;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexColorPickerField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Throwable;
use UnitEnum;

class ManageSiteAppearance extends Page
{
    use CanUseDatabaseTransactions;
    use InteractsWithFormActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Swatch;

    protected static ?string $navigationLabel = 'Megjelenés';

    protected static string|UnitEnum|null $navigationGroup = 'Webhely';

    protected static ?string $title = 'Megjelenés és tipográfia';

    protected static ?string $slug = 'site-appearance';

    protected static ?int $navigationSort = 5;

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

        $settings = SiteSetting::current();

        $this->form->fill([
            'style_preset' => $settings->resolvedStylePreset(),
            ...$settings->only([
                'font_sans',
                'font_display',
                'font_size_base',
                'line_height',
            ]),
            ...SiteColors::toFormState(
                is_array($settings->global_colors) ? $settings->global_colors : null
            ),
            ...SiteButtonStyles::toFormState(
                is_array($settings->button_styles) ? $settings->button_styles : null,
                $settings->resolvedGlobalColors()
            ),
            ...SiteTypography::toFormState(
                is_array($settings->typography) ? $settings->typography : null,
                $settings->resolvedStylePreset()
            ),
        ]);
    }

    public function selectStylePreset(string $key): void
    {
        abort_unless(static::canAccess(), 403);

        $key = SiteStylePresets::normalizeKey($key);
        $preset = SiteStylePresets::get($key);
        $colors = SiteColors::normalize($preset['colors'] ?? null);

        $this->form->fill([
            ...($this->data ?? []),
            'style_preset' => $key,
            'font_sans' => $preset['font_sans'],
            'font_display' => $preset['font_display'],
            'font_size_base' => $preset['font_size_base'],
            'line_height' => $preset['line_height'],
            ...SiteColors::toFormState($colors),
            ...SiteButtonStyles::toFormState(SiteButtonStyles::defaults($colors), $colors),
            ...SiteTypography::toFormState(SiteTypography::defaults($key), $key),
        ]);

        Notification::make()
            ->title('Stílus kiválasztva: '.$preset['label'])
            ->body('A színek, gombok, betűk és tipográfia méretek frissültek. Mentés után a térközök és borderek is érvényesülnek.')
            ->success()
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Group::make([
                $this->appearanceSection(
                    'alap-stilus',
                    'Alap stílus',
                    'Válassz kategóriát, majd egy vizuális irányt. A stílus a térközöket, sarkokat, gombokat és tipográfiát állítja; a színeket utána finomhangolhatod.',
                )->schema([
                    Hidden::make('style_preset')
                        ->required()
                        ->rule('in:'.implode(',', SiteStylePresets::keys())),
                    ViewField::make('style_preset_cards')
                        ->label('')
                        ->view('filament.pages.partials.site-style-preset-cards')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),
                $this->appearanceSection(
                    'fejlec-lablec',
                    'Fejléc és lábléc',
                    'Új böngészőlapon nyílik a GrapesJS szerkesztő (Filament keret nélkül).',
                )->schema([
                    ViewField::make('layout_links')
                        ->label('')
                        ->view('filament.pages.partials.site-layout-builder-links')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),
                $this->appearanceSection(
                    'globalis-szinek',
                    'Globális színek',
                    'A beépített szekciók és dinamikus blokkok ezeket használják. Módosításkor a teljes webhely frissül (pl. var(--color-primary)).',
                )
                    ->columns(2)
                    ->schema([
                        FlexColorPickerField::make('color_primary')
                            ->label('Primary')
                            ->hex()
                            ->required(),
                        FlexColorPickerField::make('color_text')
                            ->label('Szöveg')
                            ->hex()
                            ->required(),
                        FlexColorPickerField::make('color_accent')
                            ->label('Accent')
                            ->hex()
                            ->required(),
                        FlexColorPickerField::make('color_light')
                            ->label('Light')
                            ->hex()
                            ->required(),
                        Repeater::make('color_extra')
                            ->label('További színek')
                            ->addActionLabel('Szín hozzáadása')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['key'] ?? null)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Név')
                                    ->required()
                                    ->maxLength(40)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                                        if (filled($get('key'))) {
                                            return;
                                        }

                                        $key = SiteColors::normalizeKey('', (string) $state);
                                        if ($key !== null) {
                                            $set('key', $key);
                                        }
                                    }),
                                TextInput::make('key')
                                    ->label('CSS kulcs')
                                    ->required()
                                    ->maxLength(40)
                                    ->helperText('var(--color-{kulcs}) – csak kisbetű, szám, kötőjel')
                                    ->rule('regex:/^[a-z][a-z0-9-]{0,40}$/')
                                    ->rule(fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                                        if (in_array((string) $value, SiteColors::baseKeys(), true)) {
                                            $fail('Ez a kulcs foglalt (primary, text, accent, light).');
                                        }
                                    }),
                                FlexColorPickerField::make('value')
                                    ->label('Szín')
                                    ->hex()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
                $this->appearanceSection(
                    'gombok',
                    'Gombok',
                    'Három gombstílus a webhelyen. Az oldalépítőben gombonként választható (elsődleges / másodlagos / inverse). Üres mezőnél az alapértelmezés érvényes (másodlagos háttér tipikusan átlátszó).',
                )->schema([
                    Section::make('Elsődleges')
                        ->description('Kitöltött CTA – fő művelet.')
                        ->columns(4)
                        ->compact()
                        ->schema([
                            FlexColorPickerField::make('btn_primary_bg')->label('Háttér')->hex(),
                            FlexColorPickerField::make('btn_primary_fg')->label('Szöveg')->hex(),
                            FlexColorPickerField::make('btn_primary_hover_bg')->label('Hover háttér')->hex(),
                            FlexColorPickerField::make('btn_primary_border')->label('Keret')->hex(),
                        ]),
                    Section::make('Másodlagos')
                        ->description('Outline / visszafogott gomb. Üres háttér = átlátszó (keretes stílus).')
                        ->columns(4)
                        ->compact()
                        ->schema([
                            FlexColorPickerField::make('btn_secondary_bg')->label('Háttér')->hex(),
                            FlexColorPickerField::make('btn_secondary_fg')->label('Szöveg')->hex(),
                            FlexColorPickerField::make('btn_secondary_hover_bg')->label('Hover háttér')->hex(),
                            FlexColorPickerField::make('btn_secondary_border')->label('Keret')->hex(),
                        ]),
                    Section::make('Inverse')
                        ->description('Világos gomb sötét háttéren (hero, banner).')
                        ->columns(4)
                        ->compact()
                        ->schema([
                            FlexColorPickerField::make('btn_inverse_bg')->label('Háttér')->hex(),
                            FlexColorPickerField::make('btn_inverse_fg')->label('Szöveg')->hex(),
                            FlexColorPickerField::make('btn_inverse_hover_bg')->label('Hover háttér')->hex(),
                            FlexColorPickerField::make('btn_inverse_border')->label('Keret')->hex(),
                        ]),
                ]),
                $this->appearanceSection(
                    'tipografia',
                    'Tipográfia',
                    'Két betűtípus a teljes webhelyen; a méreteket szerepenként állíthatod. A lista árvíztűrő (magyar ékezetes) Google / Bunny Fonts családokból áll.',
                )
                    ->columns(2)
                    ->schema([
                        SelectField::make('font_sans')
                            ->label('Törzsszöveg betűtípus')
                            ->options(SiteFonts::selectOptions())
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->helperText('Pl. „'.SiteFonts::PANGRAM.'”'),
                        SelectField::make('font_display')
                            ->label('Címsor betűtípus')
                            ->options(SiteFonts::selectOptions())
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->helperText('Serif / display ajánlott címsorhoz; sans is választható.'),
                        FlexTextInput::make('font_size_base')
                            ->label('Alap betűméret')
                            ->placeholder('16px')
                            ->helperText('Törzsszöveg alapmérete (pl. 16px vagy 1rem)'),
                        FlexTextInput::make('line_height')
                            ->label('Sormagasság')
                            ->placeholder('1.6')
                            ->helperText('Pl. 1.6'),
                        FlexTextInput::make('type_section_title')
                            ->label(SiteTypography::roleLabels()['section_title'])
                            ->placeholder('clamp(1.85rem, 3.5vw, 2.75rem)')
                            ->helperText(SiteTypography::roleHints()['section_title'])
                            ->columnSpan(1),
                        FlexTextInput::make('type_section_lead')
                            ->label(SiteTypography::roleLabels()['section_lead'])
                            ->placeholder('1.05rem')
                            ->helperText(SiteTypography::roleHints()['section_lead'])
                            ->columnSpan(1),
                        FlexTextInput::make('type_card_title')
                            ->label(SiteTypography::roleLabels()['card_title'])
                            ->placeholder('1.15rem')
                            ->helperText(SiteTypography::roleHints()['card_title'])
                            ->columnSpan(1),
                        FlexTextInput::make('type_card_body')
                            ->label(SiteTypography::roleLabels()['card_body'])
                            ->placeholder('0.95rem')
                            ->helperText(SiteTypography::roleHints()['card_body'])
                            ->columnSpan(1),
                    ]),
            ])
                ->extraAttributes([
                    'class' => 'ts-appearance-accordion flex flex-col gap-6',
                    'x-data' => '{ openKey: \'alap-stilus\' }',
                    'x-on:click.capture' => 'const header = $event.target.closest(\'.fi-section-header\'); if (! header) return; const wrap = header.closest(\'[data-appearance-section]\'); if (! wrap || ! $el.contains(wrap)) return; const key = wrap.getAttribute(\'data-appearance-section\'); if (! key) return; $event.preventDefault(); $event.stopPropagation(); openKey = key; $el.querySelectorAll(\'[data-appearance-section]\').forEach((node) => { const section = node.querySelector(\'section.fi-collapsible\'); if (! section) return; const data = Alpine.$data(section); if (data) data.isCollapsed = node.getAttribute(\'data-appearance-section\') !== key; });',
                ]),
        ]);
    }

    protected function appearanceSection(string $key, string $heading, ?string $description = null): Section
    {
        return Section::make($heading)
            ->description($description)
            ->collapsible()
            ->collapsed($key !== 'alap-stilus')
            ->extraAttributes([
                'data-appearance-section' => $key,
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $state = $this->form->getState();
            $settings = SiteSetting::current();
            $globalColors = SiteColors::fromFormState($state);
            $stylePreset = SiteStylePresets::normalizeKey($state['style_preset'] ?? null);
            $settings->update([
                'style_preset' => $stylePreset,
                'font_sans' => $state['font_sans'] ?? $settings->font_sans,
                'font_display' => $state['font_display'] ?? $settings->font_display,
                'font_size_base' => $state['font_size_base'] ?? $settings->font_size_base,
                'line_height' => $state['line_height'] ?? $settings->line_height,
                'typography' => SiteTypography::fromFormState($state, $stylePreset),
                'global_colors' => $globalColors,
                'button_styles' => SiteButtonStyles::fromFormState($state, $globalColors),
            ]);
            app(SiteThemeService::class)->writeThemeCss($settings->fresh());

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->rollBackDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Megjelenés mentve')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Html::make(new HtmlString(<<<'HTML'
                <style>
                    .ts-appearance-save-bar {
                        position: sticky;
                        bottom: 0;
                        z-index: 40;
                        margin-top: 1.25rem;
                        padding: 0.85rem 1rem;
                        border-top: 1px solid color-mix(in srgb, currentColor 12%, transparent);
                        background: color-mix(in srgb, var(--gray-50, #f9fafb) 92%, transparent);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    }
                    .dark .ts-appearance-save-bar,
                    :is(.dark) .ts-appearance-save-bar {
                        background: color-mix(in srgb, var(--gray-950, #0a0a0a) 88%, transparent);
                    }
                </style>
            HTML)),
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Megjelenés mentése')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ])
                        ->alignment(Alignment::Start)
                        ->extraAttributes(['class' => 'ts-appearance-save-bar'])
                        ->key('form-actions'),
                ]),
        ]);
    }
}
