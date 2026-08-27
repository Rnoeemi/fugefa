<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplates\Schemas\EmailTemplateForm;
use App\Filament\Resources\EmailTemplates\Tables\EmailTemplatesTable;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmailTemplateResource extends BaseResource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Envelope;

    protected static ?string $navigationLabel = 'E-mail sablonok';

    protected static ?string $modelLabel = 'e-mail sablon';

    protected static ?string $pluralModelLabel = 'E-mail sablonok';

    protected static string|UnitEnum|null $navigationGroup = 'Adminisztráció';

    protected static ?int $navigationSort = 80;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return EmailTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
