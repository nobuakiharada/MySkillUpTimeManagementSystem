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


    //終了ボタン押下
    public function finish(Request $request, $id)
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

    //編集ボタン押下（修正）
    public function edit($id = null)
    {
        if ($id) {
            // 指定IDのレコードを取得
            $skillUpTime = TodaySkillUpTime::findOrFail($id);

            return view('edit', compact('skillUpTime'));
        }
        return view('edit');
    }

    //本日の研鑽一覧より編集ボタン押下
    public function update(Request $request, $id = null)
    {
        $data = $request->only(['user_name', 'user_id', 'date', 'start_time', 'end_time', 'study_content']);

        if ($data['start_time'] && $data['end_time']) {
            $startTime = Carbon::createFromFormat('H:i', $data['start_time']);
            $endTime = Carbon::createFromFormat('H:i', $data['end_time']);
            // start_timeとend_timeの差分を計算（分単位）
            $totalStudyTime = $startTime->diffInMinutes($endTime);
            // 差分をdataに追加
            $data['total_study_time'] = $totalStudyTime;
        }
        if ($data['end_time']) {
            $data['start_flag'] = '0';
            $data['end_flag'] = '1';
        } else {
            $data['start_flag'] = '1';
            $data['end_flag'] = '0';
        }

        $skillUpTime = null;
        if ($id) {
            $skillUpTime = TodaySkillUpTime::findOrFail($id); // 指定したIDのデータを取得
            // 取得したデータを更新
            $skillUpTime->update($data);
        } else {
            // 新規登録
            TodaySkillUpTime::create($data);
        }
        // 今日の日付の総勉強時間を合計
        $userId = $data['user_id'];
        $date = $data['date'];
        $dayTotalStudyTime = TodaySkillUpTime::getTotalStudyTimeForDay($userId, $date);

        // 入力日付の総勉強時間をDB登録
        $record = new TodayTotalSkillUpTime();
        $record->date = $data['date'];
        $judgeResult = TodayTotalSkillUpTime::todayJudgment($dayTotalStudyTime);
        TodayTotalSkillUpTime::updateOrCreate(
            ['date' => $data['date']], // 検索条件（主キー）
            [
                'total_minutes' => $dayTotalStudyTime,
                'judge_flag' => $judgeResult ? '0' : '1',
            ]
        );
        return redirect()->route('today.list')->with('message', '自己研鑽を修正しました。');
    }

    public function destroy($id)
    {
        // 指定したIDのデータを取得して削除
        $todaySkillUpTime = TodaySkillUpTime::findOrFail($id);
        $todaySkillUpTime->delete();
        return redirect()->route('today.list')->with('message', '１件の自己研鑽情報を削除しました。');
    }

    public function creat()
    {

        // 成功メッセージを付けてリダイレクト
        return redirect()->route('home')->with('success', '自己研鑽時間が削除されました！');
    }
}