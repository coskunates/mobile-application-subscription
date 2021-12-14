<?php

namespace App\Caches;

use App\Models\Device;
use App\Models\Subscription;
use Illuminate\Support\Facades\Redis;

/**
 * Class SubscriptionCache
 * @package App\Caches
 */
class SubscriptionCache
{
    const CACHE_KEY = 'subscriptions';
    const DEVICE_APPLICATION_SUBSCRIPTION_CACHE_KEY = 'subscription_das';

    /**
     * @param int $subscriptionId
     * @return array
     */
    public static function get(int $subscriptionId): array
    {
        $subscription = Redis::connection()->client()->hGet(self::CACHE_KEY, $subscriptionId);
        if (!$subscription) {
            $subscription = Subscription::findOne($subscriptionId);
            if (!empty($subscription)) {
                self::set($subscription);
                $subscription = $subscription->toArray();
            }
        } else {
            $subscription = unserialize($subscription);
        }

        return $subscription;
    }

    /**
     * @param int $deviceId
     * @param int $applicationId
     * @return array|null
     */
    public static function getByDeviceApplicationId(int $deviceId, int $applicationId): ?array
    {
        $field = $deviceId . '_' . $applicationId;
        $subscriptionId = Redis::connection()->client()
            ->hGet(self::DEVICE_APPLICATION_SUBSCRIPTION_CACHE_KEY, $field);
        if (!$subscriptionId) {
            $subscription = Subscription::where('application_id', $applicationId)
                ->where('device_id', $deviceId)
                ->limit(1)
                ->get()
                ->first();
            if (!empty($subscription)) {
                self::set($subscription);
                $subscription = $subscription->toArray();
            }
        } else {
            $subscription = self::get($subscriptionId);
        }

        return !empty($subscription) ? $subscription : null;
    }

    /**
     * @param Subscription $subscription
     * @return void
     */
    public static function set(Subscription $subscription): void
    {
        Redis::connection()->client()
            ->hSet(self::CACHE_KEY, $subscription->id, serialize($subscription->toArray()));

        $field = $subscription->device_id . '_' . $subscription->application_id;
        Redis::connection()->client()
            ->hSet(self::DEVICE_APPLICATION_SUBSCRIPTION_CACHE_KEY, $field, $subscription->id);
    }
}
