<?php

namespace App\Filament\Worker\Resources\Packages;

use App\Filament\Worker\Resources\Packages\Pages\CreateWorkerPackage;
use App\Filament\Worker\Resources\Packages\Pages\EditWorkerPackage;
use App\Filament\Worker\Resources\Packages\Pages\ListWorkerPackages;
use App\Models\Worker;
use App\Models\WorkerPackage;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WorkerPackageResource extends Resource
{
    protected static ?string $model = WorkerPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleStack;

    protected static ?string $navigationLabel = 'Csomagok';

    protected static ?string $modelLabel = 'csomag';

    protected static ?string $pluralModelLabel = 'Csomagok';

    protected static string|UnitEnum|null $navigationGroup = 'Beállítások';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'packages';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $worker = Filament::auth()->user();

        if ($worker instanceof Worker) {
            return $query->where('worker_id', $worker->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Csomag')
                ->columns(2)
                ->schema([
                    FlexTextInput::make('name')
                        ->label('Megnevezés')
                        ->required()
                        ->columnSpanFull(),
                    FlexTextareaField::make('description')
                        ->label('Leírás')
                        ->rows(3)
                        ->columnSpanFull(),
                    FlexTextInput::make('duration_minutes')
                        ->label('Időtartam (perc)')
                        ->numeric()
                        ->required()
                        ->minValue(15)
                        ->step(15)
                        ->helperText('15 perces lépésközzel (pl. 30, 45, 60).'),
                    FlexTextInput::make('price')
                        ->label('Ár (Ft)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->helperText('Csak tájékoztató – online fizetés nincs.'),
                    FlexTextInput::make('sort_order')
                        ->label('Sorrend')
                        ->numeric()
                        ->default(0),
                    SwitchField::make('is_active')
                        ->label('Aktív')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duration_minutes')
                    ->label('Időtartam')
                    ->suffix(' perc')
                    ->alignEnd(),
                TextColumn::make('price')
                    ->label('Ár')
                    ->formatStateUsing(fn ($state): string => number_format((int) $state, 0, ',', ' ').' Ft')
                    ->alignEnd(),
                IconColumn::make('is_active')
                    ->label('Aktív')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Sorrend')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkerPackages::route('/'),
            'create' => CreateWorkerPackage::route('/create'),
            'edit' => EditWorkerPackage::route('/{record}/edit'),
        ];
    }
}
