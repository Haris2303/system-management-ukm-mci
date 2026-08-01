<?php

namespace App\Filament\Resources\TagihanKas\Pages;

use App\Filament\Resources\TagihanKas\TagihanKasResource;
use App\Models\TagihanKas;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTagihanKas extends CreateRecord
{
    protected static string $resource = TagihanKasResource::class;

    private int $createdCount = 0;

    protected function handleRecordCreation(array $data): Model
    {
        $userIds = $data['user_ids'] ?? [];
        unset($data['user_ids']);

        $records = collect($userIds)
            ->map(fn($userId) => TagihanKas::create([...$data, 'user_id' => $userId]));

        $this->createdCount = $records->count();

        return $records->first();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->createdCount > 1
            ? "{$this->createdCount} tagihan berhasil dibuat!"
            : 'Tagihan berhasil dibuat!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
