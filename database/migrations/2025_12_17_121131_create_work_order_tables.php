<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subsystem_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('work_type', ['corrective', 'preventive', 'predictive', 'emergency', 'project'])->default('corrective');
            
            $table->date('scheduled_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            
            $table->text('safety_instructions')->nullable();
            $table->json('required_tools')->nullable();
            $table->json('required_materials')->nullable();
            
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->text('completion_notes')->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            
            $table->json('attachments')->nullable();
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['facility_id', 'status']);
            $table->index(['assigned_to', 'scheduled_date']);
            $table->index(['priority', 'due_date']);
        });

        Schema::create('work_order_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('completed_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamp('completed_at');
            $table->enum('status', ['completed', 'completed_with_issues', 'failed'])->default('completed');
            
            $table->decimal('actual_hours', 8, 2);
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('material_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->virtualAs('labor_cost + material_cost');
            
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

        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('work_order_completions');
        Schema::dropIfExists('work_orders');
    }
};