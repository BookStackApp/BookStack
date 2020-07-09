<?php namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\OpenIdException;
use BookStack\Exceptions\UserRegistrationException;
use Exception;
use Lcobucci\JWT\Token;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use OpenIDConnectClient\AccessToken;
use OpenIDConnectClient\Exception\InvalidTokenException;
use OpenIDConnectClient\OpenIDConnectProvider;

/**
 * Class OpenIdService
 * Handles any app-specific OpenId tasks.
 */
class OpenIdService extends ExternalAuthService
{
    protected $config;

    /**
     * OpenIdService constructor.
     */
    public function __construct(RegistrationService $registrationService, User $user)
    {
        parent::__construct($registrationService, $user);
        
        $this->config = config('openid');
    }

    /**
     * Initiate a authorization flow.
     * @throws Error
     */
    public function login(): array
    {
        $provider = $this->getProvider();
        return [
            'url' => $provider->getAuthorizationUrl(),
            'state' => $provider->getState(),
        ];
    }

    /**
     * Initiate a logout flow.
     * @throws Error
     */
    public function logout(): array
    {
        $this->actionLogout();
        $url = '/';
        $id = null;

        return ['url' => $url, 'id' => $id];
    }

    /**
     * Refresh the currently logged in user.
     * @throws Error
     */
    public function refresh(): bool
    {
        // Retrieve access token for current session
        $json = session()->get('openid_token');
        $accessToken = new AccessToken(json_decode($json, true) ?? []);

        // Check if both the access token and the ID token (if present) are unexpired
        $idToken = $accessToken->getIdToken();
        if (!$accessToken->hasExpired() && (!$idToken || !$idToken->isExpired())) {
            return true;
        }

        // If no refresh token available, logout
        if ($accessToken->getRefreshToken() === null) {
            $this->actionLogout();
            return false;
        }

        // ID token or access token is expired, we refresh it using the refresh token
        try {
            $provider = $this->getProvider();

            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);
        } catch (IdentityProviderException $e) {
            // Refreshing failed, logout
            $this->actionLogout();
            return false;
        } catch (\Exception $e) {
            // Unknown error, logout and throw
            $this->actionLogout();
            throw $e;
        }

        // A valid token was obtained, we update the access token
        session()->put('openid_token', json_encode($accessToken));

        return true;
    }

    /**
     * Process the Authorization response from the authorization server and
     * return the matching, or new if registration active, user matched to
     * the authorization server.
     * Returns null if not authenticated.
     * @throws Error
     * @throws OpenIdException
     * @throws ValidationError
     * @throws JsonDebugException
     * @throws UserRegistrationException
     */
    public function processAuthorizeResponse(?string $authorizationCode): ?User
    {
        $provider = $this->getProvider();

        // Try to exchange authorization code for access token
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $authorizationCode,
        ]);

        return $this->processAccessTokenCallback($accessToken);
    }

    /**
     * Do the required actions to log a user out.
     */
    protected function actionLogout()
    {
        auth()->logout();
        session()->invalidate();
    }

    /**
     * Load the underlying OpenID Connect Provider.
     * @throws Error
     * @throws Exception
     */
    protected function getProvider(): OpenIDConnectProvider
    {
        // Setup settings
        $settings = $this->config['openid'];
        $overrides = $this->config['openid_overrides'] ?? [];

        if ($overrides && is_string($overrides)) {
            $overrides = json_decode($overrides, true);
        }

        $openIdSettings = $this->loadOpenIdDetails();
        $settings = array_replace_recursive($settings, $openIdSettings, $overrides);

        // Setup services
        $services = $this->loadOpenIdServices();
        $overrides = $this->config['openid_services'] ?? [];

        $services = array_replace_recursive($services, $overrides);

        return new OpenIDConnectProvider($settings, $services);
    }

    /**
     * Load services utilized by the OpenID Connect provider.
     */
    protected function loadOpenIdServices(): array
    {
        return [
            'signer' => new \Lcobucci\JWT\Signer\Rsa\Sha256(),
        ];
    }

    /**
     * Load dynamic service provider options required by the OpenID Connect provider.
     */
    protected function loadOpenIdDetails(): array
    {
        return [
            'redirectUri' => url('/openid/redirect'),
        ];
    }

    /**
     * Calculate the display name
     */
    protected function getUserDisplayName(Token $token, string $defaultValue): string
    {
        $displayNameAttr = $this->config['display_name_attributes'];

        $displayName = [];
        foreach ($displayNameAttr as $dnAttr) {
            $dnComponent = $token->getClaim($dnAttr, '');
            if ($dnComponent !== '') {
                $displayName[] = $dnComponent;
            }
        }

        if (count($displayName) == 0) {
            $displayName = $defaultValue;
        } else {
            $displayName = implode(' ', $displayName);
        }

        return $displayName;
    }

    /**
     * Get the value to use as the external id saved in BookStack
     * used to link the user to an existing BookStack DB user.
     */
    protected function getExternalId(Token $token, string $defaultValue)
    {
        $userNameAttr = $this->config['external_id_attribute'];
        if ($userNameAttr === null) {
            return $defaultValue;
        }

        return $token->getClaim($userNameAttr, $defaultValue);
    }

    /**
     * Extract the details of a user from an ID token.
     */
    protected function getUserDetails(Token $token): array
    {
        $email = null;
        $emailAttr = $this->config['email_attribute'];
        if ($token->hasClaim($emailAttr)) {
            $email = $token->getClaim($emailAttr);
        }

        return [
            'external_id' => $token->getClaim('sub'),
            'email' => $email,
            'name' => $this->getUserDisplayName($token, $email),
        ];
    }

    /**
     * Processes a received access token for a user. Login the user when
     * they exist, optionally registering them automatically.
     * @throws OpenIdException
     * @throws JsonDebugException
     * @throws UserRegistrationException
     */
    public function processAccessTokenCallback(AccessToken $accessToken): User
    {
        $userDetails = $this->getUserDetails($accessToken->getIdToken());
        $isLoggedIn = auth()->check();

        if ($this->config['dump_user_details']) {
            throw new JsonDebugException($accessToken->jsonSerialize());
        }

        if ($userDetails['email'] === null) {
            throw new OpenIdException(trans('errors.openid_no_email_address'));
        }

        if ($isLoggedIn) {
            throw new OpenIdException(trans('errors.openid_already_logged_in'), '/login');
        }

        $user = $this->getOrRegisterUser($userDetails);
        if ($user === null) {
            throw new OpenIdException(trans('errors.openid_user_not_registered', ['name' => $userDetails['external_id']]), '/login');
        }

        auth()->login($user);
        session()->put('openid_token', json_encode($accessToken));
        return $user;
    }
}
