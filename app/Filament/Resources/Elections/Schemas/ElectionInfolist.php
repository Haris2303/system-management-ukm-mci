<?php

namespace App\Filament\Resources\Elections\Schemas;

use App\Models\Election;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ElectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Pemilihan')
                    ->schema([
                        TextEntry::make('judul')->label('Judul')->weight('bold'),
                        TextEntry::make('posisi')->label('Posisi')->badge()->color('primary'),
                        TextEntry::make('status')->label('Status')->badge()
                            ->color(fn(string $state) => match ($state) {
                                'aktif'   => 'success',
                                'draft'   => 'gray',
                                'selesai' => 'info',
                                'tie'     => 'warning',
                                default   => 'gray',
                            }),
                        TextEntry::make('waktu_mulai')->label('Mulai')->dateTime('d M Y, H:i'),
                        TextEntry::make('waktu_selesai')->label('Selesai')->dateTime('d M Y, H:i'),
                        TextEntry::make('deskripsi')->label('Deskripsi')->columnSpanFull(),
                        IconEntry::make('is_anonim')->label('Anonim')->boolean(),
                        IconEntry::make('tampil_realtime')->label('Real-time')->boolean(),
                    ])->columns(2),

                // ── Hasil Seri & Resolusi ─────────────────────────────
                Section::make('Hasil Seri & Resolusi')
                    ->schema([
                        // Banner peringatan — hanya tampil selama status masih 'tie'
                        TextEntry::make('tie_warning_banner')
                            ->label('')
                            ->getStateUsing(fn() => '⚖️ Pemilihan ini berakhir SERI dan sedang menunggu keputusan presidium')
                            ->badge()
                            ->color('warning')
                            ->columnSpanFull()
                            ->hidden(fn(Election $record) => $record->status !== 'tie'),

                        // Metode resolusi — hanya tampil setelah resolved
                        TextEntry::make('tie_resolution_type')
                            ->label('Metode Resolusi')
                            ->badge()
                            ->formatStateUsing(fn(?string $state) => match ($state) {
                                'revote'       => '🔄 Revote',
                                'deliberation' => '🤝 Musyawarah',
                                'casting_vote' => '🗳️ Casting Vote',
                                default        => '—',
                            })
                            ->hidden(fn(Election $record) => $record->tie_resolved_at === null),

                        // Pemenang yang ditetapkan
                        TextEntry::make('tieWinnerCandidate.user.name')
                            ->label('Pemenang yang Ditetapkan')
                            ->weight('bold')
                            ->hidden(fn(Election $record) => $record->tie_winner_candidate_id === null),

                        // Waktu penetapan
                        TextEntry::make('tie_resolved_at')
                            ->label('Ditetapkan Pada')
                            ->dateTime('d M Y, H:i')
                            ->hidden(fn(Election $record) => $record->tie_resolved_at === null),

                        // Berita acara / catatan musyawarah
                        TextEntry::make('tie_resolution_notes')
                            ->label('Berita Acara / Catatan')
                            ->columnSpanFull()
                            ->hidden(fn(Election $record) => empty($record->tie_resolution_notes)),
                    ])
                    ->columns(2)
                    ->hidden(fn(Election $record) => $record->status !== 'tie' && $record->tie_resolved_at === null),

                // Rekap Suara Realtime
                Section::make('Rekap Hasil Suara')
                    ->schema([
                        ViewEntry::make('rekap_suara')
                            ->view('filament.infolists.components.election-results')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
