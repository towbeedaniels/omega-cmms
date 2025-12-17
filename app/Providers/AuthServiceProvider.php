<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\WorkOrder::class => \App\Policies\WorkOrderPolicy::class,
        \App\Models\Asset::class => \App\Policies\AssetPolicy::class,
        \App\Models\Facility::class => \App\Policies\FacilityPolicy::class,
        \App\Models\InventoryReference::class => \App\Policies\InventoryPolicy::class,
        \App\Models\ScheduledReport::class => \App\Policies\ReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Sanctum configuration
        Sanctum::usePersonalAccessTokenModel(\App\Models\PersonalAccessToken::class);

        // Define gates for CMMS permissions
        $this->defineGates();
    }

    /**
     * Define authorization gates for CMMS.
     */
    private function defineGates(): void
    {
        // Facility management gates
        Gate::define('facility.view', function ($user) {
            return in_array($user->access_level, ['view', 'edit', 'admin']);
        });

        Gate::define('facility.edit', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('facility.delete', function ($user) {
            return $user->access_level === 'admin';
        });

        // Asset management gates
        Gate::define('asset.view', function ($user) {
            return in_array($user->access_level, ['view', 'edit', 'admin']);
        });

        Gate::define('asset.edit', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('asset.delete', function ($user) {
            return $user->access_level === 'admin';
        });

        // Work order gates
        Gate::define('work-order.view', function ($user) {
            return in_array($user->access_level, ['view', 'edit', 'admin']);
        });

        Gate::define('work-order.create', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('work-order.edit', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('work-order.delete', function ($user) {
            return $user->access_level === 'admin';
        });

        Gate::define('work-order.approve', function ($user) {
            return $user->access_level === 'admin' || $user->role_id === 2; // Admin or Manager
        });

        Gate::define('work-order.complete', function ($user) {
            return in_array($user->role_id, [2, 3]); // Manager or Technician
        });

        // Inventory gates
        Gate::define('inventory.view', function ($user) {
            return in_array($user->access_level, ['view', 'edit', 'admin']);
        });

        Gate::define('inventory.edit', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('inventory.adjust', function ($user) {
            return $user->access_level === 'admin' || $user->role_id === 2; // Admin or Manager
        });

        // Report gates
        Gate::define('report.view', function ($user) {
            return in_array($user->access_level, ['view', 'edit', 'admin']);
        });

        Gate::define('report.generate', function ($user) {
            return in_array($user->access_level, ['edit', 'admin']);
        });

        Gate::define('report.export', function ($user) {
            return $user->access_level === 'admin';
        });

        // User management gates
        Gate::define('user.manage', function ($user) {
            return $user->access_level === 'admin';
        });

        // Facility scope restrictions
        Gate::define('access-facility', function ($user, $facilityId) {
            if ($user->access_level === 'admin') {
                return true;
            }

            $userFacilities = json_decode($user->facility_scope, true) ?? [];
            return in_array($facilityId, $userFacilities);
        });
    }
}