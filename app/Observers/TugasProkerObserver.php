<?php

namespace App\Observers;

use App\Models\TugasProker;

class TugasProkerObserver
{
    /** Tugas baru ditambahkan → recalculate progress proker */
    public function created(TugasProker $tugas): void
    {
        $this->updateProgressProker($tugas);
    }

    /** Tugas diupdate (is_selesai toggle, dsb.) → recalculate */
    public function updated(TugasProker $tugas): void
    {
        // Hanya recalc jika is_selesai yang berubah (optimasi)
        if ($tugas->wasChanged('is_selesai')) {
            $this->updateProgressProker($tugas);
        }
    }

    /** Tugas dihapus → recalculate progress proker */
    public function deleted(TugasProker $tugas): void
    {
        $this->updateProgressProker($tugas);
    }

    /**
     * Handle the TugasProker "restored" event.
     */
    public function restored(TugasProker $tugasProker): void
    {
        //
    }

    /**
     * Handle the TugasProker "force deleted" event.
     */
    public function forceDeleted(TugasProker $tugasProker): void
    {
        //
    }

    /**
     * Helper: panggil method updateProgress() di model ProgramKerja.
     * Method tersebut akan menghitung ulang persentase + auto-update status.
     */
    private function updateProgressProker(TugasProker $tugas): void
    {
        $proker = $tugas->programKerja;

        if ($proker) {
            $proker->updateProgress();
        }
    }
}
