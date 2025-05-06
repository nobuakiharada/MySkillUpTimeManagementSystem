<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodayTotalSkillUpTime;

class TotalSkillUpTimeController extends Controller
{
    public function index()
    {

        $totalSkillUpTime = TodayTotalSkillUpTime::orderBy('date', 'desc')->paginate(30);

        return view('skillUpList', compact('totalSkillUpTime'));
    }

    public function destroy($date)
    {
        $record = TodayTotalSkillUpTime::find($date);

        if ($record) {
            $record->delete();
        }
        return redirect()->route('skillUpResult')->with('message', '研鑽記録を削除しました。');
    }

    public function register()
    {
        return view('totalSkillUpTime.register');
    }

    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'date' => 'required|date|unique:today_total_skill_up_time,date', // 日付はユニーク
            'total_minutes' => 'required|integer|min:1',
        ]);

        $totalStudyTime = $request->total_minutes;
        $judgeResult = TodayTotalSkillUpTime::todayJudgment($totalStudyTime);
        $judgeFlag = $judgeResult ? '0' : '1';

        // 保存
        TodayTotalSkillUpTime::create([
            'date' => $request->date,
            'total_minutes' => $totalStudyTime,
            'judge_flag' => $judgeFlag,
        ]);

        // 一覧画面（ホーム）へリダイレクト
        return redirect()->route('skillUpResult')->with('message', '研鑽記録を削除しました。');
    }
}