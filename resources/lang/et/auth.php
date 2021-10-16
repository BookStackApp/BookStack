<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Kasutajanimi ja parool ei klapi.',
    'throttle' => 'Liiga palju sisselogimiskatseid. Proovi uuesti :seconds sekundi pärast.',

    // Login & Register
    'sign_up' => 'Registreeru',
    'log_in' => 'Logi sisse',
    'log_in_with' => 'Logi sisse :socialDriver abil',
    'sign_up_with' => 'Registreeru :socialDriver abil',
    'logout' => 'Logi välja',

    'name' => 'Nimi',
    'username' => 'Kasutajanimi',
    'email' => 'E-post',
    'password' => 'Parool',
    'password_confirm' => 'Kinnita parool',
    'password_hint' => 'Peab olema rohkem kui 7 tähemärki',
    'forgot_password' => 'Unustasid parooli?',
    'remember_me' => 'Jäta mind meelde',
    'ldap_email_hint' => 'Sisesta kasutajakonto e-posti aadress.',
    'create_account' => 'Loo konto',
    'already_have_account' => 'Kasutajakonto juba olemas?',
    'dont_have_account' => 'Sul ei ole veel kontot?',
    'social_login' => 'Social Login',
    'social_registration' => 'Social Registration',
    'social_registration_text' => 'Registreeru ja logi sisse välise teenuse kaudu.',

    'register_thanks' => 'Aitäh, et registreerusid!',
    'register_confirm' => 'Please check your email and click the confirmation button to access :appName.',
    'registrations_disabled' => 'Registreerumine on hetkel keelatud',
    'registration_email_domain_invalid' => 'That email domain does not have access to this application',
    'register_success' => 'Thanks for signing up! You are now registered and signed in.',


    // Password Reset
    'reset_password' => 'Lähtesta parool',
    'reset_password_send_instructions' => 'Siseta oma e-posti aadress ning sulle saadetakse link parooli lähtestamiseks.',
    'reset_password_send_button' => 'Saada lähtestamise link',
    'reset_password_sent' => 'Kui süsteemis leidub e-posti aadress :email, saadetakse sinna link parooli lähtestamiseks.',
    'reset_password_success' => 'Sinu parool on edukalt lähtestatud.',
    'email_reset_subject' => 'Lähtesta oma :appName parool',
    'email_reset_text' => 'Said selle e-kirja, sest meile laekus soov sinu konto parooli lähtestamiseks.',
    'email_reset_not_requested' => 'Kui sa ei soovinud parooli lähtestada, ei pea sa rohkem midagi tegema.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Confirm Email',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed!',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Vajuta allolevale nupule, et seada parool ja ligipääs saada:',
    'user_invite_email_action' => 'Sea konto parool',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'Registreerumise lõpetamiseks ja ligipääsu saamiseks pead seadma parooli, millega edaspidi rakendusse sisse logid.',
    'user_invite_page_confirm_button' => 'Kinnita parool',
    'user_invite_success' => 'Parool seatud, sul on nüüd ligipääs!',

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