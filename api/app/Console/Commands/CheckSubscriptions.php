<?php

namespace App\Console\Commands;

use App\Caches\DeviceCache;
use App\Jobs\ProcessCheckSubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Console\Command;

/**
 * Class CheckSubscriptions
 * @package App\Console\Commands
 */
class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:check_subscriptions {mod}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks Subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Subscription::select([
             'id', 'device_id', 'application_id', 'receipt'
            ])->where('expired_at',
                '<',
                Carbon::now(new DateTimeZone(getenv('LOCAL_TIMEZONE')))
                    ->format('Y-m-d H:i:s')
            )->where('worker_group', $this->argument('mod'))
            ->chunk(1000, function ($subscriptions) {
                $deviceIds = array_column($subscriptions->toArray(), 'device_id');
                $devices = DeviceCache::multiGet($deviceIds);
                /** @var Subscription $subscription */
                foreach ($subscriptions as $subscription) {
                    ProcessCheckSubscription::dispatch($subscription, $devices[$subscription->device_id]['os'])
                        ->onQueue('check_subscription');
                }
            });

        return Command::SUCCESS;
    }
}
