<?php

namespace App\Providers;

use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        View::composer(['layouts.admin.navbar', 'layouts.admin.*'], function ($view) {
            try {
                if (Schema::hasTable('password_reset_requests')) {
                    $laporanGantiPw = PasswordResetRequest::orderBy('created_at', 'desc')->take(20)->get();
                    $unreadLaporanCount = PasswordResetRequest::where('status', 'menunggu')->count();
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
