<?php

namespace App\Filament\Resources\Elections\Pages;

use App\Filament\Resources\Elections\ElectionResource;
use App\Models\User;
use App\Services\ElectionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewElection extends ViewRecord
{
    protected static string $resource = ElectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rekap_suara')
                ->label('Lihat Rekap Suara')
                ->icon('heroicon-o-presentation-chart-bar')
                ->color('success')
                ->url(fn() => route('elections.rekap', $this->record))
                ->openUrlInNewTab(),

            // ── Tie Resolution Actions (hanya tampil saat status 'tie') ──

            Action::make('mulai_revote')
                ->label('Mulai Putaran Kedua')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn() => $this->record->status === 'tie')
                ->requiresConfirmation()
                ->modalHeading('Mulai Revote?')
                ->modalDescription('Sistem akan membuat sesi pemilihan baru secara otomatis. Hanya kandidat yang seri yang akan diikutsertakan.')
                ->action(function (): void {
                    $newElection = app(ElectionService::class)->createRevote($this->record);

                    Notification::make()
                        ->title('Sesi revote berhasil dibuat!')
                        ->success()
                        ->send();

                    $this->redirect(ElectionResource::getUrl('view', ['record' => $newElection]));
                }),

            Action::make('musyawarah')
                ->label('Tetapkan Pemenang Manual')
                ->icon('heroicon-o-document-check')
                ->color('info')
                ->visible(fn() => $this->record->status === 'tie')
                ->slideOver()
                ->form([
                    Select::make('tie_winner_candidate_id')
                        ->label('Kandidat Pemenang')
                        ->options(function () {
                            $candidates = $this->record->candidates()
                                ->withCount('votes as jumlah_suara')
                                ->with('user')
                                ->get();
                            $max = $candidates->max('jumlah_suara');

                            return $candidates
                                ->where('jumlah_suara', $max)
                                ->pluck('user.name', 'id');
                        })
                        ->required(),

                    Textarea::make('tie_resolution_notes')
                        ->label('Berita Acara / Catatan Musyawarah')
                        ->placeholder('Tuliskan hasil musyawarah, alasan penetapan, dan pihak yang hadir...')
                        ->rows(5)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'tie_resolution_type'     => 'deliberation',
                        'tie_winner_candidate_id' => $data['tie_winner_candidate_id'],
                        'tie_resolution_notes'    => $data['tie_resolution_notes'],
                        'tie_resolved_at'         => now(),
                        'status'                  => 'selesai',
                    ]);

                    Notification::make()
                        ->title('Pemenang berhasil ditetapkan melalui musyawarah!')
                        ->success()
                        ->send();

                    $this->redirect(ElectionResource::getUrl('view', ['record' => $this->record]));
                }),

            Action::make('casting_vote')
                ->label('Casting Vote Presidium')
                ->icon('heroicon-o-key')
                ->color('danger')
                ->visible(fn() => $this->record->status === 'tie')
                ->slideOver()
                ->form([
                    Select::make('tie_casting_voter_id')
                        ->label('Pilih Presidium Sidang')
                        ->options(fn() => User::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Placeholder::make('casting_vote_info')
                        ->label('Informasi')
                        ->content('Akun yang dipilih akan mendapatkan satu token voting ekstra yang hanya berlaku untuk pemilihan ini.'),
                ])
                ->action(function (array $data): void {
                    app(ElectionService::class)->grantCastingVote($this->record, $data['tie_casting_voter_id']);

                    Notification::make()
                        ->title('Token casting vote telah diberikan. Presidium dapat melakukan voting sekarang.')
                        ->success()
                        ->send();
                }),

            EditAction::make(),
        ];
    }
}
