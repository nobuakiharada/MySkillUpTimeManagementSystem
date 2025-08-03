<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TodayTotalSkillUpTime;
use App\Models\BreakTime;

class TotalSkillUpTimeController extends Controller
{
    public function __construct() {}

    // 自己研鑽まとめ一覧表示
    public function index(Request $request)
    {
        $userId = 1020; //$userId = Auth::id();

        // 月リスト生成
        $months = collect();
        $baseDate = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 6; $i++) {
            $months->push($baseDate->copy()->subMonths($i));
        }

        // 選択された年月（なければ今月）
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($selectedMonth)->endOfMonth()->toDateString();

        // 該当月の記録取得
        $totalSkillUpTime = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            // ->paginate(30) // ページネーションは選択月滞在が難しくなるので排除
            ->get();

        // 各レコードに曜日情報を追加
        foreach ($totalSkillUpTime as $record) {
            $record->weekday = Carbon::parse($record->date)->isoFormat('dddd'); // 例: 月曜日
        }

        // 該当月の総学習時間（分単位の合計）
        $monthlyTotalMinutes = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_minutes');

        // 該当月の総休憩時間（分単位の合計）
        $monthlyBreakTime = BreakTime::where('user_id', $userId)
            ->whereBetween('today', [$startOfMonth, $endOfMonth])
            ->get();
        $monthlyBreakTime = $monthlyBreakTime->pluck('total_break_time', 'today')->toArray();

        return view('skillUpList', compact(
            'totalSkillUpTime',
            'months',
            'selectedMonth',
            'monthlyTotalMinutes',
            'userId',
            'monthlyBreakTime',
        ));
    }


    // 自己研鑽まとめ情報の編集画面表示
    public function edit($date)
    {
        $userId = 1020; // 本来は Auth::id() などを使う
        $record = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereDate('date', $date)
            ->firstOrFail();

        return view('totalSkillUpTime.edit', compact('record'));
    }


    // 自己研鑽まとめ情報の修正
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

        return redirect()->route('skillUpResult')->with('summaryMessage', $date . ' の総学習時間を修正しました。',);
    }


    // 自己研鑽まとめ情報の削除
    public function destroy(Request $request, $date)
    {
        $userId = 1020; //$userId = Auth::id();

        // クエリビルダでそのまま削除（モデルを経由しない）
        TodayTotalSkillUpTime::where('user_id', $userId)
            ->where('date', $date)
            ->delete();

        // hiddenフィールドで渡された月情報を取得（デフォルトは今月）
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        Log::info('削除処理で受け取った month:', ['month' => $request->input('month')]);

        return redirect()->route('skillUpResult', ['month' => $selectedMonth])
            ->with('summaryMessage', $date . ' の総自己研鑽記録を削除しました。');
    }


    // 特殊ボタン処理（未研鑽日登録・総自己研鑽時間更新）
    public function uniqueButton(Request $request, $type)
    {
        $userId = 1020; //$userId = Auth::id();
        $selectedMonth = $request->query('month');

        if ($type === 'unstudySave') {
            $result = TodayTotalSkillUpTime::fillMissingDates($userId, $selectedMonth);
            if (!$result) {
                return redirect()->back()->with('summaryMessage', '欠損日の補完中にエラーが発生しました。');
            }
            return redirect()->route('skillUpResult', ['month' => $selectedMonth])->with('summaryMessage', "{$selectedMonth} の未研鑽日を正常に登録しました。");
        }

        if ($type === 'reRegister') {
            TodayTotalSkillUpTime::calculateAndSaveDailyStudyJudgments($userId, $selectedMonth);
            return redirect()->route('skillUpResult', ['month' => $selectedMonth])->with('summaryMessage', "{$selectedMonth} の総自己研鑽時間を再登録しました。",);
        }

        return redirect()->back()->with('summaryMessage', '無効な操作が指定されました。');
    }


    /* ----------------API------------  */
    // 未登録日チェックAPI
    public function unstudyDaysFillCheck(Request $request)
    {
        $userId = 1020; // 本番では Auth::id() を使う

        $selectedMonth = $request->input('selectedMonth');

        // 月初と月末をCarbonで取得
        try {
            $monthCarbon = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '無効な月形式です。',
            ], 400);
        }

        $now = Carbon::now();
        $isCurrentMonth = $monthCarbon->isSameMonth($now);

        $start = $monthCarbon;
        $end = $isCurrentMonth ? Carbon::yesterday() : $monthCarbon->copy()->endOfMonth();

        $datesToCheck = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $datesToCheck->push($date->format('Y-m-d'));
        }

        $existingDates = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'));

        $missingDates = $datesToCheck->diff($existingDates);

        return response()->json([
            'status' => $missingDates->isEmpty() ? 'already_filled' : 'unfilled',
        ]);
    }
}