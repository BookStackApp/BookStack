<?php namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\SamlException;
use BookStack\Exceptions\UserRegistrationException;
use Exception;
use Illuminate\Support\Str;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\ValidationError;

/**
 * Class Saml2Service
 * Handles any app-specific SAML tasks.
 */
class Saml2Service extends ExternalAuthService
{
    protected $config;
    protected $registrationService;
    protected $user;

    /**
     * Saml2Service constructor.
     */
    public function __construct(RegistrationService $registrationService, User $user)
    {
        $this->config = config('saml2');
        $this->registrationService = $registrationService;
        $this->user = $user;
    }

    /**
     * Initiate a login flow.
     * @throws Error
     */
    public function login(): array
    {
        $toolKit = $this->getToolkit();
        $returnRoute = url('/saml2/acs');
        return [
            'url' => $toolKit->login($returnRoute, [], false, false, true),
            'id' => $toolKit->getLastRequestID(),
        ];
    }

    /**
     * Initiate a logout flow.
     * @throws Error
     */
    public function logout(): array
    {
        $toolKit = $this->getToolkit();
        $returnRoute = url('/');

        try {
            $url = $toolKit->logout($returnRoute, [], null, null, true);
            $id = $toolKit->getLastRequestID();
        } catch (Error $error) {
            if ($error->getCode() !== Error::SAML_SINGLE_LOGOUT_NOT_SUPPORTED) {
                throw $error;
            }

            $this->actionLogout();
            $url = '/';
            $id = null;
        }

        return ['url' => $url, 'id' => $id];
    }

    /**
     * Process the ACS response from the idp and return the
     * matching, or new if registration active, user matched to the idp.
     * Returns null if not authenticated.
     * @throws Error
     * @throws SamlException
     * @throws ValidationError
     * @throws JsonDebugException
     * @throws UserRegistrationException
     */
    public function processAcsResponse(?string $requestId): ?User
    {
        $toolkit = $this->getToolkit();
        $toolkit->processResponse($requestId);
        $errors = $toolkit->getErrors();

        if (!empty($errors)) {
            throw new Error(
                'Invalid ACS Response: '.implode(', ', $errors)
            );
        }

        if (!$toolkit->isAuthenticated()) {
            return null;
        }

        $attrs = $toolkit->getAttributes();
        $id = $toolkit->getNameId();

        return $this->processLoginCallback($id, $attrs);
    }

