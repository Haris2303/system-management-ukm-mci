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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('ringkasan')->nullable();          // Preview di landing page
            $table->longText('konten');                     // Isi artikel lengkap (HTML/Markdown)
            $table->string('kategori')->default('Berita');  // Berita | Kegiatan | Prestasi | Pengumuman
            $table->string('tag')->nullable();              // Comma-separated tags
            $table->foreignId('author_id')->constrained('users');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false); // Pin di hero berita
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
