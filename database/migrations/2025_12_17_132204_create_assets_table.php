<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
            $table->foreignId('model_id')->constrained('asset_models')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('assets')->onDelete('set null');
            
            $table->string('asset_tag')->unique();
            $table->string('serial_number')->nullable();
            $table->string('name');
            $table->enum('status', ['operational', 'under_maintenance', 'out_of_service', 'retired', 'pending'])
                  ->default('operational');
                  
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            
            $table->json('specifications')->nullable();
            $table->json('location_details')->nullable();
            $table->json('attachments')->nullable();
            
            $table->integer('useful_life_years')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            
            $table->boolean('is_critical')->default(false);
            $table->boolean('requires_calibration')->default(false);
            $table->date('last_calibration_date')->nullable();
            $table->date('next_calibration_date')->nullable();
            
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['facility_id', 'status']);
            $table->index(['model_id', 'is_critical']);
            $table->index('next_maintenance_date');
            $table->index('next_calibration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};