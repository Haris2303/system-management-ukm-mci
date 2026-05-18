<?php

namespace App\Filament\Resources\Agendas\Schemas;

use App\Models\Agenda;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AgendaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)->components([
                    Section::make('Informasi Agenda')
                        ->schema([
                            TextEntry::make('nama_agenda')
                                ->label('Nama Agenda')
                                ->weight('bold'),

                            TextEntry::make('lokasi')
                                ->label('Lokasi')
                                ->placeholder('–'),

                            TextEntry::make('deskripsi')
                                ->label('Deskripsi')
                                ->columnSpanFull()
                                ->placeholder('Tidak ada deskripsi.'),

                            TextEntry::make('waktu_mulai')
                                ->label('Waktu Mulai')
                                ->dateTime('d M Y, H:i'),

                            TextEntry::make('waktu_selesai')
                                ->label('Waktu Selesai')
                                ->dateTime('d M Y, H:i'),

                            IconEntry::make('is_active')
                                ->label('Status')
                                ->boolean(),
                        ])->columns(2),

                    Section::make('Statistik Presensi')
                        ->schema([
                            TextEntry::make('presensis_hadir_count')
                                ->label('Total Hadir')
                                ->state(fn(Agenda $record): int => $record->presensis()->where('status', 'Hadir')->count())
                                ->badge()
                                ->color('success'),

                            TextEntry::make('presensis_absen_count')
                                ->label('Total Absen')
                                ->state(fn(Agenda $record): int => $record->presensis()->where('status', 'Absen')->count())
                                ->badge()
                                ->color('danger'),

                            TextEntry::make('presensis_izin_count')
                                ->label('Total Izin')
                                ->state(fn(Agenda $record): int => $record->presensis()->where('status', 'Izin')->count())
                                ->badge()
                                ->color('warning'),
                        ])->columns(3),
                ]),

                Section::make('QR Code Presensi')
                    ->description('Tampilkan QR Code ini kepada anggota untuk melakukan presensi melalui aplikasi mobile.')
                    ->schema([
                        TextEntry::make('qr_code_token')
                            ->label('Token QR Code')
                            ->copyable()
                            ->copyMessage('Token berhasil disalin!')
                            ->fontFamily('mono'),

                        // Render QR Code sebagai base64 image
                        ImageEntry::make('qr_code_image')
                            ->label('QR Code')
                            ->state(function (Agenda $record): string {
                                // Generate QR Code sebagai SVG lalu convert ke base64 PNG
                                $qrSvg = QrCode::format('svg')
                                    ->size(300)
                                    ->errorCorrection('H')
                                    ->margin(2)
                                    ->generate($record->qr_code_token);

                                return 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                            })
                            ->imageWidth(300)
                            ->imageHeight(300),
                    ]),

            ]);
    }
}
