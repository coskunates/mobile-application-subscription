<?php

namespace App\Caches;

use App\Models\ApplicationRemoteCredential;
use Illuminate\Support\Facades\Redis;

/**
 * Class ApplicationRemoteCredentialCache
 * @package App\Caches
 */
class ApplicationRemoteCredentialCache
{
    const CACHE_KEY = 'credentials';

    /**
     * @param int $applicationId
     * @param int $os
     * @return array|null
     */
    public static function get(int $applicationId, int $os): ?array
    {
        $field = $applicationId . '_' . $os;
        $credentials = Redis::connection()->client()->hGet(self::CACHE_KEY, $field);
        if (!$credentials) {
            $credentials = ApplicationRemoteCredential::where('application_id', $applicationId)
                ->where('os', $os)
                ->limit(1)
                ->get()
                ->first();
            if (!empty($credentials)) {
                $credentials = $credentials->toArray();
                Redis::connection()->client()->hSet(self::CACHE_KEY, $field, serialize($credentials));
            }
        } else {
            $credentials = unserialize($credentials);
        }

        return !empty($credentials) ? $credentials : null;
    }
}
