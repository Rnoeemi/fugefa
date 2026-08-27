<?php

namespace App\Filament\Resources\Guests\RelationManagers;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Models\Booking;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\NumberStepper;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Foglalások';

    protected static ?string $modelLabel = 'foglalás';

    protected static ?string $pluralModelLabel = 'foglalások';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SelectField::make('accommodation_id')
                    ->label('Szállás')
                    ->relationship('accommodation', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                FlexDatePicker::make('check_in')
                    ->label('Érkezés')
                    ->required(),
                FlexDatePicker::make('check_out')
                    ->label('Távozás')
                    ->required(),
                NumberStepper::make('guests_count')
                    ->label('Vendégek száma')
                    ->minValue(1)
                    ->maxValue(50)
                    ->default(1)
                    ->required(),
                SelectField::make('status')
                    ->label('Státusz')
                    ->options(BookingStatus::class)
                    ->enum(BookingStatus::class)
                    ->default(BookingStatus::Confirmed)
                    ->required(),
                SelectField::make('source')
                    ->label('Forrás')
                    ->options(BookingSource::class)
                    ->enum(BookingSource::class)
                    ->default(BookingSource::Admin)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge(),
                TextColumn::make('accommodation.name')
                    ->label('Szállás'),
                TextColumn::make('check_in')
                    ->label('Érkezés')
                    ->date('Y.m.d.'),
                TextColumn::make('check_out')
                    ->label('Távozás')
                    ->date('Y.m.d.'),
                TextColumn::make('guests_count')
                    ->label('Fő')
                    ->alignCenter(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['guest_id'] = $this->getOwnerRecord()->getKey();
                        $data['created_by'] = auth()->id();
                        $data['source'] ??= BookingSource::Admin->value;

                        if (($data['status'] ?? null) === BookingStatus::Confirmed->value) {
                            $data['confirmed_at'] = now();
                        }

                        return BookingForm::validateAvailability($data, enforceMinNights: false);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Booking $record): string => BookingResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->bookings()->count();

        return $count > 0 ? (string) $count : null;
    }
}
