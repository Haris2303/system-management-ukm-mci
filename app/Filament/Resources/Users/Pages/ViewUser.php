<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── Demisionerkan (Pensiunkan akun) ─────────────
            Action::make('demisionerkan')
                ->label('Demisionerkan')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->visible(
                    fn(User $r): bool =>
                    ! $r->hasRole('demisioner') && auth()->user()?->id !== $r->id
                )
                ->requiresConfirmation()
                ->modalHeading('Pensiunkan Akun Pengguna?')
                ->modalDescription(
                    fn(User $r) =>
                    "Akun {$r->name} akan diubah ke status DEMISIONER. "
                        . "Akun tetap tersimpan sebagai arsip historis, tapi tidak bisa "
                        . "login ke panel admin maupun mobile app. "
                        . "Tindakan ini bisa dibatalkan kapan saja dengan mengubah role."
                )
                ->modalSubmitActionLabel('Ya, Pensiunkan')
                ->modalIcon('heroicon-o-archive-box')
                ->action(function (User $record): void {
                    // Hapus semua role lama, lalu assign demisioner saja
                    $record->syncRoles(['demisioner']);

                    // Hapus semua token Sanctum (kick out dari mobile)
                    $record->tokens()->delete();

                    // Hapus semua sesi web (kick out dari panel admin)
                    DB::table('sessions')->where('user_id', $record->id)->delete();

                    Notification::make()
                        ->title("🏛️ {$record->name} berhasil dipensiunkan.")
                        ->body('Akun sekarang berstatus demisioner dan tidak dapat login.')
                        ->success()
                        ->duration(4000)
                        ->send();
                }),

            // ── Reset Password ──────────────────────────────
            Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset Password Pengguna?')
                ->modalDescription(
                    fn(User $r) =>
                    "Password {$r->name} akan direset ke default: 'password123'. "
                        . "User wajib ganti password setelah login pertama."
                )
                ->action(function (User $record): void {
                    $record->update(['password' => Hash::make('password123')]);
                    Notification::make()
                        ->title("🔑 Password {$record->name} direset.")
                        ->body('Password baru: password123')
                        ->warning()
                        ->duration(5000)
                        ->send();
                }),

            // ── Kick (Keluarkan) ────────────────────────────
            Action::make('kick')
                ->label('Keluarkan')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->visible(
                    fn(User $r): bool =>
                    ! $r->isKicked() && auth()->user()?->id !== $r->id
                )
                ->requiresConfirmation()
                ->modalHeading('Keluarkan Pengguna?')
                ->modalDescription(
                    fn(User $r) =>
                    "Akun {$r->name} akan dikeluarkan dari sistem. "
                    . "Semua sesi dan token akan langsung dicabut. "
                    . "Data pengguna tetap tersimpan dengan catatan dikeluarkan."
                )
                ->modalSubmitActionLabel('Ya, Keluarkan')
                ->modalIcon('heroicon-o-user-minus')
                ->form([
                    Textarea::make('kicked_reason')
                        ->label('Alasan Dikeluarkan')
                        ->placeholder('Opsional — tuliskan alasan pengeluaran...')
                        ->rows(3),
                ])
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'kicked_at'     => now(),
                        'kicked_by'     => auth()->id(),
                        'kicked_reason' => $data['kicked_reason'] ?? null,
                    ]);

                    // Cabut semua token Sanctum (kick dari mobile)
                    $record->tokens()->delete();

                    // Hapus semua sesi web (kick dari panel admin)
                    DB::table('sessions')->where('user_id', $record->id)->delete();

                    Notification::make()
                        ->title("🚫 {$record->name} berhasil dikeluarkan.")
                        ->body('Semua sesi dan token telah dicabut.')
                        ->danger()
                        ->duration(4000)
                        ->send();
                }),

            EditAction::make(),
        ];
    }
}
