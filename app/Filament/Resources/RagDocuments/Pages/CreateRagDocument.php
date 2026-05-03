<?php

namespace App\Filament\Resources\RagDocuments\Pages;

use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Jobs\ProcessPdfJob;
use Filament\Resources\Pages\CreateRecord;

class CreateRagDocument extends CreateRecord
{
    protected static string $resource = RagDocumentResource::class;

    /**
     * Force status awal ke 'processing' agar admin tahu PDF sedang diolah.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status']       = 'processing';
        $data['total_chunks'] = 0;
        return $data;
    }

    /**
     * ⭐ Setelah record disimpan, dispatch job otomatis.
     */
    protected function afterCreate(): void
    {
        ProcessPdfJob::dispatch($this->getRecord()->id);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'PDF diupload! Chatbot sedang belajar dari dokumen Anda ⏳';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
