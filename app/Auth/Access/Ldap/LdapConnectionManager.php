<?php

namespace BookStack\Auth\Access\Ldap;

use BookStack\Exceptions\LdapException;
use BookStack\Exceptions\LdapFailedBindException;
use ErrorException;
use Illuminate\Support\Facades\Log;

class LdapConnectionManager
{
    protected array $connectionCache = [];

    /**
     * Attempt to start and bind to a new LDAP connection as the configured LDAP system user.
     */
    public function startSystemBind(LdapConfig $config): LdapConnection
    {
        // Incoming options are string|false
        $dn = $config->get('dn');
        $pass = $config->get('pass');

        $isAnonymous = ($dn === false || $pass === false);

        try {
            return $this->startBind($dn ?: null, $pass ?: null, $config);
        } catch (LdapFailedBindException $exception) {
            $msg = ($isAnonymous ? trans('errors.ldap_fail_anonymous') : trans('errors.ldap_fail_authed'));
            throw new LdapFailedBindException($msg);
        }
    }

    /**
     * Attempt to start and bind to a new LDAP connection.
     * Will attempt against multiple defined fail-over hosts if set in the provided config.
     *
     * Throws a LdapFailedBindException error if the bind connected but failed.
     * Otherwise, generic LdapException errors would be thrown.
     *
     * @throws LdapException
     */
    public function startBind(?string $dn, ?string $password, LdapConfig $config): LdapConnection
    {
        // Check LDAP extension in installed
        if (!function_exists('ldap_connect') && config('app.env') !== 'testing') {
            throw new LdapException(trans('errors.ldap_extension_not_installed'));
        }

        // Disable certificate verification.
        // This option works globally and must be set before a connection is created.
        if ($config->get('tls_insecure')) {
            LdapConnection::setGlobalOption(LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        }

        $serverDetails = $config->getServers();
        $lastException = null;

        foreach ($serverDetails as $server) {
            try {
                $connection = $this->startServerConnection($server['host'], $server['port'], $config);
            } catch (LdapException $exception) {
                $lastException = $exception;
                continue;
            }

            try {
                $bound = $connection->bind($dn, $password);
                if (!$bound) {
                    throw new LdapFailedBindException('Failed to perform LDAP bind');
                }
            } catch (ErrorException $exception) {
                Log::error('LDAP bind error: ' . $exception->getMessage());
                $lastException = new LdapException('Encountered error during LDAP bind');
                continue;
            }

            $this->connectionCache[$server['host'] . ':' . $server['port']] = $connection;
            return $connection;
        }

        throw $lastException;
    }

    /**
     * Attempt to start a server connection from the provided details.
     * @throws LdapException
     */
    protected function startServerConnection(string $host, int $port, LdapConfig $config): LdapConnection
    {
        if (isset($this->connectionCache[$host . ':' . $port])) {
            return $this->connectionCache[$host . ':' . $port];
        }

        /** @var LdapConnection $ldapConnection */
        $ldapConnection = app()->make(LdapConnection::class, [$host, $port]);

        if (!$ldapConnection) {
            throw new LdapException(trans('errors.ldap_cannot_connect'));
        }

        // Set any required options
        if ($config->get('version')) {
            $ldapConnection->setVersion($config->get('version'));
        }

        // Start and verify TLS if it's enabled
        if ($config->get('start_tls')) {
            try {
                $tlsStarted = $ldapConnection->startTls();
            } catch (ErrorException $exception) {
                $tlsStarted = false;
            }

            if (!$tlsStarted) {
                throw new LdapException('Could not start TLS connection');
            }
        }

        return $ldapConnection;
    }
}
