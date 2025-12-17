<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\WorkOrderCreated::class => [
            \App\Listeners\SendWorkOrderNotification::class,
            \App\Listeners\LogWorkOrderCreation::class,
        ],
        \App\Events\WorkOrderAssigned::class => [
            \App\Listeners\SendAssignmentNotification::class,
            \App\Listeners\LogWorkOrderAssignment::class,
        ],
        \App\Events\WorkOrderCompleted::class => [
            \App\Listeners\SendCompletionNotification::class,
            \App\Listeners\UpdateAssetMaintenance::class,
            \App\Listeners\LogWorkOrderCompletion::class,
        ],
        \App\Events\InventoryLowStock::class => [
            \App\Listeners\SendLowStockNotification::class,
            \App\Listeners\CreateRequisition::class,
        ],
        \App\Events\AssetMaintenanceDue::class => [
            \App\Listeners\SendMaintenanceDueNotification::class,
            \App\Listeners\CreatePreventiveWorkOrder::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}