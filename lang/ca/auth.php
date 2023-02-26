<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Les credencials no coincideixen amb les que hi ha emmagatzemades.',
    'throttle' => 'Massa intents d\'inici de sessió. Torna-ho a provar d\'aquí a :seconds segons.',

    // Login & Register
    'sign_up' => 'Registra-m\'hi',
    'log_in' => 'Inicia la sessió',
    'log_in_with' => 'Inicia la sessió amb :socialDriver',
    'sign_up_with' => 'Registra-m\'hi amb :socialDriver',
    'logout' => 'Tanca la sessió',

    'name' => 'Nom',
    'username' => 'Nom d\'usuari',
    'email' => 'Adreça electrònica',
    'password' => 'Contrasenya',
    'password_confirm' => 'Confirmeu la contrasenya',
    'password_hint' => 'Must be at least 8 characters',
    'forgot_password' => 'Heu oblidat la contrasenya?',
    'remember_me' => 'Recorda\'m',
    'ldap_email_hint' => 'Introduïu una adreça electrònica per a aquest compte.',
    'create_account' => 'Crea el compte',
    'already_have_account' => 'Ja teniu un compte?',
    'dont_have_account' => 'No teniu cap compte?',
    'social_login' => 'Inici de sessió amb xarxes social',
    'social_registration' => 'Registre social',
    'social_registration_text' => 'Registreu-vos i inicieu la sessió fent servir un altre servei.',

    'register_thanks' => 'Gràcies per registrar-vos!',
    'register_confirm' => 'Reviseu el vostre correu electrònic i feu clic al botó de confirmació per a accedir a :appName.',
    'registrations_disabled' => 'Actualment, els registres estan desactivats',
    'registration_email_domain_invalid' => 'Aquest domini de correu electrònic no té accés a aquesta aplicació',
    'register_success' => 'Gràcies per registrar-vos! Ja us hi heu registrat i heu iniciat la sessió.',

    // Login auto-initiation
    'auto_init_starting' => 'Attempting Login',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

    // Password Reset
    'reset_password' => 'Restableix la contrasenya',
    'reset_password_send_instructions' => 'Introduïu la vostra adreça electrònica a continuació i us enviarem un correu electrònic amb un enllaç per a restablir la contrasenya.',
    'reset_password_send_button' => 'Envia l\'enllaç de restabliment',
    'reset_password_sent' => 'S\'enviarà un enllaç per a restablir la contrasenya a :email, si es troba aquesta adreça al sistema.',
    'reset_password_success' => 'La vostra contrasenya s\'ha restablert correctament.',
    'email_reset_subject' => 'Restabliu la contrasenya a :appName',
    'email_reset_text' => 'Rebeu aquest correu electrònic perquè heu rebut una petició de restabliment de contrasenya per al vostre compte.',
    'email_reset_not_requested' => 'Si no heu demanat restablir la contrasenya, no cal que prengueu cap acció.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmeu la vostra adreça electrònica a :appName',
    'email_confirm_greeting' => 'Gràcies per unir-vos a :appName!',
    'email_confirm_text' => 'Confirmeu la vostra adreça electrònica fent clic al botó a continuació:',
    'email_confirm_action' => 'Confirma el correu',
    'email_confirm_send_error' => 'Cal confirmar l\'adreça electrònica, però el sistema no ha pogut enviar el correu electrònic. Poseu-vos en contacte amb l\'administrador perquè s\'asseguri que el correu electrònic està ben configurat.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'S\'ha tornat a enviar el correu electrònic de confirmació. Reviseu la vostra safata d\'entrada.',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

    'email_not_confirmed' => 'Adreça electrònica no confirmada',
    'email_not_confirmed_text' => 'La vostra adreça electrònica encara no està confirmada.',
    'email_not_confirmed_click_link' => 'Feu clic a l\'enllaç del correu electrònic que us vam enviar poc després que us registréssiu.',
    'email_not_confirmed_resend' => 'Si no podeu trobar el correu, podeu tornar a enviar el correu electrònic de confirmació enviant el formulari a continuació.',
    'email_not_confirmed_resend_button' => 'Torna a enviar el correu de confirmació',

    // User Invite
    'user_invite_email_subject' => 'Us han convidat a unir-vos a :appName!',
    'user_invite_email_greeting' => 'Us hem creat un compte en el vostre nom a :appName.',
    'user_invite_email_text' => 'Feu clic al botó a continuació per a definir una contrasenya per al compte i obtenir-hi accés:',
    'user_invite_email_action' => 'Defineix una contrasenya per al compte',
    'user_invite_page_welcome' => 'Us donem la benvinguda a :appName!',
    'user_invite_page_text' => 'Per a enllestir el vostre compte i obtenir-hi accés, cal que definiu una contrasenya, que es farà servir per a iniciar la sessió a :appName en futures visites.',
    'user_invite_page_confirm_button' => 'Confirma la contrasenya',
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
