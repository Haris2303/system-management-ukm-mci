<?php

namespace App\Filament\Resources\TransaksiKas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransaksiKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi')
                    ->description('Catat kas masuk (donasi/bantuan) atau kas keluar (pengeluaran).')
                    ->schema([

                        Select::make('jenis')
                            ->label('Jenis Transaksi')
                            ->options([
                                'masuk'  => '⬆️ Kas Masuk (Donasi/Bantuan)',
                                'keluar' => '⬇️ Kas Keluar (Pengeluaran)',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('nominal')
                            ->label('Nominal (Rupiah)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1000)
                            ->step(1000)
                            ->helperText('Masukkan tanpa titik atau koma. Contoh: 150000'),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->default(now('Asia/Jayapura'))
                            ->timezone('Asia/Jayapura')
                            ->required()
                            ->maxDate(now('Asia/Jayapura')),

                        Hidden::make('dicatat_oleh')
                            ->default(fn() => auth()->id()),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Contoh: "Donasi dari alumni angkatan 2020" atau "Pembelian alat presentasi untuk seminar"')
                            ->columnSpanFull(),

                        FileUpload::make('bukti')
                            ->label('Foto Bukti / Kuitansi')
                            ->image()
                            ->disk('public')
                            ->directory('bukti-kas')
                            ->imageResizeMode('contain')
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText('Opsional. Maks 2 MB.'),

                    ])->columns(1),
            ]);
    }
}
