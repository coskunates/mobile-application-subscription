<?php

namespace App\Http\Middleware;

use App\Models\ApplicationRemoteCredential;
use App\Traits\Response;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class BasicAuth
 * @package App\Http\Middleware
 */
class BasicAuth
{
    use Response;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
           $credentials = ApplicationRemoteCredential::where('username', $_SERVER['PHP_AUTH_USER'])
                ->where('password', $_SERVER['PHP_AUTH_PW'])
                ->limit(1)
                ->get()
                ->first();
           if (!empty($credentials)) {
               $request->attributes->add([
                   'application_id' => $credentials['application_id'],
                   'os' => $credentials['os']
               ]);

               return $next($request);
           }
        }

        return $this->errorResponse(
            __('auth.invalid_username_password'),
            JsonResponse::HTTP_UNAUTHORIZED,
            'warning'
        );
    }
}
