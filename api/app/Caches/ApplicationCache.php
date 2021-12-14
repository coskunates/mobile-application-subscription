<?php

namespace App\Caches;

use App\Models\Application;
use Illuminate\Support\Facades\Redis;

/**
 * Class ApplicationCache
 * @package App\Caches
 */
class ApplicationCache
{
    const CACHE_KEY = 'applications';
    /**
     * @param int $id
     * @return array
     */
    public static function get(int $id): array
    {
        $application = Redis::connection()->client()->hGet(self::CACHE_KEY, $id);
        if (!$application) {
            $application = Application::findOrFail($id)->toArray();
            Redis::connection()->client()->hSet(self::CACHE_KEY, $id, serialize($application));
        } else {
            $application = unserialize($application);
        }

        return $application;
    }
}
