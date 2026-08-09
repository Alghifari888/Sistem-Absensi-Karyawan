<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Leave;
use App\Observers\LeaveObserver;
use App\Models\Overtime;
use App\Observers\OvertimeObserver;
use App\Models\Setting;
use App\Observers\SettingObserver;

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
        // Daftarkan semua observer untuk model terkait (audit log CRUD)
        User::observe(UserObserver::class);
        Leave::observe(LeaveObserver::class);
        Overtime::observe(OvertimeObserver::class);
        Setting::observe(SettingObserver::class);

        // Listener App\Listeners\Log{SuccessfulLogin,SuccessfulLogout,FailedLogin}
        // otomatis ter-attach ke event Login/Logout/Failed oleh Laravel (event
        // auto-discovery, aktif secara default lewat Application::configure()
        // -> withEvents() di bootstrap/app.php). Tidak perlu didaftarkan manual.
    }
}
