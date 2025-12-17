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
            $table->foreignId('asset_id')->nullable()->constrained('assets')->onDelete('set null');
            $table->foreignId('subsystem_id')->nullable()->constrained('subsystems')->onDelete('set null');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};