<?php

namespace App\Caches;

use App\Helpers\ArrayHelper;
use App\Models\Device;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

/**
 * Class DeviceCache
 * @package App\Caches
 */
class DeviceCache
{
    const CACHE_KEY = 'devices';
    const DEVICE_UNIQUE_ID_CACHE_KEY = 'device_uids';

    /**
     * @param int $id
     * @return array
     */
    public static function get(int $id): array
    {
        $device = Redis::connection()->client()->hGet(self::CACHE_KEY, $id);
        if (!$device) {
            $device = Device::findOne($id);
            if (!empty($device)) {
                self::set($device);
                $device = $device->toArray();
            }
        } else {
            $device = unserialize($device);
        }

        return $device;
    }

    /**
     * @param array $ids
     * @return array
     */
    public static function multiGet(array $ids): array
    {
        $devices = Redis::connection()->client()->hMGet(self::CACHE_KEY, $ids);
        foreach ($devices as &$device) {
            $device = unserialize($device);
        }

        $deviceIds = array_column($devices, 'id');
        $missingIds = array_diff($ids, $deviceIds);
        if (!empty($missingIds)) {
            $missingDevices = Device::whereIn('id', $missingIds)->limit(count($missingIds))->get();
            if (!empty($missingDevices)) {
                $devices = array_merge($devices, $missingDevices->toArray());
                self::multiSet($missingDevices);
            }
        }

        return ArrayHelper::valueToKey($devices, 'id');
    }

    /**
     * @param string $uid
     * @return array|null
     */
    public static function getByUid(string $uid): ?array
    {
        $deviceId = Redis::connection()->client()->hGet(self::DEVICE_UNIQUE_ID_CACHE_KEY, $uid);
        if (!$deviceId) {
            $device = Device::where('unique_id', $uid)->limit(1)->get()->first();
            if (!empty($device)) {
                self::set($device);
                $device = $device->toArray();
            }
        } else {
            $device = self::get($deviceId);
        }

        return !empty($device) ? $device : null;
    }

    /**
     * @param Device $device
     * @return void
     */
    public static function set(Device $device): void
    {
        Redis::connection()->client()->hSet(self::CACHE_KEY, $device->id, serialize($device->toArray()));
        Redis::connection()->client()->hSet(self::DEVICE_UNIQUE_ID_CACHE_KEY, $device->unique_id, $device->id);
    }

    /**
     * @param Collection $devices
     * @return void
     */
    public static function multiSet(Collection $devices): void
    {
        $deviceCacheData = [];
        $deviceUniqueIdCacheData = [];
        /** @var Device $device */
        foreach ($devices as $device) {
            $deviceCacheData[$device->id] = serialize($device->toArray());
            $deviceUniqueIdCacheData[$device->unique_id] = $device->id;
        }

        Redis::connection()->client()->hMSet(self::CACHE_KEY, $deviceCacheData);
        Redis::connection()->client()->hMSet(self::DEVICE_UNIQUE_ID_CACHE_KEY, $deviceUniqueIdCacheData);
    }
}
