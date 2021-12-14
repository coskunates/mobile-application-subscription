<?php

namespace App\Listeners;

use App\Jobs\ProcessReport;

/**
 * Class ReportListener
 * @package App\Listeners
 */
class ReportListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(object $event)
    {
        ProcessReport::dispatch($event)->onQueue("reports");
    }
}
