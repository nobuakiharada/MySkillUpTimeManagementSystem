<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\TodaySkillUpTime;

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

        $justNow = false;
        if ($newSkillUpTimeRecord?->end_flag === "1" && !Session::has('todaySkillUpTime')) {
            $message = '本日の自己研鑽を開始しましょう！';
            session()->put('message', $message);
        }

        if ($newSkillUpTimeRecord?->start_flag === "1" || Session::has('todaySkillUpTime')) {
            $justNow = true;
            $message = '本日の自己研鑽中です！目標時間達成まで頑張って！';
            session()->put('message', $message);
        }

        session()->put('justNow', $justNow);

        return view('home')->with('newSkillUpTimeRecord', $newSkillUpTimeRecord);
    }
}