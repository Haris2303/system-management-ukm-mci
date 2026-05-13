<?php

namespace App\Filament\Resources\Agendas\Pages;

use App\Filament\Resources\Agendas\AgendaResource;
use App\Models\Presensi;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAgenda extends ViewRecord
{
    protected static string $resource = AgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('catat_izin')
                ->label('Catat Izin')
                ->icon('heroicon-m-document-check')
                ->color('warning')
                ->modalHeading('Catat Anggota Izin')
                ->modalDescription('Pilih anggota yang tidak hadir karena izin. Status mereka akan tercatat sebagai Izin dan tidak dihitung Absen saat agenda ditutup.')
                ->modalSubmitActionLabel('Simpan Izin')
                ->schema([
                    Select::make('user_ids')
                        ->label('Pilih Anggota')
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->options(fn() => User::whereDoesntHave(
                            'roles',
                            fn($q) => $q->whereIn('name', ['super_admin', 'demisioner'])
                        )
                            ->whereNotIn('id', $this->getRecord()->presensis()->pluck('user_id'))
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->helperText('Hanya menampilkan anggota yang belum tercatat hadir/izin.')
                        ->noSearchResultsMessage('Anggota tidak ditemukan.')
                        ->placeholder('Cari nama anggota...'),
                ])
                ->action(function (array $data): void {
                    $agenda  = $this->getRecord();
                    $jumlah  = 0;

                    foreach ($data['user_ids'] as $userId) {
                        Presensi::firstOrCreate(
                            ['user_id' => $userId, 'agenda_id' => $agenda->id],
                            ['status' => 'Izin', 'jam_hadir' => now('Asia/Jayapura')],
                        );
                        $jumlah++;
                    }

                    Notification::make()
                        ->title('Izin berhasil dicatat')
                        ->body("{$jumlah} anggota dicatat izin.")
                        ->success()
                        ->send();
                })
                ->visible(fn(): bool => (bool) $this->getRecord()->is_active),

            Action::make('tutup_agenda')
                ->label('Tutup Agenda')
                ->icon('heroicon-m-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Tutup Agenda Sekarang?')
                ->modalDescription('Semua anggota yang belum melakukan presensi akan otomatis dicatat sebagai Absen. Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Tutup Agenda')
                ->visible(fn(): bool => (bool) $this->getRecord()->is_active)
                ->action(function (): void {
                    $this->getRecord()->tutup();

                    Notification::make()
                        ->title('Agenda ditutup')
                        ->body('Data absen anggota yang tidak hadir telah dicatat.')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
                }),
        ];
    }
}
