<?php

namespace App\Filament\Resources\TagihanKas\Tables;

use App\Models\TagihanKas;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TagihanKasTable
{

    public static function configure(Table $table,): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('bulan_tagihan_format')
                    ->label('Periode')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => $state === 'lunas' ? 'success' : 'warning')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'lunas'         => '✅ Lunas',
                        'belum_dibayar' => '⏳ Belum Dibayar',
                        default         => $state,
                    }),

                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y, H:i')
                    ->timezone('Asia/Jayapura')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->timezone('Asia/Jayapura')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'belum_dibayar' => '⏳ Belum Dibayar',
                        'lunas'         => '✅ Lunas',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Filter Anggota')
                    ->options(fn() => User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('bulan_tagihan')
                    ->label('Filter Bulan')
                    ->options(self::getBulanOptions()),
            ])
            ->recordActions([
                // ═══════════════════════════════════════════════
                // ⭐ ACTION KHUSUS: TANDAI LUNAS (1 KLIK)
                // ═══════════════════════════════════════════════
                Action::make('tandai_lunas')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('sm')
                    ->visible(fn(TagihanKas $record): bool => $record->status === 'belum_dibayar')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran Tunai')
                    ->modalDescription(
                        fn(TagihanKas $r) =>
                        "Tandai tagihan {$r->user->name} untuk periode {$r->bulan_tagihan_format} ({$r->nominal_format}) sebagai LUNAS?"
                    )
                    ->modalSubmitActionLabel('Ya, Tandai Lunas')
                    ->modalIcon('heroicon-o-banknotes')
                    ->action(function (TagihanKas $record): void {
                        $record->update([
                            'status'        => 'lunas',
                            'tanggal_bayar' => now('Asia/Jayapura'),
                        ]);

                        Notification::make()
                            ->title('Pembayaran berhasil dicatat!')
                            ->body("Tagihan {$record->user->name} ({$record->bulan_tagihan_format}) telah dilunasi.")
                            ->success()
                            ->duration(4000)
                            ->send();
                    }),

                // Action: Batalkan status lunas (jika ada kesalahan input)
                Action::make('batalkan_lunas')
                    ->label('Batalkan Lunas')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->size('sm')
                    ->visible(fn(TagihanKas $record): bool => $record->status === 'lunas')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Status Lunas?')
                    ->modalDescription('Status akan dikembalikan ke "Belum Dibayar". Gunakan hanya jika ada kesalahan pencatatan.')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->action(function (TagihanKas $record): void {
                        $record->update([
                            'status'        => 'belum_dibayar',
                            'tanggal_bayar' => null,
                        ]);

                        Notification::make()
                            ->title('Status pembayaran dibatalkan.')
                            ->warning()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // Bulk action: tandai lunas multiple sekaligus
                    BulkAction::make('tandai_lunas_massal')
                        ->label('Tandai Lunas (Massal)')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Lunas Massal?')
                        ->modalDescription('Semua tagihan terpilih yang belum lunas akan ditandai LUNAS.')
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'belum_dibayar') {
                                    $record->update([
                                        'status'        => 'lunas',
                                        'tanggal_bayar' => now('Asia/Jayapura'),
                                    ]);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title("{$count} tagihan berhasil dilunasi!")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc')->striped();
    }

    /**
     * Generate opsi bulan tagihan: 6 bulan ke belakang + 6 bulan ke depan
     */
    private static function getBulanOptions(): array
    {
        $bulanIndo = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $options = [];
        $now = now('Asia/Jayapura');

        for ($i = -6; $i <= 6; $i++) {
            $date = $now->copy()->addMonths($i)->startOfMonth();
            $key  = $date->format('Y-m');
            $options[$key] = $bulanIndo[$date->format('m')] . ' ' . $date->format('Y');
        }

        return $options;
    }
}
