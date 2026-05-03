<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ── Tabel Program Kerja ────────────────────────────────
        Schema::create('program_kerjas', function (Blueprint $table) {
            $table->id();
            // Null = proker umum (untuk semua) / proker Ketua UKM
            $table->foreignId('divisi_id')
                ->nullable()
                ->constrained('divisis')
                ->nullOnDelete();

            $table->string('nama_proker');
            $table->text('deskripsi')->nullable();

            // PIC = Person In Charge (penanggung jawab)
            $table->foreignId('pic_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('status', ['planning', 'active', 'completed'])
                ->default('planning');

            // Progress 0-100, di-update otomatis oleh Observer
            $table->integer('progress_persen')->default(0);

            $table->timestamps();

            $table->index(['divisi_id', 'status']);
            $table->index('pic_id');
        });

        // ── Tabel Tugas Proker ─────────────────────────────────
        Schema::create('tugas_prokers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proker_id')
                ->constrained('program_kerjas')
                ->cascadeOnDelete();
            $table->string('nama_tugas');
            $table->boolean('is_selesai')->default(false);
            $table->integer('urut')->default(0);
            $table->timestamps();

            $table->index(['proker_id', 'is_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_prokers');
        Schema::dropIfExists('program_kerjas');
    }
};
