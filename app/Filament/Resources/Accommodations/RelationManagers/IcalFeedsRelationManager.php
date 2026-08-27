<?php

namespace App\Filament\Resources\Accommodations\RelationManagers;

use App\Services\IcalSyncService;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SwitchField;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IcalFeedsRelationManager extends RelationManager
{
    protected static string $relationship = 'icalFeeds';

    protected static ?string $title = 'iCal szinkron';

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->supportsIcal();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FlexTextInput::make('name')->label('Megnevezés')->placeholder('Booking.com, Airbnb…'),
            FlexTextInput::make('import_url')->label('Import URL (külső iCal)')->url()->columnSpanFull(),
            SwitchField::make('is_active')->label('Aktív')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Név')->placeholder('—'),
                TextColumn::make('export_token')
                    ->label('Export URL')
                    ->formatStateUsing(fn ($record) => $record->exportUrl())
                    ->copyable()
                    ->wrap(),
                TextColumn::make('import_url')->label('Import URL')->limit(40)->placeholder('—'),
                TextColumn::make('last_imported_at')->label('Utolsó import')->dateTime()->placeholder('—'),
                IconColumn::make('is_active')->boolean()->label('Aktív'),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([
                Action::make('import')
                    ->label('Import most')
                    ->action(function ($record): void {
                        $count = app(IcalSyncService::class)->import($record);
                        Notification::make()
                            ->title("Import kész ({$count} esemény)")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
