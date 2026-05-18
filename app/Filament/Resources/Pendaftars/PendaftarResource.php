<?php

namespace App\Filament\Resources\Pendaftars;

use App\Filament\Resources\Pendaftars\Pages\CreatePendaftar;
use App\Filament\Resources\Pendaftars\Pages\EditPendaftar;
use App\Filament\Resources\Pendaftars\Pages\ListPendaftars;
use App\Filament\Resources\Pendaftars\Pages\ViewPendaftar;
use App\Filament\Resources\Pendaftars\RelationManagers\JawabanPendaftarsRelationManager;
use App\Filament\Resources\Pendaftars\Schemas\PendaftarForm;
use App\Filament\Resources\Pendaftars\Schemas\PendaftarInfolist;
use App\Filament\Resources\Pendaftars\Tables\PendaftarsTable;
use App\Models\Pendaftar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PendaftarResource extends Resource
{
    protected static ?string $model = Pendaftar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $recordTitleAttribute = 'nama';

    protected static ?string $navigationLabel = 'Pendaftar';

    protected static ?string $modelLabel = 'Pendaftar';

    protected static ?string $pluralModelLabel = 'Daftar Pendaftar';

    protected static \UnitEnum|string|null $navigationGroup = 'Rekrutmen';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PendaftarForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PendaftarInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PendaftarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            JawabanPendaftarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPendaftars::route('/'),
            'create' => CreatePendaftar::route('/create'),
            'view' => ViewPendaftar::route('/{record}'),
            'edit' => EditPendaftar::route('/{record}/edit'),
        ];
    }

    // Badge: jumlah pendaftar menunggu di sidebar nav
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::menunggu()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ACCESS CONTROL — siapa saja yang boleh akses Resource ini
    // ═══════════════════════════════════════════════════════════════════════

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAny([
            'kelola_pendaftar',
            'kelola_pendaftar_divisi',
        ]) ?? false;
    }

    // ═══════════════════════════════════════════════════════════════════════
    // QUERY SCOPING — Ketua Divisi hanya lihat pendaftar di divisinya
    // ═══════════════════════════════════════════════════════════════════════

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // Super admin / ketua UKM punya 'kelola_pendaftar' (semua)
        if ($user?->can('kelola_pendaftar')) {
            return $query;
        }

        // Ketua Divisi hanya lihat pendaftar di divisinya saja
        if ($user?->can('kelola_pendaftar_divisi') && $user->divisi_id) {
            return $query->where('divisi_id', $user->divisi_id);
        }

        // Fallback: tidak punya akses sama sekali
        return $query->whereRaw('1 = 0');
    }
}
