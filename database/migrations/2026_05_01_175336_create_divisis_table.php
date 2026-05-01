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
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                         // "Web Development", "AI & ML", dll.
            $table->string('slug')->unique();               // "web-development"
            $table->text('deskripsi')->nullable();
            $table->string('icon')->default('💻');         // Emoji icon
            $table->string('ketua')->nullable();            // Nama ketua divisi
            $table->boolean('is_active')->default(true);   // Bisa dibuka/ditutup
            $table->integer('urut')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};
