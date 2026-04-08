<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realtor_id')->constrained('users')->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->enum('outcome', ['interested', 'not-interested', 'needs-followup', 'pending'])->default('pending');
            $table->decimal('offered_price', 15, 2)->nullable();
            $table->enum('offer_status', ['pending', 'accepted', 'rejected', 'counter'])->default('pending');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('approved')->default(false);
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_visits');
    }
};
