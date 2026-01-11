<?php

declare(strict_types=1);

namespace BookStack\Search\Queries;

use BookStack\Http\HttpRequestService;
use BookStack\Search\Queries\Services\OpenAiLlmQueryService;
use BookStack\Search\Queries\Services\LlmQueryService;

class LlmQueryServiceProvider
{
    public function __construct(
        protected HttpRequestService $http,
    ) {
    }

    public function get(): LlmQueryService
    {
        $service = $this->getServiceName();

        if ($service === 'openai') {
            return new OpenAiLlmQueryService(config('services.openai'), $this->http);
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
