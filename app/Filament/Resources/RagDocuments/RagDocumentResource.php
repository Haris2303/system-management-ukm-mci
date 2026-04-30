<?php

namespace App\Filament\Resources\RagDocuments;

use App\Filament\Resources\RagDocuments\Pages\CreateRagDocument;
use App\Filament\Resources\RagDocuments\Pages\EditRagDocument;
use App\Filament\Resources\RagDocuments\Pages\ListRagDocuments;
use App\Filament\Resources\RagDocuments\Schemas\RagDocumentForm;
use App\Filament\Resources\RagDocuments\Tables\RagDocumentsTable;
use App\Models\RagDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RagDocumentResource extends Resource
{
    protected static ?string $model = RagDocument::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen Chatbot';

    protected static string|UnitEnum|null $navigationGroup = 'Chatbot AI';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return RagDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RagDocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRagDocuments::route('/'),
            'create' => CreateRagDocument::route('/create'),
        ];
    }
}
