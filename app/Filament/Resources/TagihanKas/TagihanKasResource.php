<?php

namespace App\Filament\Resources\TagihanKas;

use App\Filament\Resources\TagihanKas\Pages\CreateTagihanKas;
use App\Filament\Resources\TagihanKas\Pages\EditTagihanKas;
use App\Filament\Resources\TagihanKas\Pages\ListTagihanKas;
use App\Filament\Resources\TagihanKas\Schemas\TagihanKasForm;
use App\Filament\Resources\TagihanKas\Tables\TagihanKasTable;
use App\Models\TagihanKas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TagihanKasResource extends Resource
{
    protected static ?string $model = TagihanKas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Tagihan Kas';

    protected static ?string $modelLabel = 'Tagihan Kas';

    protected static ?string $pluralModelLabel = 'Tagihan Kas Anggota';

    protected static \UnitEnum|string|null $navigationGroup = 'E-Kas Keuangan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return TagihanKasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagihanKasTable::configure($table);
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
            'index' => ListTagihanKas::route('/'),
            'create' => CreateTagihanKas::route('/create'),
            'edit' => EditTagihanKas::route('/{record}/edit'),
        ];
    }

    /**
     * Badge: tampilkan jumlah tagihan belum dibayar di sidebar.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::belumDibayar()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('kelola_tagihan_kas') ?? false;
    }
}
