<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds','order_id')) {
                $table->foreignId('order_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('refunds','amount')) {
                $table->unsignedInteger('amount')->after('order_id');
            }
            if (!Schema::hasColumn('refunds','reason')) {
                $table->text('reason')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('refunds','processed_by')) {
                $table->foreignId('processed_by')->nullable()
                      ->after('reason')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void {
        Schema::table('refunds', function (Blueprint $table) {
            if (Schema::hasColumn('refunds','processed_by')) {
                $table->dropConstrainedForeignId('processed_by');
            }
            if (Schema::hasColumn('refunds','order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
            if (Schema::hasColumn('refunds','reason')) $table->dropColumn('reason');
            if (Schema::hasColumn('refunds','amount')) $table->dropColumn('amount');
        });
    }
};
