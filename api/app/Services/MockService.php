<?php

namespace App\Services;

use App\Caches\ApplicationRemoteCredentialCache;
use App\Exceptions\MockException;
use App\Helpers\DateHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class MockService
 * @package App\Services
 */
class MockService
{
    /**
     * @var string $mockApiUrl
     */
    protected string $mockApiUrl = '';

    /**
     * @var int $applicationId
     */
    protected int $applicationId = 0;

    /**
     * @var int $os
     */
    protected int $os = 0;

    /**
     * @var string $receipt
     */
    protected string $receipt = '';

    /**
     * @param string $mockApiUrl
     */
    public function __construct(string $mockApiUrl = '')
    {
        if (empty($mockApiUrl)) {
            $this->setMockApiUrl(getenv('MOCK_API_URL'));
        }
    }
    /**
     * @return string
     */
    public function getMockApiUrl(): string
    {
        return $this->mockApiUrl;
    }

    /**
     * @param string $mockApiUrl
     * @return $this
     */
    public function setMockApiUrl(string $mockApiUrl): MockService
    {
        $this->mockApiUrl = $mockApiUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getApplicationId(): int
    {
        return $this->applicationId;
    }

    /**
     * @param int $applicationId
     * @return $this
     */
    public function setApplicationId(int $applicationId): MockService
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOs(): int
    {
        return $this->os;
    }

    /**
     * @param int $os
     * @return $this
     */
    public function setOs(int $os): MockService
    {
        $this->os = $os;

        return $this;
    }

    /**
     * @return string
     */
    public function getReceipt(): string
    {
        return $this->receipt;
    }

    /**
     * @param string $receipt
     * @return $this
     */
    public function setReceipt(string $receipt): MockService
    {
        $this->receipt = $receipt;

        return $this;
    }

    /**
     * @return array
     * @throws GuzzleException
     * @throws MockException
     */
    public function response(): array
    {
        if (!$this->getOs()) {
            throw new MockException(__('mock.os_is_required'), 1);
        }

        if (!$this->getApplicationId()) {
            throw new MockException(__('mock.application_id_is_required'), 2);
        }

        if (!$this->getReceipt()) {
            throw new MockException(__('mock.receipt_is_required'), 2);
        }

        $url = $this->getMockApiUrl() . $this->getOs();
        $client = new Client();

        $response = $client->post($url, [
            'headers' => [
                'Authorization' => $this->getAuth()
            ],
            'json' => [
                'receipt' => $this->getReceipt()
            ]
        ]);

         $response = $response->getBody()->getContents();
         $response = json_decode($response, true);
         $response = $response['data'];

         if (!empty($response['expired_at'])) {
             $response['expired_at'] = DateHelper::getLocalDateTime(
                 $response['expired_at'],
                 getenv('REMOTE_TIMEZONE'),
                 getenv('LOCAL_TIMEZONE')
             );
         }

        return $response;
    }

    /**
     * @return string
     * @throws MockException
     */
    protected function getAuth(): string
    {
        $credentials = ApplicationRemoteCredentialCache::get($this->getApplicationId(), $this->getOs());
        if (empty($credentials)) {
            throw new MockException(__('mock.credentials_not_found'), 3);
        }

       return 'Basic ' . base64_encode($credentials['username'] . ':' . $credentials['password']);
    }
}
