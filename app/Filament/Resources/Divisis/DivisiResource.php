<?php

namespace App\Filament\Resources\Divisis;

use App\Filament\Resources\Divisis\Pages\CreateDivisi;
use App\Filament\Resources\Divisis\Pages\EditDivisi;
use App\Filament\Resources\Divisis\Pages\ListDivisis;
use App\Filament\Resources\Divisis\Schemas\DivisiForm;
use App\Filament\Resources\Divisis\Tables\DivisisTable;
use App\Models\Divisi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DivisiResource extends Resource
{
    protected static ?string $model = Divisi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::RectangleGroup;

    protected static ?string $recordTitleAttribute = 'slug';

    protected static ?string $navigationLabel = 'Divisi';

    protected static ?string $modelLabel = 'Divisi';

    protected static ?string $pluralModelLabel = 'Divisi';

    protected static \UnitEnum|string|null $navigationGroup = 'Rekrutmen';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return DivisiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DivisisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // ── Navigation badge: tampilkan jumlah divisi yang sedang buka ──
    public static function getNavigationBadge(): ?string
    {
        $open = Divisi::where('is_active', true)->count();
        return $open > 0 ? "{$open} buka" : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDivisis::route('/'),
            'create' => CreateDivisi::route('/create'),
            'edit' => EditDivisi::route('/{record}/edit'),
        ];
    }
}
