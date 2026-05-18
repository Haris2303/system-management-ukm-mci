<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Divisi;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(
                        fn(User $r) =>
                        'https://ui-avatars.com/api/?name=' . urlencode($r->name)
                            . '&background=1a4ff5&color=fff'
                    )
                    ->size(40),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(User $r) => $r->email),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'super_admin'  => '👑 Super Admin',
                        'ketua_ukm'    => '👨‍💼 Ketua UKM',
                        'sekretaris'   => '📝 Sekretaris',
                        'bendahara'    => '💰 Bendahara',
                        'ketua_divisi' => '🏆 Ketua Divisi',
                        'anggota'      => '👥 Anggota',
                        'demisioner'   => '🏛️ Demisioner',
                        default        => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'super_admin'  => 'danger',
                        'ketua_ukm'    => 'success',
                        'sekretaris'   => 'info',
                        'bendahara'    => 'warning',
                        'ketua_divisi' => 'primary',
                        'demisioner'        => 'gray',
                        default        => 'gray',
                    }),

                TextColumn::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('email_verified_at')
                    ->label('Terverifikasi')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('kicked_at')
                    ->label('Status Kick')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn($state) => '🚫 Dikeluarkan ' . \Carbon\Carbon::parse($state)->format('d M Y'))
                    ->description(
                        fn(User $r) => $r->kickedBy
                            ? 'oleh ' . $r->kickedBy->name . ($r->kicked_reason ? ' · ' . $r->kicked_reason : '')
                            : null
                    )
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter Role')
                    ->relationship('roles', 'name')
                    ->options(fn() => Role::pluck('name', 'name'))
                    ->multiple(),

                SelectFilter::make('divisi_id')
                    ->label('Filter Divisi')
                    ->options(Divisi::query()->orderBy('urut')->pluck('nama', 'id'))
                    ->searchable(),

                Filter::make('dikeluarkan')
                    ->label('Dikeluarkan')
                    ->query(fn(Builder $query) => $query->whereNotNull('kicked_at'))
                    ->toggle(),
            ])
            ->recordActions([
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

                // ── Pulihkan dari kick ──────────────────────────
                Action::make('pulihkan_kick')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn(User $r): bool => $r->isKicked())
                    ->requiresConfirmation()
                    ->modalHeading('Pulihkan Pengguna?')
                    ->modalDescription(
                        fn(User $r) =>
                        "Akun {$r->name} akan dipulihkan. "
                        . "Status dikeluarkan akan dihapus dan akun dapat login kembali."
                    )
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalIcon('heroicon-o-arrow-path')
                    ->action(function (User $record): void {
                        $record->update([
                            'kicked_at'     => null,
                            'kicked_by'     => null,
                            'kicked_reason' => null,
                        ]);

                        Notification::make()
                            ->title("✅ {$record->name} berhasil dipulihkan.")
                            ->body('Akun dapat login kembali.')
                            ->success()
                            ->duration(4000)
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn(User $r) => auth()->user()?->id !== $r->id),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
