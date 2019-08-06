<?php namespace BookStack\Auth\Access;

use BookStack\Auth\Access;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\SamlException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


/**
 * Class Saml2Service
 * Handles any app-specific SAML tasks.
 * @package BookStack\Services
 */
class Saml2Service
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

    /**
     * Extract the details of a user from a SAML response.
     * @param $samlID
     * @param $samlAttributes
     * @return array
     */
    public function getUserDetails($samlID, $samlAttributes)
    {
        $emailAttr = $this->config['email_attribute'];
        $displayNameAttr = $this->config['display_name_attribute'];
        $userNameAttr = $this->config['user_name_attribute'];

        $email = $this->getSamlResponseAttribute($samlAttributes, $emailAttr, null);

        if ($userNameAttr === null) {
          $userName = $samlID;
        } else {
          $userName = $this->getSamlResponseAttribute($samlAttributes, $userNameAttr, $samlID);
        }

        $displayName = [];
        foreach ($displayNameAttr as $dnAttr) {
          $dnComponent = $this->getSamlResponseAttribute($samlAttributes, $dnAttr, null);
          if ($dnComponent !== null) {
            $displayName[] = $dnComponent;
          }
        }

        if (count($displayName) == 0) {
          $displayName = $userName;
        } else {
          $displayName = implode(' ', $displayName);
        }

        return [
            'uid'   => $userName,
            'name'  => $displayName,
            'dn'    => $samlID,
            'email' => $email,
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
            $data = $samlAttributes[$propertyKey];
            if (!is_array($data)) {
              return $data;
            } else if (count($data) == 0) {
              return $defaultValue;
            } else if (count($data) == 1) {
              return $data[0];
            } else {
              return $data;
            }
        }

        return $defaultValue;
    }

    protected function registerUser($userDetails) {

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

    public function processLoginCallback($samlID, $samlAttributes) {

        $userDetails = $this->getUserDetails($samlID, $samlAttributes);
        $user = $this->user
            ->where('external_auth_id', $userDetails['uid'])
            ->first();

        $isLoggedIn = auth()->check();

        if (!$isLoggedIn) {
            if ($user === null && config('services.saml.auto_register') === true) {
                $user = $this->registerUser($userDetails);
            }

            if ($user !== null) {
                auth()->login($user);
            }
        }

        return $user;
    }

    /**
     * Sync the SAML groups to the user roles for the current user
     * @param \BookStack\Auth\User $user
     * @param array $samlAttributes
     */
    public function syncGroups(User $user, array $samlAttributes)
    {
        $userSamlGroups = $this->getUserGroups($samlAttributes);

        // Get the ids for the roles from the names
        $samlGroupsAsRoles = $this->matchSamlGroupsToSystemsRoles($userSamlGroups);

        // Sync groups
        if ($this->config['remove_from_groups']) {
            $user->roles()->sync($samlGroupsAsRoles);
            $this->userRepo->attachDefaultRole($user);
        } else {
            $user->roles()->syncWithoutDetaching($samlGroupsAsRoles);
        }
    }

    /**
     * Match an array of group names from SAML to BookStack system roles.
     * Formats group names to be lower-case and hyphenated.
     * @param array $groupNames
     * @return \Illuminate\Support\Collection
     */
    protected function matchSamlGroupsToSystemsRoles(array $groupNames)
    {
        foreach ($groupNames as $i => $groupName) {
            $groupNames[$i] = str_replace(' ', '-', trim(strtolower($groupName)));
        }

        $roles = Role::query()->where(function (Builder $query) use ($groupNames) {
            $query->whereIn('name', $groupNames);
            foreach ($groupNames as $groupName) {
                $query->orWhere('external_auth_id', 'LIKE', '%' . $groupName . '%');
            }
        })->get();

        $matchedRoles = $roles->filter(function (Role $role) use ($groupNames) {
            return $this->roleMatchesGroupNames($role, $groupNames);
        });

        return $matchedRoles->pluck('id');
    }

    /**
     * Check a role against an array of group names to see if it matches.
     * Checked against role 'external_auth_id' if set otherwise the name of the role.
     * @param \BookStack\Auth\Role $role
     * @param array $groupNames
     * @return bool
     */
    protected function roleMatchesGroupNames(Role $role, array $groupNames)
    {
        if ($role->external_auth_id) {
            $externalAuthIds = explode(',', strtolower($role->external_auth_id));
            foreach ($externalAuthIds as $externalAuthId) {
                if (in_array(trim($externalAuthId), $groupNames)) {
                    return true;
                }
            }
            return false;
        }

        $roleName = str_replace(' ', '-', trim(strtolower($role->display_name)));
        return in_array($roleName, $groupNames);
    }

}
