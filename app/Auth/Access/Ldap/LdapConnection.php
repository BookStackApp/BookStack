<?php

namespace BookStack\Auth\Access\Ldap;

use ErrorException;

/**
 * An object-orientated wrapper for core ldap functions,
 * holding an internal connection instance.
 */
class LdapConnection
{
    /**
     * The core ldap connection resource.
     * @var resource
     */
    protected $connection;

    protected string $hostName;
    protected int $port;

    public function __construct(string $hostName, int $port)
    {
        $this->hostName = $hostName;
        $this->port = $port;
        $this->connection = $this->connect();
    }

    /**
     * Start a connection to an LDAP server.
     * Does not actually call out to the external server until an action is performed.
     *
     * @return resource
     */
    protected function connect()
    {
        return ldap_connect($this->hostName, $this->port);
    }

    /**
     * Set the value of a LDAP option for the current connection.
     *
     * @param mixed    $value
     */
    public function setOption(int $option, $value): bool
    {
        return ldap_set_option($this->connection, $option, $value);
    }

    /**
     * Start TLS for this LDAP connection.
     */
    public function startTls(): bool
    {
        return ldap_start_tls($this->connection);
    }

    /**
     * Set the version number for this ldap connection.
     */
    public function setVersion(int $version): bool
    {
        return $this->setOption(LDAP_OPT_PROTOCOL_VERSION, $version);
    }

    /**
     * Search LDAP tree using the provided filter.
     *
     * @return resource
     */
    public function search(string $baseDn, string $filter, array $attributes = null)
    {
        return ldap_search($this->connection, $baseDn, $filter, $attributes);
    }

    /**
     * Get entries from an ldap search result.
     *
     * @param resource $ldapSearchResult
     * @return array|false
     */
    public function getEntries($ldapSearchResult)
    {
        return ldap_get_entries($this->connection, $ldapSearchResult);
    }

    /**
     * Search and get entries immediately.
     *
     * @return array|false
     */
    public function searchAndGetEntries(string $baseDn, string $filter, array $attributes = null)
    {
        $search = $this->search($baseDn, $filter, $attributes);

        return $this->getEntries($search);
    }

    /**
     * Bind to LDAP directory.
     *
     * @throws ErrorException
     */
    public function bind(string $bindRdn = null, string $bindPassword = null): bool
    {
        return ldap_bind($this->connection, $bindRdn, $bindPassword);
    }

    /**
     * Explode a LDAP dn string into an array of components.
     *
     * @return array|false
     */
    public static function explodeDn(string $dn, int $withAttrib)
    {
        return ldap_explode_dn($dn, $withAttrib);
    }

    /**
     * Escape a string for use in an LDAP filter.
     */
    public static function escape(string $value, string $ignore = '', int $flags = 0): string
    {
        return ldap_escape($value, $ignore, $flags);
    }

    /**
     * Set a non-connection-specific LDAP option.
     * @param mixed $value
     */
    public static function setGlobalOption(int $option, $value): bool
    {
        return ldap_set_option(null, $option, $value);
    }
}
