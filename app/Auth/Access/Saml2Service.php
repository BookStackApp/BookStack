<?php namespace BookStack\Auth\Access;

use BookStack\Auth\Access;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SamlException;
use Illuminate\Contracts\Auth\Authenticatable;


/**
 * Class Saml2Service
 * Handles any app-specific SAML tasks.
 * @package BookStack\Services
 */
class Saml2Service extends Access\ExternalAuthService
{
    protected $config;
    protected $userRepo;
    protected $user;
    protected $enabled;

    /**
     * Saml2Service constructor.
     * @param \BookStack\Auth\UserRepo $userRepo
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
     * @return bool
     */
    public function shouldSyncGroups()
    {
        return $this->enabled && $this->config['user_to_groups'] !== false;
    }

    /** Calculate the display name
     *  @param array $samlAttributes
     *  @param string $defaultValue
     *  @return string
     */
    protected function getUserDisplayName(array $samlAttributes, string $defaultValue)
    {
        $displayNameAttr = $this->config['display_name_attribute'];

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

    protected function getUserName(array $samlAttributes, string $defaultValue)
    {
        $userNameAttr = $this->config['user_name_attribute'];

        if ($userNameAttr === null) {
            $userName = $defaultValue;
        } else {
            $userName = $this->getSamlResponseAttribute($samlAttributes, $userNameAttr, $defaultValue);
        }

        return $userName;
    }

    /**
     * Extract the details of a user from a SAML response.
     * @param $samlID
     * @param $samlAttributes
     * @return array
     */
    public function getUserDetails($samlID, $samlAttributes)
    {
        $emailAttr = $this->config['email_attribute'];
        $userName = $this->getUserName($samlAttributes, $samlID);

        return [
            'uid'   => $userName,
            'name'  => $this->getUserDisplayName($samlAttributes, $userName),
            'dn'    => $samlID,
            'email' => $this->getSamlResponseAttribute($samlAttributes, $emailAttr, null),
        ];
    }

    /**
     * Get the groups a user is a part of from the SAML response.
     * @param array $samlAttributes
     * @return array
     */
    public function getUserGroups($samlAttributes)
    {
        $groupsAttr = $this->config['group_attribute'];
        $userGroups = $samlAttributes[$groupsAttr];

        if (!is_array($userGroups)) {
            $userGroups = [];
        }

        return $userGroups;
    }

    /**
     *  For an array of strings, return a default for an empty array,
     *  a string for an array with one element and the full array for
     *  more than one element.
     *
     *  @param array $data
     *  @param $defaultValue
     *  @return string
     */
    protected function simplifyValue(array $data, $defaultValue) {
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
     * @param array $userDetails
     * @param string $propertyKey
     * @param $defaultValue
     * @return mixed
     */
    protected function getSamlResponseAttribute(array $samlAttributes, string $propertyKey, $defaultValue)
    {
        if (isset($samlAttributes[$propertyKey])) {
            $data = $this->simplifyValue($samlAttributes[$propertyKey], $defaultValue);
        } else {
            $data = $defaultValue;
        }

        return $data;
    }

    /**
     *  Register a user that is authenticated but not
     *  already registered.
     *  @param array $userDetails
     *  @return User
     */
    protected function registerUser($userDetails)
    {
        // Create an array of the user data to create a new user instance
        $userData = [
            'name' => $userDetails['name'],
            'email' => $userDetails['email'],
            'password' => str_random(30),
            'external_auth_id' => $userDetails['uid'],
            'email_confirmed' => true,
        ];

        $user = $this->user->forceCreate($userData);
        $this->userRepo->attachDefaultRole($user);
        $this->userRepo->downloadAndAssignUserAvatar($user);
        return $user;
    }

    /**
     * Get the user from the database for the specified details.
     * @param array $userDetails
     * @return User|null
     */
    protected function getOrRegisterUser($userDetails)
    {
        $isRegisterEnabled = config('services.saml.auto_register') === true;
        $user = $this->user
          ->where('external_auth_id', $userDetails['uid'])
          ->first();

        if ($user === null && $isRegisterEnabled) {
            $user = $this->registerUser($userDetails);
        }

        return $user;
    }

    /**
     *  Process the SAML response for a user. Login the user when
     *  they exist, optionally registering them automatically.
     *  @param string $samlID
     *  @param array $samlAttributes
     *  @throws SamlException
     */
    public function processLoginCallback($samlID, $samlAttributes)
    {
        $userDetails = $this->getUserDetails($samlID, $samlAttributes);
        $isLoggedIn = auth()->check();

        if ($isLoggedIn) {
            throw new SamlException(trans('errors.saml_already_logged_in'), '/login');
        } else {
            $user = $this->getOrRegisterUser($userDetails);
            if ($user === null) {
                throw new SamlException(trans('errors.saml_user_not_registered', ['name' => $userDetails['uid']]), '/login');
            } else {
                $groups = $this->getUserGroups($samlAttributes);
                $this->syncWithGroups($user, $groups);
                auth()->login($user);
            }
        }

        return $user;
    }
}
