<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->timestamp('waktu_mulai')->nullable()->change();
            $table->timestamp('waktu_selesai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->timestamp('waktu_mulai')->nullable(false)->change();
            $table->timestamp('waktu_selesai')->nullable(false)->change();
        });
    }
};
