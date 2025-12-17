<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Asset Classification Tables
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('depreciation_years')->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('asset_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_calibration')->default(false);
            $table->integer('calibration_interval_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('support_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('asset_subcategories')->onDelete('cascade');
            $table->string('name');
            $table->string('model_number')->unique();
            $table->json('specifications')->nullable();
            $table->json('documentation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['manufacturer_id', 'subcategory_id']);
        });

        // Main Assets Table
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('model_id')->constrained()->onDelete('cascade');
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

        // Subsystems Table
        Schema::create('subsystems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('subsystems')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['mechanical', 'electrical', 'plumbing', 'hvac', 'security', 'fire', 'other']);
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['building_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsystems');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('models');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('asset_subcategories');
        Schema::dropIfExists('asset_categories');
    }
};