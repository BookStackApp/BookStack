<?php

namespace BookStack\Access\Oidc;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Extended OAuth2Provider for using with OIDC.
 * Credit to the https://github.com/steverhoades/oauth2-openid-connect-client
 * project for the idea of extending a League\OAuth2 client for this use-case.
 */
class OidcOAuthProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected string $authorizationEndpoint;
    protected string $tokenEndpoint;

    /**
     * Scopes to use for the OIDC authorization call.
     */
    protected array $scopes = ['openid', 'profile', 'email'];

    /**
     * Returns the base URL for authorizing a client.
     */
    public function getBaseAuthorizationUrl(): string
    {
        return $this->authorizationEndpoint;
    }

    /**
     * Returns the base URL for requesting an access token.
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->tokenEndpoint;
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return '';
    }

    /**
     * Add another scope to this provider upon the default.
     */
    public function addScope(string $scope): void
    {
        $this->scopes[] = $scope;
        $this->scopes = array_unique($this->scopes);
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     */
    protected function getDefaultScopes(): array
    {
        return $this->scopes;
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data     Parsed response data
     *
     * @throws IdentityProviderException
     *
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400 || isset($data['error'])) {
            throw new IdentityProviderException(
                $data['error'] ?? $response->getReasonPhrase(),
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GenericResourceOwner($response, '');
    }

    /**
     * Creates an access token from a response.
     *
     * The grant that was used to fetch the response can be used to provide
     * additional context.
     *
     * @param array         $response
     * @param AbstractGrant $grant
     *
     * @return OidcAccessToken
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        return new OidcAccessToken($response);
    }
}
