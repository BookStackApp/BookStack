<?php

namespace BookStack\Api;

use BookStack\Exceptions\ApiAuthException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;

class ApiTokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     */
    protected $request;

    /**
     * The last auth exception thrown in this request.
     *
     * @var ApiAuthException
     */
    protected $lastAuthException;

    /**
     * ApiTokenGuard constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        // Return the user if we've already retrieved them.
        // Effectively a request-instance cache for this method.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        try {
            $user = $this->getAuthorisedUserFromRequest();
        } catch (ApiAuthException $exception) {
            $this->lastAuthException = $exception;
        }

        $this->user = $user;

        return $user;
    }

    /**
     * Determine if current user is authenticated. If not, throw an exception.
     *
     * @throws ApiAuthException
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function authenticate()
    {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        if ($this->lastAuthException) {
            throw $this->lastAuthException;
        }

        throw new ApiAuthException('Unauthorized');
    }

    /**
     * Check the API token in the request and fetch a valid authorised user.
     *
     * @throws ApiAuthException
     */
    protected function getAuthorisedUserFromRequest(): Authenticatable
    {
        $authToken = trim($this->request->headers->get('Authorization', ''));
        $this->validateTokenHeaderValue($authToken);

        [$id, $secret] = explode(':', str_replace('Token ', '', $authToken));
        $token = ApiToken::query()
            ->where('token_id', '=', $id)
            ->with(['user'])->first();

        $this->validateToken($token, $secret);

        return $token->user;
    }

    /**
     * Validate the format of the token header value string.
     *
     * @throws ApiAuthException
     */
    protected function validateTokenHeaderValue(string $authToken): void
    {
        if (empty($authToken)) {
            throw new ApiAuthException(trans('errors.api_no_authorization_found'));
        }

        if (strpos($authToken, ':') === false || strpos($authToken, 'Token ') !== 0) {
            throw new ApiAuthException(trans('errors.api_bad_authorization_format'));
        }
    }

    /**
     * Validate the given secret against the given token and ensure the token
     * currently has access to the instance API.
     *
     * @throws ApiAuthException
     */
    protected function validateToken(?ApiToken $token, string $secret): void
    {
        if ($token === null) {
            throw new ApiAuthException(trans('errors.api_user_token_not_found'));
        }

        if (!Hash::check($secret, $token->secret)) {
            throw new ApiAuthException(trans('errors.api_incorrect_token_secret'));
        }

        $now = Carbon::now();
        if ($token->expires_at <= $now) {
            throw new ApiAuthException(trans('errors.api_user_token_expired'), 403);
        }

        if (!$token->user->can('access-api')) {
            throw new ApiAuthException(trans('errors.api_user_no_api_permission'), 403);
        }
    }

    /**
     * @inheritDoc
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['id']) || empty($credentials['secret'])) {
            return false;
        }

        $token = ApiToken::query()
            ->where('token_id', '=', $credentials['id'])
            ->with(['user'])->first();

        if ($token === null) {
            return false;
        }

        return Hash::check($credentials['secret'], $token->secret);
    }

    /**
     * "Log out" the currently authenticated user.
     */
    public function logout()
    {
        $this->user = null;
    }
}
