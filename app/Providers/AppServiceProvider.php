<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Only register Telescope in local environment if it's installed
        if ($this->app->environment('local') && class_exists('Laravel\Telescope\TelescopeServiceProvider')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);

        // Enable strict mode for Eloquent
        Model::shouldBeStrict(!$this->app->isProduction());

        // Register custom validators
        $this->registerValidators();

        // Register macros
        $this->registerMacros();
    }

    /**
     * Register custom validation rules.
     */
    private function registerValidators(): void
    {
        \Validator::extend('color_code', function ($attribute, $value, $parameters) {
            return preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value);
        }, 'The :attribute must be a valid hex color code.');

        \Validator::extend('asset_tag', function ($attribute, $value, $parameters) {
            return preg_match('/^[A-Z]{2,4}-\d{4,6}$/', $value);
        }, 'The :attribute must be in the format: XX-12345.');

        \Validator::extend('work_order_number', function ($attribute, $value, $parameters) {
            return preg_match('/^WO-\d{4}-\d{4}$/', $value);
        }, 'The :attribute must be in the format: WO-YYYY-XXXX.');
    }

    /**
     * Register custom macros.
     */
    private function registerMacros(): void
    {
        \Illuminate\Support\Collection::macro('toTree', function () {
            $items = $this->items;
            $tree = [];
            $childs = [];

            foreach ($items as &$item) {
                $childs[$item['parent_id']][] = &$item;
                $item['children'] = [];
            }

            foreach ($items as &$item) {
                if (isset($childs[$item['id']])) {
                    $item['children'] = $childs[$item['id']];
                }
                if (empty($item['parent_id'])) {
                    $tree[] = &$item;
                }
            }

            return collect($tree);
        });
    }
}