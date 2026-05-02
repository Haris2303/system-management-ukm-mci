<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Divisi;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
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
                        default        => $state,
                    })
                    ->color(fn(string $state) => match ($state) {
                        'super_admin'  => 'danger',
                        'ketua_ukm'    => 'success',
                        'sekretaris'   => 'info',
                        'bendahara'    => 'warning',
                        'ketua_divisi' => 'primary',
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

                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn(User $r) => auth()->id() !== $r->id),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
