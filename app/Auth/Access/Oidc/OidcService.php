<?php

namespace BookStack\Auth\Access\Oidc;

use function auth;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\OpenIdConnectException;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Exceptions\UserRegistrationException;
use function config;
use Exception;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClient;
use function trans;
use function url;

/**
 * Class OpenIdConnectService
 * Handles any app-specific OIDC tasks.
 */
class OidcService
{
    protected $registrationService;
    protected $loginService;
    protected $httpClient;

    /**
     * OpenIdService constructor.
     */
    public function __construct(RegistrationService $registrationService, LoginService $loginService, HttpClient $httpClient)
    {
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
        $this->httpClient = $httpClient;
    }

    /**
     * Initiate an authorization flow.
     *
     * @return array{url: string, state: string}
     */
    public function login(): array
    {
        $settings = $this->getProviderSettings();
        $provider = $this->getProvider($settings);

        return [
            'url'   => $provider->getAuthorizationUrl(),
            'state' => $provider->getState(),
        ];
    }

    /**
     * Process the Authorization response from the authorization server and
     * return the matching, or new if registration active, user matched to
     * the authorization server.
     * Returns null if not authenticated.
     *
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function processAuthorizeResponse(?string $authorizationCode): ?User
    {
        $settings = $this->getProviderSettings();
        $provider = $this->getProvider($settings);

        // Try to exchange authorization code for access token
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $authorizationCode,
        ]);

        return $this->processAccessTokenCallback($accessToken, $settings);
    }

    /**
     * @throws OidcIssuerDiscoveryException
     * @throws ClientExceptionInterface
     */
    protected function getProviderSettings(): OidcProviderSettings
    {
        $config = $this->config();
        $settings = new OidcProviderSettings([
            'issuer'                => $config['issuer'],
            'clientId'              => $config['client_id'],
            'clientSecret'          => $config['client_secret'],
            'redirectUri'           => url('/oidc/callback'),
            'authorizationEndpoint' => $config['authorization_endpoint'],
            'tokenEndpoint'         => $config['token_endpoint'],
        ]);

        // Use keys if configured
        if (!empty($config['jwt_public_key'])) {
            $settings->keys = [$config['jwt_public_key']];
        }

        // Run discovery
        if ($config['discover'] ?? false) {
            $settings->discoverFromIssuer($this->httpClient, Cache::store(null), 15);
        }

        $settings->validate();

        return $settings;
    }

    /**
     * Load the underlying OpenID Connect Provider.
     */
    protected function getProvider(OidcProviderSettings $settings): OidcOAuthProvider
    {
        return new OidcOAuthProvider($settings->arrayForProvider(), [
            'httpClient'     => $this->httpClient,
            'optionProvider' => new HttpBasicAuthOptionProvider(),
        ]);
    }

    /**
     * Calculate the display name.
     */
    protected function getUserDisplayName(OidcIdToken $token, string $defaultValue): string
    {
        $displayNameAttr = $this->config()['display_name_claims'];

        $displayName = [];
        foreach ($displayNameAttr as $dnAttr) {
            $dnComponent = $token->getClaim($dnAttr) ?? '';
            if ($dnComponent !== '') {
                $displayName[] = $dnComponent;
            }
        }

        if (count($displayName) == 0) {
            $displayName[] = $defaultValue;
        }

        return implode(' ', $displayName);
    }

    /**
     * Extract the details of a user from an ID token.
     *
     * @return array{name: string, email: string, external_id: string}
     */
    protected function getUserDetails(OidcIdToken $token): array
    {
        $id = $token->getClaim('sub');

        return [
            'external_id' => $id,
            'email'       => $token->getClaim('email'),
            'name'        => $this->getUserDisplayName($token, $id),
        ];
    }

    /**
     * Processes a received access token for a user. Login the user when
     * they exist, optionally registering them automatically.
     *
     * @throws OpenIdConnectException
     * @throws JsonDebugException
     * @throws UserRegistrationException
     * @throws StoppedAuthenticationException
     */
    protected function processAccessTokenCallback(OidcAccessToken $accessToken, OidcProviderSettings $settings): User
    {
        $idTokenText = $accessToken->getIdToken();
        $idToken = new OidcIdToken(
            $idTokenText,
            $settings->issuer,
            $settings->keys,
        );

        if ($this->config()['dump_user_details']) {
            throw new JsonDebugException($idToken->getAllClaims());
        }

        try {
            $idToken->validate($settings->clientId);
        } catch (OidcInvalidTokenException $exception) {
            throw new OpenIdConnectException("ID token validate failed with error: {$exception->getMessage()}");
        }

        $userDetails = $this->getUserDetails($idToken);
        $isLoggedIn = auth()->check();

        if (empty($userDetails['email'])) {
            throw new OpenIdConnectException(trans('errors.oidc_no_email_address'));
        }

        if ($isLoggedIn) {
            throw new OpenIdConnectException(trans('errors.oidc_already_logged_in'), '/login');
        }

        $user = $this->registrationService->findOrRegister(
            $userDetails['name'],
            $userDetails['email'],
            $userDetails['external_id']
        );

        if ($user === null) {
            throw new OpenIdConnectException(trans('errors.oidc_user_not_registered', ['name' => $userDetails['external_id']]), '/login');
        }

        $this->loginService->login($user, 'oidc');

        return $user;
    }

    /**
     * Get the OIDC config from the application.
     */
    protected function config(): array
    {
        return config('oidc');
    }
}
