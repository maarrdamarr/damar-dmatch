<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stadium_seats', function (Blueprint $table) {
            $table->enum('seat_class', ['regular','vip','vvip'])->default('regular')->after('seat_number');
            $table->unsignedTinyInteger('ring')->default(1)->after('seat_class');      // 1=outer(Reg),2=mid(VIP),3=inner(VVIP)
            $table->unsignedSmallInteger('angle_deg')->default(0)->after('ring');      // 0..359
            $table->index(['ring','angle_deg']);
            $table->index(['seat_class']);
        });
    }
    public function down(): void {
        Schema::table('stadium_seats', function (Blueprint $table) {
            $table->dropIndex(['ring','angle_deg']);
            $table->dropIndex(['seat_class']);
            $table->dropColumn(['seat_class','ring','angle_deg']);
        });
    }
};
