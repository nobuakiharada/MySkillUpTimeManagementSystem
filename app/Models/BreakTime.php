<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'break_times';  // テーブル名の指定

    protected $fillable = [
        'user_id',          // ユーザーID
        'today',            // 日付
        'break_start',      // 休憩開始時間
        'break_end',        // 休憩終了時間
        'total_break_time'  // 休憩時間（分単位）
    ];

    // タイムスタンプを使う場合、これを true にする
    public $timestamps = true;  // "created_at" や "updated_at" を使うので true にする

    // 日付型のカラムがあれば、ここで指定する
    protected $dates = ['today'];

    // 関連する User モデルとリレーションを設定（必要に応じて）
    public function user()
    {
        return $this->belongsTo(User::class);  // User モデルとの関連
    }
}