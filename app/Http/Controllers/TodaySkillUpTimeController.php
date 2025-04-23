<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodaySkillUpTime;

class TodaySkillUpTimeController extends Controller
{
    
    public function index()
    {
        $userId = 1020; // または auth()->id()
        $today = now()->toDateString();
    
        // 今日のレコードだけ取得（ユーザーも限定）
        $todaySkillUpTimes = TodaySkillUpTime::where('user_id', $userId)
            ->where('date', $today)
            ->orderBy('start_time', 'asc')
            ->get();
    
        return view('todayindex', compact('todaySkillUpTimes'));
    }
    
    public function store(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'total_study_time' => 'nullable|integer',
            'study_content' => 'nullable|string',
            'start_flag' => 'required|in:0,1',
            'break_flag' => 'required|in:0,1',
            'end_flag' => 'required|in:0,1',
        ]);

        // 新しい自己研鑽時間のレコードを作成
        TodaySkillUpTime::create($validated);
        
        // 成功メッセージを付けてリダイレクト
        return redirect()->route('home')->with('success', '自己研鑽時間が登録されました！');
    }

    public function edit($id)
    {
        // 指定したIDのデータを取得
        $skillUpTime = TodaySkillUpTime::findOrFail($id);
        
        // 終了時に時間差を自動計算
        if ($request->start_time && $request->end_time) {
            $start = Carbon::createFromFormat('H:i', $request->start_time);
            $end = Carbon::createFromFormat('H:i', $request->end_time);
            $validated['total_study_time'] = $start->diffInMinutes($end);
        }
        // 編集フォームにデータを渡す
        return view('today.edit', compact('skillUpTime'));
    }
    
    public function update(Request $request, $id)
    {
        // バリデーション
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'total_study_time' => 'nullable|integer',
            'study_content' => 'nullable|string',
            'start_flag' => 'required|in:0,1',
            'break_flag' => 'required|in:0,1',
            'end_flag' => 'required|in:0,1',
        ]);
    
        // 指定したIDのデータを取得
        $skillUpTime = TodaySkillUpTime::findOrFail($id);
    
        // 取得したデータを更新
        $skillUpTime->update($validated);
    
        // 更新後にホーム画面へリダイレクト
        return redirect()->route('home')->with('success', '自己研鑽時間が更新されました！');
    }

    public function destroy($id)
    {
        // 指定したIDのデータを取得して削除
        $todaySkillUpTime = TodaySkillUpTime::findOrFail($id);
        $todaySkillUpTime->delete();

        // 成功メッセージを付けてリダイレクト
        return redirect()->route('home')->with('success', '自己研鑽時間が削除されました！');
    }
}
