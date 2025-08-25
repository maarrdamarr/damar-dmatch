<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');             // refund | swap
            $table->string('reference');        // nomor pemesanan
            $table->json('payload')->nullable();// {amount, reason, old_sp_id, new_sp_id}
            $table->string('status')->default('open'); // open|processing|resolved|rejected
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('support_tickets'); }
};
