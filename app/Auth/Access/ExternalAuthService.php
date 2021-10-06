<?php

namespace BookStack\Auth\Access;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ExternalAuthService
{
    protected $registrationService;
    protected $user;

    /**
     * ExternalAuthService base constructor.
     */
    public function __construct(RegistrationService $registrationService, User $user)
    {
        $this->registrationService = $registrationService;
        $this->user = $user;
    }
    
    /**
     * Get the user from the database for the specified details.
     * @throws UserRegistrationException
     */
    protected function getOrRegisterUser(array $userDetails): ?User
    {
        $user = User::query()
          ->where('external_auth_id', '=', $userDetails['external_id'])
          ->first();

        if (is_null($user)) {
            $userData = [
                'name'             => $userDetails['name'],
                'email'            => $userDetails['email'],
                'password'         => Str::random(32),
                'external_auth_id' => $userDetails['external_id'],
            ];

            $user = $this->registrationService->registerUser($userData, null, false);
        }

        return $user;
    }

    /**
     * Check a role against an array of group names to see if it matches.
     * Checked against role 'external_auth_id' if set otherwise the name of the role.
     */
    protected function roleMatchesGroupNames(Role $role, array $groupNames): bool
    {
        if ($role->external_auth_id) {
            return $this->externalIdMatchesGroupNames($role->external_auth_id, $groupNames);
        }

        $roleName = str_replace(' ', '-', trim(strtolower($role->display_name)));

        return in_array($roleName, $groupNames);
    }

    /**
     * Check if the given external auth ID string matches one of the given group names.
     */
    protected function externalIdMatchesGroupNames(string $externalId, array $groupNames): bool
    {
        $externalAuthIds = explode(',', strtolower($externalId));

        foreach ($externalAuthIds as $externalAuthId) {
            if (in_array(trim($externalAuthId), $groupNames)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match an array of group names to BookStack system roles.
     * Formats group names to be lower-case and hyphenated.
     */
    protected function matchGroupsToSystemsRoles(array $groupNames): Collection
    {
        foreach ($groupNames as $i => $groupName) {
            $groupNames[$i] = str_replace(' ', '-', trim(strtolower($groupName)));
        }

        $roles = Role::query()->get(['id', 'external_auth_id', 'display_name']);
        $matchedRoles = $roles->filter(function (Role $role) use ($groupNames) {
            return $this->roleMatchesGroupNames($role, $groupNames);
        });

        return $matchedRoles->pluck('id');
    }

    /**
     * Sync the groups to the user roles for the current user.
     */
    public function syncWithGroups(User $user, array $userGroups): void
    {
        // Get the ids for the roles from the names
        $groupsAsRoles = $this->matchGroupsToSystemsRoles($userGroups);

        // Sync groups
        if ($this->config['remove_from_groups']) {
            $user->roles()->sync($groupsAsRoles);
            $user->attachDefaultRole();
        } else {
            $user->roles()->syncWithoutDetaching($groupsAsRoles);
        }
    }
}
