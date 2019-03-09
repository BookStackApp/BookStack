<?php namespace BookStack\Auth\Access;

use BookStack\Auth\Access;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\LdapException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LdapService
 * Handles any app-specific LDAP tasks.
 * @package BookStack\Services
 */
class LdapService
{

    protected $ldap;
    protected $ldapConnection;
    protected $config;
    protected $userRepo;
    protected $enabled;

    /**
     * LdapService constructor.
     * @param Ldap $ldap
     * @param \BookStack\Auth\UserRepo $userRepo
     */
    public function __construct(Access\Ldap $ldap, UserRepo $userRepo)
    {
        $this->ldap = $ldap;
        $this->config = config('services.ldap');
        $this->userRepo = $userRepo;
        $this->enabled = config('auth.method') === 'ldap';
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
     * Search for attributes for a specific user on the ldap
     * @param string $userName
     * @param array $attributes
     * @return null|array
     * @throws LdapException
     */
    private function getUserWithAttributes($userName, $attributes)
    {
        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

        // Find user
        $userFilter = $this->buildFilter($this->config['user_filter'], ['user' => $userName]);
        $baseDn = $this->config['base_dn'];

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $this->ldap->setOption($ldapConnection, LDAP_OPT_REFERRALS, $followReferrals);
        $users = $this->ldap->searchAndGetEntries($ldapConnection, $baseDn, $userFilter, $attributes);
        if ($users['count'] === 0) {
            return null;
        }

        return $users[0];
    }

    /**
     * Get the details of a user from LDAP using the given username.
     * User found via configurable user filter.
     * @param $userName
     * @return array|null
     * @throws LdapException
     */
    public function getUserDetails($userName)
    {
        $emailAttr = $this->config['email_attribute'];
        $displayNameAttr = $this->config['display_name_attribute'];

        $user = $this->getUserWithAttributes($userName, ['cn', 'uid', 'dn', $emailAttr, $displayNameAttr]);

        if ($user === null) {
            return null;
        }

        return [
            'uid'   => (isset($user['uid'])) ? $user['uid'][0] : $user['dn'],
            'name'  => (isset($uset[$displayNameAttr])) ? (is_array($user[$displayNameAttr]) ? $user[$displayNameAttr][0] : $user[$displayNameAttr]) : $user['cn'][0],
            'dn'    => $user['dn'],
            'email' => (isset($user[$emailAttr])) ? (is_array($user[$emailAttr]) ? $user[$emailAttr][0] : $user[$emailAttr]) : null
        ];
    }

    /**
     * @param Authenticatable $user
     * @param string          $username
     * @param string          $password
     * @return bool
     * @throws LdapException
     */
    public function validateUserCredentials(Authenticatable $user, $username, $password)
    {
        $ldapUser = $this->getUserDetails($username);
        if ($ldapUser === null) {
            return false;
        }

        if ($ldapUser['uid'] !== $user->external_auth_id) {
            return false;
        }

        $ldapConnection = $this->getConnection();
        try {
            $ldapBind = $this->ldap->bind($ldapConnection, $ldapUser['dn'], $password);
        } catch (\ErrorException $e) {
            $ldapBind = false;
        }

        return $ldapBind;
    }

    /**
     * Bind the system user to the LDAP connection using the given credentials
     * otherwise anonymous access is attempted.
     * @param $connection
     * @throws LdapException
     */
    protected function bindSystemUser($connection)
    {
        $ldapDn = $this->config['dn'];
        $ldapPass = $this->config['pass'];

        $isAnonymous = ($ldapDn === false || $ldapPass === false);
        if ($isAnonymous) {
            $ldapBind = $this->ldap->bind($connection);
        } else {
            $ldapBind = $this->ldap->bind($connection, $ldapDn, $ldapPass);
        }

        if (!$ldapBind) {
            throw new LdapException(($isAnonymous ? trans('errors.ldap_fail_anonymous') : trans('errors.ldap_fail_authed')));
        }
    }

    /**
     * Get the connection to the LDAP server.
     * Creates a new connection if one does not exist.
     * @return resource
     * @throws LdapException
     */
    protected function getConnection()
    {
        if ($this->ldapConnection !== null) {
            return $this->ldapConnection;
        }

        // Check LDAP extension in installed
        if (!function_exists('ldap_connect') && config('app.env') !== 'testing') {
            throw new LdapException(trans('errors.ldap_extension_not_installed'));
        }

        // Get port from server string and protocol if specified.
        $ldapServer = explode(':', $this->config['server']);
        $hasProtocol = preg_match('/^ldaps{0,1}\:\/\//', $this->config['server']) === 1;
        if (!$hasProtocol) {
            array_unshift($ldapServer, '');
        }
        $hostName = $ldapServer[0] . ($hasProtocol?':':'') . $ldapServer[1];
        $defaultPort = $ldapServer[0] === 'ldaps' ? 636 : 389;

        /*
         * Check if TLS_INSECURE is set. The handle is set to NULL due to the nature of
         * the LDAP_OPT_X_TLS_REQUIRE_CERT option. It can only be set globally and not
         * per handle.
         */
        if ($this->config['tls_insecure']) {
            $this->ldap->setOption(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        }

        $ldapConnection = $this->ldap->connect($hostName, count($ldapServer) > 2 ? intval($ldapServer[2]) : $defaultPort);

        if ($ldapConnection === false) {
            throw new LdapException(trans('errors.ldap_cannot_connect'));
        }

        // Set any required options
        if ($this->config['version']) {
            $this->ldap->setVersion($ldapConnection, $this->config['version']);
        }

        $this->ldapConnection = $ldapConnection;
        return $this->ldapConnection;
    }

    /**
     * Build a filter string by injecting common variables.
     * @param string $filterString
     * @param array $attrs
     * @return string
     */
    protected function buildFilter($filterString, array $attrs)
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $newKey = '${' . $key . '}';
            $newAttrs[$newKey] = $this->ldap->escape($attrText);
        }
        return strtr($filterString, $newAttrs);
    }

