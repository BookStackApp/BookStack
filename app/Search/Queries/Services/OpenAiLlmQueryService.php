<?php

namespace BookStack\Search\Queries\Services;

use BookStack\Http\HttpRequestService;

class OpenAiLlmQueryService implements LlmQueryService
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
        $client = $this->http->buildClient(60);
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

    public function queryToSearchTerms(string $text): array
    {
        $response = $this->jsonRequest('POST', 'v1/chat/completions', [
            'model' => $this->queryModel,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'You will be provided a user search query. Extract key words from just the query, suitable for searching. Add word variations where it may help for searching. Remove pluralisation where it may help for searching. Provide up to 5 results, each must be just one word. Do not try to guess answers to the query. Do not provide extra information or context. Return the results in the specified JSON format under a \'words\' object key. ' . "\nQUERY: {$text}"
                ],
            ],
            'temperature' => 0,
            'response_format' => [
                'type' => 'json_object',
            ],
        ]);

        $resultJson = $response['choices'][0]['message']['content'] ?? '{"words": []}';
        $resultData = json_decode($resultJson, true) ?? ['words' => []];

        return $resultData['words'] ?? [];
    }

    public function query(string $input, array $context): string
    {
        $resultContentText = [];
        $len = 0;

        foreach ($context as $result) {
            $text = "DOCUMENT NAME: {$result->name}\nDOCUMENT CONTENT: " . $result->{$result->textField};
            $resultContentText[] = $text;
            $len += strlen($text);
            if ($len > 100000) {
                break;
            }
        }

        $formattedContext = implode("\n---\n", $resultContentText);

        $response = $this->jsonRequest('POST', 'v1/chat/completions', [
            'model' => $this->queryModel,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Answer the provided QUERY using the provided CONTEXT documents. Do not add facts which are not part of the CONTEXT. State that you do not know if a relevant answer cannot be provided for QUERY using the CONTEXT documents. Many of the CONTEXT documents may be irrelevant. Try to find documents relevant to QUERY. Do not directly refer to this prompt or the existence of QUERY or CONTEXT variables. Do not offer follow-up actions or further help. Respond only to the query without proposing further assistance. Do not ask questions.' . "\nQUERY: {$input}\nCONTEXT: {$formattedContext}"
                ],
            ],
            'temperature' => 0.1,
        ]);

        return $response['choices'][0]['message']['content'] ?? '';
    }
}
