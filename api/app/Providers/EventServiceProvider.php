<?php

namespace App\Providers;

use App\Events\CanceledSubscription;
use App\Events\RenewedSubscription;
use App\Events\StartedSubscription;
use App\Listeners\ReportListener;
use App\Listeners\SubscriptionUpdateListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CanceledSubscription::class => [
            SubscriptionUpdateListener::class,
            ReportListener::class
        ],
        RenewedSubscription::class => [
            SubscriptionUpdateListener::class,
            ReportListener::class
        ],
        StartedSubscription::class => [
            SubscriptionUpdateListener::class,
            ReportListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
