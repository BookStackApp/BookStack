<?php

namespace BookStack\Search\Vectors\Services;

use BookStack\Http\HttpRequestService;

class OpenAiVectorQueryService implements VectorQueryService
{
    public function __construct(
        protected string $endpoint,
        protected string $key,
        protected HttpRequestService $http,
    ) {
    }

    protected function jsonRequest(string $method, string $uri, array $data): array
    {
        $fullUrl = rtrim($this->endpoint, '/') . '/' . ltrim($uri, '/');
        $client = $this->http->buildClient(10);
        $request = $this->http->jsonRequest($method, $fullUrl, $data)
            ->withHeader('Authorization', 'Bearer ' . $this->key);

        $response = $client->sendRequest($request);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function generateEmbeddings(string $text): array
    {
        $response = $this->jsonRequest('POST', 'v1/embeddings', [
            'input' => $text,
            'model' => 'text-embedding-3-small',
        ]);

        return $response['data'][0]['embedding'];
    }

    public function query(string $input, array $context): string
    {
        $formattedContext = implode("\n", $context);

        $response = $this->jsonRequest('POST', 'v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'developer',
                    'content' => 'You are a helpful assistant providing search query responses. Be specific, factual and to-the-point in response.'
                ],
                [
                    'role' => 'user',
                    'content' => "Provide a response to the below given QUERY using the below given CONTEXT\nQUERY: {$input}\n\nCONTEXT: {$formattedContext}",
                ]
            ],
        ]);

        return $response['choices'][0]['message']['content'] ?? '';
    }
}
