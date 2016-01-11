<?php namespace BookStack\Services;


use BookStack\Exceptions\LdapException;
use Illuminate\Contracts\Auth\Authenticatable;

class LdapService
{

    protected $ldapConnection;

    /**
     * Get the details of a user from LDAP using the given username.
     * User found via configurable user filter.
     * @param $userName
     * @return array|null
     * @throws LdapException
     */
    public function getUserDetails($userName)
    {
        $ldapConnection = $this->getConnection();

        // Find user
        $userFilter = $this->buildFilter(config('services.ldap.user_filter'), ['user' => $userName]);
        $baseDn = config('services.ldap.base_dn');
        $ldapSearch = ldap_search($ldapConnection, $baseDn, $userFilter, ['cn', 'uid', 'dn']);
        $users = ldap_get_entries($ldapConnection, $ldapSearch);
        if ($users['count'] === 0) return null;

        $user = $users[0];
        return [
            'uid'  => $user['uid'][0],
            'name' => $user['cn'][0],
            'dn'   => $user['dn']
        ];
    }

    /**
     * @param Authenticatable $user
     * @param string          $username
     * @param string          $password
     * @return bool
     * @throws LdapException
     */
    public function validateUserCredentials(Authenticatable $user, $username, $password)
    {
        $ldapUser = $this->getUserDetails($username);
        if ($ldapUser === null) return false;
        if ($ldapUser['uid'] !== $user->external_auth_id) return false;

        $ldapConnection = $this->getConnection();
        $ldapBind = @ldap_bind($ldapConnection, $ldapUser['dn'], $password);
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
        $ldapDn = config('services.ldap.dn');
        $ldapPass = config('services.ldap.pass');

        $isAnonymous = ($ldapDn === false || $ldapPass === false);
        if ($isAnonymous) {
            $ldapBind = ldap_bind($connection);
        } else {
            $ldapBind = ldap_bind($connection, $ldapDn, $ldapPass);
        }

        if (!$ldapBind) throw new LdapException('LDAP access failed using ' . $isAnonymous ? ' anonymous bind.' : ' given dn & pass details');
    }

    /**
     * Get the connection to the LDAP server.
     * Creates a new connection if one does not exist.
     * @return resource
     * @throws LdapException
     */
    protected function getConnection()
    {
        if ($this->ldapConnection !== null) return $this->ldapConnection;

        // Check LDAP extension in installed
        if (!function_exists('ldap_connect')) {
            throw new LdapException('LDAP PHP extension not installed');
        }

        // Get port from server string if specified.
        $ldapServer = explode(':', config('services.ldap.server'));
        $ldapConnection = ldap_connect($ldapServer[0], count($ldapServer) > 1 ? $ldapServer[1] : 389);

        if ($ldapConnection === false) {
            throw new LdapException('Cannot connect to ldap server, Initial connection failed');
        }

        // Set any required options
        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3); // TODO - make configurable

        $this->ldapConnection = $ldapConnection;
        return $this->ldapConnection;
    }

    /**
     * Build a filter string by injecting common variables.
     * @param       $filterString
     * @param array $attrs
     * @return string
     */
    protected function buildFilter($filterString, array $attrs)
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $newKey = '${' . $key . '}';
            $newAttrs[$newKey] = $attrText;
        }
        return strtr($filterString, $newAttrs);
    }

}