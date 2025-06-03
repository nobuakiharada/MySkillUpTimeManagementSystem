<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $userId = 1020; // Auth::id() とかに置き換えてOK
            $today = Carbon::today()->toDateString();

            $breakTime = BreakTime::where('user_id', $userId)
                ->where('today', $today)
                ->first();

            $totalBreakTime = $breakTime?->total_break_time ?? 0;

            View::share('totalBreakTime', $totalBreakTime);
        });
    }
}