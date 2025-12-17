<?php

namespace App\Listeners;

use App\Events\WorkOrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogWorkOrderCompletion
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
    public function handle(WorkOrderCompleted $event): void
    {
        //
    }
}
