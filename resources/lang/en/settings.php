<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Settings',
    'settings_save' => 'Save Settings',
    'settings_save_success' => 'Settings saved',

    /**
     * App settings
     */

    'app_settings' => 'App Settings',
    'app_name' => 'Application name',
    'app_name_desc' => 'This name is shown in the header and any emails.',
    'app_name_header' => 'Show Application name in header?',
    'app_public_viewing' => 'Allow public viewing?',
    'app_secure_images' => 'Enable higher security image uploads?',
    'app_secure_images_desc' => 'For performance reasons, all images are public. This option adds a random, hard-to-guess string in front of image urls. Ensure directory indexes are not enabled to prevent easy access.',
    'app_editor' => 'Page editor',
    'app_editor_desc' => 'Select which editor will be used by all users to edit pages.',
    'app_custom_html' => 'Custom HTML head content',
    'app_custom_html_desc' => 'Any content added here will be inserted into the bottom of the <head> section of every page. This is handy for overriding styles or adding analytics code.',
    'app_logo' => 'Application logo',
    'app_logo_desc' => 'This image should be 43px in height. <br>Large images will be scaled down.',
    'app_primary_color' => 'Application primary color',
    'app_primary_color_desc' => 'This should be a hex value. <br>Leave empty to reset to the default color.',
    'app_homepage' => 'Application Homepage',
    'app_homepage_desc' => 'Select a view to show on the homepage instead of the default view. Page permissions are ignored for selected pages.',
    'app_homepage_select' => 'Select a page',
    'app_disable_comments' => 'Disable comments',
    'app_disable_comments_desc' => 'Disable comments across all pages in the application. Existing comments are not shown.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Registration Settings',
    'reg_allow' => 'Allow registration?',
    'reg_default_role' => 'Default user role after registration',
    'reg_confirm_email' => 'Require email confirmation?',
    'reg_confirm_email_desc' => 'If domain restriction is used then email confirmation will be required and the below value will be ignored.',
    'reg_confirm_restrict_domain' => 'Restrict registration to domain',
    'reg_confirm_restrict_domain_desc' => 'Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application. <br> Note that users will be able to change their email addresses after successful registration.',
    'reg_confirm_restrict_domain_placeholder' => 'No restriction set',

    /**
     * Maintenance settings
     */

    'maint' => 'Maintenance',
    'maint_image_cleanup' => 'Cleanup Images',
    'maint_image_cleanup_desc' => "Scans page & revision content to check which images and drawings are currently in use and which images are redundant. Ensure you create a full database and image backup before running this.",
    'maint_image_cleanup_ignore_revisions' => 'Ignore images in revisions',
    'maint_image_cleanup_run' => 'Run Cleanup',
    'maint_image_cleanup_warning' => ':count potentially unused images were found. Are you sure you want to delete these images?',
    'maint_image_cleanup_success' => ':count potentially unused images found and deleted!',
    'maint_image_cleanup_nothing_found' => 'No unused images found, Nothing deleted!',

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
    'role_external_auth_id' => 'External Authentication IDs',
    'role_system' => 'System Permissions',
    'role_manage_users' => 'Manage users',
    'role_manage_roles' => 'Manage roles & role permissions',
    'role_manage_entity_permissions' => 'Manage all book, chapter & page permissions',
    'role_manage_own_entity_permissions' => 'Manage permissions on own book, chapter & pages',
    'role_manage_settings' => 'Manage app settings',
    'role_asset' => 'Asset Permissions',
    'role_asset_desc' => 'These permissions control default access to the assets within the system. Permissions on Books, Chapters and Pages will override these permissions.',
    'role_asset_admins' => 'Admins are automatically given access to all content but these options may show or hide UI options.',
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
        'ar' => 'العربية',
        'de' => 'Deutsch (Sie)',
        'de_informal' => 'Deutsch (Du)',
        'es' => 'Español',
        'es_AR' => 'Español Argentina',
        'fr' => 'Français',
        'nl' => 'Nederlands',
        'pt_BR' => 'Português do Brasil',
        'sk' => 'Slovensky',
        'sv' => 'Svenska',
        'kr' => '한국어',
        'ja' => '日本語',
        'pl' => 'Polski',
        'it' => 'Italian',
        'ru' => 'Русский',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文'
    ]
    ///////////////////////////////////
];
