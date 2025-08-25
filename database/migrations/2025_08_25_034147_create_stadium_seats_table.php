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
        Schema::create('stadium_seats', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // A, B, C ...
            $table->string('row_label'); // 1..N
            $table->string('seat_number'); // 1..N
            $table->unique(['section','row_label','seat_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stadium_seats');
    }
};
