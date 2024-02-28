<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Këto kredenciale nuk përputhen me të dhënat tona.',
    'throttle' => 'Shumë përpjekje për hyrje. Ju lutemi provoni përsëri në :seconds sekonda.',

    // Login & Register
    'sign_up' => 'Regjistrohu',
    'log_in' => 'Logohu',
    'log_in_with' => 'Logohu me :socialDriver',
    'sign_up_with' => 'Regjistrohu me :socialDriver',
    'logout' => 'Shkyçu',

    'name' => 'Emri',
    'username' => 'Emri i përdoruesit',
    'email' => 'Email',
    'password' => 'Fjalkalimi',
    'password_confirm' => 'Konfirmo fjalëkalimin',
    'password_hint' => 'Duhet të jetë të paktën 8 karaktere',
    'forgot_password' => 'Keni harruar fjalëkalimin?',
    'remember_me' => 'Më mbaj mend',
    'ldap_email_hint' => 'Ju lutem fusni një email që do përdorni për këtë llogari.',
    'create_account' => 'Krijo një llogari',
    'already_have_account' => 'Keni një llogari?',
    'dont_have_account' => 'Nuk keni akoma llogari?',
    'social_login' => 'Kyçu me rrjete sociale',
    'social_registration' => 'Regjistrohu me rrjete sociale',
    'social_registration_text' => 'Regjistrohu dhe logohu duhet përdorur një shërbim tjetër.',

    'register_thanks' => 'Faleminderit që u regjistruat!',
    'register_confirm' => 'Ju lutem kontrolloni emai-in tuaj dhe klikoni te butoni i konfirmimit për të aksesuar :appName.',
    'registrations_disabled' => 'Regjistrimet janë të mbyllura',
    'registration_email_domain_invalid' => 'Ky domain email-i nuk ka akses te ky aplikacion',
    'register_success' => 'Faleminderit që u regjistruar! Ju tani jeni të regjistruar dhe të loguar.',

    // Login auto-initiation
    'auto_init_starting' => 'Përpjekje për t\'u kyçur',
    'auto_init_starting_desc' => 'Jemi duke kontaktuar sistemin e verifikimit për të filluar proçesin e kyçjes. Nëse nuk ka progres për 5 sekonda, klikoni linkun më poshtë.',
    'auto_init_start_link' => 'Vazhdoni me verifikimin',

    // Password Reset
    'reset_password' => 'Rivendosni fjalëkalimin',
    'reset_password_send_instructions' => 'Shkruani email-in tuaj më poshtë dhe do të merrni një link në email për të rikthyer fjalëkalimin.',
    'reset_password_send_button' => 'Dërgo linkun e rikthimit të fjalëkalimit',
    'reset_password_sent' => 'Një link për rikthimin e fjalëkalimit do ju dërgohet në :email nëse adresa e email-it ndodhet në sistem.',
    'reset_password_success' => 'Fjalëkalimi juaj u rikthye me sukses.',
    'email_reset_subject' => 'Rikthe fjalëkalimin për :appName',
    'email_reset_text' => 'You are receiving this email because we received a password reset request for your account.',
    'email_reset_not_requested' => 'If you did not request a password reset, no further action is required.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Confirm Email',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
