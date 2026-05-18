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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            // voter_id NULLABLE + terpisah untuk menjaga anonimitas
            // Kita simpan hash voter, bukan ID langsung
            $table->string('voter_hash', 64);               // SHA-256 hash dari user_id + election_id
            $table->timestamps();
            // Satu voter hanya bisa vote satu kali per pemilihan
            $table->unique(['election_id', 'voter_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
