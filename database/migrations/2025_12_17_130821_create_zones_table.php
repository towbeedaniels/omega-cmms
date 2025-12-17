<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('zone_type', ['floor', 'wing', 'department', 'area']);
            $table->string('level')->nullable();
            $table->json('boundaries')->nullable();
            $table->boolean('is_restricted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['building_id', 'zone_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};