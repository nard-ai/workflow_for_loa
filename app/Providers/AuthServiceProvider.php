<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\PFMOPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('view-approvals', function ($user) {
            // All users can view approvals
            return true;
        });

        Gate::define('manage-approvers', function ($user) {
            // Only department heads or VPAA can manage approvers
            return ($user->position === 'Head' || $user->position === 'VPAA') && $user->accessRole === 'Approver';
        });

        Gate::define('approve-requests', function ($user) {
            // Only users with Approver role can approve/reject requests
            return $user->accessRole === 'Approver';
        });

        // PFMO-specific gates
        Gate::define('access-pfmo', function ($user) {
            return app(\App\Policies\PFMOPolicy::class)->accessPFMO($user);
        });

        Gate::define('approve-pfmo-requests', function ($user) {
            return app(\App\Policies\PFMOPolicy::class)->approvePFMORequests($user);
        });

        Gate::define('manage-pfmo-workflows', function ($user) {
            return app(\App\Policies\PFMOPolicy::class)->managePFMOWorkflows($user);
        });

        Gate::define('view-pfmo-metrics', function ($user) {
            return app(\App\Policies\PFMOPolicy::class)->viewPFMOMetrics($user);
        });

        Gate::define('assign-pfmo-requests', function ($user) {
            return app(\App\Policies\PFMOPolicy::class)->assignPFMORequests($user);
        });
    }
}