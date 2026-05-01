<?php

namespace App\Filament\Resources\Pendaftars\Pages;

use App\Filament\Resources\Pendaftars\PendaftarResource;
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

            // Tombol Luluskan langsung dari halaman view
            Action::make('luluskan')
                ->label('Luluskan')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn() => $this->getRecord()->status === 'menunggu')
                ->requiresConfirmation()
                ->modalHeading('Luluskan Pendaftar?')
                ->modalDescription(fn() => "Pendaftar {$this->getRecord()->nama} akan dinyatakan LULUS dan akun anggota akan dibuat otomatis.")
                ->action(function (): void {
                    $record = $this->getRecord();
                    $email  = $record->email ?? $record->emailDummy();

                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name'     => $record->nama,
                            'email'    => $email,
                            'password' => Hash::make('password123'),
                        ]
                    );

                    $record->update(['status' => 'lulus', 'user_id' => $user->id]);

                    Notification::make()
                        ->title("🎉 {$record->nama} berhasil diluluskan!")
                        ->body("Akun dibuat dengan email: {$email}")
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'user_id']);
                }),
        ];
    }
}
