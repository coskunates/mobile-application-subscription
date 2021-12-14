<?php

namespace App\Models;

use App\Caches\SubscriptionCache;
use App\Events\CanceledSubscription;
use App\Events\RenewedSubscription;
use App\Events\StartedSubscription;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $receipt
 * @property int $status
 * @property string|null $expired_at
 * @property int $application_id
 * @property int $device_id
 * @property int $id
 * @property int $worker_group
 */
class Subscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired_at' => 'datetime'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($subscription) {
            if (!is_null($subscription->expired_at)) {
                StartedSubscription::dispatch($subscription);
            }

            SubscriptionCache::set($subscription);
        });

        static::updated(function ($subscription) {
            if (is_null($subscription->expired_at)) {
                CanceledSubscription::dispatch($subscription);
            } else {
                RenewedSubscription::dispatch($subscription);
            }
            SubscriptionCache::set($subscription);
        });
    }
}
