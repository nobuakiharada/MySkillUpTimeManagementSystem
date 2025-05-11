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
        Schema::create('today_total_skill_up_time', function (Blueprint $table) {
            $table->integer('user_id'); // ユーザーID
            $table->date('date')->primary(); // 日付が主キー
            $table->integer('total_minutes')->default(0); // 総自己研鑽時間（分）
            $table->char('judge_flag', 1)->default('1'); // 判断フラグ ('0':優 or '1':劣)
            $table->timestamps(); // created_at, updated_at を追加する場合

            $table->primary(['user_id', 'date']); // 複合主キー
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_total_skill_up_time');
    }
};