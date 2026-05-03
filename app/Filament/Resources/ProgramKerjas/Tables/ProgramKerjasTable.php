<?php

namespace App\Filament\Resources\ProgramKerjas\Tables;

use App\Models\Divisi;
use App\Models\ProgramKerja;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProgramKerjasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_proker')
                    ->label('Nama Proker')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color(fn(?string $state) => $state ? 'primary' : 'gray')
                    ->formatStateUsing(fn(?string $state) => $state ?? '🌐 Umum'),

                TextColumn::make('pic.name')
                    ->label('PIC')
                    ->placeholder('—'),

                TextColumn::make('progress_persen')
                    ->label('Progress')
                    ->formatStateUsing(fn(int $state) => "{$state}%")
                    ->badge()
                    ->color(fn(ProgramKerja $r) => $r->warna_progress)
                    ->sortable(),

                TextColumn::make('tugas_prokers_count')
                    ->label('Tugas')
                    ->counts('tugasProkers')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(
                        fn(int $state, ProgramKerja $r): string =>
                        $r->tugasProkers()->where('is_selesai', true)->count() . " / {$state}"
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'planning'  => 'gray',
                        'active'    => 'warning',
                        'completed' => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'planning'  => '📋 Planning',
                        'active'    => '🚀 Active',
                        'completed' => '✅ Completed',
                        default     => $state,
                    }),

                TextColumn::make('tanggal_selesai')
                    ->label('Deadline')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn(ProgramKerja $r) => $r->isTerlambat() ? 'danger' : 'gray')
                    ->description(
                        fn(ProgramKerja $r) => $r->isTerlambat()
                            ? '⚠️ Terlambat'
                            : ($r->sisaHari() >= 0 ? "Sisa {$r->sisaHari()} hari" : null)
                    ),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'planning'  => '📋 Planning',
                        'active'    => '🚀 Active',
                        'completed' => '✅ Completed',
                    ]),

                SelectFilter::make('divisi_id')
                    ->label('Filter Divisi')
                    ->options(Divisi::query()->orderBy('urut')->pluck('nama', 'id'))
                    ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'ketua_ukm'])),

                Filter::make('terlambat')
                    ->label('Hanya yang Terlambat')
                    ->query(fn($q) => $q->where('tanggal_selesai', '<', now())
                        ->where('progress_persen', '<', 100)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
