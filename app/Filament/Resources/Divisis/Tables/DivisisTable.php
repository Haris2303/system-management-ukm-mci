<?php

namespace App\Filament\Resources\Divisis\Tables;

use App\Models\Divisi;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DivisisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('urut')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(fn($state) => '<span class="text-brand-500 text-xl"><i class="' . e($state) . '"></i></span>')
                    ->html()
                    ->width(60),

                TextColumn::make('nama')
                    ->label('Nama Divisi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Divisi $r) => $r->deskripsi
                        ? Str::limit($r->deskripsi, 60) : '—'),

                TextColumn::make('ketua')
                    ->label('Ketua')
                    ->placeholder('—'),

                TextColumn::make('pendaftars_count')
                    ->label('Pendaftar')
                    ->counts('pendaftars')
                    ->badge()
                    ->color('primary'),

                // ── Status rekrutmen dengan label yang jelas ──
                TextColumn::make('is_active')
                    ->label('Status Rekrutmen')
                    ->badge()
                    ->color(fn(bool $state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn(bool $state) => $state
                        ? '🟢 Rekrutmen Dibuka'
                        : '🔴 Rekrutmen Ditutup'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Rekrutmen')
                    ->trueLabel('Dibuka')
                    ->falseLabel('Ditutup'),
            ])
            ->recordActions([
                // ═══════════════════════════════════════════════
                // ⭐ BUKA REKRUTMEN
                // ═══════════════════════════════════════════════
                Action::make('buka_rekrutmen')
                    ->label('Buka Rekrutmen')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->visible(fn(Divisi $record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Buka Rekrutmen Divisi Ini?')
                    ->modalDescription(
                        fn(Divisi $record): string =>
                        "Divisi {$record->icon} {$record->nama} akan membuka pendaftaran. "
                            . "Calon anggota dapat langsung memilih divisi ini di form pendaftaran."
                    )
                    ->modalSubmitActionLabel('Ya, Buka Rekrutmen')
                    ->modalIcon('heroicon-o-lock-open')
                    ->action(function (Divisi $record): void {
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title("🟢 Rekrutmen {$record->nama} dibuka!")
                            ->body('Divisi ini sekarang muncul di form pendaftaran.')
                            ->success()
                            ->duration(4000)
                            ->send();
                    }),

                // ═══════════════════════════════════════════════
                // ⭐ TUTUP REKRUTMEN
                // ═══════════════════════════════════════════════
                Action::make('tutup_rekrutmen')
                    ->label('Tutup Rekrutmen')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn(Divisi $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Rekrutmen Divisi Ini?')
                    ->modalDescription(
                        fn(Divisi $record): string =>
                        "Divisi {$record->icon} {$record->nama} akan menutup pendaftaran. "
                            . "Divisi ini tidak akan muncul di form pendaftaran sampai dibuka kembali. "
                            . "Data pendaftar yang sudah masuk tetap tersimpan."
                    )
                    ->modalSubmitActionLabel('Ya, Tutup Rekrutmen')
                    ->modalIcon('heroicon-o-lock-closed')
                    ->action(function (Divisi $record): void {
                        $record->update(['is_active' => false]);

                        Notification::make()
                            ->title("🔴 Rekrutmen {$record->nama} ditutup.")
                            ->body('Divisi ini tidak lagi muncul di form pendaftaran.')
                            ->warning()
                            ->duration(4000)
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Divisi $record) {
                        if ($record->pendaftars()->exists()) {
                            Notification::make()
                                ->title('Tidak bisa dihapus!')
                                ->body('Divisi ini sudah memiliki data pendaftar.')
                                ->danger()->send();
                            throw new \Exception('Divisi memiliki pendaftar.');
                        }
                    }),
            ])->toolbarActions([
                BulkActionGroup::make([

                    // ── Buka rekrutmen semua divisi terpilih ──
                    BulkAction::make('buka_semua')
                        ->label('Buka Rekrutmen')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Buka Rekrutmen Divisi Terpilih?')
                        ->modalDescription('Semua divisi yang dipilih akan membuka rekrutmen sekaligus.')
                        ->modalSubmitActionLabel('Ya, Buka Semua')
                        ->action(function (Collection $records): void {
                            $count = $records->where('is_active', false)->count();
                            $records->each(fn(Divisi $d) => $d->update(['is_active' => true]));

                            Notification::make()
                                ->title("🟢 {$count} divisi berhasil dibuka rekrutmennya!")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // ── Tutup rekrutmen semua divisi terpilih ──
                    BulkAction::make('tutup_semua')
                        ->label('Tutup Rekrutmen')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tutup Rekrutmen Divisi Terpilih?')
                        ->modalDescription('Semua divisi yang dipilih akan menutup rekrutmen sekaligus. Data pendaftar yang sudah ada tetap tersimpan.')
                        ->modalSubmitActionLabel('Ya, Tutup Semua')
                        ->action(function (Collection $records): void {
                            $count = $records->where('is_active', true)->count();
                            $records->each(fn(Divisi $d) => $d->update(['is_active' => false]));

                            Notification::make()
                                ->title("🔴 {$count} divisi berhasil ditutup rekrutmennya.")
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('urut')
            ->defaultSort('urut');;
    }
}
