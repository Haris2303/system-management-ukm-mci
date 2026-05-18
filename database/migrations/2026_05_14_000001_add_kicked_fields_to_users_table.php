<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('kicked_at')->nullable()->after('avatar');
            $table->foreignId('kicked_by')->nullable()->constrained('users')->nullOnDelete()->after('kicked_at');
            $table->string('kicked_reason')->nullable()->after('kicked_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kicked_by']);
            $table->dropColumn(['kicked_at', 'kicked_by', 'kicked_reason']);
        });
    }
};
