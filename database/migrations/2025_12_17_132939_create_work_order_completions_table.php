<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('completed_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamp('completed_at');
            $table->enum('status', ['completed', 'completed_with_issues', 'failed'])->default('completed');
            
            $table->decimal('actual_hours', 8, 2);
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('material_cost', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->json('parts_used')->nullable();
            $table->string('signature_path')->nullable();
            $table->json('attachments')->nullable();
            
            $table->integer('downtime_hours')->nullable();
            $table->decimal('cost_savings', 15, 2)->nullable();
            
            $table->boolean('customer_satisfaction')->nullable();
            $table->text('customer_feedback')->nullable();
            
            $table->timestamps();
            
            $table->index(['work_order_id']);
            $table->index(['completed_by', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_completions');
    }
};