<?php namespace BookStack\Services;


use BookStack\Exceptions\LdapException;

class LdapService
{

    public function getUserDetails($userName)
    {

        if(!function_exists('ldap_connect')) {
            throw new LdapException('LDAP PHP extension not installed');
        }


        $ldapServer = explode(':', config('services.ldap.server'));
        $ldapConnection = ldap_connect($ldapServer[0], count($ldapServer) > 1 ? $ldapServer[1] : 389);

        if ($ldapConnection === false) {
            throw new LdapException('Cannot connect to ldap server, Initial connection failed');
        }

        // Options

        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3); // TODO - make configurable

        $ldapDn = config('services.ldap.dn');
        $ldapPass = config('services.ldap.pass');
        $isAnonymous = ($ldapDn === false || $ldapPass === false);
        if ($isAnonymous) {
            $ldapBind = ldap_bind($ldapConnection);
        } else {
            $ldapBind = ldap_bind($ldapConnection, $ldapDn, $ldapPass);
        }

        if (!$ldapBind) throw new LdapException('LDAP access failed using ' . $isAnonymous ? ' anonymous bind.' : ' given dn & pass details');

        // Find user
        $userFilter = $this->buildFilter(config('services.ldap.user_filter'), ['user' => $userName]);
        //dd($userFilter);
        $baseDn = config('services.ldap.base_dn');
        $ldapSearch = ldap_search($ldapConnection, $baseDn, $userFilter);
        $users = ldap_get_entries($ldapConnection, $ldapSearch);

        dd($users);
    }


    private function buildFilter($filterString, $attrs)
    {
        $newAttrs = [];
        foreach ($attrs as $key => $attrText) {
            $newKey = '${'.$key.'}';
            $newAttrs[$newKey] = $attrText;
        }
        return strtr($filterString, $newAttrs);
    }

}