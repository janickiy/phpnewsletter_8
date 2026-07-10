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
        if (! Schema::hasColumn('organizations', 'owner_id')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->unsignedInteger('owner_id')->nullable()->after('id');

                $table->foreign('owner_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('organizations', 'owner_id')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            });
        }
    }
};
