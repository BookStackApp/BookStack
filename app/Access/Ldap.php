<?php

namespace BookStack\Access;

/**
 * Class Ldap
 * An object-orientated thin abstraction wrapper for common PHP LDAP functions.
 * Allows the standard LDAP functions to be mocked for testing.
 */
class Ldap
{
    /**
     * Connect to an LDAP server.
     *
     * @return resource|\LDAP\Connection|false
     */
    public function connect(string $hostName)
    {
        return ldap_connect($hostName);
    }

    /**
     * Set the value of an LDAP option for the given connection.
     *
     * @param resource|\LDAP\Connection|null $ldapConnection
     */
    public function setOption($ldapConnection, int $option, mixed $value): bool
    {
        return ldap_set_option($ldapConnection, $option, $value);
    }

    /**
     * Start TLS on the given LDAP connection.
     */
    public function startTls($ldapConnection): bool
    {
        return ldap_start_tls($ldapConnection);
    }

    /**
     * Set the version number for the given LDAP connection.
     *
     * @param resource|\LDAP\Connection $ldapConnection
     */
    public function setVersion($ldapConnection, int $version): bool
    {
        return $this->setOption($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, $version);
    }

    /**
     * Search LDAP tree using the provided filter.
     *
     * @param resource|\LDAP\Connection   $ldapConnection
     *
     * @return resource|\LDAP\Result
     */
    public function search($ldapConnection, string $baseDn, string $filter, array $attributes = null)
    {
        return ldap_search($ldapConnection, $baseDn, $filter, $attributes);
    }

    /**
     * Get entries from an LDAP search result.
     *
     * @param resource|\LDAP\Connection $ldapConnection
     * @param resource|\LDAP\Result $ldapSearchResult
     */
    public function getEntries($ldapConnection, $ldapSearchResult): array|false
    {
        return ldap_get_entries($ldapConnection, $ldapSearchResult);
    }

    /**
     * Search and get entries immediately.
     *
     * @param resource|\LDAP\Connection   $ldapConnection
     */
    public function searchAndGetEntries($ldapConnection, string $baseDn, string $filter, array $attributes = null): array|false
    {
        $search = $this->search($ldapConnection, $baseDn, $filter, $attributes);

        return $this->getEntries($ldapConnection, $search);
    }

    /**
     * Bind to LDAP directory.
     *
     * @param resource|\LDAP\Connection $ldapConnection
     */
    public function bind($ldapConnection, string $bindRdn = null, string $bindPassword = null): bool
    {
        return ldap_bind($ldapConnection, $bindRdn, $bindPassword);
    }

    /**
     * Explode an LDAP dn string into an array of components.
     */
    public function explodeDn(string $dn, int $withAttrib): array|false
    {
        return ldap_explode_dn($dn, $withAttrib);
    }

    /**
     * Escape a string for use in an LDAP filter.
     */
    public function escape(string $value, string $ignore = '', int $flags = 0): string
    {
        return ldap_escape($value, $ignore, $flags);
    }
}
