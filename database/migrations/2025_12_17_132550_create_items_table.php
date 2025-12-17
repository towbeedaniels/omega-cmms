<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_type_id')->constrained('inventory_types')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('item_categories')->onDelete('cascade');
            $table->foreignId('unit_of_measurement_id')->constrained('units_of_measurement')->onDelete('cascade');
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers')->onDelete('set null');
            
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('part_number')->nullable();
            $table->json('specifications')->nullable();
            
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('average_cost', 15, 2)->default(0);
            $table->decimal('last_purchase_cost', 15, 2)->nullable();
            $table->integer('current_stock')->default(0);
            
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            $table->integer('reorder_point')->default(0);
            $table->integer('lead_time_days')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_serialized')->default(false);
            $table->boolean('is_lot_tracked')->default(false);
            
            $table->integer('shelf_life_days')->nullable();
            $table->string('storage_conditions')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['inventory_type_id', 'is_active']);
            $table->index(['category_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};