<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            
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
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};