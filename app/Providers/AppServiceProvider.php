<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\Leave;
use App\Observers\LeaveObserver;
use App\Models\Overtime;
use App\Observers\OvertimeObserver;

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
        // Daftarkan semua observer untuk model terkait
        User::observe(UserObserver::class);
        Leave::observe(LeaveObserver::class);
      Overtime::observe(OvertimeObserver::class); 
    }
}
