<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClearSessionOnDateChange
{
    public function handle(Request $request, Closure $next)
    {
        $today = date('Y-m-d'); // 今日の日付（例: 2025-06-04）

        // セッションに保存された最後のアクセス日付を取得
        $lastAccessDate = session('last_access_date');

        if (!$lastAccessDate) {
            Session::put('last_access_date', $today);
            // 日付が違ったらセッションをリセット
        } elseif ($lastAccessDate !== $today) {
            // セッションをクリア（全データ削除）
            session()->flush();
            // Auth::logout();
            // return redirect('/login')->withErrors([
            //     'message' => '日付が変わったため、再ログインしてください。',
            // ]);

            // 今日の日付をセット
            session(['last_access_date' => $today]);
        }

        return $next($request);
    }
}