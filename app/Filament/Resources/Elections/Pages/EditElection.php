<?php

namespace App\Filament\Resources\Elections\Pages;

use App\Filament\Resources\Elections\ElectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditElection extends EditRecord
{
    protected static string $resource = ElectionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (in_array($this->record->status, ['selesai', 'tie'])) {
            Notification::make()
                ->title('Pemilihan sudah selesai dan tidak dapat diedit.')
                ->warning()
                ->send();

            $this->redirect(ElectionResource::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pemilihan berhasil diperbarui!';
    }
}
