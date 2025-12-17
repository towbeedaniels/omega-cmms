<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsystems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
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
    }
};