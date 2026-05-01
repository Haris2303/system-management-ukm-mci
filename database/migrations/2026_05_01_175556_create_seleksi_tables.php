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
        // ── 1. Pertanyaan Seleksi ─────────────────────────────────
        Schema::create('pertanyaan_seleksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')
                ->constrained('divisis')        // Asumsi tabel divisis sudah ada
                ->cascadeOnDelete();
            $table->text('pertanyaan_teks');
            $table->boolean('is_active')->default(true);
            $table->integer('urut')->default(0);  // Urutan tampil ke pendaftar
            $table->timestamps();

            $table->index(['divisi_id', 'is_active']);
        });

        // ── 2. Pendaftars ─────────────────────────────────────────
        Schema::create('pendaftars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained('divisis')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nim')->index();
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('angkatan', 10)->nullable();
            $table->enum('status', ['menunggu', 'lulus', 'ditolak'])->default('menunggu');
            $table->foreignId('user_id')               // Diisi setelah diluluskan
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            // Satu NIM hanya boleh daftar satu kali per divisi
            $table->unique(['nim', 'divisi_id']);
            $table->index('status');
        });

        // ── 3. Jawaban Pendaftar ──────────────────────────────────
        Schema::create('jawaban_pendaftars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftar_id')
                ->constrained('pendaftars')
                ->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')
                ->constrained('pertanyaan_seleksis')
                ->cascadeOnDelete();
            $table->text('jawaban_teks');
            $table->integer('nilai_skor')->nullable(); // Diisi Ketua Divisi
            $table->timestamps();

            // Satu pendaftar satu jawaban per pertanyaan
            $table->unique(['pendaftar_id', 'pertanyaan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_pendaftars');
        Schema::dropIfExists('pendaftars');
        Schema::dropIfExists('pertanyaan_seleksis');
    }
};
