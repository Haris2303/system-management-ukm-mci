<?php

namespace App\Filament\Resources\RagDocuments\Pages;

use App\Filament\Resources\RagDocuments\RagDocumentResource;
use App\Jobs\ProcessPdfJob;
use Filament\Resources\Pages\CreateRecord;

class CreateRagDocument extends CreateRecord
{
    protected static string $resource = RagDocumentResource::class;

    protected function afterCreate(): void
    {
        // Otomatis dispatch job setelah save
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
