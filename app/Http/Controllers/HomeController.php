<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TodaySkillUpTime;

class HomeController extends Controller
{
    public function index()
    {
        $userId = 1020; // または auth()->id()
        // 今日の最新レコードだけ取得（ユーザーも限定）
        $newSkillUpTimeRecord = TodaySkillUpTime::getLatestRecordForToday($userId);
        // 今日の自己研鑽情報を5件取得
        $todaySkillUpTimeAllRecords = TodaySkillUpTime::getTodayRecords($userId);
        Session::put('todaySkillUpTimeAllRecords', $todaySkillUpTimeAllRecords);

        $justNow = false;
        $message = '本日の自己研鑽を開始しましょう！';
        if ($newSkillUpTimeRecord?->start_flag === "1" || Session::has('todaySkillUpTime')) {
            $justNow = true;
            $message = '自己研鑽が開始されてます！！';
        }
        session()->put('justNow', $justNow);
        session()->put('message', $message);

        return view('home');
    }
}