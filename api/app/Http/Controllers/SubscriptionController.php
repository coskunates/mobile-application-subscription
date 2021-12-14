<?php

namespace App\Http\Controllers;

use App\Caches\SubscriptionCache;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SubscriptionController
 * @package App\Http\Controllers
 */
class SubscriptionController extends Controller
{
    use Response;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        $deviceId = $request->attributes->get('device')->id;
        $applicationId = $request->attributes->get('application_id');
        $subscription = SubscriptionCache::getByDeviceApplicationId($deviceId, $applicationId);

        return $this->successResponse([
            'status' =>$subscription['status'] > 0,
            'expired_at' => $subscription['expired_at']
        ], JsonResponse::HTTP_OK, __('general.subscription'));
    }
}
