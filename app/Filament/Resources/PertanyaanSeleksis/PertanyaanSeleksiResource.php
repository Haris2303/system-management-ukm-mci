<?php

namespace App\Filament\Resources\PertanyaanSeleksis;

use App\Filament\Resources\PertanyaanSeleksis\Pages\CreatePertanyaanSeleksi;
use App\Filament\Resources\PertanyaanSeleksis\Pages\EditPertanyaanSeleksi;
use App\Filament\Resources\PertanyaanSeleksis\Pages\ListPertanyaanSeleksis;
use App\Filament\Resources\PertanyaanSeleksis\Schemas\PertanyaanSeleksiForm;
use App\Filament\Resources\PertanyaanSeleksis\Tables\PertanyaanSeleksisTable;
use App\Models\PertanyaanSeleksi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PertanyaanSeleksiResource extends Resource
{
    protected static ?string $model = PertanyaanSeleksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QuestionMarkCircle;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Pertanyaan Seleksi';

    protected static ?string $modelLabel = 'Pertanyaan Seleksi';

    protected static ?string $pluralModelLabel = 'Pertanyaan Seleksi';

    protected static \UnitEnum|string|null $navigationGroup = 'Rekrutmen';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PertanyaanSeleksiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PertanyaanSeleksisTable::configure($table);
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
            'index' => ListPertanyaanSeleksis::route('/'),
            'create' => CreatePertanyaanSeleksi::route('/create'),
            'edit' => EditPertanyaanSeleksi::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('kelola_pertanyaan_seleksi') ?? false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // Ketua Divisi hanya lihat pertanyaan di divisinya
        if ($user?->isKetuaDivisi() && $user->divisi_id) {
            return $query->where('divisi_id', $user->divisi_id);
        }

        return $query;
    }
}
