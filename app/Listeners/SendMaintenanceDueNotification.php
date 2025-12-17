<?php

namespace App\Listeners;

use App\Events\AssetMaintenanceDue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMaintenanceDueNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AssetMaintenanceDue $event): void
    {
        //
    }
}
