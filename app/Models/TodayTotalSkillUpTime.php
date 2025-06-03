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
    public static function fillMissingDates(int $userId, string $month): bool
    {
        try {
            $startDate = Carbon::parse($month)->startOfMonth();
            $yesterday = Carbon::yesterday();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            // 「昨日」と「選択月の月末」のうち、早い方を上限にする
            $endDate = $yesterday->lt($endOfMonth) ? $yesterday : $endOfMonth;

            // 対象月の既存日付
            $existingDates = self::where('user_id', $userId)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->pluck('date')
                ->toArray();

            $datesToInsert = [];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if (!in_array($date->toDateString(), $existingDates)) {
                    $datesToInsert[] = [
                        'user_id' => $userId,
                        'date' => $date->toDateString(),
                        'total_minutes' => 0,
                        'judge_flag' => '1', // 努力不足
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($datesToInsert)) {
                self::insert($datesToInsert);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('欠損日補完エラー: ' . $e->getMessage());
            return false;
        }
    }

    // ユーザーの全学習日の総学習記録を再登録する処理
    public static function calculateAndSaveDailyStudyJudgments(int $userId, string $month)
    {
        // 指定月の開始日と終了日（昨日まで）
        $startDate = Carbon::parse($month)->startOfMonth();
        $yesterday = Carbon::yesterday();
        $endOfMonth = Carbon::parse($month)->endOfMonth();
        $endDate = $yesterday->lt($endOfMonth) ? $yesterday : $endOfMonth;

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = collect($period)->map(fn($date) => $date->toDateString());

        foreach ($dates as $date) {
            // 指定日の総勉強時間を取得（nullなら0扱い）
            $totalStudyTime = TodaySkillUpTime::getTotalStudyTimeForDay($userId, $date) ?? 0;

            // 曜日判定（日曜=0, 土曜=6）
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $isWeekend = ($dayOfWeek === 0 || $dayOfWeek === 6); // 土日

            // 判定ロジック：平日60分 / 休日150分
            $judgeFlag = ($isWeekend && $totalStudyTime >= 150) || (!$isWeekend && $totalStudyTime >= 60) ? '0' : '1';

            // データ更新（存在前提）
            TodayTotalSkillUpTime::where('user_id', $userId)
                ->whereDate('date', $date)
                ->update([
                    'total_minutes' => (int)$totalStudyTime,
                    'judge_flag' => $judgeFlag,
                ]);
        }
    }

    // 総自己学習時間の合否判定処理
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

    // 休日か判定処理
    protected static function isHoliday(Carbon $date): bool
    {
        $holidays = Yasumi::create('Japan', $date->year);
        return $holidays->isHoliday($date);
    }

    // 今日の日付の総勉強時間をDB登録or更新する処理
    public static function upsertTotalStudyTime(int $userId, Carbon $date, int $totalStudyTime): void
    {
        $judgeResult = self::totalStudyTimeJudgement($date, $totalStudyTime) ? '0' : '1';

        $record = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($record) {
            // 既存レコードがある → update
            TodayTotalSkillUpTime::where('user_id', $userId)
                ->where('date', $date->toDateString())
                ->update([
                    'total_minutes' => $totalStudyTime,
                    'judge_flag' => $judgeResult,
                    'updated_at' => now(), // timestamps を手動で
                ]);
        } else {
            // レコードがない → insert
            TodayTotalSkillUpTime::create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
                'total_minutes' => $totalStudyTime,
                'judge_flag' => $judgeResult,
                'updated_at' => now()
            ]);
        };
    }
}