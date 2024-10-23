<?php

namespace BookStack\Access;

use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\LdapException;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\User;
use ErrorException;
use Illuminate\Support\Facades\Log;

/**
 * Class LdapService
 * Handles any app-specific LDAP tasks.
 */
class LdapService
{
    /**
     * @var resource|\LDAP\Connection
     */
    protected $ldapConnection;

    protected array $config;
    protected bool $enabled;

    public function __construct(
        protected Ldap $ldap,
        protected UserAvatars $userAvatars,
        protected GroupSyncService $groupSyncService
    ) {
        $this->config = config('services.ldap');
        $this->enabled = config('auth.method') === 'ldap';
    }

    /**
     * Check if groups should be synced.
     */
    public function shouldSyncGroups(): bool
    {
        return $this->enabled && $this->config['user_to_groups'] !== false;
    }

    /**
     * Search for attributes for a specific user on the ldap.
     *
     * @throws LdapException
     */
    private function getUserWithAttributes(string $userName, array $attributes): ?array
    {
        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

        // Clean attributes
        foreach ($attributes as $index => $attribute) {
            if (str_starts_with($attribute, 'BIN;')) {
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
     *
     * @throws LdapException|JsonDebugException
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
        $isBinary = str_starts_with($propertyKey, 'BIN;');
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
     *
     * @param resource|\LDAP\Connection $connection
     *
     * @throws LdapException
     */
    protected function bindSystemUser($connection): void
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
     *
     * @throws LdapException
     *
     * @return resource|\LDAP\Connection
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

        // Disable certificate verification.
        // This option works globally and must be set before a connection is created.
        if ($this->config['tls_insecure']) {
            $this->ldap->setOption(null, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        }

        // Configure any user-provided CA cert files for LDAP.
        // This option works globally and must be set before a connection is created.
        if ($this->config['tls_ca_cert']) {
            $this->configureTlsCaCerts($this->config['tls_ca_cert']);
        }

        $ldapHost = $this->parseServerString($this->config['server']);
        $ldapConnection = $this->ldap->connect($ldapHost);

        if ($ldapConnection === false) {
            throw new LdapException(trans('errors.ldap_cannot_connect'));
        }

        // Set any required options
        if ($this->config['version']) {
            $this->ldap->setVersion($ldapConnection, $this->config['version']);
        }

        // Start and verify TLS if it's enabled
        if ($this->config['start_tls']) {
            try {
                $started = $this->ldap->startTls($ldapConnection);
            } catch (\Exception $exception) {
                $error = $exception->getMessage() . ' :: ' . ldap_error($ldapConnection);
                ldap_get_option($ldapConnection, LDAP_OPT_DIAGNOSTIC_MESSAGE, $detail);
                Log::info("LDAP STARTTLS failure: {$error} {$detail}");
                throw new LdapException('Could not start TLS connection. Further details in the application log.');
            }
            if (!$started) {
                throw new LdapException('Could not start TLS connection');
            }
        }

        $this->ldapConnection = $ldapConnection;

        return $this->ldapConnection;
    }

    /**
     * Configure TLS CA certs globally for ldap use.
     * This will detect if the given path is a directory or file, and set the relevant
     * LDAP TLS options appropriately otherwise throw an exception if no file/folder found.
     *
     * Note: When using a folder, certificates are expected to be correctly named by hash
     * which can be done via the c_rehash utility.
     *
     * @throws LdapException
     */
    protected function configureTlsCaCerts(string $caCertPath): void
    {
        $errMessage = "Provided path [{$caCertPath}] for LDAP TLS CA certs could not be resolved to an existing location";
        $path = realpath($caCertPath);
        if ($path === false) {
            throw new LdapException($errMessage);
        }

        if (is_dir($path)) {
            $this->ldap->setOption(null, LDAP_OPT_X_TLS_CACERTDIR, $path);
        } else if (is_file($path)) {
            $this->ldap->setOption(null, LDAP_OPT_X_TLS_CACERTFILE, $path);
        } else {
            throw new LdapException($errMessage);
        }
    }

    /**
     * Parse an LDAP server string and return the host suitable for a connection.
     * Is flexible to formats such as 'ldap.example.com:8069' or 'ldaps://ldap.example.com'.
     */
    protected function parseServerString(string $serverString): string
    {
        if (str_starts_with($serverString, 'ldaps://') || str_starts_with($serverString, 'ldap://')) {
            return $serverString;
        }

        return "ldap://{$serverString}";
    }

    /**
     * Build a filter string by injecting common variables.
     * Both "${var}" and "{var}" style placeholders are supported.
     * Dollar based are old format but supported for compatibility.
     */
    protected function buildFilter(string $filterString, array $attrs): string
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $escapedText = $this->ldap->escape($attrText);
            $oldVarKey = '${' . $key . '}';
            $newVarKey = '{' . $key . '}';
            $newAttrs[$oldVarKey] = $escapedText;
            $newAttrs[$newVarKey] = $escapedText;
        }

        return strtr($filterString, $newAttrs);
    }

    /**
     * Get the groups a user is a part of on ldap.
     *
     * @throws JsonDebugException
     * @throws LdapException
     */
    public function getUserGroups(string $userName): array
    {
        if ($this->config['group_style'] == 'RFC2307') {
            return $this->getUserGroupsRFC2307($userName);
        } else {
            return $this->getUserGroupsRFC2307bis($userName);
        }
    }

    /**
     * Get the groups a user is a part of on ldap.
     * Assuming RFC2307 style group memberships, i.e.
     * memberUid in groups with extra query required
     * to resolve groups
     *
     * @throws LdapException
     * @throws JsonDebugException
     */
    public function getUserGroupsRFC2307(string $userName): array
    {
        $memberAttr = $this->config['group_member_attribute'];
        $additionalGroupFilters = $this->buildFilter($this->config['group_filter'], ['user' => $userName]);
        $baseDn = $this->config['group_base_dn'];

        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

        $filter = sprintf("(&(%s=%s)%s)", $memberAttr, $userName, $additionalGroupFilters);

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $this->ldap->setOption($ldapConnection, LDAP_OPT_REFERRALS, $followReferrals);
        $groups = $this->ldap->searchAndGetEntries($ldapConnection, $baseDn, $filter, ["CN"]);
        if ($groups['count'] === 0) {
            return [];
        }

        $groupDNs = $this->extractGroupDNsFromSearchResponse($groups);
        $formattedGroups = $this->extractGroupNamesFromLdapGroupDns($groupDNs);

        if ($this->config['dump_user_groups']) {
            throw new JsonDebugException([
                'username'                     => $userName,
                'group_details_from_ldap'            => $groups,
                'parsed_resulting_group_names' => $formattedGroups,
            ]);
        }

        return $formattedGroups;
    }

    /**
     * Get the groups a user is a part of on ldap.
     * Assuming RFC2307bis style group memberships, i.e.
     * memberOf and member Attributes in User and Group
     * respectively
     *
     * @throws LdapException
     * @throws JsonDebugException
     */
    public function getUserGroupsRFC2307bis(string $userName): array
    {
        $groupsAttr = $this->config['group_attribute'];
        $user = $this->getUserWithAttributes($userName, [$groupsAttr]);

        if ($user === null) {
            return [];
        }

        $userGroups = $this->extractGroupsFromSearchResponseEntry($user);
        $allGroups = $this->getGroupsRecursive($userGroups, []);
        $formattedGroups = $this->extractGroupNamesFromLdapGroupDns($allGroups);

        if ($this->config['dump_user_groups']) {
            throw new JsonDebugException([
                'details_from_ldap'            => $user,
                'parsed_direct_user_groups'    => $userGroups,
                'parsed_recursive_user_groups' => $allGroups,
                'parsed_resulting_group_names' => $formattedGroups,
            ]);
        }

        return $formattedGroups;
    }

    protected function extractGroupNamesFromLdapGroupDns(array $groupDNs): array
    {
        $names = [];

        foreach ($groupDNs as $groupDN) {
            $exploded = $this->ldap->explodeDn($groupDN, 1);
            if ($exploded !== false && count($exploded) > 0) {
                $names[] = $exploded[0];
            }
        }

        return array_unique($names);
    }

    /**
     * Build an array of all relevant groups DNs after recursively scanning
     * across parents of the groups given.
     *
     * @throws LdapException
     */
    protected function getGroupsRecursive(array $groupDNs, array $checked): array
    {
        $groupsToAdd = [];
        foreach ($groupDNs as $groupDN) {
            if (in_array($groupDN, $checked)) {
                continue;
            }

            $parentGroups = $this->getParentsOfGroup($groupDN);
            $groupsToAdd = array_merge($groupsToAdd, $parentGroups);
            $checked[] = $groupDN;
        }

        $uniqueDNs = array_unique(array_merge($groupDNs, $groupsToAdd), SORT_REGULAR);

        if (empty($groupsToAdd)) {
            return $uniqueDNs;
        }

        return $this->getGroupsRecursive($uniqueDNs, $checked);
    }

    /**
     * @throws LdapException
     */
    protected function getParentsOfGroup(string $groupDN): array
    {
        $groupsAttr = strtolower($this->config['group_attribute']);
        $ldapConnection = $this->getConnection();
        $this->bindSystemUser($ldapConnection);

        $followReferrals = $this->config['follow_referrals'] ? 1 : 0;
        $this->ldap->setOption($ldapConnection, LDAP_OPT_REFERRALS, $followReferrals);
        $read = $this->ldap->read($ldapConnection, $groupDN, '(objectClass=*)', [$groupsAttr]);
        $results = $this->ldap->getEntries($ldapConnection, $read);
        if ($results['count'] === 0) {
            return [];
        }

        return $this->extractGroupsFromSearchResponseEntry($results[0]);
    }

    /**
     * Extract an array of group DN values from the given LDAP search response entry
     */
    protected function extractGroupsFromSearchResponseEntry(array $ldapEntry): array
    {
        $groupsAttr = strtolower($this->config['group_attribute']);
        $groupDNs = [];
        $count = 0;

        if (isset($ldapEntry[$groupsAttr]['count'])) {
            $count = (int) $ldapEntry[$groupsAttr]['count'];
        }

        for ($i = 0; $i < $count; $i++) {
            $dn = $ldapEntry[$groupsAttr][$i];
            if (!in_array($dn, $groupDNs)) {
                $groupDNs[] = $dn;
            }
        }

        return $groupDNs;
    }

    /**
     * Extract an array of group DN values from the given LDAP search response
     */
    protected function extractGroupDNsFromSearchResponse(array $searchResult): array
    {
        $groupDNs = [];
        $count = 0;

        if (isset($searchResult['count'])) {
            $count = (int) $searchResult['count'];
        }

        for ($i = 0; $i < $count; $i++) {
            $dn = $searchResult[$i]['dn'];
            if (!in_array($dn, $groupDNs)) {
                $groupDNs[] = $dn;
            }
        }

        return $groupDNs;
    }

    /**
     * Sync the LDAP groups to the user roles for the current user.
     *
     * @throws LdapException
     * @throws JsonDebugException
     */
    public function syncGroups(User $user, string $username): void
    {
        $userLdapGroups = $this->getUserGroups($username);
        $this->groupSyncService->syncUserWithFoundGroups($user, $userLdapGroups, $this->config['remove_from_groups']);
    }

    /**
     * Save and attach an avatar image, if found in the ldap details, and attach
     * to the given user model.
     */
    public function saveAndAttachAvatar(User $user, array $ldapUserDetails): void
    {
        if (is_null(config('services.ldap.thumbnail_attribute')) || is_null($ldapUserDetails['avatar'])) {
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
