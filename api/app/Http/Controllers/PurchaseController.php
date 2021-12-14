<?php

namespace App\Http\Controllers;

use App\Caches\SubscriptionCache;
use App\Exceptions\MockException;
use App\Http\Requests\PurchaseRequest;
use App\Models\Subscription;
use App\Services\MockService;
use App\Traits\Response;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class PurchaseController
 * @package App\Http\Controllers
 */
class PurchaseController extends Controller
{
    use Response;

    /**
     * @param PurchaseRequest $request
     * @return JsonResponse
     * @throws MockException
     */
    public function purchase(PurchaseRequest $request): JsonResponse
    {
        $receipt = $request->get('receipt');

        $os = $request->attributes->get('device')->os;
        $applicationId = $request->attributes->get('application_id');

        try {
            $mockService = new MockService();
            $purchase = $mockService->setOs($os)
                ->setApplicationId($applicationId)
                ->setReceipt($receipt)
                ->response();
        } catch (GuzzleException $exception) {
            $purchase['status'] = false;
            Log::warning($exception->getMessage());
        }

        $subscription = $this->handleSubscriptionProcess($request, $purchase);

        $message = ($subscription->status) ?
            __('general.purchase_confirmed') :
            __('general.purchase_not_confirmed');
        return $this->successResponse([
            'status' => $subscription->status > 0,
            'expired_at' => $subscription->expired_at
        ], JsonResponse::HTTP_OK, $message);
    }

    /**
     * @param PurchaseRequest $request
     * @param array $purchase
     * @return Subscription
     */
    protected function handleSubscriptionProcess(PurchaseRequest $request, array $purchase): Subscription
    {
        $receipt = $request->get('receipt');

        $applicationId = $request->attributes->get('application_id');
        $deviceId = $request->attributes->get('device')->id;
        $subscription = Subscription::where('application_id', $applicationId)
            ->where('device_id', $deviceId)
            ->limit(1)
            ->get()
            ->first();
        if (empty($subscription)) {
            $subscription = new Subscription();
        }

        $subscription->device_id = $deviceId;
        $subscription->application_id = $applicationId;
        $subscription->worker_group = $deviceId % intval(getenv('WORKER_COUNT'));
        $subscription->receipt = $receipt;
        $subscription->status = $purchase['status'] ? 1 : 0;
        $subscription->expired_at = !empty($purchase['expired_at']) ? $purchase['expired_at'] : null;
        $subscription->save();

        return $subscription;
    }
}
