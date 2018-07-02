<?php namespace BookStack\Services;

use BookStack\Exceptions\LdapException;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class LdapService
 * Handles any app-specific LDAP tasks.
 * @package BookStack\Services
 */
class LdapService
{

	protected $ldap;
	protected $ldapConnection;
	protected $config;

	/**
	 * LdapService constructor.
	 * @param Ldap $ldap
	 */
	public function __construct(Ldap $ldap)
	{
		$this->ldap = $ldap;
		$this->config = config('services.ldap');
	}

	/**
	 * Search for attributes for a specific user on the ldap
	 * @param string $userName
	 * @param array $attributes
	 * @return null|array
	 * @throws LdapException
	 */
	private function getUserWithAttributes($userName,$attributes)
	{
		$ldapConnection = $this->getConnection();
		$this->bindSystemUser($ldapConnection);

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
	 * @param $userName
	 * @return array|null
	 * @throws LdapException
	 */
	public function getUserDetails($userName)
	{
		$emailAttr = $this->config['email_attribute'];
		$user = $this->getUserWithAttributes($userName, ['cn', 'uid', 'dn', $emailAttr]);

		if ($user === null) {
			return null;
		}

		return [
			'uid'   => (isset($user['uid'])) ? $user['uid'][0] : $user['dn'],
			'name'  => $user['cn'][0],
			'dn'    => $user['dn'],
			'email' => (isset($user[$emailAttr])) ? (is_array($user[$emailAttr]) ? $user[$emailAttr][0] : $user[$emailAttr]) : null
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
		if ($ldapUser === null) {
			return false;
		}
		if ($ldapUser['uid'] !== $user->external_auth_id) {
			return false;
		}

		$ldapConnection = $this->getConnection();
		try {
			$ldapBind = $this->ldap->bind($ldapConnection, $ldapUser['dn'], $password);
		} catch (\ErrorException $e) {
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

		// Get port from server string and protocol if specified.
		$ldapServer = explode(':', $this->config['server']);
		$hasProtocol = preg_match('/^ldaps{0,1}\:\/\//', $this->config['server']) === 1;
		if (!$hasProtocol) {
			array_unshift($ldapServer, '');
		}
		$hostName = $ldapServer[0] . ($hasProtocol?':':'') . $ldapServer[1];
		$defaultPort = $ldapServer[0] === 'ldaps' ? 636 : 389;
		$ldapConnection = $this->ldap->connect($hostName, count($ldapServer) > 2 ? intval($ldapServer[2]) : $defaultPort);

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
	 * Build a filter string by injecting common variables.
	 * @param string $filterString
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

	/**
	 * Get the groups a user is a part of on ldap
	 * @param string $userName
	 * @return array|null
	 */
	public function getUserGroups($userName)
	{
		$groupsAttr = $this->config['group_attribute'];
		$user = $this->getUserWithAttributes($userName, [$groupsAttr]);

		if ($user === null) {
			return null;
		}

		$userGroups = $this->groupFilter($user);
		$userGroups = $this->getGroupsRecursive($userGroups,[]);
		return $userGroups;
	}

	/**
	 * Get the parent groups of an array of groups
	 * @param array $groupsArray
	 * @param array $checked
	 * @return array
	 */
	private function getGroupsRecursive($groupsArray,$checked) {
		$groups_to_add = [];
		foreach ($groupsArray as $groupName) {
			if (in_array($groupName,$checked)) continue;

			$groupsToAdd = $this->getGroupGroups($groupName);
			$groups_to_add = array_merge($groups_to_add,$groupsToAdd);
			$checked[] = $groupName;
		}
		$groupsArray = array_unique(array_merge($groupsArray,$groups_to_add), SORT_REGULAR);

		if (!empty($groups_to_add)) {
			return $this->getGroupsRecursive($groupsArray,$checked);
		} else {
			return $groupsArray;
		}
	}

	/**
	 * Get the parent groups of a single group
	 * @param string $groupName
	 * @return array
	 */
	private function getGroupGroups($groupName)
	{
		$ldapConnection = $this->getConnection();
		$this->bindSystemUser($ldapConnection);

		$followReferrals = $this->config['follow_referrals'] ? 1 : 0;
		$this->ldap->setOption($ldapConnection, LDAP_OPT_REFERRALS, $followReferrals);

		$baseDn = $this->config['base_dn'];
		$groupsAttr = strtolower($this->config['group_attribute']);

		$groups = $this->ldap->searchAndGetEntries($ldapConnection, $baseDn, 'CN='.$groupName, [$groupsAttr]);
		if ($groups['count'] === 0) {
			return [];
		}

		$groupGroups = $this->groupFilter($groups[0]);
		return $groupGroups;
	}

	/**
	 * Filter out LDAP CN and DN language in a ldap search return
	 * Gets the base CN (common name) of the string
	 * @param string $ldapSearchReturn
	 * @return array
	 */
	protected function groupFilter($ldapSearchReturn)
	{
		$groupsAttr = strtolower($this->config['group_attribute']);
		$ldapGroups = [];
		$count = 0;
		if (isset($ldapSearchReturn[$groupsAttr]['count'])) $count = (int) $ldapSearchReturn[$groupsAttr]['count'];
		for ($i=0;$i<$count;$i++) {
			$dnComponents = ldap_explode_dn($ldapSearchReturn[$groupsAttr][$i],1);
			if (!in_array($dnComponents[0],$ldapGroups)) {
				$ldapGroups[] = $dnComponents[0];
			}
		}
		return $ldapGroups;
	}

}