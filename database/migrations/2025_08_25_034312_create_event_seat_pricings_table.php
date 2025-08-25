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
        Schema::create('event_seat_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stadium_seat_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price'); // dalam rupiah
            $table->string('status')->default('available'); // available|reserved|sold
            $table->timestamps();
            $table->unique(['event_id','stadium_seat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_seat_pricings');
    }
};
