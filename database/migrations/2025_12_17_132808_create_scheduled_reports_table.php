<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('report_type');
            $table->enum('schedule_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->json('schedule_config')->nullable();
            $table->timestamp('next_run_date');
            $table->timestamp('last_run_date')->nullable();
            $table->json('recipients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};