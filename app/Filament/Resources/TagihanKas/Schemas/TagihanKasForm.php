<?php

namespace App\Filament\Resources\TagihanKas\Schemas;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class TagihanKasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Tagihan')
                    ->description('Buat tagihan iuran wajib untuk anggota.')
                    ->schema([

                        static::anggotaField($schema->getOperation()),

                        DatePicker::make('bulan_tagihan')
                            ->label('Tanggal Tagihan')
                            ->required()
                            ->default(now('Asia/Jayapura'))
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->formatStateUsing(fn($state) => $state ? Carbon::parse($state) : null)
                            ->dehydrateStateUsing(fn($state) => $state ? Carbon::parse($state)->format('Y-m') : null)
                            ->helperText('Tagihan akan dikelompokkan berdasarkan bulan dari tanggal yang dipilih.'),

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

    /**
     * Field Anggota: multi-select + tombol "Pilih Semua" saat membuat tagihan baru,
     * single select (semua user) saat mengedit tagihan yang sudah ada.
     */
    private static function anggotaField(string $operation): Select
    {
        if ($operation !== 'create') {
            return Select::make('user_id')
                ->label('Anggota')
                ->options(fn() => User::query()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->columnSpanFull();
        }

        return Select::make('user_ids')
            ->label('Anggota')
            ->multiple()
            ->options(fn() => static::eligibleAnggotaQuery()->pluck('name', 'id'))
            ->searchable()
            ->required()
            ->hintAction(
                Action::make('pilih_semua_anggota')
                    ->label('Pilih Semua Anggota')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn(Set $set) => $set('user_ids', static::eligibleAnggotaQuery()->pluck('id')->toArray()))
            )
            ->columnSpanFull();
    }

    /**
     * Anggota yang berhak ditagih: bukan Super Admin, bukan demisioner, dan belum dikeluarkan.
     */
    private static function eligibleAnggotaQuery()
    {
        return User::query()
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'demisioner']))
            ->whereNull('kicked_at')
            ->orderBy('name');
    }
}