    /**
     * Get the groups a user is a part of on ldap
     * @param string $userName
     * @return array
     * @throws LdapException
     */
    public function getUserGroups($userName)
    {
        $groupsAttr = $this->config['group_attribute'];
        $user = $this->getUserWithAttributes($userName, [$groupsAttr]);

        if ($user === null) {
            return [];
        }

        $userGroups = $this->groupFilter($user);
        $userGroups = $this->getGroupsRecursive($userGroups, []);
        return $userGroups;
    }

    /**
     * Get the parent groups of an array of groups
     * @param array $groupsArray
     * @param array $checked
     * @return array
     * @throws LdapException
     */
    private function getGroupsRecursive($groupsArray, $checked)
    {
        $groups_to_add = [];
        foreach ($groupsArray as $groupName) {
            if (in_array($groupName, $checked)) {
                continue;
            }

            $groupsToAdd = $this->getGroupGroups($groupName);
            $groups_to_add = array_merge($groups_to_add, $groupsToAdd);
            $checked[] = $groupName;
        }
        $groupsArray = array_unique(array_merge($groupsArray, $groups_to_add), SORT_REGULAR);

        if (!empty($groups_to_add)) {
            return $this->getGroupsRecursive($groupsArray, $checked);
        } else {
            return $groupsArray;
        }
    }

    /**
     * Get the parent groups of a single group
     * @param string $groupName
     * @return array
     * @throws LdapException
     */
    private function getGroupGroups($groupName)
    {
        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $this->ldap->setOption($ldapConnection, LDAP_OPT_REFERRALS, $followReferrals);

        $baseDn = $this->config['base_dn'];
        $groupsAttr = strtolower($this->config['group_attribute']);

        $groupFilter = 'CN=' . $this->ldap->escape($groupName);
        $groups = $this->ldap->searchAndGetEntries($ldapConnection, $baseDn, $groupFilter, [$groupsAttr]);
        if ($groups['count'] === 0) {
            return [];
        }

        $groupGroups = $this->groupFilter($groups[0]);
        return $groupGroups;
    }

    /**
     * Filter out LDAP CN and DN language in a ldap search return
     * Gets the base CN (common name) of the string
     * @param array $userGroupSearchResponse
     * @return array
     */
    protected function groupFilter(array $userGroupSearchResponse)
    {
        $groupsAttr = strtolower($this->config['group_attribute']);
        $ldapGroups = [];
        $count = 0;

        if (isset($userGroupSearchResponse[$groupsAttr]['count'])) {
            $count = (int) $userGroupSearchResponse[$groupsAttr]['count'];
        }

        for ($i=0; $i<$count; $i++) {
            $dnComponents = $this->ldap->explodeDn($userGroupSearchResponse[$groupsAttr][$i], 1);
            if (!in_array($dnComponents[0], $ldapGroups)) {
                $ldapGroups[] = $dnComponents[0];
            }
        }

        return $ldapGroups;
    }

    /**
     * Sync the LDAP groups to the user roles for the current user
     * @param \BookStack\Auth\User $user
     * @param string $username
     * @throws LdapException
     */
    public function syncGroups(User $user, string $username)
    {
        $userLdapGroups = $this->getUserGroups($username);

        // Get the ids for the roles from the names
        $ldapGroupsAsRoles = $this->matchLdapGroupsToSystemsRoles($userLdapGroups);

        // Sync groups
        if ($this->config['remove_from_groups']) {
            $user->roles()->sync($ldapGroupsAsRoles);
            $this->userRepo->attachDefaultRole($user);
        } else {
            $user->roles()->syncWithoutDetaching($ldapGroupsAsRoles);
        }
    }

    /**
     * Match an array of group names from LDAP to BookStack system roles.
     * Formats LDAP group names to be lower-case and hyphenated.
     * @param array $groupNames
     * @return \Illuminate\Support\Collection
     */
    protected function matchLdapGroupsToSystemsRoles(array $groupNames)
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
