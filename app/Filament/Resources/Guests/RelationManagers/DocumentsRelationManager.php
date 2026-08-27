<?php

namespace App\Filament\Resources\Guests\RelationManagers;

use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexDatePicker;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextInput;
use Bjanczak\FilamentFlexFields\Filament\Forms\Components\FlexTextareaField;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Okmányok';

    protected static ?string $modelLabel = 'okmány';

    protected static ?string $pluralModelLabel = 'okmányok';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                FlexTextInput::make('full_name_on_document')
                    ->label('Név az igazolványon')
                    ->maxLength(255),
                FlexTextInput::make('document_number')
                    ->label('Okmányszám')
                    ->maxLength(64),
                FlexDatePicker::make('birth_date')
                    ->label('Születési dátum'),
                FlexTextInput::make('nationality')
                    ->label('Állampolgárság')
                    ->maxLength(8)
                    ->default('HU'),
                FlexTextInput::make('address_on_card')
                    ->label('Cím a lakcímkártyán')
                    ->maxLength(255)
                    ->columnSpanFull(),
                FileUpload::make('id_card_front_path')
                    ->label('Személyi előlap')
                    ->image()
                    ->disk('public')
                    ->directory('guest-documents')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->required(),
                FileUpload::make('id_card_back_path')
                    ->label('Személyi hátlap')
                    ->image()
                    ->disk('public')
                    ->directory('guest-documents')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->required(),
                FileUpload::make('address_card_front_path')
                    ->label('Lakcímkártya előlap')
                    ->image()
                    ->disk('public')
                    ->directory('guest-documents')
                    ->visibility('public')
                    ->imagePreviewHeight('160')
                    ->required()
                    ->columnSpanFull(),
                FlexTextareaField::make('validation_notes')
                    ->label('Validációs megjegyzések')
                    ->rows(3)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function (mixed $state): ?string {
                        if (is_array($state)) {
                            return implode("\n", $state);
                        }

                        return filled($state) ? (string) $state : null;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name_on_document')
            ->columns([
                ImageColumn::make('id_card_front_path')
                    ->label('Előlap')
                    ->disk('public')
                    ->height(48)
                    ->square(),
                ImageColumn::make('id_card_back_path')
                    ->label('Hátlap')
                    ->disk('public')
                    ->height(48)
                    ->square(),
                ImageColumn::make('address_card_front_path')
                    ->label('Lakcím')
                    ->disk('public')
                    ->height(48)
                    ->square(),
                TextColumn::make('full_name_on_document')
                    ->label('Név')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('document_number')
                    ->label('Okmányszám')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('booking.check_in')
                    ->label('Foglalás')
                    ->date()
                    ->placeholder('—'),
                IconColumn::make('is_validated')
                    ->label('Valid')
                    ->boolean(),
                IconColumn::make('ntak_ready')
                    ->label('NTAK')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Feltöltve')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->documents()->count();

        return $count > 0 ? (string) $count : null;
    }
}
