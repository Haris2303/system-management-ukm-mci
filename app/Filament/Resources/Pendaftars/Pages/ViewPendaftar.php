<?php

namespace App\Filament\Resources\Pendaftars\Pages;

use App\Filament\Resources\Pendaftars\PendaftarResource;
use App\Models\Pendaftar;
use App\Models\User;
use App\Services\PendaftarService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPendaftar extends ViewRecord
{
    protected static string $resource = PendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('luluskan')
                ->label('Luluskan')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn(Pendaftar $r): bool => $r->status === 'menunggu')
                ->requiresConfirmation()
                ->modalHeading('Luluskan Pendaftar?')
                ->modalDescription(function (Pendaftar $r): string {
                    $email = $r->effectiveEmail();
                    return "Pendaftar {$r->nama} ({$r->nim}) akan dinyatakan LULUS.\n\n"
                        . "Akun anggota akan dibuat otomatis:\n"
                        . "• Email: {$email}\n"
                        . "• Password default: password123\n"
                        . "• Role: 👥 Anggota (akses mobile app)\n"
                        . "• Divisi: " . ($r->divisi?->nama ?? '(tidak ada)');
                })
                ->modalSubmitActionLabel('Ya, Luluskan & Buat Akun')
                ->action(function (Pendaftar $record): void {
                    app(PendaftarService::class)->luluskan($record);

                    \Filament\Notifications\Notification::make()
                        ->title("🎉 {$record->nama} berhasil diluluskan!")
                        ->body("Akun dibuat dengan email: " . $record->effectiveEmail()
                            . " · Role: Anggota")
                        ->success()
                        ->duration(5000)
                        ->send();
                }),

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
                )->schema([
                    Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->placeholder('Contoh: Nilai wawancara kurang, kuota penuh, dsb...')
                        ->required() // Wajib diisi agar admin tidak menolak tanpa alasan
                        ->rows(3),
                ])
                ->action(function (Pendaftar $record): void {
                    // Panggil fungsi tolak dari PendaftarService di sini!
                    app(PendaftarService::class)->tolak($record);

                    Notification::make()
                        ->title("❌ {$record->nama} ditolak.")
                        ->danger()
                        ->send();
                }),
        ];
    }
}
