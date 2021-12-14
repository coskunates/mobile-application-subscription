<?php

namespace App\Services\Platforms;

use DateTimeZone;
use Illuminate\Support\Carbon;

/**
 * Class GooglePlayStore
 * @package App\Services\Platforms
 */
class GooglePlayStore implements PlatformInterface
{
    const NAME = 'Google Play Store';

    /**
     * @param string $receipt
     * @return array
     */
    public function check(string $receipt): array
    {
        $result['status'] = false;

        $lastNumber = intval(substr($receipt, -1));
        if ($lastNumber % 2 === 1) {
            $result['status'] = true;
            $result['expired_at'] = Carbon::now('UTC')
                ->timezone(new DateTimeZone(getenv('REMOTE_TIMEZONE')))
                ->addHours(rand(1,10))
                ->format('Y-m-d H:i:s');
        }

        return $result;
    }
}
