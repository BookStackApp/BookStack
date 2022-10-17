<?php

namespace BookStack\Auth\Access\Ldap;

use BookStack\Auth\Access\GroupSyncService;
use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\LdapException;
use BookStack\Exceptions\LdapFailedBindException;
use BookStack\Uploads\UserAvatars;
use Illuminate\Support\Facades\Log;

/**
 * Class LdapService
 * Handles any app-specific LDAP tasks.
 */
class LdapService
{
    protected LdapConnectionManager $ldap;
    protected GroupSyncService $groupSyncService;
    protected UserAvatars $userAvatars;

    protected array $config;

    public function __construct(LdapConnectionManager $ldap, UserAvatars $userAvatars, GroupSyncService $groupSyncService)
    {
        $this->ldap = $ldap;
        $this->userAvatars = $userAvatars;
        $this->groupSyncService = $groupSyncService;
        $this->config = config('services.ldap');
    }

    /**
     * Search for attributes for a specific user on the ldap.
     *
     * @throws LdapException
     */
    protected function getUserWithAttributes(string $userName, array $attributes): ?array
    {
        $connection = $this->ldap->startSystemBind($this->config);

        // Clean attributes
        foreach ($attributes as $index => $attribute) {
            if (strpos($attribute, 'BIN;') === 0) {
                $attributes[$index] = substr($attribute, strlen('BIN;'));
            }
        }

        // Find user
        $userFilter = $this->buildFilter($this->config['user_filter'], ['user' => $userName]);
        $baseDn = $this->config['base_dn'];

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $connection->setOption(LDAP_OPT_REFERRALS, $followReferrals);
        $users = $connection->searchAndGetEntries($baseDn, $userFilter, $attributes);
        if ($users['count'] === 0) {
            return null;
        }

        return $users[0];
    }

    /**
     * Get the details of a user from LDAP using the given username.
     * User found via configurable user filter.
     *
     * @throws LdapException
     */
    public function getUserDetails(string $userName): ?array
    {
        $idAttr = $this->config['id_attribute'];
        $emailAttr = $this->config['email_attribute'];
        $displayNameAttr = $this->config['display_name_attribute'];
        $thumbnailAttr = $this->config['thumbnail_attribute'];

        $user = $this->getUserWithAttributes($userName, array_filter([
            'cn', 'dn', $idAttr, $emailAttr, $displayNameAttr, $thumbnailAttr,
        ]));

        if (is_null($user)) {
            return null;
        }

        $userCn = $this->getUserResponseProperty($user, 'cn', null);
        $formatted = [
            'uid'   => $this->getUserResponseProperty($user, $idAttr, $user['dn']),
            'name'  => $this->getUserResponseProperty($user, $displayNameAttr, $userCn),
            'dn'    => $user['dn'],
            'email' => $this->getUserResponseProperty($user, $emailAttr, null),
            'avatar' => $thumbnailAttr ? $this->getUserResponseProperty($user, $thumbnailAttr, null) : null,
        ];

        if ($this->config['dump_user_details']) {
            throw new JsonDebugException([
                'details_from_ldap'        => $user,
                'details_bookstack_parsed' => $formatted,
            ]);
        }

        return $formatted;
    }

    /**
     * Get a property from an LDAP user response fetch.
     * Handles properties potentially being part of an array.
     * If the given key is prefixed with 'BIN;', that indicator will be stripped
     * from the key and any fetched values will be converted from binary to hex.
     */
    protected function getUserResponseProperty(array $userDetails, string $propertyKey, $defaultValue)
    {
        $isBinary = strpos($propertyKey, 'BIN;') === 0;
        $propertyKey = strtolower($propertyKey);
        $value = $defaultValue;

        if ($isBinary) {
            $propertyKey = substr($propertyKey, strlen('BIN;'));
        }

        if (isset($userDetails[$propertyKey])) {
            $value = (is_array($userDetails[$propertyKey]) ? $userDetails[$propertyKey][0] : $userDetails[$propertyKey]);
            if ($isBinary) {
                $value = bin2hex($value);
            }
        }

        return $value;
    }

    /**
     * Check if the given credentials are valid for the given user.
     *
     * @throws LdapException
     */
    public function validateUserCredentials(?array $ldapUserDetails, string $password): bool
    {
        if (is_null($ldapUserDetails)) {
            return false;
        }

        try {
            $this->ldap->startBind($ldapUserDetails['dn'], $password, $this->config);
        } catch (LdapFailedBindException $e) {
            return false;
        } catch (LdapException $e) {
            throw $e;
        }

        return true;
    }

