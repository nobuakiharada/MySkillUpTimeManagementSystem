<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Yasumi\Yasumi; // 土日祝判定ライブラリ（後述）

class TodayTotalSkillUpTime extends Model
{
    protected $table = 'today_total_skill_up_time';

    // プライマリーキーを指定
    protected $primaryKey = 'date';
    public $incrementing = false; // 'date'は自動増分でない
    protected $keyType = 'string'; // date型だが、Laravelではstring扱いでOK

    // 自動的に日時のカラムを使う（created_at, updated_at）
    public $timestamps = true;

    // ホワイトリスト（fillable）で一括代入可能なカラムを指定
    protected $fillable = [
        'date',
        'total_minutes',
        'judge_flag',
    ];



    public static function todayJudgment(int $totalStudyTime): bool
    {
        $today = Carbon::today();

        // 土日 or 祝日なら true
        $isWeekendOrHoliday = $today->isWeekend() || self::isHoliday($today);

        if ($isWeekendOrHoliday && $totalStudyTime >= 150) {
            return true;
        } else if (!($isWeekendOrHoliday) && $totalStudyTime >= 60) {
            return true;
        } else {
            return false;
        }
    }

    protected static function isHoliday(Carbon $date): bool
    {
        $holidays = Yasumi::create('Japan', $date->year);
        return $holidays->isHoliday($date);
    }
}