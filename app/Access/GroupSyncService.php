<?php

namespace BookStack\Access;

use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Support\Collection;

class GroupSyncService
{
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
        foreach ($this->parseRoleExternalAuthId($externalId) as $externalAuthId) {
            if (in_array($externalAuthId, $groupNames)) {
                return true;
            }
        }

        return false;
    }

    protected function parseRoleExternalAuthId(string $externalId): array
    {
        $inputIds = preg_split('/(?<!\\\),/', strtolower($externalId));
        $cleanIds = [];

        foreach ($inputIds as $inputId) {
            $cleanIds[] = str_replace('\,', ',', trim($inputId));
        }

        return $cleanIds;
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
    public function syncUserWithFoundGroups(User $user, array $userGroups, bool $detachExisting): void
    {
        // Get the ids for the roles from the names
        $groupsAsRoles = $this->matchGroupsToSystemsRoles($userGroups);

        // Sync groups
        if ($detachExisting) {
            $user->roles()->sync($groupsAsRoles);
            $user->attachDefaultRole();
        } else {
            $user->roles()->syncWithoutDetaching($groupsAsRoles);
        }
    }
}
