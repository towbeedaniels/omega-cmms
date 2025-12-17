<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_consumable')->default(true);
            $table->boolean('is_trackable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units_of_measurement', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->unique();
            $table->enum('type', ['length', 'weight', 'volume', 'count', 'time', 'other']);
            $table->decimal('conversion_factor', 10, 4)->default(1);
            $table->string('base_unit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_of_measurement_id')->constrained()->onDelete('cascade');
            $table->foreignId('manufacturer_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('part_number')->nullable();
            $table->json('specifications')->nullable();
            
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('average_cost', 15, 2)->default(0);
            $table->decimal('last_purchase_cost', 15, 2)->nullable();
            
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
            $table->index(['code']);
        });

        Schema::create('inventory_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('cascade');
            
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

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            
            $table->enum('transaction_type', [
                'receipt', 'issue', 'adjustment', 'transfer_in', 'transfer_out',
                'return', 'write_off', 'consumption'
            ]);
            
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->string('lot_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('remarks')->nullable();
            
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
            
            $table->index(['item_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['warehouse_id', 'transaction_type']);
            $table->index(['lot_number', 'expiry_date']);
        });

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
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_references');
        Schema::dropIfExists('items');
        Schema::dropIfExists('units_of_measurement');
        Schema::dropIfExists('inventory_types');
    }
};