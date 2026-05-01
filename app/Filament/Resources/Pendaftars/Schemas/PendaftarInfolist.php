<?php

namespace App\Filament\Resources\Pendaftars\Schemas;

use App\Models\Pendaftar;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PendaftarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pribadi')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Lengkap')
                            ->weight('bold'),
                        TextEntry::make('nim')
                            ->label('NIM'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('—'),
                        TextEntry::make('no_hp')
                            ->label('No. HP')
                            ->placeholder('—'),
                        TextEntry::make('divisi.nama')
                            ->label('Divisi')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('angkatan')
                            ->label('Angkatan')
                            ->placeholder('—'),
                    ])->columns(3),

                Section::make('Hasil Penilaian')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state) => match ($state) {
                                'lulus'   => 'success',
                                'ditolak' => 'danger',
                                default   => 'gray',
                            })
                            ->formatStateUsing(fn(Pendaftar $r) => $r->status === 'lulus' ? '✅ Lulus' : ($r->status === 'ditolak' ? '❌ Ditolak' : '⏳ Menunggu')),

                        TextEntry::make('total_skor')
                            ->label('Total Skor')
                            ->state(fn(Pendaftar $r) => $r->totalSkor() . ' pts')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('rata_skor')
                            ->label('Rata-rata Skor')
                            ->state(fn(Pendaftar $r) => $r->rataSkor() . ' / 100')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('user.email')
                            ->label('Akun Anggota Dibuat')
                            ->placeholder('Belum ada akun')
                            ->icon('heroicon-o-user-circle'),
                    ])->columns(2),
            ]);
    }
}
