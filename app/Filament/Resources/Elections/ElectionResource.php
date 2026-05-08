<?php

namespace App\Filament\Resources\Elections;

use App\Filament\Resources\Elections\Pages\CreateElection;
use App\Filament\Resources\Elections\Pages\EditElection;
use App\Filament\Resources\Elections\Pages\ListElections;
use App\Filament\Resources\Elections\Pages\ViewElection;
use App\Filament\Resources\Elections\Schemas\ElectionForm;
use App\Filament\Resources\Elections\Schemas\ElectionInfolist;
use App\Filament\Resources\Elections\Tables\ElectionsTable;
use App\Models\Election;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ElectionResource extends Resource
{
    protected static ?string $model = Election::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'E-Voting';

    protected static ?string $modelLabel = 'Pemilihan';

    protected static ?string $pluralModelLabel = 'Daftar Pemilihan';

    protected static string|UnitEnum|null $navigationGroup = 'E-Voting';

    protected static ?int $navigationSort  = 1;

    public static function form(Schema $schema): Schema
    {
        return ElectionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ElectionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectionsTable::configure($table);
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
            'index' => ListElections::route('/'),
            'create' => CreateElection::route('/create'),
            'view' => ViewElection::route('/{record}'),
            'edit' => EditElection::route('/{record}/edit'),
        ];
    }
}
