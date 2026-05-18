<?php

namespace App\Filament\Resources\PertanyaanSeleksis\Schemas;

use App\Models\Divisi;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PertanyaanSeleksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pertanyaan')
                    ->schema([
                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(Divisi::query()->pluck('nama', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('urut')
                            ->label('Nomor Urut')
                            ->numeric()
                            ->default(0)
                            ->helperText('Urutan tampil pertanyaan kepada pendaftar.'),

                        Textarea::make('pertanyaan_teks')
                            ->label('Teks Pertanyaan')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->placeholder('Contoh: Mengapa Anda tertarik bergabung dengan Divisi Web Development?'),

                        Toggle::make('is_active')
                            ->label('Aktifkan Pertanyaan')
                            ->helperText('Hanya pertanyaan aktif yang ditampilkan ke pendaftar.')
                            ->default(true)
                            ->columnSpanFull(),

                    ])->columns(2),
            ]);
    }
}
