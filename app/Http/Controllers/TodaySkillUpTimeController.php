<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\TodaySkillUpTime;
use App\Models\TodayTotalSkillUpTime;
use App\Http\Requests\StoreSkillUpTimeRequest;

class TodaySkillUpTimeController extends Controller
{

    public function index()
    {
        $userId = 1020; // または auth()->id()
        $today = now()->toDateString();

        // 今日のレコードだけ取得（ユーザーも限定）
        $todaySkillUpTimes = TodaySkillUpTime::where('user_id', $userId)
            ->where('date', $today)->orderBy('id', 'desc')->get();

        return view('todayindex', compact('todaySkillUpTimes'));
    }

    // 開始ボタン押下により、レコード作成
    public function store(StoreSkillUpTimeRequest $request)
    {
        // バリデーション
        $validatedRequest = $request->validated();
        // セッションに本日の自己研鑽情報がまだないときだけ新規作成・セッション保存
        if (!Session::has('todaySkillUpTime')) {
            $todaySkillUpTime = TodaySkillUpTime::create($validatedRequest);
            Session::put('todaySkillUpTime', $todaySkillUpTime);
        }
        // 今日の日付の全データ取得
        $userId = 1020;
        $todaySkillUpTimeAllRecords = TodaySkillUpTime::getTodayRecords($userId);

        // start.blade.php を表示＋メッセージと本日の自己研鑽内容を渡す
        return view('start', [
            'message' => '自己研鑽が開始されました！',
            'todaySkillUpTimeAllRecords' => $todaySkillUpTimeAllRecords,
        ]);
    }

    //編集ボタン押下（修正）
    public function edit($id, Request $request)
    {
        // 指定したIDのデータを取得
        $skillUpRecord = TodaySkillUpTime::findOrFail($id);

        // 終了時に時間差を自動計算
        if ($request->start_time && $request->end_time) {
            $start = Carbon::createFromFormat('H:i', $request->start_time);
            $end = Carbon::createFromFormat('H:i', $request->end_time);
            $validated['total_study_time'] = $start->diffInMinutes($end);
        }
        // 編集フォームにデータを渡す
        return view('today.edit', compact('skillUpTime'));
    }

    //終了ボタン押下
    public function update(Request $request, $id)
    {
        $skillUpTime = TodaySkillUpTime::findOrFail($id); // 指定したIDのデータを取得
        // リクエストから必要なデータのみを取得
        $data = $request->only(['start_flag', 'end_flag', 'study_content']);

        $startTime = Carbon::parse($skillUpTime->start_time);  // レコードのstart_time
        $endTime = Carbon::now()->format('H:i');  // 現在時刻を取得
        // start_timeとend_timeの差分を計算（分単位）
        $todayStudyTime = $startTime->diffInMinutes($endTime);
        // 差分をdataに追加
        $data['total_study_time'] = $todayStudyTime;
        $data['end_time'] = $endTime;


        // 取得したデータを更新
        $skillUpTime->update($data);
        // セッションから 'todaySkillUpTime' を削除
        Session::forget('todaySkillUpTime');

        // 今日の日付の総勉強時間を合計
        $userId = 1020;
        $totalStudyTime = TodaySkillUpTime::getTotalStudyTimeForToday($userId);

        // 今日の日付の総勉強時間をDB登録
        $record = new TodayTotalSkillUpTime();
        $record->date = now()->toDateString();
        $judgeResult = TodayTotalSkillUpTime::todayJudgment($totalStudyTime);
        TodayTotalSkillUpTime::updateOrCreate(
            ['date' => now()->toDateString()], // 検索条件（主キー）
            [
                'total_minutes' => $totalStudyTime,
                'judge_flag' => $judgeResult ? '0' : '1',
            ]
        );

        // end.blade.php を表示＋メッセージと本日の総自己研鑽時間を渡す
        return view('end', [
            'message' => '自己研鑽を終了しました。',
            'totalStudyTime' => $totalStudyTime,
        ]);
    }

    public function destroy($id)
    {
        // 指定したIDのデータを取得して削除
        $todaySkillUpTime = TodaySkillUpTime::findOrFail($id);
        $todaySkillUpTime->delete();

        return view('delete', [
            'message' => '１件の自己研鑽情報を削除しました。',
        ]);
    }

    public function register()
    {

        // 成功メッセージを付けてリダイレクト
        return redirect()->route('home')->with('success', '自己研鑽時間が削除されました！');
    }
}