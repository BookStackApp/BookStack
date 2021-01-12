<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Contracts\Redis\Factory as RedisConnection;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;
use UnexpectedValueException;

final class StatusController extends ApiController
{
    /** @var ResponseFactory */
    private $responseFactory;

    /** @var ConnectionInterface */
    private $databaseConnection;

    /** @var RedisConnection */
    private $redisConnection;

    /** @var bool */
    private $isDebug;

    private const STATUS_OK = 'OK';
    private const STATUS_ERROR = 'ERROR';
    private const CACHE_TEST_KEY = 'status-api-test';

    public function __construct(
        ResponseFactory $responseFactory,
        ConnectionInterface $databaseConnection,
        RedisConnection $redisConnection
    ) {
        $this->responseFactory = $responseFactory;
        $this->databaseConnection = $databaseConnection;
        $this->redisConnection = $redisConnection;

        $this->isDebug = config('app.debug');
    }

    public function simpleStatus(): JsonResponse
    {
        return $this->responseFactory->json([
            'status' => self::STATUS_OK,
        ]);
    }

    public function status(): JsonResponse
    {
        $response = ['status' => self::STATUS_OK];

        if (config('api.status.cache')) {
            $this->testCache($response);
        }
        if (config('api.status.database')) {
            $this->testDatabase($response);
        }
        if (config('api.status.redis')) {
            $this->testRedis($response);
        }

        return $this->responseFactory->json(
            $response,
            $response['status'] === self::STATUS_OK
                ? Response::HTTP_OK
                : Response::HTTP_SERVICE_UNAVAILABLE
        );
    }

    private function testCache(array &$response): void
    {
        $response['components']['cache'] = self::STATUS_OK;

        $value = Str::random(50);
        Cache::put(self::CACHE_TEST_KEY, 30, $value);

        $returnedValue = Cache::pull(self::CACHE_TEST_KEY);
        if ($returnedValue !== $value) {
            $this->processError($response, 'cache', new UnexpectedValueException(
                $returnedValue . ' does not match expected ' . $value
            ));

            return;
        }

        if (Cache::get(self::CACHE_TEST_KEY) !== null) {
            $this->processError($response, 'cache', new UnexpectedValueException(
                'Cache did not forget test value'
            ));
        }
    }

    private function testDatabase(array &$response): void
    {
        try {
            $this->databaseConnection->transaction(static function (): void {
            });

            $response['components']['database'] = self::STATUS_OK;
        } catch (Throwable $e) {
            $this->processError($response, 'database', $e);
        }
    }

    private function testRedis(array &$response): void
    {
        try {
            $this->redisConnection->connection()->ping();

            $response['components']['redis'] = self::STATUS_OK;
        } catch (Throwable $e) {
            $this->processError($response, 'redis', $e);
        }
    }

    private function processError(array &$response, string $type, Throwable $e): void
    {
        $response['status'] = self::STATUS_ERROR;
        $response['components'][$type] = self::STATUS_ERROR;

        if (!$this->isDebug) {
            return;
        }

        if (!isset($response['errors'])) {
            $response['errors'] = [];
        }

        $response['errors'][$type] = $e->getMessage();
    }
}
