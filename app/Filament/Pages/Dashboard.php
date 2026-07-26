<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getColumns(): array|int
    {
        return 3;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateLaporanKeuangan')
                ->label('Generate Laporan')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->url(fn () => route('laporan-keuangan.pdf'))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('lihat_saldo_kas') ?? false),
        ];
    }
}
