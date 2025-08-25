<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Jika tabel belum ada, buat lengkap
        if (!Schema::hasTable('ticket_prints')) {
            Schema::create('ticket_prints', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->string('source')->default('online');        // online|cashier
                $table->foreignId('printed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
            return;
        }

        // Jika tabel ada, tambahkan kolom yg hilang (tanpa "after")
        Schema::table('ticket_prints', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_prints', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable();
            }
            if (!Schema::hasColumn('ticket_prints', 'source')) {
                $table->string('source')->default('online');
            }
            if (!Schema::hasColumn('ticket_prints', 'printed_by')) {
                $table->unsignedBigInteger('printed_by')->nullable();
            }
        });

        // Tambah foreign key jika belum ada (dipisah agar aman di tabel existing)
        Schema::table('ticket_prints', function (Blueprint $table) {
            // order_id -> orders.id
            try {
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            } catch (\Throwable $e) {}

            // printed_by -> users.id (SET NULL)
            try {
                $table->foreign('printed_by')->references('id')->on('users')->onDelete('set null');
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        // Jangan drop tabel; hapus FK & kolom tambahan saja (opsional)
        Schema::table('ticket_prints', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_prints','printed_by')) {
                try { $table->dropForeign(['printed_by']); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('ticket_prints','order_id')) {
                try { $table->dropForeign(['order_id']); } catch (\Throwable $e) {}
            }
            // Kolom boleh dibiarkan, atau di-drop jika kamu mau benar-benar revert:
            // $table->dropColumn(['source','printed_by','order_id']);
        });
    }
};
