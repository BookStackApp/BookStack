<?php

namespace BookStack\Search\Vectors\Services;

use BookStack\Http\HttpRequestService;

class OpenAiVectorQueryService implements VectorQueryService
{
    protected string $key;
    protected string $endpoint;
    protected string $embeddingModel;
    protected string $queryModel;

    public function __construct(
        protected array $options,
        protected HttpRequestService $http,
    ) {
        // TODO - Some kind of validation of options
        $this->key = $this->options['key'] ?? '';
        $this->endpoint = $this->options['endpoint'] ?? '';
        $this->embeddingModel = $this->options['embedding_model'] ?? '';
        $this->queryModel = $this->options['query_model'] ?? '';
    }

    protected function jsonRequest(string $method, string $uri, array $data): array
    {
        $fullUrl = rtrim($this->endpoint, '/') . '/' . ltrim($uri, '/');
        $client = $this->http->buildClient(30);
        $request = $this->http->jsonRequest($method, $fullUrl, $data)
            ->withHeader('Authorization', 'Bearer ' . $this->key);

        $response = $client->sendRequest($request);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function generateEmbeddings(string $text): array
    {
        $response = $this->jsonRequest('POST', 'v1/embeddings', [
            'input' => $text,
            'model' => $this->embeddingModel,
        ]);

        return $response['data'][0]['embedding'];
    }

    public function query(string $input, array $context): string
    {
        $formattedContext = implode("\n", $context);

        $response = $this->jsonRequest('POST', 'v1/chat/completions', [
            'model' => $this->queryModel,
            'messages' => [
                [
                    'role' => 'developer',
                    'content' => 'You are a helpful assistant providing search query responses. Be specific, factual and to-the-point in response. Don\'t try to converse or continue the conversation.'
                ],
                [
                    'role' => 'user',
                    'content' => "Provide a response to the below given QUERY using the below given CONTEXT. The CONTEXT is split into parts via lines. Ignore any nonsensical lines of CONTEXT.\nQUERY: {$input}\n\nCONTEXT: {$formattedContext}",
                ]
            ],
        ]);

        return $response['choices'][0]['message']['content'] ?? '';
    }
}
