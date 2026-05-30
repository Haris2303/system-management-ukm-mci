<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft','aktif','selesai','tie') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('elections', function (Blueprint $table) {
            $table->timestamp('tie_resolved_at')->nullable()->after('status');
            $table->enum('tie_resolution_type', ['revote', 'deliberation', 'casting_vote'])->nullable()->after('tie_resolved_at');
            $table->foreignId('tie_winner_candidate_id')->nullable()->after('tie_resolution_type')
                ->constrained('candidates')->nullOnDelete();
            $table->text('tie_resolution_notes')->nullable()->after('tie_winner_candidate_id');
            $table->foreignId('tie_casting_voter_id')->nullable()->after('tie_resolution_notes')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('parent_election_id')->nullable()->after('tie_casting_voter_id')
                ->constrained('elections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropForeign(['tie_winner_candidate_id']);
            $table->dropForeign(['tie_casting_voter_id']);
            $table->dropForeign(['parent_election_id']);
            $table->dropColumn([
                'tie_resolved_at',
                'tie_resolution_type',
                'tie_winner_candidate_id',
                'tie_resolution_notes',
                'tie_casting_voter_id',
                'parent_election_id',
            ]);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE elections MODIFY COLUMN status ENUM('draft','aktif','selesai') NOT NULL DEFAULT 'draft'");
        }
    }
};
