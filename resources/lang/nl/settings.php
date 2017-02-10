<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Instellingen',
    'settings_save' => 'Instellingen Opslaan',
    'settings_save_success' => 'Instellingen Opgeslagen',

    /**
     * App settings
     */

    'app_settings' => 'App Instellingen',
    'app_name' => 'Applicatienaam',
    'app_name_desc' => 'De applicatienaam wordt in e-mails in in de header weergegeven.',
    'app_name_header' => 'Applicatienaam in de header weergeven?',
    'app_public_viewing' => 'Publieke bewerkingen toestaan?',
    'app_secure_images' => 'Beter beveiligide afbeeldingen gebruiken?',
    'app_secure_images_desc' => 'Omwille van de performance zijn alle afbeeldingen publiek toegankelijk. Zorg ervoor dat je de \'directory index\' niet hebt ingeschakeld.',
    'app_editor' => 'Pagina Bewerker',
    'app_editor_desc' => 'Selecteer welke editor je wilt gebruiken.',
    'app_custom_html' => 'Speciale HTML in de head',
    'app_custom_html_desc' => 'Alles wat je hier toevoegd wordt in de <head> sectie van elke pagina meengenomen. Dit kun je bijvoorbeeld voor analytics gebruiken.',
    'app_logo' => 'Applicatielogo',
    'app_logo_desc' => 'De afbeelding moet 43px hoog zijn. <br>Grotere afbeeldingen worden geschaald.',
    'app_primary_color' => 'Applicatie hoofdkleur',
    'app_primary_color_desc' => 'Geef een hexadecimale waarde. <br>Als je niks invult wordt de standaardkleur gebruikt.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Registratieinstellingen',
    'reg_allow' => 'Registratie toestaan?',
    'reg_default_role' => 'Standaard rol na registratie',
    'reg_confirm_email' => 'E-mailbevesting vereist?',
    'reg_confirm_email_desc' => 'If domain restriction is used then email confirmation will be required and the below value will be ignored.',
    'reg_confirm_restrict_domain' => 'Restrict registration to domain',
    'reg_confirm_restrict_domain_desc' => 'Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application. <br> Note that users will be able to change their email addresses after successful registration.',
    'reg_confirm_restrict_domain_placeholder' => 'No restriction set',

    /**
     * Role settings
     */

    'roles' => 'Roles',
    'role_user_roles' => 'User Roles',
    'role_create' => 'Create New Role',
    'role_create_success' => 'Role successfully created',
    'role_delete' => 'Delete Role',
    'role_delete_confirm' => 'This will delete the role with the name \':roleName\'.',
    'role_delete_users_assigned' => 'This role has :userCount users assigned to it. If you would like to migrate the users from this role select a new role below.',
    'role_delete_no_migration' => "Don't migrate users",
    'role_delete_sure' => 'Are you sure you want to delete this role?',
    'role_delete_success' => 'Role successfully deleted',
    'role_edit' => 'Edit Role',
    'role_details' => 'Role Details',
    'role_name' => 'Role Name',
    'role_desc' => 'Short Description of Role',
    'role_system' => 'System Permissions',
    'role_manage_users' => 'Manage users',
    'role_manage_roles' => 'Manage roles & role permissions',
    'role_manage_entity_permissions' => 'Manage all book, chapter & page permissions',
    'role_manage_own_entity_permissions' => 'Manage permissions on own book, chapter & pages',
    'role_manage_settings' => 'Manage app settings',
    'role_asset' => 'Asset Permissions',
    'role_asset_desc' => 'These permissions control default access to the assets within the system. Permissions on Books, Chapters and Pages will override these permissions.',
    'role_all' => 'All',
    'role_own' => 'Own',
    'role_controlled_by_asset' => 'Controlled by the asset they are uploaded to',
    'role_save' => 'Save Role',
    'role_update_success' => 'Role successfully updated',
    'role_users' => 'Users in this role',
    'role_users_none' => 'No users are currently assigned to this role',

    /**
     * Users
     */

    'users' => 'Users',
    'user_profile' => 'User Profile',
    'users_add_new' => 'Add New User',
    'users_search' => 'Search Users',
    'users_role' => 'User Roles',
    'users_external_auth_id' => 'External Authentication ID',
    'users_password_warning' => 'Only fill the below if you would like to change your password:',
    'users_system_public' => 'This user represents any guest users that visit your instance. It cannot be used to log in but is assigned automatically.',
    'users_delete' => 'Delete User',
    'users_delete_named' => 'Delete user :userName',
    'users_delete_warning' => 'This will fully delete this user with the name \':userName\' from the system.',
    'users_delete_confirm' => 'Are you sure you want to delete this user?',
    'users_delete_success' => 'Users successfully removed',
    'users_edit' => 'Edit User',
    'users_edit_profile' => 'Edit Profile',
    'users_edit_success' => 'User successfully updated',
    'users_avatar' => 'User Avatar',
    'users_avatar_desc' => 'This image should be approx 256px square.',
    'users_preferred_language' => 'Preferred Language',
    'users_social_accounts' => 'Social Accounts',
    'users_social_accounts_info' => 'Here you can connect your other accounts for quicker and easier login. Disconnecting an account here does not previously authorized access. Revoke access from your profile settings on the connected social account.',
    'users_social_connect' => 'Connect Account',
    'users_social_disconnect' => 'Disconnect Account',
    'users_social_connected' => ':socialAccount account was successfully attached to your profile.',
    'users_social_disconnected' => ':socialAccount account was successfully disconnected from your profile.',

    // Since these labels are already localized this array does not need to be
    // translated in the language-specific files.
    // DELETE BELOW IF COPIED FROM EN
    ///////////////////////////////////
    'language_select' => [
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'pt_BR' => 'Português do Brasil'
    ]
    ///////////////////////////////////
];
