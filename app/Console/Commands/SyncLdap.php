<?php

namespace BookStack\Console\Commands;

use Auth;
use Hash;
use Str;
use Log;
use BookStack\Auth\Access\Ldap;
use Illuminate\Console\Command;
use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Auth\Access\Guards\LdapSessionGuard;


class SyncLdap extends Command
{
    /**
     * Used to sync users with LDAP server on a per request basis
     *
     * Use case:
     * SSO logins need user account to exist/roles synced prior to login
     *
     * .env setting examples:
     * LDAP_SYNC_USER_FILTER=(&(memberOf=CN=app-bookstack,OU=groups,OU=Access,DC=example.com))
     *  the origin to sync
     * LDAP_SYNC_USER_RECURSIVE_GROUPS=true
     *  if there's nested groups, pull those in too
     * LDAP_SYNC_EXCLUDE_EMAIL="admin@example.com,testaccount@example.com"
     *  comma seperated list of strings
     *  allow for email exclusions to be defined to skip adding the accounts
     *  uses string matching, so can also block wildcards (ie: "-disabled")
     */

    public $users = array();
    public $users_checked = array();
    public $cn_checked = array(); // list of groups that have already been fetched
    public $groups = array();
    public $sync_user_filter;
    public $sync_user_recursive_groups;
    public $sync_user_exclude_email;
    public $id_attribute;
    public $LDAP;
    public $ldap;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:syncldap {filter? : Optional LDAP filter for initial group pull}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batch syncs LDAP users and groups';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $config = config('services.ldap');
        $this->id_attribute = $config['id_attribute'];
        $this->sync_user_filter = $config['sync_user_filter'];
        $this->sync_user_recursive_groups = $config['sync_user_recursive_groups'];
        $this->sync_user_exclude_email = $config['sync_user_exclude_email'];
        $this->LDAP = new Ldap();
        $this->ldap = new LdapService($this->LDAP);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('filter')) {
            $this->sync_user_filter = $this->argument('filter');
        }

        Log::info("[syncldap] starting...");
        if (config('auth.method') !== 'ldap') {
            dd("Must be using ldap for auth method");
        }

        Log::info("[syncldap] retrieving users");
        // get all users with the LDAP_SYNC_USER_FILTER
        //   [Use that to limit specific group/dn users to be auto-added]
        $data = $this->ldap->getAllUsers();

        // if there's a nested group/cn found in the users returned, recurse those as well
        //   [recursion enabled/disabled with LDAP_SYNC_USER_RECURSIVE_GROUPS]
        $this->checkDnForUserRecursive($data);

        Log::info("[syncldap] retrieved " . count($this->users) . " in " . count($this->cn_checked) . " groups");
        $usercount = 1;

        // check if there's any strings to exclude from emails
        if ($this->sync_user_exclude_email) {
            $email_excludes = explode(',', $this->sync_user_exclude_email);
        } else {
            $email_excludes = false;
        }

        // run thru the returned list of all user records
        foreach ($this->users as $userdata) {
            // did we find an id_attribute?
            if (isset($userdata[$this->id_attribute][0])) {
                $user_id = $userdata[$this->id_attribute][0];

                Log::info("[syncldap] fetching user details for " . $user_id . "(" . $usercount . "/" . count($this->users) . ")");

                // fetch the user details and check if they exist
                $ldapUserDetails = $this->ldap->getUserDetails($user_id);

                // check if email in excludes array
                $exclude = false;
                if (is_array($email_excludes)) {
                    foreach ($email_excludes as $exclude_string) {
                        if (strpos($ldapUserDetails["email"], trim($exclude_string)) !== false) {
                            $exclude = true;
                        }
                    }
                }

                if (!$exclude) {
                    $user = User::where('email', '=', $ldapUserDetails["email"])->first();
                    if ($user === null) {
                        // user doesn't exist
                        $user = new User();
                        $user->password = Hash::make(Str::random(32));
                        $user->email = $ldapUserDetails['email'];
                        $user->name = $ldapUserDetails['name'];
                        $user->external_auth_id = $user_id;
                        $user->save();
                    } else {
                        // user exists but this is the first time they're being paired to LDAP
                        //   so set the external_auth_id
                        if (is_null($user->external_auth_id)) {
                            $user->email = $user_id;
                            $user->save();
                        }
                    }
                    // sync the user groups to bookstack groups
                    Log::info("[syncldap] syncing groups for " . $user_id . "(" . $usercount . "/" . count($this->users) . ")");

                    $this->ldap->syncGroups($user, $user_id);
                } else {
                    Log::info("[syncldap] user email in exclude list " . $user_id . " [" . $ldapUserDetails["email"] . " - " . $this->sync_user_exclude_email . "] (" . $usercount . "/" . count($this->users) . ")");
                }



                $usercount++;
            }
        }
    }

    private function checkDnForUserRecursive($data)
    {
        // passes in the results of LdapService->getAllUsers (uses LDAP_SYNC_USER_FILTER)
        // needs to recurse and check for all nested groups
        //  nested recursion can be enabled/disabled with LDAP_SYNC_USER_RECURSIVE_GROUPS
        for ($i = 0; $i < count($data); $i++) {
            if (isset($data[$i][$this->id_attribute][0])) {
                $userdata = $data[$i][$this->id_attribute][0];
                if (!in_array($userdata, $this->users_checked)) {
                    $this->users_checked[] = $userdata;
                    $this->users[] = $data[$i];
                }
            } elseif ((isset($data[$i]["dn"]) && $this->sync_user_recursive_groups)) {
                // found a nested group record [dn => cn=GROUP ] for recursion
                $new_dn = $data[$i]["dn"];
                foreach ($this->LDAP->explodeDn($new_dn, 0) as $attribute) {
                    // pop out the cn record for the group name
                    $pieces = explode("=", $attribute);
                    if (strtolower($pieces[0]) == 'cn') {
                        // was the group already checked?
                        if (!in_array($pieces[1], $this->cn_checked)) {
                            $filter = "(memberOf=" . $new_dn . ")";
                            $this->cn_checked[] = $pieces[1];
                            $newdata = $this->ldap->getAllUsers($filter);
                            $this->checkDnForUserRecursive($newdata);
                        }
                    }
                }
            }
        }
    }
}
