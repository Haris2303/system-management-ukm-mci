<?php

namespace App\Filament\Resources\PertanyaanSeleksis\Tables;

use App\Models\Divisi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PertanyaanSeleksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('urut')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('pertanyaan_teks')
                    ->label('Pertanyaan')
                    ->limit(80)
                    ->searchable()
                    ->wrap(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('jawaban_pendaftars_count')
                    ->label('Jawaban')
                    ->counts('jawabanPendaftars')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('divisi_id')
                    ->label('Filter Divisi')
                    ->options(Divisi::query()->pluck('nama', 'id'))
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('divisi_id')
            ->reorderable('urut');
    }
}
