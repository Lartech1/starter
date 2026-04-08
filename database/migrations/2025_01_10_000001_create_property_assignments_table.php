<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('realtor_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['assigned', 'active', 'completed'])->default('assigned');
            $table->timestamps();
            $table->unique(['property_id', 'realtor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_assignments');
    }
};
