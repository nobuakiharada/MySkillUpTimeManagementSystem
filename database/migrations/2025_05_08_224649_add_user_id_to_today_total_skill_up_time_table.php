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
        Schema::table('today_total_skill_up_time', function (Blueprint $table) {
            $table->integer('user_id')->default(1020);
            $table->dropPrimary(); // 一旦主キーを解除
            $table->primary(['user_id', 'date']); // 複合主キーに変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('today_total_skill_up_time', function (Blueprint $table) {
            $table->dropPrimary(['user_id', 'date']);
            $table->dropColumn('user_id');
            $table->primary('date');
        });
    }
};