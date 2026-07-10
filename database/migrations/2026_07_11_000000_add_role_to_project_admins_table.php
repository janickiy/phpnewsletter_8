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
        Schema::table('project_admins', function (Blueprint $table): void {
            $table->string('role')->default('project_admin')->after('user_id');
            $table->index(['project_id', 'role'], 'idx_project_admins_project_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_admins', function (Blueprint $table): void {
            $table->dropIndex('idx_project_admins_project_role');
            $table->dropColumn('role');
        });
    }
};
