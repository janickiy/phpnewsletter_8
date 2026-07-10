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
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organization_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->string('default_sender_name')->nullable();
            $table->string('default_from_email')->nullable();
            $table->string('default_reply_to')->nullable();
            $table->string('timezone')->nullable();
            $table->unsignedInteger('unsubscribe_template_id')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('unsubscribe_template_id')
                ->references('id')
                ->on('templates')
                ->nullOnDelete();

            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
