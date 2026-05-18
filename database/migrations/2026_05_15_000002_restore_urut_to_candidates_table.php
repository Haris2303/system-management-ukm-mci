<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->unsignedSmallInteger('urut')->default(0)->after('foto');
        });

        // Isi urut otomatis untuk data yang sudah ada berdasarkan urutan id
        $electionIds = DB::table('candidates')->distinct()->pluck('election_id');

        foreach ($electionIds as $electionId) {
            $candidates = DB::table('candidates')
                ->where('election_id', $electionId)
                ->orderBy('id')
                ->pluck('id');

            foreach ($candidates as $index => $id) {
                DB::table('candidates')->where('id', $id)->update(['urut' => $index + 1]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('urut');
        });
    }
};
