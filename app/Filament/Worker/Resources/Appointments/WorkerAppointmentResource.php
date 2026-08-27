<?php

namespace App\Filament\Worker\Resources\Appointments;

use App\Enums\AppointmentStatus;
use App\Filament\Worker\Resources\Appointments\Pages\ListWorkerAppointments;
use App\Models\Appointment;
use App\Models\Worker;
use BackedEnum;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\PhoneField;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\SelectField;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WorkerAppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Időpontjaim';

    protected static ?string $modelLabel = 'időpont';

    protected static ?string $pluralModelLabel = 'Időpontok';

    protected static string|UnitEnum|null $navigationGroup = 'Foglalások';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'appointments';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $worker = Filament::auth()->user();

        if ($worker instanceof Worker) {
            $query->where('worker_id', $worker->id);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Időpont')
                ->columns(2)
                ->schema([
                    SelectField::make('status')
                        ->label('Státusz')
                        ->options(AppointmentStatus::class)
                        ->required(),
                    DateTimePicker::make('starts_at')
                        ->label('Kezdés')
                        ->seconds(false)
                        ->required(),
                    DateTimePicker::make('ends_at')
                        ->label('Befejezés')
                        ->seconds(false)
                        ->required(),
                    FlexTextInput::make('customer_name')
                        ->label('Ügyfél neve')
                        ->required(),
                    FlexTextInput::make('customer_email')
                        ->label('E-mail')
                        ->email()
                        ->required(),
                    PhoneField::make('customer_phone')
                        ->label('Telefon')
                        ->defaultCountry('HU'),
                    FlexTextareaField::make('notes')
                        ->label('Megjegyzés')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Kezdés')
                    ->dateTime('Y.m.d. H:i')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Ügyfél')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('E-mail'),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(AppointmentStatus::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkerAppointments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
