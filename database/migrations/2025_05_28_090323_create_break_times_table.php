<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakTimesTable extends Migration
{
    public function up()
    {
        Schema::create('break_times', function (Blueprint $table) {
            $table->id();  // プライマリーキー (自動インクリメント)
            $table->integer('user_id')->default(1020);
            $table->date('today');  // 休憩が取られた日付
            $table->time('break_start')->nullable();  // 休憩開始時刻
            $table->time('break_end')->nullable();  // 休憩終了時刻
            $table->integer('total_break_time')->default(0);  // 休憩時間（分単位）
            $table->timestamps();  // created_at, updated_at (省略する場合は削除)
            $table->index('user_id');  // インデックス追加
            $table->index('today');  // インデックス追加
        });
    }

    public function down()
    {
        Schema::dropIfExists('break_times');
    }
}