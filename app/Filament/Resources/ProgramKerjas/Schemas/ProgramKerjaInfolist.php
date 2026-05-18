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
                        ->size('lg')
                        ->columnSpanFull(),

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
                    TextEntry::make('progress_bar')
                        ->label('Tingkat Penyelesaian')
                        ->html()
                        ->columnSpanFull()
                        ->state(function (ProgramKerja $r): string {
                            $pct     = $r->progress_persen;
                            $selesai = $r->tugasProkers()->where('is_selesai', true)->count();
                            $total   = $r->tugasProkers()->count();

                            [$barColor, $textColor, $bgColor] = match (true) {
                                $pct === 100          => ['#10b981', '#059669', '#ecfdf5'],
                                $r->isTerlambat()     => ['#ef4444', '#dc2626', '#fef2f2'],
                                $pct >= 50            => ['#f59e0b', '#d97706', '#fffbeb'],
                                default               => ['#3b82f6', '#2563eb', '#eff6ff'],
                            };

                            $badge = match (true) {
                                $r->status === 'completed' =>
                                "<span style='background:{$bgColor};color:{$textColor}' class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold'>✅ Selesai</span>",
                                $r->isTerlambat() =>
                                "<span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600'>⚠️ Terlambat " . abs($r->sisaHari()) . " hari</span>",
                                $r->sisaHari() <= 7 && $r->sisaHari() >= 0 =>
                                "<span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600'>⏰ {$r->sisaHari()} hari lagi</span>",
                                default => '',
                            };

                            return <<<HTML
                                <div class="w-full space-y-2 py-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-3xl font-bold leading-none" style="color:{$textColor}">{$pct}%</span>
                                        {$badge}
                                    </div>
                                    <div class="w-full rounded-full overflow-hidden" style="height:14px;background:#e2e8f0;">
                                        <div style="width:{$pct}%;background-color:{$barColor};height:100%;border-radius:9999px;transition:width 0.7s ease-in-out;"></div>
                                    </div>
                                    <p class="text-xs" style="color:#94a3b8">{$selesai} dari {$total} tugas selesai</p>
                                </div>
                            HTML;
                        }),

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

                    TextEntry::make('tanggal_selesai')
                        ->label('Deadline')
                        ->date('d F Y')
                        ->color(fn(ProgramKerja $r) => $r->isTerlambat() ? 'danger' : 'gray'),

                ])->columns(2),
        ]);
    }
}
