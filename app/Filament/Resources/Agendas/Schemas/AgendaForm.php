<?php

namespace App\Filament\Resources\Agendas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Information Agenda')
                    ->schema([
                        TextInput::make('nama_agenda')
                            ->label('Nama Agenda')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('Jadwal & Status')
                    ->schema([
                        DateTimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->required()
                            ->seconds(false),

                        DateTimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->required()
                            ->seconds(false)
                            ->after('waktu_mulai'),

                        Toggle::make('is_active')
                            ->label('Aktifkan Agenda')
                            ->helperText('Nonaktifkan untuk menutup presensi sementara.')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}
