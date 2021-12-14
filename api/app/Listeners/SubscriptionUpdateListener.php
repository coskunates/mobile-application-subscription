<?php

namespace App\Listeners;

use App\Jobs\SubscriptionCallback;

/**
 * Class SubscriptionUpdateListener
 * @package App\Listeners
 */
class SubscriptionUpdateListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(object $event)
    {
        SubscriptionCallback::dispatch($event)->onQueue("events");
    }
}
