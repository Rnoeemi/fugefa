<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Support\GrapesJs\SiteDynamicBlockRepository;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class ManageDynamicBlocks extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CubeTransparent;

    protected static ?string $navigationLabel = 'Dinamikus blokkok';

    protected static string|UnitEnum|null $navigationGroup = 'Webhely';

    protected static ?string $title = 'Dinamikus blokkok';

    protected static ?string $slug = 'dynamic-blocks';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->isSystemAdmin();
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public function table(Table $table): Table
    {
        $repo = app(SiteDynamicBlockRepository::class);

        return $table
            ->records(fn (): array => $repo->all())
            ->columns([
                TextColumn::make('label')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id')
                    ->label('ID')
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('dynamicKey')
                    ->label('Kulcs')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('category')
                    ->label('Kategória')
                    ->formatStateUsing(fn (string $state): string => $repo->categoryOptions()[$state] ?? $state),
                IconColumn::make('view_exists')
                    ->label('Blade')
                    ->boolean(),
                TextColumn::make('websites')
                    ->label('Webhelyek')
                    ->formatStateUsing(function ($state) use ($repo): string {
                        $options = $repo->websiteOptions();
                        $keys = is_array($state) ? $state : [];

                        return collect($keys)
                            ->map(fn (string $key): string => $options[$key] ?? $key)
                            ->implode(', ') ?: '—';
                    })
                    ->wrap(),
                TextColumn::make('params')
                    ->label('Paraméterek')
                    ->formatStateUsing(fn ($state): string => (string) count(is_array($state) ? $state : [])),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Szerkesztés')
                    ->icon(Heroicon::PencilSquare)
                    ->slideOver()
                    ->modalHeading(fn (array $record): string => 'Blokk: '.$record['label'])
                    ->fillForm(function (array $record) use ($repo): array {
                        $block = $repo->find($record['id']);

                        return [
                            'label' => $block['label'] ?? '',
                            'category' => $block['category'] ?? 'accommodations',
                            'icon' => $block['icon'] ?? 'square',
                            'keywords' => $block['keywords'] ?? [],
                            'className' => $block['className'] ?? 'ts-dyn-block',
                            'scopes' => $block['scopes'] ?? ['page'],
                            'dynamicKey' => $block['dynamicKey'] ?? '',
                            'gjsType' => $block['gjsType'] ?? '',
                            'view' => $block['view'] ?? '',
                            'params' => $block['params'] ?? [],
                            'websites' => $block['websites'] ?? [],
                        ];
                    })
                    ->schema([
                        Section::make('Alapadatok')
                            ->columns(2)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Megjelenő név')
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('category')
                                    ->label('Kategória')
                                    ->options(fn () => $repo->categoryOptions())
                                    ->required()
                                    ->native(false),
                                Select::make('icon')
                                    ->label('Ikon')
                                    ->options(fn () => $repo->iconOptions())
                                    ->required()
                                    ->searchable()
                                    ->native(false),
                                TagsInput::make('keywords')
                                    ->label('Kulcsszavak')
                                    ->columnSpanFull(),
                                TextInput::make('className')
                                    ->label('CSS osztály')
                                    ->columnSpanFull(),
                                CheckboxList::make('scopes')
                                    ->label('Hatókör')
                                    ->options([
                                        'page' => 'Oldal',
                                        'header' => 'Fejléc',
                                        'footer' => 'Lábléc',
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Technikai')
                            ->columns(2)
                            ->schema([
                                TextInput::make('dynamicKey')
                                    ->label('Dynamic key')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('gjsType')
                                    ->label('GrapesJS típus')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('view')
                                    ->label('Blade view')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpanFull()
                                    ->helperText('A renderer a site.dynamic.{dynamicKey} nézetet használja.'),
                            ]),
                        Section::make('Paraméterek')
                            ->description('Ezek jelennek meg a builder jobb oldali tulajdonságainál.')
                            ->schema([
                                Repeater::make('params')
                                    ->label('')
                                    ->addActionLabel('Paraméter hozzáadása')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['key'] ?? null)
                                    ->schema([
                                        TextInput::make('key')
                                            ->label('Kulcs')
                                            ->required(),
                                        TextInput::make('attr')
                                            ->label('HTML attr')
                                            ->placeholder('data-title')
                                            ->helperText('Üresen: data-{kulcs}'),
                                        TextInput::make('label')
                                            ->label('Címke')
                                            ->required(),
                                        Select::make('type')
                                            ->label('Típus')
                                            ->options([
                                                'text' => 'Szöveg',
                                                'textarea' => 'Hosszú szöveg',
                                                'number' => 'Szám',
                                                'select' => 'Select',
                                                'media' => 'Média (kép/videó URL)',
                                                'rich' => 'Rich szöveg (HTML)',
                                                'link' => 'Link (route / külső URL)',
                                                'items' => 'Elemlista (FAQ / vélemények)',
                                            ])
                                            ->required()
                                            ->live()
                                            ->native(false),
                                        TextInput::make('default')
                                            ->label('Alapértelmezett'),
                                        TextInput::make('min')
                                            ->label('Min')
                                            ->numeric()
                                            ->visible(fn ($get): bool => $get('type') === 'number'),
                                        TextInput::make('max')
                                            ->label('Max')
                                            ->numeric()
                                            ->visible(fn ($get): bool => $get('type') === 'number'),
                                        Select::make('options')
                                            ->label('Option set')
                                            ->options(fn () => $repo->optionSetKeys())
                                            ->visible(fn ($get): bool => $get('type') === 'select')
                                            ->native(false),
                                    ])
                                    ->columns(2),
                            ]),
                        Section::make('Webhelyek')
                            ->description('Melyik website presetben legyen elérhető a builderben.')
                            ->schema([
                                CheckboxList::make('websites')
                                    ->label('Aktív webhelyek')
                                    ->options(fn () => $repo->websiteOptions())
                                    ->columns(2),
                            ]),
                    ])
                    ->action(function (array $data, array $record) use ($repo): void {
                        $repo->update($record['id'], $data);

                        Notification::make()
                            ->title('Dinamikus blokk mentve')
                            ->success()
                            ->send();

                        $this->resetTable();
                    }),
            ])
            ->paginated(false)
            ->defaultSort('label');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->description('Az élő (SQL / SiteSetting) builder elemek JSON-definíciói. A mentés a resources/site-builder fájlokat írja.')
                ->schema([
                    EmbeddedTable::make(),
                ]),
        ]);
    }
}
