<?php

use App\Models\Agenda;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('agenda:tutup-otomatis', function () {
    $agendas = Agenda::aktif()
        ->where('waktu_selesai', '<', now('Asia/Jayapura'))
        ->get();

    if ($agendas->isEmpty()) {
        $this->info('Tidak ada agenda yang perlu ditutup.');
        return;
    }

    foreach ($agendas as $agenda) {
        $agenda->tutup();
        $this->info("Agenda [{$agenda->nama_agenda}] berhasil ditutup.");
    }
})->purpose('Tutup otomatis agenda yang sudah melewati waktu selesai');

Schedule::command('agenda:tutup-otomatis')->everyMinute();
