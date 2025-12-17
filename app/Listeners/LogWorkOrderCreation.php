<?php

namespace App\Listeners;

use App\Events\WorkOrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogWorkOrderCreation
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
    public function handle(WorkOrderCreated $event): void
    {
        //
    }
}
