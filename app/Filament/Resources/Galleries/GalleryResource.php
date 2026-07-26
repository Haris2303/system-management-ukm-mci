<?php

namespace App\Filament\Resources\Galleries;

use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Filament\Resources\Galleries\Tables\GalleriesTable;
use App\Models\Gallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    protected static ?string $recordTitleAttribute = 'judul';

    protected static ?string $navigationLabel = 'Galeri';

    protected static ?string $modelLabel = 'Foto Galeri';

    protected static ?string $pluralModelLabel = 'Galeri';

    protected static \UnitEnum|string|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        // Bendahara hanya boleh mengakses E-Kas Keuangan; sekretaris hanya
        // boleh mengakses Manajemen Presensi & User; ketua_ukm dibatasi
        // hanya E-Voting, Open Recruitment, Divisi & Laporan Keuangan E-Kas.
        return ! (auth()->user()?->hasAnyRole(['bendahara', 'sekretaris', 'ketua_ukm']) ?? false);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit'   => EditGallery::route('/{record}/edit'),
        ];
    }
}
