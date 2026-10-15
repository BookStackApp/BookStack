<?php

namespace BookStack\Access;

use LDAP\Connection;
use LDAP\Result;

/**
 * Class Ldap
 * An object-orientated thin abstraction wrapper for common PHP LDAP functions.
 * Allows the standard LDAP functions to be mocked for testing.
 */
class Ldap
{
    /**
     * Connect to an LDAP server.
     */
    public function connect(string $hostName): Connection|false
    {
        return ldap_connect($hostName);
    }

    /**
     * Set the value of an LDAP option for the given connection.
     */
    public function setOption(Connection|null $ldapConnection, int $option, mixed $value): bool
    {
        return ldap_set_option($ldapConnection, $option, $value);
    }

    /**
     * Start TLS on the given LDAP connection.
     */
    public function startTls(Connection $ldapConnection): bool
    {
        return ldap_start_tls($ldapConnection);
    }

    /**
     * Set the version number for the given LDAP connection.
     */
    public function setVersion(Connection $ldapConnection, int $version): bool
    {
        return $this->setOption($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, $version);
    }

    /**
     * Search the LDAP tree using the provided filter.
     *
     * @return Result|Result[]|false
     */
    public function search(Connection $ldapConnection, string $baseDn, string $filter, array $attributes = [])
    {
        return ldap_search($ldapConnection, $baseDn, $filter, $attributes);
    }

    /**
     * Read an entry from the LDAP tree.
     *
     * @return Result|false
     */
    protected function read(Connection $ldapConnection, string $baseDn, string $filter, array $attributes = [])
    {
        return ldap_read($ldapConnection, $baseDn, $filter, $attributes);
    }

    /**
     * Get entries from an LDAP search result.
     */
    protected function getEntries(Connection $ldapConnection, Result $ldapSearchResult): array|false
    {
        return ldap_get_entries($ldapConnection, $ldapSearchResult);
    }

    /**
     * Read from the LDAP system, and get the entries immediately.
     */
    public function readAndGetEntries(Connection $ldapConnection, string $baseDn, string $filter, array $attributes = []): array|false
    {
        $read = $this->read($ldapConnection, $baseDn, $filter, $attributes);
        return $this->getEntries($ldapConnection, $read);
    }

    /**
     * Search and get entries immediately.
     */
    public function searchAndGetEntries(Connection $ldapConnection, string $baseDn, string $filter, array $attributes = []): array|false
    {
        $search = $this->search($ldapConnection, $baseDn, $filter, $attributes);

        return $this->getEntries($ldapConnection, $search);
    }

    /**
     * Bind to the LDAP directory.
     */
    public function bind(Connection $ldapConnection, ?string $bindRdn = null, ?string $bindPassword = null): bool
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
