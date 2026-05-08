<?php

namespace App\Filament\Resources\Elections\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ElectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pemilihan')
                    ->schema([
                        TextEntry::make('judul')->label('Judul')->weight('bold'),
                        TextEntry::make('posisi')->label('Posisi')->badge()->color('primary'),
                        TextEntry::make('status')->label('Status')->badge()
                            ->color(fn(string $state) => match ($state) {
                                'aktif' => 'success',
                                'draft' => 'gray',
                                'selesai' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('waktu_mulai')->label('Mulai')->dateTime('d M Y, H:i'),
                        TextEntry::make('waktu_selesai')->label('Selesai')->dateTime('d M Y, H:i'),
                        TextEntry::make('deskripsi')->label('Deskripsi')->columnSpanFull(),
                        IconEntry::make('is_anonim')->label('Anonim')->boolean(),
                        IconEntry::make('tampil_realtime')->label('Real-time')->boolean(),
                    ])->columns(2),

                // Rekap Suara Realtime
                Section::make('Rekap Hasil Suara')
                    ->schema([
                        ViewEntry::make('rekap_suara')
                            ->view('filament.infolists.components.election-results')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
