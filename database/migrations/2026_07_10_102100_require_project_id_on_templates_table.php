<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('templates', 'project_id') || DB::getDriverName() !== 'mysql') {
            return;
        }

        $projectId = DB::table('projects')->orderBy('id')->value('id');

        if ($projectId) {
            DB::table('templates')
                ->whereNull('project_id')
                ->update(['project_id' => $projectId]);
        }

        if (DB::table('templates')->whereNull('project_id')->doesntExist()) {
            DB::statement('ALTER TABLE templates MODIFY project_id INT UNSIGNED NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('templates', 'project_id') && DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE templates MODIFY project_id INT UNSIGNED NULL');
        }
    }
};
