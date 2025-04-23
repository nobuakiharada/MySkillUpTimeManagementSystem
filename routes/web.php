<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TodaySkillUpTimeController;

// 初期画面
Route::get('/', [HomeController::class, 'index'])->name('home');

// 本日の自己研鑽時間を管理する
Route::get('/today', [TodaySkillUpTimeController::class, 'index']);  // 一覧表示
Route::post('/today/store', [TodaySkillUpTimeController::class, 'store'])->name('today.store');// 新規登録
Route::get('/today/edit/{id}', [TodaySkillUpTimeController::class, 'edit'])->name('today.edit');// 編集
Route::post('/today/update/{id}', [TodaySkillUpTimeController::class, 'update'])->name('today.update');// 更新（editの更新処理）
Route::post('/today/destroy/{id}', [TodaySkillUpTimeController::class, 'destroy'])->name('today.destroy');// 削除



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Language Switcher Route 言語切替用ルートだよ
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});
