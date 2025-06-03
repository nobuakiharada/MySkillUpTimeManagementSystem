<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TodaySkillUpTime extends Model
{
    // use HasFactory;

    protected $table = 'today_skill_up_time_management';

    protected $fillable = [
        'user_name',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'total_study_time',
        'study_content',
        'start_flag',
        'break_flag',
        'end_flag',
    ];


    /**
     * 今日の最新レコードを取得（ユーザーID指定）
     *
     * @param int $userId
     * @return \App\Models\TodaySkillUpTime|null
     */
    public static function getLatestRecordForToday($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * 今日の日付の全データを取得（ユーザーID指定、最大5件）
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTodayRecords($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->orderByDesc('id')
            ->take(5)
            ->get();
    }

    /**
     * 今日の総勉強時間を取得
     *
     * @param int $userId
     * @return float
     */
    public static function getTotalStudyTimeForToday(int $userId)
    {
        $breakTime = BreakTime::where([
            ['today', '=', Carbon::today()->toDateString()],
            ['user_id', '=', $userId],
        ])->first();
        if ($breakTime) {
            return self::where('user_id', $userId)
                ->whereDate('date', Carbon::today())
                ->sum('total_study_time') - $breakTime->total_break_time;
        } else {
            return self::where('user_id', $userId)
                ->whereDate('date', Carbon::today())
                ->sum('total_study_time');
        }
    }

    /**
     * ある日の総勉強時間を取得
     *
     * @param int $userId
     * @return float
     */
    public static function getTotalStudyTimeForDay(int $userId, $date)
    {
        $breakTime = BreakTime::where([
            ['today', '=', $date],
            ['user_id', '=', $userId],
        ])->first();
        if ($breakTime) {
            return self::where('user_id', $userId)
                ->whereDate('date', $date)
                ->sum('total_study_time') - $breakTime->total_break_time;
        } else {
            return self::where('user_id', $userId)
                ->whereDate('date', $date)
                ->sum('total_study_time');
        }
    }
}