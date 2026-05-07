<?php

namespace App\Filament\Resources\Pendaftars\Pages;

use App\Filament\Resources\Pendaftars\PendaftarResource;
use App\Models\Pendaftar;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Hash;

class ViewPendaftar extends ViewRecord
{
    protected static string $resource = PendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

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
        ];
    }
}
