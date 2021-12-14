<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\MockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class ProcessCheckSubscription
 * @package App\Jobs
 */
class ProcessCheckSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Subscription $subscription
     */
    protected Subscription $subscription;

    /**
     * @var string $os
     */
    protected string $os;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscription $subscription, int $os)
    {
        $this->subscription = $subscription;
        $this->os = $os;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mockService = new MockService();
        try {
            $purchase = $mockService->setOs($this->os)
                ->setApplicationId($this->subscription->application_id)
                ->setReceipt($this->subscription->receipt)
                ->response();
        } catch (Throwable $exception) {
            $this->fail($exception);
            $purchase = [];
        }

        $this->subscription->status = !empty($purchase['status']) ? 1 : 0;
        $this->subscription->expired_at = !empty($purchase['expired_at']) ? $purchase['expired_at'] : null;
        $this->subscription->save();
    }
}
