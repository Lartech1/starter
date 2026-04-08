<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estate_manager_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['land', 'duplex', 'flat', 'house', 'commercial', 'blocks']);
            $table->enum('status', ['available', 'sold', 'rented', 'on-hold', 'under-maintenance'])->default('available');
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('rental_price', 15, 2)->nullable();
            $table->decimal('area_size', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->boolean('has_bq')->default(false);
            $table->text('features')->nullable();
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();
            $table->string('project_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
