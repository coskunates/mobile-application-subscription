<?php

namespace App\Exceptions;

use App\Traits\Response;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use Response;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpResponseException) {
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = JsonResponse::HTTP_METHOD_NOT_ALLOWED;
            $e = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
        } elseif ($e instanceof NotFoundHttpException) {
            $status = JsonResponse::HTTP_NOT_FOUND;
            $e = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
        } elseif ($e instanceof AuthorizationException) {
            $status = JsonResponse::HTTP_FORBIDDEN;
            $e = new AuthorizationException('HTTP_FORBIDDEN', $status);
        } elseif ($e instanceof ValidationException) {
            $status = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;
            $e = new ValidationException($e->validator, $status, $e);
        } elseif ($e instanceof SignatureInvalidException) {
            $status = JsonResponse::HTTP_UNAUTHORIZED;
            $e = new SignatureInvalidException(__('auth.invalid_token'));
        }  elseif ($e instanceof MockException) {
            $status = JsonResponse::HTTP_BAD_REQUEST;
        } else {
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        $message = $e->getMessage();
        if (property_exists($e, 'validator')) {
            $message = $e->validator->errors()->first();
        }

        return $this->errorResponse($message, $status);
    }
}
