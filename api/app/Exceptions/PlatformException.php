<?php

namespace App\Exceptions;

use Exception;

/**
 * Class PlatformException
 * @package App\Exceptions
 */
class PlatformException extends Exception
{
    /**
     * @var string $platform
     */
    protected string $platform;

    /**
     * @param $message
     * @param $code
     * @param $platform
     */
    public function __construct($message="", $code=0 , $platform = NULL)
    {
        $this->platform = $platform;

        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }
}
