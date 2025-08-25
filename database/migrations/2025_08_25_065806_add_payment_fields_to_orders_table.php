<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('payment_method'); // path file
            $table->timestamp('verified_at')->nullable()->after('payment_proof');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            // status sudah ada: pending|paid|... -> kita pakai:
            // waiting_approval (non-cash), awaiting_cash (cash), paid
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof','verified_at','verified_by']);
        });
    }
};
