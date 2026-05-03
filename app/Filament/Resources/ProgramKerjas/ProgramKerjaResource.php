<?php

namespace App\Filament\Resources\ProgramKerjas;

use App\Fialment\Resources\ProgramKerjas\Pages\ViewProgramKerja;
use App\Filament\Resources\ProgramKerjas\Pages\CreateProgramKerja;
use App\Filament\Resources\ProgramKerjas\Pages\EditProgramKerja;
use App\Filament\Resources\ProgramKerjas\Pages\ListProgramKerjas;
use App\Filament\Resources\ProgramKerjas\Schemas\ProgramKerjaForm;
use App\Filament\Resources\ProgramKerjas\Tables\ProgramKerjasTable;
use App\Models\ProgramKerja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProgramKerjaResource extends Resource
{
    protected static ?string $model = ProgramKerja::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Program Kerja';

    protected static ?string $modelLabel = 'Program Kerja';

    protected static ?string $pluralModelLabel = 'Program Kerja';

    protected static \UnitEnum|string|null $navigationGroup = 'Tracking';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ProgramKerjaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramKerjasTable::configure($table);
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
            'index' => ListProgramKerjas::route('/'),
            'create' => CreateProgramKerja::route('/create'),
            'view' => ViewProgramKerja::route('/{record}'),
            'edit' => EditProgramKerja::route('/{record}/edit'),
        ];
    }

    /** Badge: jumlah proker aktif di sidebar */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ═══════════════════════════════════════════════════════════
    // SCOPING — Tampilkan hanya proker yang dapat diakses user
    // ═══════════════════════════════════════════════════════════

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // Super Admin & Ketua UKM lihat semua proker
        if ($user?->hasAnyRole(['super_admin', 'ketua_ukm'])) {
            return $query;
        }

        // User lain hanya lihat proker divisinya + proker umum (NULL)
        return $query->untukUser($user?->divisi_id);
    }
}
