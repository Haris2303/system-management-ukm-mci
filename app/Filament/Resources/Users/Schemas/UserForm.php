<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Divisi;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Foto Profil')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('no_hp')
                            ->label('No. HP / WhatsApp')
                            ->tel()
                            ->maxLength(20),

                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(Divisi::query()->orderBy('urut')->pluck('nama', 'id'))
                            ->searchable()
                            ->placeholder('— Tidak ada divisi —')
                            ->helperText('Wajib diisi untuk Ketua Divisi & Anggota.'),
                    ])->columns(2),

                Section::make('Role & Permission')
                    ->description('Tentukan role pengguna untuk membatasi akses ke fitur sistem.')
                    ->schema([
                        Select::make('roles')
                            ->label('Role Pengguna')
                            ->relationship('roles', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record) => match ($record->name) {
                                'super_admin'  => '👑 Super Admin (Akses Penuh)',
                                'ketua_ukm'    => '👨‍💼 Ketua UKM',
                                'sekretaris'   => '📝 Sekretaris',
                                'bendahara'    => '💰 Bendahara',
                                'ketua_divisi' => '🏆 Ketua Divisi',
                                'anggota'      => '👥 Anggota (Mobile Only)',
                                'demisioner'   => '🏛️ Demisioner (Akun Nonaktif)',
                                default        => $record->name,
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->maxItems(2)
                            ->helperText('Pilih satu atau dua role. Permission akan otomatis diatur sesuai role.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Password')
                    ->description('Kosongkan jika tidak ingin mengubah password.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create')
                            ->helperText('Minimal 8 karakter.'),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn(string $context): bool => $context === 'create'),
                    ])->columns(2),
            ]);
    }

    /**
     * Opsi role dengan label yang ramah dibaca.
     */
    private static function getRoleOptions(): array
    {
        return [
            'super_admin'  => '👑 Super Admin (Akses Penuh)',
            'ketua_ukm'    => '👨‍💼 Ketua UKM',
            'sekretaris'   => '📝 Sekretaris',
            'bendahara'    => '💰 Bendahara',
            'ketua_divisi' => '🏆 Ketua Divisi',
            'anggota'      => '👥 Anggota (Mobile Only)',
        ];
    }
}
