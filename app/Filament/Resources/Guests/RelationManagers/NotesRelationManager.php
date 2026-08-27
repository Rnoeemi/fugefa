<?php

namespace App\Filament\Resources\Guests\RelationManagers;

use App\Enums\GuestStatus;
use App\Models\Guest;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Jegyzetek / jelentések';

    protected static ?string $modelLabel = 'jegyzet';

    protected static ?string $pluralModelLabel = 'jegyzetek';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FlexTextareaField::make('body')
                    ->label('Jegyzet')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                SelectField::make('new_status')
                    ->label('Vendég státusz frissítése')
                    ->options(GuestStatus::class)
                    ->enum(GuestStatus::class)
                    ->placeholder('Ne változtasson')
                    ->helperText('Ha megadod, a vendég státusza is frissül a jegyzet mentésekor.')
                    ->hiddenOn('edit')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Dátum')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Szerző')
                    ->placeholder('—'),
                TextColumn::make('status_snapshot')
                    ->label('Státusz a bejegyzéskor')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('body')
                    ->label('Jegyzet')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        /** @var Guest $guest */
                        $guest = $this->getOwnerRecord();

                        $data['user_id'] = auth()->id();

                        $newStatus = $data['new_status'] ?? null;
                        unset($data['new_status']);

                        if (filled($newStatus)) {
                            $status = GuestStatus::from($newStatus);
                            $guest->update(['status' => $status]);
                            $data['status_snapshot'] = $status->value;
                        } else {
                            $data['status_snapshot'] = $guest->status?->value;
                        }

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        unset($data['new_status']);

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->notes()->count();

        return $count > 0 ? (string) $count : null;
    }
}