    /**
     * Process a response for the single logout service.
     * @throws Error
     */
    public function processSlsResponse(?string $requestId): ?string
    {
        $toolkit = $this->getToolkit();
        $redirect = $toolkit->processSLO(true, $requestId, false, null, true);

        $errors = $toolkit->getErrors();

        if (!empty($errors)) {
            throw new Error(
                'Invalid SLS Response: '.implode(', ', $errors)
            );
        }

        $this->actionLogout();
        return $redirect;
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
     * Get the metadata for this service provider.
     * @throws Error
     */
    public function metadata(): string
    {
        $toolKit = $this->getToolkit();
        $settings = $toolKit->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (!empty($errors)) {
            throw new Error(
                'Invalid SP metadata: '.implode(', ', $errors),
                Error::METADATA_SP_INVALID
            );
        }

        return $metadata;
    }

    /**
     * Load the underlying Onelogin SAML2 toolkit.
     * @throws Error
     * @throws Exception
     */
    protected function getToolkit(): Auth
    {
        $settings = $this->config['onelogin'];
        $overrides = $this->config['onelogin_overrides'] ?? [];

        if ($overrides && is_string($overrides)) {
            $overrides = json_decode($overrides, true);
        }

        $metaDataSettings = [];
        if ($this->config['autoload_from_metadata']) {
            $metaDataSettings = IdPMetadataParser::parseRemoteXML($settings['idp']['entityId']);
        }

        $spSettings = $this->loadOneloginServiceProviderDetails();
        $settings = array_replace_recursive($settings, $spSettings, $metaDataSettings, $overrides);
        return new Auth($settings);
    }

    /**
     * Load dynamic service provider options required by the onelogin toolkit.
     */
    protected function loadOneloginServiceProviderDetails(): array
    {
        $spDetails = [
            'entityId' => url('/saml2/metadata'),
            'assertionConsumerService' => [
                'url' => url('/saml2/acs'),
            ],
            'singleLogoutService' => [
                'url' => url('/saml2/sls')
            ],
        ];

        return [
            'baseurl' => url('/saml2'),
            'sp' => $spDetails
        ];
    }

    /**
     * Check if groups should be synced.
     */
    protected function shouldSyncGroups(): bool
    {
        return $this->config['user_to_groups'] !== false;
    }

    /**
     * Calculate the display name
     */
    protected function getUserDisplayName(array $samlAttributes, string $defaultValue): string
    {
        $displayNameAttr = $this->config['display_name_attributes'];

        $displayName = [];
        foreach ($displayNameAttr as $dnAttr) {
            $dnComponent = $this->getSamlResponseAttribute($samlAttributes, $dnAttr, null);
            if ($dnComponent !== null) {
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
    protected function getExternalId(array $samlAttributes, string $defaultValue)
    {
        $userNameAttr = $this->config['external_id_attribute'];
        if ($userNameAttr === null) {
            return $defaultValue;
        }

        return $this->getSamlResponseAttribute($samlAttributes, $userNameAttr, $defaultValue);
    }

    /**
     * Extract the details of a user from a SAML response.
     */
    protected function getUserDetails(string $samlID, $samlAttributes): array
    {
        $emailAttr = $this->config['email_attribute'];
        $externalId = $this->getExternalId($samlAttributes, $samlID);

        $defaultEmail = filter_var($samlID, FILTER_VALIDATE_EMAIL) ? $samlID : null;
        $email = $this->getSamlResponseAttribute($samlAttributes, $emailAttr, $defaultEmail);

        return [
            'external_id' => $externalId,
            'name' => $this->getUserDisplayName($samlAttributes, $externalId),
            'email' => $email,
            'saml_id' => $samlID,
        ];
    }

    /**
     * Get the groups a user is a part of from the SAML response.
     */
    public function getUserGroups(array $samlAttributes): array
    {
        $groupsAttr = $this->config['group_attribute'];
        $userGroups = $samlAttributes[$groupsAttr] ?? null;

        if (!is_array($userGroups)) {
            $userGroups = [];
        }

        return $userGroups;
    }

    /**
     *  For an array of strings, return a default for an empty array,
     *  a string for an array with one element and the full array for
     *  more than one element.
     */
    protected function simplifyValue(array $data, $defaultValue)
    {
        switch (count($data)) {
            case 0:
                $data = $defaultValue;
                break;
            case 1:
                $data = $data[0];
                break;
        }
        return $data;
    }

    /**
     * Get a property from an SAML response.
     * Handles properties potentially being an array.
     */
    protected function getSamlResponseAttribute(array $samlAttributes, string $propertyKey, $defaultValue)
    {
        if (isset($samlAttributes[$propertyKey])) {
            return $this->simplifyValue($samlAttributes[$propertyKey], $defaultValue);
        }

        return $defaultValue;
    }

    /**
     * Get the user from the database for the specified details.
     * @throws SamlException
     * @throws UserRegistrationException
     */
    protected function getOrRegisterUser(array $userDetails): ?User
    {
        $user = $this->user->newQuery()
          ->where('external_auth_id', '=', $userDetails['external_id'])
          ->first();

        if (is_null($user)) {
            $userData = [
                'name' => $userDetails['name'],
                'email' => $userDetails['email'],
                'password' => Str::random(32),
                'external_auth_id' => $userDetails['external_id'],
            ];

            $user = $this->registrationService->registerUser($userData, null, false);
        }

        return $user;
    }

    /**
     * Process the SAML response for a user. Login the user when
     * they exist, optionally registering them automatically.
     * @throws SamlException
     * @throws JsonDebugException
     * @throws UserRegistrationException
     */
    public function processLoginCallback(string $samlID, array $samlAttributes): User
    {
        $userDetails = $this->getUserDetails($samlID, $samlAttributes);
        $isLoggedIn = auth()->check();

        if ($this->config['dump_user_details']) {
            throw new JsonDebugException([
                'id_from_idp' => $samlID,
                'attrs_from_idp' => $samlAttributes,
                'attrs_after_parsing' => $userDetails,
            ]);
        }

        if ($userDetails['email'] === null) {
            throw new SamlException(trans('errors.saml_no_email_address'));
        }

        if ($isLoggedIn) {
            throw new SamlException(trans('errors.saml_already_logged_in'), '/login');
        }

        $user = $this->getOrRegisterUser($userDetails);
        if ($user === null) {
            throw new SamlException(trans('errors.saml_user_not_registered', ['name' => $userDetails['external_id']]), '/login');
        }

        if ($this->shouldSyncGroups()) {
            $groups = $this->getUserGroups($samlAttributes);
            $this->syncWithGroups($user, $groups);
        }

        auth()->login($user);
        return $user;
    }
}
