<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTimeZone;

/**
 * Class DateHelper
 * @package App\Helpers
 */
class DateHelper
{
    /**
     * @param string $dateTime
     * @param string $fromTimeZone
     * @param string $toTimeZone
     * @return string
     */
    public static function getLocalDateTime(string $dateTime, string $fromTimeZone, string $toTimeZone): string
    {
        return Carbon::parse($dateTime, new DateTimeZone($fromTimeZone))
            ->timezone(new DateTimeZone($toTimeZone))
            ->format('Y-m-d H:i:s');
    }
}
