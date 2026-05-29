<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropForeign(['tie_casting_voter_id']);
            $table->dropColumn('tie_casting_voter_id');
        });

        DB::statement("ALTER TABLE elections MODIFY COLUMN tie_resolution_type ENUM('revote','deliberation') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE elections MODIFY COLUMN tie_resolution_type ENUM('revote','deliberation','casting_vote') NULL");

        Schema::table('elections', function (Blueprint $table) {
            $table->foreignId('tie_casting_voter_id')->nullable()->after('tie_resolution_notes')
                ->constrained('users')->nullOnDelete();
        });
    }
};
