<?php

namespace App\Services\Platforms;

/**
 * Class PlatformInterface
 * @package App\Services\Platforms
 */
interface PlatformInterface
{
    /**
     * @param string $receipt
     * @return array
     */
    public function check(string $receipt): array;
}
