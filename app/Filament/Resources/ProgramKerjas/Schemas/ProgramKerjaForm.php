<?php

namespace App\Filament\Resources\ProgramKerjas\Schemas;

use App\Models\Divisi;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Program Kerja')
                    ->schema([
                        TextInput::make('nama_proker')
                            ->label('Nama Program Kerja')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Workshop Laravel untuk Anggota Baru')
                            ->columnSpanFull(),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        // ⭐ Mutator: divisi_id otomatis diisi saat create
                        Select::make('divisi_id')
                            ->label('Divisi')
                            ->options(Divisi::query()->orderBy('urut')->pluck('nama', 'id'))
                            ->searchable()
                            ->placeholder('— Proker Umum (Ketua UKM) —')
                            ->default(fn() => auth()->user()?->divisi_id)
                            ->disabled(fn() => ! auth()->user()?->hasAnyRole(['super_admin', 'ketua_ukm']))
                            ->dehydrated()
                            ->helperText('Otomatis terisi sesuai divisi Anda. Hanya Super Admin & Ketua UKM yang bisa mengubah.'),

                        Select::make('pic_id')
                            ->label('PIC (Penanggung Jawab)')
                            ->options(function () {
                                $user = auth()->user();
                                // Filter user berdasarkan divisi (kecuali admin)
                                $query = User::query();
                                if (! $user?->hasAnyRole(['super_admin', 'ketua_ukm']) && $user?->divisi_id) {
                                    $query->where('divisi_id', $user->divisi_id);
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->default(fn() => auth()->id())
                            ->preload(),

                        DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->default(now()),

                        DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->native(false)
                            ->after('tanggal_mulai'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'planning'  => '📋 Perencanaan',
                                'active'    => '🚀 Berjalan',
                                'completed' => '✅ Selesai',
                            ])
                            ->default('planning')
                            ->required()
                            ->helperText('Status berubah otomatis berdasarkan progress tugas.'),

                    ])->columns(2),
            ])->columns(1);
    }
}
