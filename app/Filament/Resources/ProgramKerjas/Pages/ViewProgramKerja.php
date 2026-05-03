<?php

namespace App\Fialment\Resources\ProgramKerjas\Pages;

use App\Filament\Resources\ProgramKerjas\ProgramKerjaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramKerja extends ViewRecord
{
    protected static string $resource = ProgramKerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
        ];
    }
}
