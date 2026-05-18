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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Anggota yang dicalonkan
            $table->string('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('foto')->nullable();             // Path foto kandidat
            $table->integer('urut')->default(0);            // Nomor urut kandidat
            $table->timestamps();
            $table->unique(['election_id', 'user_id']);     // 1 orang 1x per pemilihan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
