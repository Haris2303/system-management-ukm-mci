<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Models\Divisi;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn(User $r) =>
                        $r->avatar_url
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode($r->name) . '&background=1a4ff5&color=fff&size=80'
                    ),

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

                TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable()
                    ->sortable()
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

                SelectFilter::make('periode')
                    ->label('Filter Periode')
                    ->options(fn() => User::query()
                        ->whereNotNull('periode')
                        ->distinct()
                        ->orderBy('periode')
                        ->pluck('periode', 'periode'))
                    ->searchable(),

                Filter::make('dikeluarkan')
                    ->label('Dikeluarkan')
                    ->query(fn(Builder $query) => $query->whereNotNull('kicked_at'))
                    ->toggle(),
            ])
            ->recordActions([
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

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn(User $r) => UserResource::canDelete($r)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->can('kelola_users') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
