<?php

namespace App\Filament\Resources\Pendaftars\Tables;

use App\Models\Divisi;
use App\Models\Pendaftar;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                // ═══════════════════════════════════════════════
                // ⭐ ACTION: LULUSKAN
                // Ubah status → 'lulus' + insert ke tabel users
                // ═══════════════════════════════════════════════
                // Saat pendaftar diluluskan, user yang dibuat HARUS langsung diberi
                // role 'anggota' agar bisa login ke mobile app.
                Action::make('luluskan')
                    ->label('Luluskan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Pendaftar $r): bool => $r->status === 'menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Luluskan Pendaftar?')
                    ->modalDescription(function (Pendaftar $r): string {
                        $email = $r->email ?? $r->emailDummy();
                        return "Pendaftar {$r->nama} ({$r->nim}) akan dinyatakan LULUS.\n\n"
                            . "Akun anggota akan dibuat otomatis:\n"
                            . "• Email: {$email}\n"
                            . "• Password default: password123\n"
                            . "• Role: 👥 Anggota (akses mobile app)\n"
                            . "• Divisi: {$r->divisi->nama}";
                    })
                    ->modalSubmitActionLabel('Ya, Luluskan & Buat Akun')
                    ->action(function (Pendaftar $record): void {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record): void {

                            $email = $record->email ?? $record->emailDummy();

                            // Buat user baru atau ambil yang sudah ada
                            $user = \App\Models\User::firstOrCreate(
                                ['email' => $email],
                                [
                                    'name'      => $record->nama,
                                    'email'     => $email,
                                    'password'  => \Illuminate\Support\Facades\Hash::make('password123'),
                                    'divisi_id' => $record->divisi_id,    // ⭐ ikat user ke divisinya
                                    'no_hp'     => $record->no_hp,
                                ]
                            );

                            // ⭐ Assign role 'anggota' agar bisa akses mobile API
                            if (! $user->hasRole('anggota')) {
                                $user->assignRole('anggota');
                            }

                            // Update status pendaftar + simpan referensi user_id
                            $record->update([
                                'status'  => 'lulus',
                                'user_id' => $user->id,
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title("🎉 {$record->nama} berhasil diluluskan!")
                            ->body("Akun dibuat dengan email: " . ($record->email ?? $record->emailDummy())
                                . " · Role: Anggota")
                            ->success()
                            ->duration(5000)
                            ->send();
                    }),

                // ── Tolak ─────────────────────────────────────
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Pendaftar $r): bool => $r->status === 'menunggu')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pendaftar?')
                    ->modalDescription(
                        fn(Pendaftar $r) =>
                        "Pendaftar {$r->nama} ({$r->nim}) akan ditolak. Tindakan ini tidak dapat dibatalkan."
                    )
                    ->action(function (Pendaftar $record): void {
                        $record->update(['status' => 'ditolak']);
                        Notification::make()
                            ->title("❌ {$record->nama} ditolak.")
                            ->danger()
                            ->send();
                    }),

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
