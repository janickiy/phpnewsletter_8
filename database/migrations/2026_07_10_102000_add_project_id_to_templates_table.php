<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('templates', 'project_id')) {
            Schema::table('templates', function (Blueprint $table) {
                $table->unsignedInteger('project_id')->nullable()->after('id');

                $table->foreign('project_id')
                    ->references('id')
                    ->on('projects')
                    ->cascadeOnDelete();
            });
        }

        $projectId = DB::table('projects')->orderBy('id')->value('id');

        if ($projectId) {
            DB::table('templates')
                ->whereNull('project_id')
                ->update(['project_id' => $projectId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('templates', 'project_id')) {
            Schema::table('templates', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            });
        }
    }
};
