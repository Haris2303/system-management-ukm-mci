<?php

namespace App\Filament\Resources\Materis\Tables;

use App\Models\Divisi;
use App\Models\Materi;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MaterisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul Materi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('divisi_id')
                    ->label('Divisi')
                    ->badge()
                    ->color(fn(Materi $r) => $r->isUmum() ? 'gray' : 'primary')
                    ->getStateUsing(fn(Materi $r) => $r->divisi?->nama ?? '🌐 Semua Divisi'),

                IconColumn::make('file_path')
                    ->label('PDF')
                    ->icon(fn(?string $state) => $state ? 'heroicon-o-document-arrow-down' : 'heroicon-o-minus')
                    ->color(fn(?string $state) => $state ? 'success' : 'gray')
                    ->tooltip(fn(Materi $r) => $r->file_path ? "Ukuran: {$r->file_size}" : 'Tidak ada file'),

                TextColumn::make('uploader.name')
                    ->label('Diunggah Oleh')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('divisi_id')
                    ->label('Filter Divisi')
                    ->options(Divisi::query()->orderBy('urut')->pluck('nama', 'id'))
                    ->searchable()
                    ->preload()
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->where(fn (Builder $q) => $q
                            ->where('divisi_id', $data['value'])
                            ->orWhereNull('divisi_id'))
                        : $query
                    ),

                TernaryFilter::make('is_umum')
                    ->label('Jenis Materi')
                    ->placeholder('Semua materi')
                    ->trueLabel('Hanya Materi Umum')
                    ->falseLabel('Hanya Khusus Divisi')
                    ->queries(
                        true: fn($q) => $q->whereNull('divisi_id'),
                        false: fn($q) => $q->whereNotNull('divisi_id'),
                        blank: fn($q) => $q,
                    ),
            ])
            ->recordActions([

                Action::make('open_link')
                    ->label('Buka Link')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->visible(fn(Materi $r) => $r->hasLink())
                    ->url(fn(Materi $r) => $r->link_url, shouldOpenInNewTab: true),

                DeleteAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
