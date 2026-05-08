<?php

namespace App\Filament\Resources\Elections\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ElectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemilihan')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul Pemilihan')
                            ->required()->maxLength(255)->columnSpanFull(),

                        TextInput::make('posisi')
                            ->label('Posisi yang Dipilih')
                            ->placeholder('Contoh: Ketua Umum, Wakil Ketua')
                            ->required()->maxLength(100),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft'   => '📝 Draft',
                                'aktif'   => '🟢 Aktif',
                                'selesai' => '🏁 Selesai',
                            ])
                            ->required()->default('draft'),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')->rows(3)->columnSpanFull(),
                    ])->columns(2),

                Section::make('Jadwal Pemilihan')
                    ->schema([
                        DateTimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')->required()->seconds(false),
                        DateTimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai')->required()->seconds(false)->after('waktu_mulai'),
                    ])->columns(2),

                Section::make('Pengaturan Keamanan')
                    ->schema([
                        Toggle::make('is_anonim')
                            ->label('Voting Anonim (Rahasia)')
                            ->helperText('Identitas pemilih tidak dapat dilacak.')
                            ->default(true),
                        Toggle::make('tampil_realtime')
                            ->label('Tampilkan Hasil Real-time')
                            ->helperText('Jika dimatikan, hasil hanya tampil setelah voting ditutup.')
                            ->default(false),
                    ])->columns(2),

                // ── Kandidat (Repeater) ───────────────────────────
                Section::make('Daftar Kandidat')
                    ->schema([
                        Repeater::make('candidates')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Anggota')
                                    ->options(User::query()->pluck('name', 'id'))
                                    ->searchable()->required(),

                                TextInput::make('urut')
                                    ->label('Nomor Urut')
                                    ->numeric()->default(1)->required(),

                                TextInput::make('visi')
                                    ->label('Visi')->maxLength(500)->columnSpanFull(),

                                Textarea::make('misi')
                                    ->label('Misi')->rows(3)->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('+ Tambah Kandidat')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['user_id'])
                                    ? 'Kandidat #' . ($state['urut'] ?? '?') . ' — ' . (User::find($state['user_id'])?->name ?? '')
                                    : 'Kandidat Baru'
                            ),
                    ]),
            ]);
    }
}
