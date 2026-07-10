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
        Schema::create('project_subscriber', function (Blueprint $table): void {
            $table->unsignedInteger('project_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');

            $table->primary(['project_id', 'subscriber_id'], 'pk_project_subscriber');
            $table->index(['subscriber_id', 'project_id'], 'idx_project_subscriber_subscriber_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subscriber');
    }
};
