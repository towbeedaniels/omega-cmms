<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained('manufacturers')->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('asset_subcategories')->onDelete('cascade');
            $table->string('name');
            $table->string('model_number')->unique();
            $table->json('specifications')->nullable();
            $table->json('documentation')->nullable();
            $table->integer('maintenance_interval_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['manufacturer_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_models');
    }
};