<?php

namespace Tests\Api;

use BookStack\Auth\User;

trait TestsApi
{
    protected string $apiTokenId = 'apitoken';
    protected string $apiTokenSecret = 'password';

    /**
     * Set the given user as the current logged-in user via the API driver.
     * This does not ensure API access. The user may still lack required role permissions.
     */
    protected function actingAsForApi(User $user): static
    {
        parent::actingAs($user, 'api');

        return $this;
    }

    /**
     * Set the API editor role as the current user via the API driver.
     */
    protected function actingAsApiEditor(): static
    {
        $this->actingAs($this->users->editor(), 'api');

        return $this;
    }

    /**
     * Set the API admin role as the current user via the API driver.
     */
    protected function actingAsApiAdmin(): static
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
