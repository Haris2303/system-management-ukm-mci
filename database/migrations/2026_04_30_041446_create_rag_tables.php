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
        // ── Dokumen PDF yang diupload ─────────────────────────────
        Schema::create('rag_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nama_file');
            $table->string('path_file');
            $table->text('deskripsi')->nullable();
            $table->integer('total_chunks')->default(0);
            $table->enum('status', ['processing', 'ready', 'error'])->default('processing');
            $table->timestamps();
        });

        // ── Chunk teks hasil parsing PDF + embedding vector ───────
        Schema::create('rag_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('rag_documents')->cascadeOnDelete();
            $table->integer('chunk_index');
            $table->longText('content');              // Teks asli chunk
            $table->json('embedding')->nullable();    // Vector float[] dari embedding API
            $table->integer('token_count')->default(0);
            $table->timestamps();

            $table->index(['document_id', 'chunk_index']);
        });

        // ── Riwayat chat dengan chatbot ───────────────────────────
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->enum('role', ['user', 'assistant']);
            $table->text('content');
            $table->json('sources')->nullable();     // Chunk mana yang dipakai sebagai konteks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
        Schema::dropIfExists('rag_chunks');
        Schema::dropIfExists('rag_documents');
    }
};
