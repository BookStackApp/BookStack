<?php

namespace BookStack\Auth\Access\Ldap;

class LdapConfig
{
    /**
     * App provided config array.
     * @var array
     */
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a value from the config.
     */
    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }

    /**
     * Parse the potentially multi-value LDAP server host string and return an array of host/port detail pairs.
     * Multiple hosts are separated with a semicolon, for example: 'ldap.example.com:8069;ldaps://ldap.example.com'
     *
     * @return array<array{host: string, port: int}>
     */
    public function getServers(): array
    {
        $serverStringList = explode(';', $this->get('server'));

        return array_map(fn ($serverStr) => $this->parseSingleServerString($serverStr), $serverStringList);
    }

    /**
     * Parse an LDAP server string and return the host and port for a connection.
     * Is flexible to formats such as 'ldap.example.com:8069' or 'ldaps://ldap.example.com'.
     *
     * @return array{host: string, port: int}
     */
    protected function parseSingleServerString(string $serverString): array
    {
        $serverNameParts = explode(':', trim($serverString));

        // If we have a protocol just return the full string since PHP will ignore a separate port.
        if ($serverNameParts[0] === 'ldaps' || $serverNameParts[0] === 'ldap') {
            return ['host' => $serverString, 'port' => 389];
        }

        // Otherwise, extract the port out
        $hostName = $serverNameParts[0];
        $ldapPort = (count($serverNameParts) > 1) ? intval($serverNameParts[1]) : 389;

        return ['host' => $hostName, 'port' => $ldapPort];
    }
}
