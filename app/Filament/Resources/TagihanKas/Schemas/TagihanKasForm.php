<?php

namespace App\Filament\Resources\TagihanKas\Schemas;

use App\Helpers\BulanHelper;
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
                            ->options(BulanHelper::options())
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
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if ($state === 'lunas' && !$get('tanggal_bayar')) {
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
}
