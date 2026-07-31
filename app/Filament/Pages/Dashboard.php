<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getColumns(): array|int
    {
        return 3;
    }

    protected function getHeaderActions(): array
    {
        $bisaLaporanKeuangan = auth()->user()?->hasAnyRole(['super_admin', 'ketua_ukm', 'bendahara']) ?? false;
        $bisaLaporanAbsensi = auth()->user()?->hasAnyRole(['super_admin', 'ketua_ukm', 'sekretaris']) ?? false;

        return [
            ActionGroup::make([
                Action::make('generateLaporanKeuangan')
                    ->label('Laporan Keuangan')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->url(fn () => route('laporan-keuangan.pdf'))
                    ->openUrlInNewTab()
                    ->visible($bisaLaporanKeuangan),

                Action::make('generateLaporanAbsensi')
                    ->label('Laporan Absensi')
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->url(fn () => route('laporan-absensi.pdf'))
                    ->openUrlInNewTab()
                    ->visible($bisaLaporanAbsensi),
            ])
                ->label('Generate Laporan')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->button()
                ->visible($bisaLaporanKeuangan || $bisaLaporanAbsensi),
        ];
    }
}
