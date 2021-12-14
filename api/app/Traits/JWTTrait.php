<?php

namespace App\Traits;

use Firebase\JWT\JWT;

/**
 * Trait JWTTrait
 * @package App\Traits
 */
trait JWTTrait
{
    /**
     * @param array $payload
     * @return string
     */
    public function encode(array $payload): string
    {
        return JWT::encode($payload, getenv('JWT_SECRET_KEY'));
    }

    /**
     * @param string $token
     * @return array
     */
    public function decode(string $token): array
    {
        $decodedToken = JWT::decode($token, getenv('JWT_SECRET_KEY'), ['HS256']);

        return (array) $decodedToken;
    }
}
