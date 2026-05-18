<?php

namespace App\Filament\Resources\Pendaftars\Schemas;

use App\Models\Divisi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PendaftarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pendaftar')
                    ->schema([
                        Select::make('divisi_id')
                            ->label('Divisi yang Dipilih')
                            ->options(Divisi::query()->pluck('nama', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->maxLength(10),

                        TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nim')
                            ->label('NIM')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable(),

                        TextInput::make('no_hp')
                            ->label('No. HP')
                            ->tel()
                            ->nullable(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'menunggu' => '⏳ Menunggu',
                                'lulus'    => '✅ Lulus',
                                'ditolak'  => '❌ Ditolak',
                            ])
                            ->default('menunggu')
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
