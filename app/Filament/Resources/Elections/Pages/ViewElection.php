<?php

namespace App\Filament\Resources\Elections\Pages;

use App\Filament\Resources\Elections\ElectionResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
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
            EditAction::make(),
        ];
    }
}
