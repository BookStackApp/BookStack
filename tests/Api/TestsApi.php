<?php namespace Tests\Api;

trait TestsApi
{

    protected $apiTokenId = 'apitoken';
    protected $apiTokenSecret = 'password';

    /**
     * Set the API editor role as the current user via the API driver.
     */
    protected function actingAsApiEditor()
    {
        $this->actingAs($this->getEditor(), 'api');
        return $this;
    }

    /**
     * Format the given items into a standardised error format.
     */
    protected function errorResponse(string $message, int $code): array
    {
        return ["error" => ["code" => $code, "message" => $message]];
    }

    /**
     * Get an approved API auth header.
     */
    protected function apiAuthHeader(): array
    {
        return [
            "Authorization" => "Token {$this->apiTokenId}:{$this->apiTokenSecret}"
        ];
    }

}