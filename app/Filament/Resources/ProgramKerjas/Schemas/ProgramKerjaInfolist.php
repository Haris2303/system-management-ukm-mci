<?php

namespace App\Filament\Resources\ProgramKerjas\Schemas;

use App\Models\ProgramKerja;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramKerjaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Program Kerja')
                ->schema([
                    TextEntry::make('nama_proker')
                        ->label('Nama Proker')
                        ->weight('bold')
                        ->size('lg'),

                    TextEntry::make('divisi.nama')
                        ->label('Divisi')
                        ->badge()
                        ->placeholder('🌐 Umum'),

                    TextEntry::make('label_status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(ProgramKerja $r) => match ($r->status) {
                            'planning'  => 'gray',
                            'active'    => 'warning',
                            'completed' => 'success',
                            default     => 'gray',
                        }),

                    TextEntry::make('pic.name')
                        ->label('PIC (Penanggung Jawab)')
                        ->placeholder('—'),

                    TextEntry::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->date('d F Y'),

                    TextEntry::make('tanggal_selesai')
                        ->label('Deadline')
                        ->date('d F Y')
                        ->color(fn(ProgramKerja $r) => $r->isTerlambat() ? 'danger' : null),

                    TextEntry::make('deskripsi')
                        ->label('Deskripsi')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])->columns(3),

            Section::make('Progress')
                ->schema([
                    TextEntry::make('progress_persen')
                        ->label('Tingkat Penyelesaian')
                        ->formatStateUsing(fn(int $state) => "{$state}%")
                        ->size('lg')
                        ->weight('bold')
                        ->color(fn(ProgramKerja $r) => $r->warna_progress),

                    TextEntry::make('tugas_selesai')
                        ->label('Tugas Selesai')
                        ->state(
                            fn(ProgramKerja $r) =>
                            $r->tugasProkers()->where('is_selesai', true)->count()
                                . ' dari ' . $r->tugasProkers()->count() . ' tugas'
                        ),

                    TextEntry::make('sisa_hari')
                        ->label('Sisa Waktu')
                        ->state(function (ProgramKerja $r): string {
                            if ($r->status === 'completed') return '🎉 Sudah selesai';
                            if ($r->isTerlambat())          return '⚠️ Terlambat ' . abs($r->sisaHari()) . ' hari';
                            return $r->sisaHari() . ' hari lagi';
                        })
                        ->color(
                            fn(ProgramKerja $r) =>
                            $r->isTerlambat() ? 'danger'
                                : ($r->status === 'completed' ? 'success' : 'info')
                        ),
                ])->columns(3),
        ]);
    }
}
