<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('channel')->default('online'); // online|offline
            $table->string('status')->default('pending'); // pending|paid|cancelled|refunded
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('fee')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->string('payment_method')->nullable(); // va|qris|cash|transfer
            $table->string('reference')->unique(); // ORD-2025xxxx
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
