<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TodaySkillUpTimeController;
use App\Http\Controllers\TotalSkillUpTimeController;

// 初期画面
Route::get('/', [HomeController::class, 'index'])->name('home');

// 本日の自己研鑽時間を管理する
Route::get('/today/list', [TodaySkillUpTimeController::class, 'index'])->name('today.list');  // 一覧表示
Route::get('/today/store', [TodaySkillUpTimeController::class, 'store'])->name('today.store'); // 開始
Route::post('/today/store', [TodaySkillUpTimeController::class, 'store'])->name('today.store'); // 開始
Route::post('/today/finish/{id?}', [TodaySkillUpTimeController::class, 'finish'])->name('today.finish'); // 終了
Route::get('/today/edit/{id}', [TodaySkillUpTimeController::class, 'edit'])->name('today.edit'); // 編集
Route::post('/today/update/{id?}', [TodaySkillUpTimeController::class, 'update'])->name('today.update'); // 更新（editの更新処理）
Route::post('/today/destroy/{id}', [TodaySkillUpTimeController::class, 'destroy'])->name('today.destroy'); // 削除
Route::post('/today/break/{id?}', [TodaySkillUpTimeController::class, 'break'])->name('today.break');

// 自己研鑽記録の新規登録
Route::get('/today/create', [TodaySkillUpTimeController::class, 'edit'])->name('today.create'); // 新規登録処理へ

// 日々の研鑽まとめ情報を管理する
Route::get('/skillUpResult', [TotalSkillUpTimeController::class, 'index'])->name('skillUpResult'); // 総研鑽時間リスト表示
Route::get('/skillUpResult/edit/{date}', [TotalSkillUpTimeController::class, 'edit'])->name('skillUpResult.edit'); // 編集
Route::post('/skillUpResult/update/{date}', [TotalSkillUpTimeController::class, 'update'])->name('skillUpResult.update'); // 更新（editの更新処理）
Route::post('/skillUpResult/destroy/{date}', [TotalSkillUpTimeController::class, 'destroy'])->name('skillUpResult.destroy'); // 削除
Route::get('/skillUpResult/unique/{type}', [TotalSkillUpTimeController::class, 'uniqueButton'])->name('skillUpResult.uniqueButton'); // 特殊ボタン




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Language Switcher Route 言語切替用ルート
Route::get('language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
});