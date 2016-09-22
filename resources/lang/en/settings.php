<?php

return [
    
    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */
    
    'settings' => 'Settings',
    'settings_save' => 'Save Settings',
    
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

    'reg_settings' => 'Registration Settings',
    'reg_allow' => 'Allow registration?',
    'reg_default_role' => 'Default user role after registration',
    'reg_confirm_email' => 'Require email confirmation?',
    'reg_confirm_email_desc' => 'If domain restriction is used then email confirmation will be required and the below value will be ignored.',
    'reg_confirm_restrict_domain' => 'Restrict registration to domain',
    'reg_confirm_restrict_domain_desc' => 'Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application. <br> Note that users will be able to change their email addresses after successful registration.',
    'reg_confirm_restrict_domain_placeholder' => 'No restriction set',

];