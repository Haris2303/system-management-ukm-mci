<?php

namespace App\Filament\Resources\Materis;

use App\Filament\Resources\Materis\Pages\CreateMateri;
use App\Filament\Resources\Materis\Pages\EditMateri;
use App\Filament\Resources\Materis\Pages\ListMateris;
use App\Filament\Resources\Materis\Schemas\MateriForm;
use App\Filament\Resources\Materis\Tables\MaterisTable;
use App\Models\Materi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Override;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $recordTitleAttribute = 'judul';

    protected static ?string $navigationLabel = 'Distribusi Materi';

    protected static ?string $modelLabel = 'Materi';

    protected static ?string $pluralModelLabel = 'Distribusi Materi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MateriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterisTable::configure($table);
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
            'index' => ListMateris::route('/'),
            'create' => CreateMateri::route('/create'),
            'edit' => EditMateri::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('kelola_materi') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        // Jika ketua divisi mencoba mengedit materi umum (divisi_id null), tolak!
        if ($user?->hasRole('ketua_divisi') && $record->divisi_id === null) {
            return false;
        }

        // Ketua UKM tidak boleh edit materi khusus divisi
        if ($user?->hasRole('ketua_ukm') && $record->divisi_id !== null) {
            return false;
        }

        return parent::canEdit($record);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        // Jika ketua divisi mencoba menghapus materi umum (divisi_id null), tolak!
        if ($user?->hasRole('ketua_divisi') && $record->divisi_id === null) {
            return false;
        }

        // Ketua UKM tidak boleh hapus materi khusus divisi
        if ($user?->hasRole('ketua_ukm') && $record->divisi_id !== null) {
            return false;
        }

        return parent::canDelete($record);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // Super admin dan ketua UKM lihat semua
        if ($user?->hasAnyRole(['super_admin', 'ketua_ukm'])) {
            return $query;
        }

        // Ketua Divisi hanya lihat materi divisinya + materi umum
        if ($user?->isKetuaDivisi() && $user->divisi_id) {
            return $query->where(function ($q) use ($user) {
                $q->whereNull('divisi_id')
                    ->orWhere('divisi_id', $user->divisi_id);
            });
        }

        return $query;
    }
}
