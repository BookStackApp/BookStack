<?php

namespace BookStack\Auth\Access\Oidc;

use function auth;
use BookStack\Auth\Access\GroupSyncService;
use BookStack\Auth\Access\LoginService;
use BookStack\Auth\Access\RegistrationService;
use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Exceptions\UserRegistrationException;
use function config;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Client\ClientInterface as HttpClient;
use function trans;
use function url;

/**
 * Class OpenIdConnectService
 * Handles any app-specific OIDC tasks.
 */
class OidcService
{
    protected RegistrationService $registrationService;
    protected LoginService $loginService;
    protected HttpClient $httpClient;
    protected GroupSyncService $groupService;

    /**
     * OpenIdService constructor.
     */
    public function __construct(
        RegistrationService $registrationService,
        LoginService $loginService,
        HttpClient $httpClient,
        GroupSyncService $groupService
    ) {
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
        $this->httpClient = $httpClient;
        $this->groupService = $groupService;
    }

    /**
     * Initiate an authorization flow.
     *
     * @throws OidcException
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
     * return the matching, or new if registration active, user matched to the
     * authorization server. Throws if the user cannot be auth if not authenticated.
     *
     * @throws JsonDebugException
     * @throws OidcException
     * @throws StoppedAuthenticationException
     * @throws IdentityProviderException
     */
    public function processAuthorizeResponse(?string $authorizationCode): User
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
     * @throws OidcException
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
            try {
                $settings->discoverFromIssuer($this->httpClient, Cache::store(null), 15);
            } catch (OidcIssuerDiscoveryException $exception) {
                throw new OidcException('OIDC Discovery Error: ' . $exception->getMessage());
            }
        }

        $settings->validate();

        return $settings;
    }

    /**
     * Load the underlying OpenID Connect Provider.
     */
    protected function getProvider(OidcProviderSettings $settings): OidcOAuthProvider
    {
        $provider = new OidcOAuthProvider($settings->arrayForProvider(), [
            'httpClient'     => $this->httpClient,
            'optionProvider' => new HttpBasicAuthOptionProvider(),
        ]);

        foreach ($this->getAdditionalScopes() as $scope) {
            $provider->addScope($scope);
        }

        return $provider;
    }

    /**
     * Get any user-defined addition/custom scopes to apply to the authentication request.
     *
     * @return string[]
     */
    protected function getAdditionalScopes(): array
    {
        $scopeConfig = $this->config()['additional_scopes'] ?: '';

        $scopeArr = explode(',', $scopeConfig);
        $scopeArr = array_map(fn (string $scope) => trim($scope), $scopeArr);

        return array_filter($scopeArr);
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
     * Extract the assigned groups from the id token.
     *
     * @return string[]
     */
    protected function getUserGroups(OidcIdToken $token): array
    {
        $groupsAttr = $this->config()['groups_claim'];
        if (empty($groupsAttr)) {
            return [];
        }

        $groupsList = Arr::get($token->getAllClaims(), $groupsAttr);
        if (!is_array($groupsList)) {
            return [];
        }

        return array_values(array_filter($groupsList, function ($val) {
            return is_string($val);
        }));
    }

    /**
     * Extract the details of a user from an ID token.
     *
     * @return array{name: string, email: string, external_id: string, groups: string[]}
     */
    protected function getUserDetails(OidcIdToken $token): array
    {
        $id = $token->getClaim('sub');

        return [
            'external_id' => $id,
            'email'       => $token->getClaim('email'),
            'name'        => $this->getUserDisplayName($token, $id),
            'groups'      => $this->getUserGroups($token),
        ];
    }

    /**
     * Processes a received access token for a user. Login the user when
     * they exist, optionally registering them automatically.
     *
     * @throws OidcException
     * @throws JsonDebugException
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
            throw new OidcException("ID token validate failed with error: {$exception->getMessage()}");
        }

        $userDetails = $this->getUserDetails($idToken);
        $isLoggedIn = auth()->check();

        if (empty($userDetails['email'])) {
            throw new OidcException(trans('errors.oidc_no_email_address'));
        }

        if ($isLoggedIn) {
            throw new OidcException(trans('errors.oidc_already_logged_in'));
        }

        try {
            $user = $this->registrationService->findOrRegister(
                $userDetails['name'],
                $userDetails['email'],
                $userDetails['external_id']
            );
        } catch (UserRegistrationException $exception) {
            throw new OidcException($exception->getMessage());
        }

        if ($this->shouldSyncGroups()) {
            $groups = $userDetails['groups'];
            $detachExisting = $this->config()['remove_from_groups'];
            $this->groupService->syncUserWithFoundGroups($user, $groups, $detachExisting);
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

    /**
     * Check if groups should be synced.
     */
    protected function shouldSyncGroups(): bool
    {
        return $this->config()['user_to_groups'] !== false;
    }
}
