<?php

namespace App\Filament\Resources\Materis\Pages;

use App\Filament\Resources\Materis\MateriResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMateri extends CreateRecord
{
    protected static string $resource = MateriResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-fill uploader dengan admin yang sedang login
        $data['uploaded_by'] = auth()->id();
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Materi berhasil ditambahkan!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
