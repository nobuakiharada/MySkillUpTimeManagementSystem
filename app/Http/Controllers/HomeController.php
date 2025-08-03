<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\TodaySkillUpTime;
use App\Models\TodayTotalSkillUpTime;

class HomeController extends Controller
{
    public function index()
    {
        $userId = 1020; // または auth()->id()
        View::share('userId', $userId);
        // 今日の最新レコードだけ取得（ユーザーも限定）
        $newSkillUpTimeRecord = TodaySkillUpTime::getLatestRecordForToday($userId);
        // 今日の自己研鑽情報を5件取得
        $todaySkillUpTimeAllRecords = TodaySkillUpTime::getTodayRecords($userId);
        if (!$todaySkillUpTimeAllRecords->isEmpty()) {
            Session::put('todaySkillUpTimeAllRecords', $todaySkillUpTimeAllRecords);
        }
        // 合計学習時間と判定レコードを取得（なければnull）
        $totalStudyTime = TodaySkillUpTime::getTotalStudyTimeForToday($userId);
        View::share('totalStudyTime', $totalStudyTime);

        $justNow = false;
        $message = null;
        // 今日の自己研鑽時間が存在する場合、メッセージを設定
        if ($newSkillUpTimeRecord?->start_flag === "1" || Session::has('todaySkillUpTime')) {
            $justNow = true;
            $message = '本日の自己研鑽中です！目標時間達成まで頑張って！';
        } elseif ($newSkillUpTimeRecord?->end_flag === "1") {
            $message = '本日の自己研鑽を開始しましょう！';
        }

        if ($message !== null) {
            session()->put('message', $message);
        }
        session()->put('justNow', $justNow);

        return view('home')->with([
            'newSkillUpTimeRecord' => $newSkillUpTimeRecord,
        ]);
    }
}