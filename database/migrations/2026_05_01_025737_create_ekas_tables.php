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
        Schema::create('tagihan_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('bulan_tagihan', 7);     // Format: YYYY-MM (contoh: 2025-01)
            $table->bigInteger('nominal');          // ⚠️ bigInteger untuk Rupiah (bukan decimal)
            $table->enum('status', ['belum_dibayar', 'lunas'])->default('belum_dibayar');
            $table->timestamp('tanggal_bayar')->nullable();
            $table->text('catatan')->nullable();    // Catatan opsional dari bendahara
            $table->timestamps();

            // Cegah duplikasi: 1 user 1 tagihan per bulan
            $table->unique(['user_id', 'bulan_tagihan']);
            $table->index('status');
        });

        // ── Tabel Transaksi Kas (Arus Kas Umum) ────────────────
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->bigInteger('nominal');          // ⚠️ bigInteger untuk Rupiah
            $table->string('keterangan');
            $table->date('tanggal');
            $table->string('bukti')->nullable();    // Path foto kuitansi/bukti
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenis', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
        Schema::dropIfExists('tagihan_kas');
    }
};
