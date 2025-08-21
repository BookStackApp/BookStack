<?php

declare(strict_types=1);

namespace BookStack\Search\Queries;

use BookStack\Http\HttpRequestService;
use BookStack\Search\Queries\Services\OpenAiVectorQueryService;
use BookStack\Search\Queries\Services\VectorQueryService;

class VectorQueryServiceProvider
{
    public function __construct(
        protected HttpRequestService $http,
    ) {
    }

    public function get(): VectorQueryService
    {
        $service = $this->getServiceName();

        if ($service === 'openai') {
            return new OpenAiVectorQueryService(config('services.openai'), $this->http);
        }

        throw new \Exception("No '{$service}' LLM service found");
    }

    protected static function getServiceName(): string
    {
        return strtolower(config('services.llm'));
    }

    public static function isEnabled(): bool
    {
        return !empty(static::getServiceName());
    }
}
