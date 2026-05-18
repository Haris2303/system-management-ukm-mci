<?php

namespace App\Filament\Resources\TagihanKas\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TagihanKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Tagihan')
                    ->description('Buat tagihan iuran wajib untuk anggota.')
                    ->schema([

                        Select::make('user_id')
                            ->label('Anggota')
                            ->options(fn() => User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('bulan_tagihan')
                            ->label('Bulan Tagihan')
                            ->options(self::getBulanOptions())
                            ->required()
                            ->searchable(),

                        TextInput::make('nominal')
                            ->label('Nominal (Rupiah)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1000)
                            ->step(1000)
                            ->default(50000)
                            ->helperText('Masukkan nominal iuran tanpa titik atau koma.'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'belum_dibayar' => '⏳ Belum Dibayar',
                                'lunas'         => '✅ Lunas',
                            ])
                            ->default('belum_dibayar')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'lunas') {
                                    $set('tanggal_bayar', now('Asia/Jayapura')->format('Y-m-d H:i:s'));
                                }
                            }),

                        DateTimePicker::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->seconds(false)
                            ->timezone('Asia/Jayapura')
                            ->visible(fn(Get $get) => $get('status') === 'lunas'),

                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),

                    ])->columns(1),
            ]);
    }

    /**
     * Generate opsi bulan tagihan: 6 bulan ke belakang + 6 bulan ke depan
     */
    private static function getBulanOptions(): array
    {
        $bulanIndo = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $options = [];
        $now = now('Asia/Jayapura');

        for ($i = -6; $i <= 6; $i++) {
            $date = $now->copy()->addMonths($i)->startOfMonth();
            $key  = $date->format('Y-m');
            $options[$key] = $bulanIndo[$date->format('m')] . ' ' . $date->format('Y');
        }

        return $options;
    }
}
