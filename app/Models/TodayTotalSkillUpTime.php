<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Yasumi\Yasumi; // 土日祝判定ライブラリ（後述）

class TodayTotalSkillUpTime extends Model
{
    protected $table = 'today_total_skill_up_time';

    // プライマリーキーを指定
    protected $primaryKey = null;
    public $incrementing = false; // 'date'は自動増分でない
    protected $keyType = 'string'; // date型だが、Laravelではstring扱いでOK

    // 自動的に日時のカラムを使う（created_at, updated_at）
    public $timestamps = true;

    // ホワイトリスト（fillable）で一括代入可能なカラムを指定
    protected $fillable = [
        'user_id',
        'date',
        'total_minutes',
        'judge_flag',
    ];

    // ユーザーの欠損日(未学習日)の補完関処理
    public static function fillMissingDates(int $userId): bool
    {
        $oldestRecord = self::where('user_id', $userId)->orderBy('date')->first();
        if (!$oldestRecord) {
            return true; // ユーザーにまだ1件もデータがない場合は何もしない
        }

        $startDate = Carbon::parse($oldestRecord->date);
        $endDate = Carbon::today();

        $existingDates = self::where('user_id', $userId)->pluck('date')->toArray();

        $datesToInsert = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!in_array($date->toDateString(), $existingDates)) {
                $datesToInsert[] = [
                    'user_id' => $userId,
                    'date' => $date->toDateString(),
                    'total_minutes' => 0,
                    'judge_flag' => '1', // デフォルトは「劣」
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($datesToInsert)) {
            try {
                DB::table((new self)->getTable())->insert($datesToInsert);
            } catch (\Exception $e) {
                Log::error("日付補完エラー（user_id={$userId}）", [
                    'error' => $e->getMessage(),
                ]);
                return false;
            }
        }

        return true;
    }

    // ユーザーの全学習日の総学習記録を再登録する処理
    public static function calculateAndSaveDailyStudyJudgments($userId)
    {
        $startDate = TodaySkillUpTime::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->value('date'); // 最も古い日付を1つ取得

        $endDate = Carbon::today()->toDateString(); // 今日

        if ($startDate) {
            $period = CarbonPeriod::create($startDate, $endDate);
            $dates = collect($period)->map(fn($date) => $date->toDateString());
        } else {
            $dates = collect(); // ユーザーに記録がない場合は空
        }

        foreach ($dates as $date) {
            // 指定日の総勉強時間を取得
            $totalStudyTime = TodaySkillUpTime::getTotalStudyTimeForDay($userId, $date) ?? 0;

            // 曜日判定（日曜=0, 土曜=6）
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $isWeekend = ($dayOfWeek === 0 || $dayOfWeek === 6); // 土日

            // 平日60分以上 / 休日150分以上なら合格（judge_flag=1）
            $judgeFlag = ($isWeekend && $totalStudyTime >= 150) || (!$isWeekend && $totalStudyTime >= 60) ? '0' : '1';

            // 更新または作成
            // TodayTotalSkillUpTime::updateOrCreate(
            //     [
            //         'user_id' => $userId,
            //         'date' => $date->toDateString(),
            //     ],
            //     [
            //         'total_minutes' => (int)$totalStudyTime,
            //         'judge_flag' => $judgeFlag,
            //     ]
            // );

            // 更新
            TodayTotalSkillUpTime::where('user_id', $userId)
                ->whereDate('date', $date)
                ->update([
                    'total_minutes' => (int)$totalStudyTime,
                    'judge_flag' => $judgeFlag,
                ]);
        }
    }

    public static function totalStudyTimeJudgement(Carbon $date, int $totalStudyTime): bool
    {
        // 土日（または祝日）なら true
        // $isWeekendOrHoliday = $date->isWeekend() || self::isHoliday($date);
        $isWeekendOrHoliday = $date->isWeekend();

        if ($isWeekendOrHoliday && $totalStudyTime >= 150) {
            return true;
        } elseif (!$isWeekendOrHoliday && $totalStudyTime >= 60) {
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