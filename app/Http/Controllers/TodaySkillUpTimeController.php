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
    public function index(Request $request)
    {
        $userId = 1020; //$userId = Auth::id();
        $selectedDate = $request->input('date', now()->toDateString());

        // 指定日のレコードを取得
        $todaySkillUpTimes = TodaySkillUpTime::where('user_id', $userId)
            ->where('date', $selectedDate)
            ->orderBy('id', 'desc')
            ->get();

        // レコードがなければメッセージを保存
        if ($todaySkillUpTimes->isEmpty()) {
            session()->flash('message', $selectedDate . ' の記録はありません。');
        }

        // 合計学習時間と判定レコードを取得（なければnull）
        $totalRecord = TodayTotalSkillUpTime::where('user_id', $userId)
            ->where('date', $selectedDate)
            ->first();
        return view('todayindex', compact('todaySkillUpTimes', 'selectedDate', 'userId', 'totalRecord'));
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
        // セッションに今日の日付の５データ情報がなければ改めて取得
        if (!Session::has('todaySkillUpTimeAllRecords')) {
            $userId = 1020; // または Auth::id();
            $todaySkillUpTimeAllRecords = TodaySkillUpTime::getTodayRecords($userId);
            if ($todaySkillUpTimeAllRecords) {
                Session::put('todaySkillUpTimeAllRecords', $todaySkillUpTimeAllRecords);
            }
        }

        return redirect()->route('home')->with('message', '自己研鑽が開始されました！');
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
        $userId = 1020; //$userId = Auth::id();
        $totalStudyTime = TodaySkillUpTime::getTotalStudyTimeForToday($userId);

        // 今日の日付の総勉強時間をDB登録or更新
        $today = Carbon::today();
        $judgeResult = TodayTotalSkillUpTime::totalStudyTimeJudgement($today, $totalStudyTime);
        $today = now()->toDateString();

        $record = TodayTotalSkillUpTime::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($record) {
            // 既存レコードがある → update
            TodayTotalSkillUpTime::where('user_id', $userId)
                ->where('date', $today)
                ->update([
                    'total_minutes' => $totalStudyTime,
                    'judge_flag' => $judgeResult ? '0' : '1',
                    'updated_at' => now(), // timestamps を手動で
                ]);
        } else {
            // レコードがない → insert
            TodayTotalSkillUpTime::create([
                'user_id' => $userId,
                'date' => $today,
                'total_minutes' => $totalStudyTime,
                'judge_flag' => $judgeResult ? '0' : '1',
            ]);
        };

        // end.blade.php を表示＋メッセージと本日の総自己研鑽時間を渡す
        return view('end', [
            'message' => '自己研鑽を終了しました。',
            'totalStudyTime' => $totalStudyTime,
        ]);
    }


    //編集ボタン押下（編集画面表示）
    public function edit($id = null)
    {
        if ($id) {
            // 指定IDのレコードを取得
            $skillUpTime = TodaySkillUpTime::findOrFail($id);

            return view('edit', compact('skillUpTime'));
        }
        return view('edit');
    }


    //編集画面より更新ボタン押下（修正）
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
        //$data['user_id'] = 1020;
        $userId = $data['user_id'];
        $date = $data['date'];
        $judgementdate = Carbon::parse($data['date']);
        $dayTotalStudyTime = TodaySkillUpTime::getTotalStudyTimeForDay($userId, $date);
        // 今日の日付の総勉強時間の判定
        $judgeFlag = TodayTotalSkillUpTime::totalStudyTimeJudgement($judgementdate, $dayTotalStudyTime);

        // 入力日付の総勉強時間をDB登録
        // まずはレコードが存在するか確認（user_id と date で複合条件）
        $record = TodayTotalSkillUpTime::where('user_id', $userId)
            ->where('date', $date)
            ->first();

        if ($record) {
            // 更新処理
            TodayTotalSkillUpTime::where('user_id', $userId)
                ->where('date', $date)
                ->update([
                    'total_minutes' => $dayTotalStudyTime,
                    'judge_flag' => $judgeFlag ? '0' : '1',
                ]);
        } else {
            // 新規作成処理
            TodayTotalSkillUpTime::create([
                'user_id' => $userId,
                'date' => $date,
                'total_minutes' => $dayTotalStudyTime,
                'judge_flag' => $judgeFlag ? '0' : '1',
            ]);
        }
        return redirect()->route('today.list')->with('message', $date . 'の自己研鑽を修正しました。');
    }


    // 自己研鑽記録の削除
    public function destroy($id)
    {
        // 指定したIDのデータを取得して削除
        $todaySkillUpTime = TodaySkillUpTime::findOrFail($id);
        $date = $todaySkillUpTime->date;
        $todaySkillUpTime->delete();
        return redirect()->route('today.list')->with('message', $todaySkillUpTime->date . 'の自己研鑽を１件削除しました。');
    }


    // 自己研鑽記録の新規登録
    public function creat()
    {
        // 成功メッセージを付けてリダイレクト
        return redirect()->route('home')->with('message', '自己研鑽時間が登録されました！');
    }
}