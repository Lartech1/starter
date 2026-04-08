<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['blocks', 'land', 'merchandise', 'service']);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->enum('status', ['pending', 'confirmed', 'in-delivery', 'delivered', 'cancelled'])->default('pending');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('delivery_address')->nullable();
            $table->date('requested_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
