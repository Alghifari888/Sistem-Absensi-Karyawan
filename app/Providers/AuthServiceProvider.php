<?php

namespace App\Providers;

use App\Models\Overtime;
use App\Policies\OvertimePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Leave;
use App\Policies\LeavePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Daftarkan Policy Overtime di sini
        Overtime::class => OvertimePolicy::class,
        Leave::class => LeavePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}