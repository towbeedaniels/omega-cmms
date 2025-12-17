<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        // Custom route model bindings
        $this->registerModelBindings();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // More restrictive rate limiting for authentication
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Rate limiting for report generation
        RateLimiter::for('reports', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Register custom route model bindings.
     */
    protected function registerModelBindings(): void
    {
        Route::bind('facility', function ($value) {
            return \App\Models\Facility::where('id', $value)
                ->orWhere('code', $value)
                ->firstOrFail();
        });

        Route::bind('asset', function ($value) {
            return \App\Models\Asset::where('id', $value)
                ->orWhere('asset_tag', $value)
                ->firstOrFail();
        });

        Route::bind('work_order', function ($value) {
            return \App\Models\WorkOrder::where('id', $value)
                ->orWhere('work_order_number', $value)
                ->firstOrFail();
        });

        Route::bind('inventory_reference', function ($value) {
            return \App\Models\InventoryReference::where('id', $value)
                ->orWhere('reference_code', $value)
                ->firstOrFail();
        });
    }
}