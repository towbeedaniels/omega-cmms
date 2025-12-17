<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('asset_id')->nullable()->constrained('assets')->onDelete('cascade');
            
            $table->string('reference_code')->unique();
            $table->string('manufacturer_part_number')->nullable();
            $table->string('supplier_part_number')->nullable();
            
            $table->integer('minimum_order_quantity')->default(1);
            $table->decimal('supplier_price', 15, 2)->nullable();
            $table->string('supplier_currency', 3)->default('USD');
            
            $table->integer('delivery_lead_time')->nullable();
            $table->string('delivery_terms')->nullable();
            
            $table->json('compatibility_info')->nullable();
            $table->json('substitute_items')->nullable();
            
            $table->text('notes')->nullable();
            $table->boolean('is_preferred')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['item_id', 'supplier_id']);
            $table->index(['asset_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_references');
    }
};