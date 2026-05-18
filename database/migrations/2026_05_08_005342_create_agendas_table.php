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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_agenda');
            $table->text('deskripsi');
            $table->timestamp('waktu_mulai');
            $table->timestamp('waktu_selesai');
            $table->string('lokasi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('qr_code_token', 64)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
