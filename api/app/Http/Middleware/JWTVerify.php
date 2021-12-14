<?php

namespace App\Http\Middleware;

use App\Traits\JWTTrait;
use App\Traits\Response;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class JWTVerify
 * @package App\Http\Middleware
 */
class JWTVerify
{
    use Response, JWTTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $decodedToken = $this->decode($request->bearerToken());

        $request->attributes->add($decodedToken);

        return $next($request);
    }
}
