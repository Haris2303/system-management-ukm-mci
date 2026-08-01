<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\TextSize;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Grid::make(1)
                // ->schema([
                Grid::make(3)
                    ->columnSpan(2)
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                static::kontakOrganisasiSection()->columnSpan(1),
                                static::informasiAkunSection()->columnSpan(1),
                                static::statusDikeluarkanSection(),
                            ])->columnSpan(2),
                        static::profilSection()->columnSpan(1),
                    ]),

                // ]),
            ]);
    }

    private static function profilSection(): Section
    {
        return Section::make('Profil')
            ->columnSpanFull()
            ->schema([
                ImageEntry::make('avatar_url')
                    ->label('Photo Profile')
                    ->circular()
                    ->imageSize(140)
                    ->alignment(Alignment::Center)
                    ->getStateUsing(
                        fn(User $r) =>
                        $r->avatar_url
                            ?? 'https://ui-avatars.com/api/?name=' . urlencode($r->name) . '&background=1a4ff5&color=fff&size=140'
                    ),

                TextEntry::make('name')
                    ->label('')
                    ->size(TextSize::Large)
                    ->weight('bold')
                    ->alignment(Alignment::Center),

                TextEntry::make('email')
                    ->label('')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->alignment(Alignment::Center),

                TextEntry::make('roles.name')
                    ->label('Role name')
                    ->badge()
                    ->separator(',')
                    ->alignment(Alignment::Center)
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
                        'demisioner'   => 'gray',
                        default        => 'gray',
                    }),

                TextEntry::make('account_status')
                    ->label('')
                    ->state(fn(User $r) => match (true) {
                        $r->isKicked()          => 'Dikeluarkan',
                        $r->hasRole('demisioner') => 'Demisioner',
                        default                   => 'Aktif',
                    })
                    ->badge()
                    ->alignment(Alignment::Center)
                    ->color(fn(string $state) => match ($state) {
                        'Dikeluarkan' => 'danger',
                        'Demisioner'  => 'gray',
                        default       => 'success',
                    }),

                Actions::make([
                    Action::make('lihat_id_card')
                        ->label('Lihat ID Card')
                        ->icon('heroicon-o-identification')
                        ->color('info')
                        ->url(fn(User $record) => route('id-card.show', [
                            'userId' => $record->id,
                            'back'   => route('filament.administrasi.resources.users.view', $record),
                        ]))
                        ->openUrlInNewTab(),
                ])
                    ->alignment(Alignment::Center)
                    ->fullWidth(),
            ]);
    }

    private static function kontakOrganisasiSection(): Section
    {
        return Section::make('Kontak & Organisasi')
            ->schema([
                TextEntry::make('no_hp')
                    ->label('No. HP / WhatsApp')
                    ->icon('heroicon-o-phone')
                    ->placeholder('—'),

                TextEntry::make('divisi.nama')
                    ->label('Divisi')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextEntry::make('periode')
                    ->label('Periode')
                    ->placeholder('—'),

                TextEntry::make('public_id')
                    ->label('Public ID')
                    ->copyable()
                    ->fontFamily('mono')
                    ->placeholder('—'),
            ])->columns(2);
    }

    private static function informasiAkunSection(): Section
    {
        return Section::make('Informasi Akun')
            ->schema([
                IconEntry::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->boolean(),

                TextEntry::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('photo_uploaded_at')
                    ->label('Foto Terakhir Diunggah')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—'),
            ])->columns(2);
    }

    private static function statusDikeluarkanSection(): Section
    {
        return Section::make('Status Dikeluarkan')
            ->icon('heroicon-o-user-minus')
            ->visible(fn(User $r) => $r->isKicked())
            ->schema([
                TextEntry::make('kicked_at')
                    ->label('Dikeluarkan Pada')
                    ->dateTime('d M Y, H:i'),

                TextEntry::make('kickedBy.name')
                    ->label('Dikeluarkan Oleh')
                    ->placeholder('—'),

                TextEntry::make('kicked_reason')
                    ->label('Alasan')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
