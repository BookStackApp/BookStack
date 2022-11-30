<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Kredentzial hauek ez dira egokiak.',
    'throttle' => 'Login saiakera kopurua pasa duzu. Mesedez, saiatu berriz :seconds segundu barru.',

    // Login & Register
    'sign_up' => 'Izena eman',
    'log_in' => 'Saioa hasi',
    'log_in_with' => ':socialDriver bidez hasi saioa',
    'sign_up_with' => ':socialDriver bidez izena eman',
    'logout' => 'Logout',

    'name' => 'Izena',
    'username' => 'Erabiltzaile izena',
    'email' => 'Eposta',
    'password' => 'Pasahitza',
    'password_confirm' => 'Berretsi pasahitza',
    'password_hint' => 'Gutxienez 8 karaktere izan behar ditu',
    'forgot_password' => 'Pasahitza ahaztu duzu?',
    'remember_me' => 'Gogoratu',
    'ldap_email_hint' => 'Mesedez, email bat sartu kontu hau erabiltzeko.',
    'create_account' => 'Sortu kontua',
    'already_have_account' => 'Dagoeneko kontu bat al duzu?',
    'dont_have_account' => 'Ez duzu konturik?',
    'social_login' => 'Kanpoko logina',
    'social_registration' => 'Erregistroa kanpotik',
    'social_registration_text' => 'Erregistratu eta saioa hasi beste zerbitzu batekin.',

    'register_thanks' => 'Mila esker erregistroagatik!',
    'register_confirm' => 'Mesedez, begiratu posta elektronikoari eta klik egin baieztapen botoia :appName sartzeko.',
    'registrations_disabled' => 'Erregistroa ez dago gaituta',
    'registration_email_domain_invalid' => 'Posta elektronikoko domeinu hori ez dago eskuragarri aplikazio honetarako',
    'register_success' => 'Eskerrik asko izen emateagatik! Orain izena emanda eta saioa hasita zaude.',

    // Login auto-initiation
    'auto_init_starting' => 'Attempting Login',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

    // Password Reset
    'reset_password' => 'Pasahitza berrezarri',
    'reset_password_send_instructions' => 'Sartu zure posta elektronikoa eta posta elektroniko bat bidaliko dizute pasahitza berritzeko esteka batekin.',
    'reset_password_send_button' => 'Bidali Reset Link',
    'reset_password_sent' => 'Posta elektronikora bidaliko da posta elektronikoko :email helbide hori sisteman aurkitzen bada.',
    'reset_password_success' => 'Zure pasahitza egoki berrezarri da.',
    'email_reset_subject' => 'Berrezarri zure :appName pasahitza',
    'email_reset_text' => 'E-mail hau jasotzen ari zara, zure konturako pasahitz eskaera jaso dugulako.',
    'email_reset_not_requested' => 'Zuk ez baduzu pasahitza berresartzea eskatu, ez duzu ezer egin beharrik.',

    // Email Confirmation
    'email_confirm_subject' => 'Baieztatu zure emaila hemen :appName',
    'email_confirm_greeting' => 'Eskerrik asko :appName proiektuan batzeagatik!',
    'email_confirm_text' => 'Mesedez, baieztatu zure email helbidea beheko botoi hau kilkatuz:',
    'email_confirm_action' => 'Berrespen e-maila',
    'email_confirm_send_error' => 'Posta elektronikoaren baieztapenak behar da, baina sistemak ezin izan du posta elektronikoa bidali. Administratzailearekin harremanetan jarri email ezarpenak ongi dauden baieztatzeko.',
    'email_confirm_success' => 'Zure posta elektronikoa baieztatu da! Helbide elektroniko hau erabili dezakezu saioa hasteko.',
    'email_confirm_resent' => 'Eragiketa baieztatzeko email bat bidali dizugu. Mesedez, begiratu zure posta elektronikoa.',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

    'email_not_confirmed' => 'Email helbidea ez da baieztatu',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Birbidali baieztapen mezua',

    // User Invite
    'user_invite_email_subject' => ':appName sartzera gonbidatu zaituzte!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Kontuko pasahitzam jarri',
    'user_invite_page_welcome' => 'Ongi etorri :appName -era!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Berretsi pasahitza',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Dagoeneko konfiguratuta',
    'mfa_setup_reconfigure' => 'Berrezarri',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Konfigurazioa',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Aplikazio mugikorra',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Baieztatu eta gaitu',
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
