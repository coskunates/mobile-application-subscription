<?php

namespace App\Services\Platforms;

/**
 * Enum BasicAuth
 * @package App\Services\Platforms
 */
enum Platforms: int
{
    case GOOGLE = 1;
    case IOS = 2;

    /**
     * @return PlatformInterface
     */
    public function service(): PlatformInterface
    {
        return match($this)
        {
            Platforms::GOOGLE => new GooglePlayStore(),
            Platforms::IOS => new IOSAppStore()
        };
    }
}
