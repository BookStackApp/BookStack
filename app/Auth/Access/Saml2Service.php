<?php namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SamlException;
use Illuminate\Support\Str;

/**
 * Class Saml2Service
 * Handles any app-specific SAML tasks.
 */
class Saml2Service extends ExternalAuthService
{
    protected $config;
    protected $userRepo;
    protected $user;
    protected $enabled;

    /**
     * Saml2Service constructor.
     */
    public function __construct(UserRepo $userRepo, User $user)
    {
        $this->config = config('services.saml');
        $this->userRepo = $userRepo;
        $this->user = $user;
        $this->enabled = config('saml2_settings.enabled') === true;
    }

    /**
     * Check if groups should be synced.
     */
    protected function shouldSyncGroups(): bool
    {
        return $this->enabled && $this->config['user_to_groups'] !== false;
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
     * @throws SamlException
     */
    public function getUserDetails(string $samlID, $samlAttributes): array
    {
        $emailAttr = $this->config['email_attribute'];
        $externalId = $this->getExternalId($samlAttributes, $samlID);
        $email = $this->getSamlResponseAttribute($samlAttributes, $emailAttr, null);

        if ($email === null) {
            throw new SamlException(trans('errors.saml_no_email_address'));
        }

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
     *  Register a user that is authenticated but not already registered.
     */
    protected function registerUser(array $userDetails): User
    {
        // Create an array of the user data to create a new user instance
        $userData = [
            'name' => $userDetails['name'],
            'email' => $userDetails['email'],
            'password' => Str::random(32),
            'external_auth_id' => $userDetails['external_id'],
            'email_confirmed' => true,
        ];

        // TODO - Handle duplicate email address scenario
        $user = $this->user->forceCreate($userData);
        $this->userRepo->attachDefaultRole($user);
        $this->userRepo->downloadAndAssignUserAvatar($user);
        return $user;
    }

    /**
     * Get the user from the database for the specified details.
     */
    protected function getOrRegisterUser(array $userDetails): ?User
    {
        $isRegisterEnabled = config('services.saml.auto_register') === true;
        $user = $this->user
          ->where('external_auth_id', $userDetails['external_id'])
          ->first();

        if ($user === null && $isRegisterEnabled) {
            $user = $this->registerUser($userDetails);
        }

        return $user;
    }

    /**
     * Process the SAML response for a user. Login the user when
     * they exist, optionally registering them automatically.
     * @throws SamlException
     */
    public function processLoginCallback(string $samlID, array $samlAttributes): User
    {
        $userDetails = $this->getUserDetails($samlID, $samlAttributes);
        $isLoggedIn = auth()->check();

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
