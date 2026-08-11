<?php

namespace App\Filament\Resources\Pendaftars\Tables;

use App\Models\Divisi;
use App\Models\Pendaftar;
use App\Models\User;
use App\Services\PendaftarService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PendaftarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Pendaftar $r) => $r->nim),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('angkatan')
                    ->label('Angkatan')
                    ->badge()
                    ->color('gray'),

                // Kolom total skor — dihitung dari relasi jawaban
                TextColumn::make('total_skor')
                    ->label('Total Skor')
                    ->state(
                        fn(Pendaftar $r): string =>
                        $r->jawabanPendaftars()->whereNotNull('nilai_skor')->exists()
                            ? (string) $r->totalSkor() . ' pts'
                            : '—'
                    )
                    ->badge()
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'lulus'    => 'success',
                        'ditolak'  => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'lulus'    => '✅ Lulus',
                        'ditolak'  => '❌ Ditolak',
                        default    => '⏳ Menunggu',
                    }),

                TextColumn::make('created_at')
                    ->label('Mendaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('divisi_id')
                    ->label('Filter Divisi')
                    ->options(Divisi::query()->pluck('nama', 'id'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => '⏳ Menunggu',
                        'lulus'    => '✅ Lulus',
                        'ditolak'  => '❌ Ditolak',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn() => auth()->user()->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