    /**
     * Build a filter string by injecting common variables.
     */
    protected function buildFilter(string $filterString, array $attrs): string
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $newKey = '${' . $key . '}';
            $newAttrs[$newKey] = LdapConnection::escape($attrText);
        }

        return strtr($filterString, $newAttrs);
    }

    /**
     * Get the groups a user is a part of on ldap.
     *
     * @throws LdapException
     * @throws JsonDebugException
     */
    public function getUserGroups(string $userName): array
    {
        $groupsAttr = $this->config['group_attribute'];
        $user = $this->getUserWithAttributes($userName, [$groupsAttr]);

        if ($user === null) {
            return [];
        }

        $userGroups = $this->groupFilter($user);
        $allGroups = $this->getGroupsRecursive($userGroups, []);

        if ($this->config['dump_user_groups']) {
            throw new JsonDebugException([
                'details_from_ldap'             => $user,
                'parsed_direct_user_groups'     => $userGroups,
                'parsed_recursive_user_groups'  => $allGroups,
            ]);
        }

        return $allGroups;
    }

    /**
     * Get the parent groups of an array of groups.
     *
     * @throws LdapException
     */
    private function getGroupsRecursive(array $groupsArray, array $checked): array
    {
        $groupsToAdd = [];
        foreach ($groupsArray as $groupName) {
            if (in_array($groupName, $checked)) {
                continue;
            }

            $parentGroups = $this->getGroupGroups($groupName);
            $groupsToAdd = array_merge($groupsToAdd, $parentGroups);
            $checked[] = $groupName;
        }

        $groupsArray = array_unique(array_merge($groupsArray, $groupsToAdd), SORT_REGULAR);

        if (empty($groupsToAdd)) {
            return $groupsArray;
        }

        return $this->getGroupsRecursive($groupsArray, $checked);
    }

    /**
     * Get the parent groups of a single group.
     *
     * @throws LdapException
     */
    private function getGroupGroups(string $groupName): array
    {
        $connection = $this->ldap->startSystemBind($this->config);

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $connection->setOption(LDAP_OPT_REFERRALS, $followReferrals);

        $baseDn = $this->config['base_dn'];
        $groupsAttr = strtolower($this->config['group_attribute']);

        $groupFilter = 'CN=' . $connection->escape($groupName);
        $groups = $connection->searchAndGetEntries($baseDn, $groupFilter, [$groupsAttr]);
        if ($groups['count'] === 0) {
            return [];
        }

        return $this->groupFilter($groups[0]);
    }

    /**
     * Filter out LDAP CN and DN language in a ldap search return.
     * Gets the base CN (common name) of the string.
     */
    protected function groupFilter(array $userGroupSearchResponse): array
    {
        $groupsAttr = strtolower($this->config['group_attribute']);
        $ldapGroups = [];
        $count = 0;

        if (isset($userGroupSearchResponse[$groupsAttr]['count'])) {
            $count = (int) $userGroupSearchResponse[$groupsAttr]['count'];
        }

        for ($i = 0; $i < $count; $i++) {
            $dnComponents = LdapConnection::explodeDn($userGroupSearchResponse[$groupsAttr][$i], 1);
            if (!in_array($dnComponents[0], $ldapGroups)) {
                $ldapGroups[] = $dnComponents[0];
            }
        }

        return $ldapGroups;
    }

    /**
     * Sync the LDAP groups to the user roles for the current user.
     *
     * @throws LdapException
     * @throws JsonDebugException
     */
    public function syncGroups(User $user, string $username)
    {
        $userLdapGroups = $this->getUserGroups($username);
        $this->groupSyncService->syncUserWithFoundGroups($user, $userLdapGroups, $this->config['remove_from_groups']);
    }

    /**
     * Check if groups should be synced.
     */
    public function shouldSyncGroups(): bool
    {
        return $this->config['user_to_groups'] !== false;
    }

    /**
     * Save and attach an avatar image, if found in the ldap details, and attach
     * to the given user model.
     */
    public function saveAndAttachAvatar(User $user, array $ldapUserDetails): void
    {
        if (is_null($this->config['thumbnail_attribute']) || is_null($ldapUserDetails['avatar'])) {
            return;
        }

        try {
            $imageData = $ldapUserDetails['avatar'];
            $this->userAvatars->assignToUserFromExistingData($user, $imageData, 'jpg');
        } catch (\Exception $exception) {
            Log::info("Failed to use avatar image from LDAP data for user id {$user->id}");
        }
    }
}
