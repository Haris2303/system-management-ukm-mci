<?php

namespace App\Filament\Resources\ProgramKerjas\Pages;

use App\Filament\Resources\ProgramKerjas\ProgramKerjaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewProgramKerja extends ViewRecord
{
    protected static string $resource = ProgramKerjaResource::class;

    // Polling 5s sebagai fallback (create/delete tugas juga ter-cover)
    protected ?string $pollingInterval = '5s';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    // Dipanggil saat RelationManager dispatch 'proker-progress-updated'
    #[On('proker-progress-updated')]
    public function refreshProgress(): void
    {
        $this->record->refresh();
    }
}
