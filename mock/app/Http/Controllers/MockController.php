<?php

namespace App\Http\Controllers;

use App\Http\Requests\MockRequest;
use App\Services\Platforms\Platforms;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class MockController
 * @package App\Http\Controllers
 */
class MockController extends Controller
{
    use Response;

    /**
     * @param MockRequest $request
     * @return JsonResponse
     */
    public function mock(MockRequest $request): JsonResponse
    {
        $receipt = $request->get('receipt');
        if (intval(substr($receipt, -2)) % 6 === 0) {
            return $this->errorResponse(
                __('general.rate_limit'),
                JsonResponse::HTTP_TOO_MANY_REQUESTS
            );
        }

        $os = $request->attributes->get('os');
        $platformService = Platforms::from($os)->service();
        $platformResponse = $platformService->check($receipt);

        $message = ($platformResponse['status']) ?
            __('general.purchase_confirmed') :
            __('general.purchase_not_confirmed');
        return $this->successResponse($platformResponse, JsonResponse::HTTP_OK, $message);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function hook(Request $request): JsonResponse
    {
        Log::info('Incoming request params: ', $request->all());
        $random = rand(0, 10);
        if ($random === 10) {
            return $this->errorResponse(
                __('general.service_unavailable'),
                JsonResponse::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS,
                'warning'
            );
        } else {
            return $this->successResponse([],
                JsonResponse::HTTP_OK,
                __('general.success')
            );
        }
    }
}
