<?php

namespace App\Filament\Resources\Galleries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->size(64),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'Kegiatan'  => 'info',
                        'Prestasi'  => 'success',
                        'Rapat'     => 'warning',
                        'Pelatihan' => 'primary',
                        default     => 'gray',
                    }),

                IconColumn::make('is_featured')
                    ->label('Utama')
                    ->boolean(),

                TextColumn::make('urut')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->options([
                        'Umum'      => 'Umum',
                        'Kegiatan'  => 'Kegiatan',
                        'Prestasi'  => 'Prestasi',
                        'Rapat'     => 'Rapat',
                        'Pelatihan' => 'Pelatihan',
                    ]),

                SelectFilter::make('is_featured')
                    ->label('Tampilan Utama')
                    ->options([1 => 'Ya', 0 => 'Tidak']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('urut');
    }
}
