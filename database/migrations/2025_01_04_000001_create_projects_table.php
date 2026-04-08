<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['residential', 'commercial', 'school', 'industrial', 'other']);
            $table->string('location')->nullable();
            $table->enum('status', ['planning', 'ongoing', 'completed', 'suspended'])->default('planning');
            $table->integer('completion_percentage')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('spent', 15, 2)->default(0);
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->string('image_url')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
