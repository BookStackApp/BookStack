<?php namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\LdapException;
use ErrorException;

/**
 * Class LdapService
 * Handles any app-specific LDAP tasks.
 */
class LdapService extends ExternalAuthService
{

    protected $ldap;
    protected $ldapConnection;
    protected $config;
    protected $enabled;

    /**
     * LdapService constructor.
     */
    public function __construct(Ldap $ldap)
    {
        $this->ldap = $ldap;
        $this->config = config('services.ldap');
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
     * Search for attributes for a specific user on the ldap.
     * @throws LdapException
     */
    private function getUserWithAttributes(string $userName, array $attributes): ?array
    {
        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

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
     * @throws LdapException
     */
    public function getUserDetails(string $userName): ?array
    {
        $idAttr = $this->config['id_attribute'];
        $emailAttr = $this->config['email_attribute'];
        $displayNameAttr = $this->config['display_name_attribute'];
        $thumbnailAttr = $this->config['thumbnail_attribute'];

        $user = $this->getUserWithAttributes($userName, ['cn', 'dn', $idAttr, $emailAttr, $displayNameAttr]);

        if ($user === null) {
            return null;
        }

        $userCn = $this->getUserResponseProperty($user, 'cn', null);
        $formatted = [
            'uid'   => $this->getUserResponseProperty($user, $idAttr, $user['dn']),
            'name'  => $this->getUserResponseProperty($user, $displayNameAttr, $userCn),
            'dn'    => $user['dn'],
            'email' => $this->getUserResponseProperty($user, $emailAttr, null),
            'avatar'=> $this->getUserResponseProperty($user, $thumbnailAttr, null),
        ];

        if ($this->config['dump_user_details']) {
            throw new JsonDebugException([
                'details_from_ldap' => $user,
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
     * @throws LdapException
     */
    public function validateUserCredentials(?array $ldapUserDetails, string $password): bool
    {
        if (is_null($ldapUserDetails)) {
            return false;
        }

        $ldapConnection = $this->getConnection();
        try {
            $ldapBind = $this->ldap->bind($ldapConnection, $ldapUserDetails['dn'], $password);
        } catch (ErrorException $e) {
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

         // Check if TLS_INSECURE is set. The handle is set to NULL due to the nature of
         // the LDAP_OPT_X_TLS_REQUIRE_CERT option. It can only be set globally and not per handle.
        if ($this->config['tls_insecure']) {
            $this->ldap->setOption(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        }

        $serverDetails = $this->parseServerString($this->config['server']);
        $ldapConnection = $this->ldap->connect($serverDetails['host'], $serverDetails['port']);

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
     * Parse a LDAP server string and return the host and port for a connection.
     * Is flexible to formats such as 'ldap.example.com:8069' or 'ldaps://ldap.example.com'.
     */
    protected function parseServerString(string $serverString): array
    {
        $serverNameParts = explode(':', $serverString);

        // If we have a protocol just return the full string since PHP will ignore a separate port.
        if ($serverNameParts[0] === 'ldaps' || $serverNameParts[0] === 'ldap') {
            return ['host' => $serverString, 'port' => 389];
        }

        // Otherwise, extract the port out
        $hostName = $serverNameParts[0];
        $ldapPort = (count($serverNameParts) > 1) ? intval($serverNameParts[1]) : 389;
        return ['host' => $hostName, 'port' => $ldapPort];
    }

    /**
     * Build a filter string by injecting common variables.
     */
    protected function buildFilter(string $filterString, array $attrs): string
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $newKey = '${' . $key . '}';
            $newAttrs[$newKey] = $this->ldap->escape($attrText);
        }
        return strtr($filterString, $newAttrs);
    }

    /**
     * Get the groups a user is a part of on ldap.
     * @throws LdapException
     */
    public function getUserGroups(string $userName): array
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
     * Get the parent groups of an array of groups.
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
     * @throws LdapException
     */
    private function getGroupGroups(string $groupName): array
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
            $count = (int)$userGroupSearchResponse[$groupsAttr]['count'];
        }

        for ($i = 0; $i < $count; $i++) {
            $dnComponents = $this->ldap->explodeDn($userGroupSearchResponse[$groupsAttr][$i], 1);
            if (!in_array($dnComponents[0], $ldapGroups)) {
                $ldapGroups[] = $dnComponents[0];
            }
        }

        return $ldapGroups;
    }

    /**
     * Sync the LDAP groups to the user roles for the current user.
     * @throws LdapException
     */
    public function syncGroups(User $user, string $username)
    {
        $userLdapGroups = $this->getUserGroups($username);
        $this->syncWithGroups($user, $userLdapGroups);
    }
}
