<?php

namespace App\Filament\Resources\TransaksiKas;

use App\Filament\Resources\TransaksiKas\Pages\CreateTransaksiKas;
use App\Filament\Resources\TransaksiKas\Pages\EditTransaksiKas;
use App\Filament\Resources\TransaksiKas\Pages\ListTransaksiKas;
use App\Filament\Resources\TransaksiKas\Schemas\TransaksiKasForm;
use App\Filament\Resources\TransaksiKas\Tables\TransaksiKasTable;
use App\Models\TransaksiKas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransaksiKasResource extends Resource
{
    protected static ?string $model = TransaksiKas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowsRightLeft;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Arus Kas';

    protected static ?string $modelLabel = 'Transaksi Kas';

    protected static ?string $pluralModelLabel = 'Arus Kas Organisasi';

    protected static \UnitEnum|string|null $navigationGroup = 'E-Kas Keuangan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TransaksiKasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransaksiKasTable::configure($table);
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
            'index' => ListTransaksiKas::route('/'),
            'create' => CreateTransaksiKas::route('/create'),
            'edit' => EditTransaksiKas::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('kelola_tagihan_kas') ?? false;
    }
}
