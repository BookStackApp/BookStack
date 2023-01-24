<?php

namespace Tests\Api;

trait TestsApi
{
    protected $apiTokenId = 'apitoken';
    protected $apiTokenSecret = 'password';

    /**
     * Set the API editor role as the current user via the API driver.
     */
    protected function actingAsApiEditor()
    {
        $this->actingAs($this->users->editor(), 'api');

        return $this;
    }

    /**
     * Set the API admin role as the current user via the API driver.
     */
    protected function actingAsApiAdmin()
    {
        $this->actingAs($this->users->admin(), 'api');

        return $this;
    }

    /**
     * Format the given items into a standardised error format.
     */
    protected function errorResponse(string $message, int $code): array
    {
        return ['error' => ['code' => $code, 'message' => $message]];
    }

    /**
     * Get the structure that matches a permission error response.
     */
    protected function permissionErrorResponse(): array
    {
        return $this->errorResponse('You do not have permission to perform the requested action.', 403);
    }

    /**
     * Format the given (field_name => ["messages"]) array
     * into a standard validation response format.
     */
    protected function validationResponse(array $messages): array
    {
        $err = $this->errorResponse('The given data was invalid.', 422);
        $err['error']['validation'] = $messages;

        return $err;
    }

    /**
     * Get an approved API auth header.
     */
    protected function apiAuthHeader(): array
    {
        return [
            'Authorization' => "Token {$this->apiTokenId}:{$this->apiTokenSecret}",
        ];
    }
}
