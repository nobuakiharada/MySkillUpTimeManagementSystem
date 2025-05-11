<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\TodayTotalSkillUpTime;

class TotalSkillUpTimeController extends Controller
{
    public function index(Request $request)
    {
        $userId = 1020;

        // 欠損日補完（失敗時はエラーメッセージ付きで戻す）
        // $result = TodayTotalSkillUpTime::fillMissingDates($userId);
        // if (!$result) {
        //     return redirect()->back()->with('message', '欠損日の補完中にエラーが発生しました。');
        // }

        // 月リスト生成（例：過去12ヶ月分）
        $months = collect();
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i)->startOfMonth();
            $months->push($month->format('Y-m'));
        }

        // 選択された年月（なければ今月）
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($selectedMonth)->endOfMonth()->toDateString();

        // 該当月の記録取得
        $totalSkillUpTime = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            ->paginate(30);

        // 該当月の総学習時間（分単位の合計）
        $monthlyTotalMinutes = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_minutes');

        // ユーザーの全学習日の総学習記録を再登録する処理
        // TodayTotalSkillUpTime::calculateAndSaveDailyStudyJudgments($userId);

        return view('skillUpList', compact(
            'totalSkillUpTime',
            'months',
            'selectedMonth',
            'monthlyTotalMinutes',
            'userId',
        ));
    }

    public function store(Request $request)
    {
        $userId = 1020; //$userId = Auth::id();

        // バリデーション
        $validated = $request->validate([
            'date' => "required|date|unique:today_total_skill_up_time,date,NULL,id,user_id,$userId", // 複合ユニーク
            'total_minutes' => 'required|integer|min:1',
        ]);

        $totalStudyTime = $request->total_minutes;
        $today = Carbon::today();
        $judgeResult = TodayTotalSkillUpTime::totalStudyTimeJudgment($today, $totalStudyTime);

        TodayTotalSkillUpTime::create([
            'user_id' => $userId,
            'date' => $request->date,
            'total_minutes' => $totalStudyTime,
            'judge_flag' => $judgeResult ? '0' : '1',
        ]);

        return redirect()->route('skillUpResult')->with('message', '研鑽記録を登録しました。');
    }

    public function edit($date)
    {
        $userId = 1020; // 本来は Auth::id() などを使う
        $record = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereDate('date', $date)
            ->firstOrFail();

        return view('totalSkillUpTime.edit', compact('record'));
    }

    public function update(Request $request, $date)
    {
        $userId = 1020;

        $request->validate([
            'hours' => 'required|integer|min:0',
            'minutes' => 'required|integer|min:0|max:59',
        ]);

        $totalMinutes = $request->input('hours') * 60 + $request->input('minutes');

        // 自動判定
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $isWeekend = ($dayOfWeek === 0 || $dayOfWeek === 6);
        $judgeFlag = ($isWeekend && $totalMinutes >= 150) || (!$isWeekend && $totalMinutes >= 60) ? '0' : '1';

        TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereDate('date', $date)
            ->update([
                'total_minutes' => $totalMinutes,
                'judge_flag' => $judgeFlag,
                'updated_at' => now(), // timestamps を手動で
            ]);

        return redirect()->route('skillUpResult')->with('message', $date . ' の総学習時間を修正しました。');
    }

    public function destroy($date)
    {
        $userId = 1020; //$userId = Auth::id();

        // クエリビルダでそのまま削除（モデルを経由しない）
        TodayTotalSkillUpTime::where('user_id', $userId)
            ->where('date', $date)
            ->delete();

        return redirect()->route('skillUpResult')->with('message', $date . ' の総学習時間をリセットしました。');
    }
}