<?php

namespace App\Jobs;

use App\Caches\ApplicationCache;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SubscriptionCallback
 * @package App\Jobs
 */
class SubscriptionCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var object $event
     */
    protected object $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $application = ApplicationCache::get($this->event->getSubscription()->application_id);

        $client = new Client();
        $client->post($application['hook_url'], [
            'json' => [
                'application_id' => $this->event->getSubscription()->application_id,
                'device_id' => $this->event->getSubscription()->device_id,
                'event' => $this->event->getName()
            ]
        ]);
    }
}
