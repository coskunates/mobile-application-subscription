<?php

namespace App\Http\Controllers;

use App\Caches\ApplicationCache;
use App\Caches\DeviceCache;
use App\Http\Requests\RegisterRequest;
use App\Models\Device;
use App\Traits\JWTTrait;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;

/**
 * Class RegisterController
 * @package App\Http\Controllers
 */
class RegisterController extends Controller
{
    use Response, JWTTrait;

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $applicationId = $request->get('app_id');
        $application = ApplicationCache::get($applicationId);

        $uid = $request->get('uid');
        $device = DeviceCache::getByUid($uid);
        if (empty($device)) {
            $device = $this->addDevice($request);
        }

        return $this->successResponse([
                'token' => $this->encode([
                    'device' => [
                        'id' => $device['id'],
                        'language' => $device['language'],
                        'os' => $device['os'],
                        'uid' => $device['unique_id']
                    ],
                    'application_id' => $application['id'],
                    'iat' => time(),
                    'exp' => time() + 3600
                ])
            ],
            JsonResponse::HTTP_OK,
            __('general.register_success')
        );
    }

    /**
     * @param RegisterRequest $request
     * @return array
     */
    protected function addDevice(RegisterRequest $request): array
    {
        $device = new Device();
        $device->unique_id = $request->get('uid');
        $device->language = $request->get('language');
        $device->os = Device::OPERATING_SYSTEMS[$request->get('os')];
        $device->save();

        DeviceCache::set($device);

        return $device->toArray();
    }
}
