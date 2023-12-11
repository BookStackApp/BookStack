<?php

namespace BookStack\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;

class HttpRequestService
{
    protected ?HandlerStack $handler = null;

    /**
     * Build a new http client for sending requests on.
     */
    public function buildClient(int $timeout, array $options = []): ClientInterface
    {
        $defaultOptions = [
            'timeout' => $timeout,
            'handler' => $this->handler,
        ];

        return new Client(array_merge($options, $defaultOptions));
    }

    /**
     * Create a new JSON http request for use with a client.
     */
    public function jsonRequest(string $method, string $uri, array $data): GuzzleRequest
    {
        $headers = ['Content-Type' => 'application/json'];
        return new GuzzleRequest($method, $uri, $headers, json_encode($data));
    }

    /**
     * Mock any http clients built from this service, and response with the given responses.
     * Returns history which can then be queried.
     * @link https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     */
    public function mockClient(array $responses = [], bool $pad = true): HttpClientHistory
    {
        // By default, we pad out the responses with 10 successful values so that requests will be
        // properly recorded for inspection. Otherwise, we can't later check if we're received
        // too many requests.
        if ($pad) {
            $response = new Response(200, [], 'success');
            $responses = array_merge($responses, array_fill(0, 10, $response));
        }

        $container = [];
        $history = Middleware::history($container);
        $mock = new MockHandler($responses);
        $this->handler = HandlerStack::create($mock);
        $this->handler->push($history, 'history');

        return new HttpClientHistory($container);
    }

    /**
     * Clear mocking that has been set up for clients.
     */
    public function clearMocking(): void
    {
        $this->handler = null;
    }
}
