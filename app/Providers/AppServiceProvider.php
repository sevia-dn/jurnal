<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer('layouts.admin.navbar', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('password_reset_requests')) {
                    $laporanGantiPw = \App\Models\PasswordResetRequest::orderBy('created_at', 'desc')->take(20)->get();
                    $unreadLaporanCount = \App\Models\PasswordResetRequest::where('status', 'menunggu')->count();
                } else {
                    $laporanGantiPw = collect();
                    $unreadLaporanCount = 0;
                }
            } catch (\Throwable $e) {
                $laporanGantiPw = collect();
                $unreadLaporanCount = 0;
            }

            $view->with([
                'laporanGantiPw' => $laporanGantiPw,
                'unreadLaporanCount' => $unreadLaporanCount,
            ]);
        });
    }
}
