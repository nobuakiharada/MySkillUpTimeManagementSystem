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
        Schema::create('today_skill_up_time_management', function (Blueprint $table) {
            $table->id(); // 自動インクリメントID
            $table->string('user_name'); // ユーザー名
            $table->integer('user_id'); // ユーザーID (integer 型)
            $table->date('date'); // 日付
            $table->time('start_time'); // 開始時間
            $table->time('end_time')->nullable(); // 終了時間
            $table->integer('total_study_time')->default(0); // 総学習時間（分単位）
            $table->text('study_content')->nullable(); // 学習内容
            $table->char('start_flag', 1)->default('0'); // 開始有効フラグ ('0' or '1')
            $table->char('break_flag', 1)->default('0'); // 休憩有効フラグ ('0' or '1')
            $table->char('end_flag', 1)->default('0'); // 終了有効フラグ ('0' or '1')
            $table->timestamps(); // 作成日時と更新日時

            // インデックスを追加
            $table->index('user_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_skill_up_time_management');
    }
};
