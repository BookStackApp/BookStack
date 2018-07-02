<?php namespace BookStack\Repos;

use BookStack\Services\Ldap;
use BookStack\Services\LdapService;
use BookStack\Role;
use BookStack\Repos\UserRepo;

class LdapRepo
{

    protected $ldap = null;
    protected $ldapService = null;

    protected $config;

    /**
     * LdapRepo constructor.
     * @param \BookStack\Repos\UserRepo $userRepo
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->config = config('services.ldap');

        if (config('auth.method') !== 'ldap') {
            return false;
        }

        $this->ldapService = new LdapService(new Ldap);
        $this->userRepo = $userRepo;
    }

    /**
     * If there is no ldap connection, all methods calls to this library will return null
     */
    public function __call($method, $arguments)
    {
        if ($this->ldap === null) {
            return null;
        }

        return call_user_func_array(array($this,$method), $arguments);
    }

    /**
     * Sync the LDAP groups to the user roles for the current user
     * @param \BookStack\User $user
     * @param string $userName
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function syncGroups($user, $userName)
    {
        $userLdapGroups = $this->ldapService->getUserGroups($userName);
        $userLdapGroups = $this->groupNameFilter($userLdapGroups);
        // get the ids for the roles from the names
        $ldapGroupsAsRoles = Role::whereIn('name', $userLdapGroups)->pluck('id');
        // sync groups
        if ($this->config['remove_from_groups']) {
            $user->roles()->sync($ldapGroupsAsRoles);
            $this->userRepo->attachDefaultRole($user);
        } else {
            $user->roles()->syncWithoutDetaching($ldapGroupsAsRoles);
        }

        // make the user an admin?
        if (in_array($this->config['admin'], $userLdapGroups)) {
            $this->userRepo->attachSystemRole($user, 'admin');
        }
    }

    /**
     * Filter to convert the groups from ldap to the format of the roles name on BookStack
     * Spaces replaced with -, all lowercase letters
     * @param array $groups
     * @return array
     */
    private function groupNameFilter($groups)
    {
        $return = [];
        foreach ($groups as $groupName) {
            $return[] = str_replace(' ', '-', strtolower($groupName));
        }
        return $return;
    }
}
