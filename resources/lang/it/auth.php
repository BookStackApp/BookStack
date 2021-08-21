<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Credenziali errate.',
    'throttle' => 'Troppi tentativi di login. Riprova in :seconds secondi.',

    // Login & Register
    'sign_up' => 'Registrati',
    'log_in' => 'Login',
    'log_in_with' => 'Login con :socialDriver',
    'sign_up_with' => 'Registrati con :socialDriver',
    'logout' => 'Esci',

    'name' => 'Nome',
    'username' => 'Username',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirm' => 'Conferma Password',
    'password_hint' => 'Deve essere più di 7 caratteri',
    'forgot_password' => 'Password dimenticata?',
    'remember_me' => 'Ricordami',
    'ldap_email_hint' => 'Inserisci un email per usare quest\'account.',
    'create_account' => 'Crea Account',
    'already_have_account' => 'Hai già un account?',
    'dont_have_account' => 'Non hai un account?',
    'social_login' => 'Login Social',
    'social_registration' => 'Registrazione Social',
    'social_registration_text' => 'Registrati usando un altro servizio.',

    'register_thanks' => 'Grazie per esserti registrato!',
    'register_confirm' => 'Controlla la tua mail e clicca il bottone di conferma per accedere a :appName.',
    'registrations_disabled' => 'La registrazione è disabilitata',
    'registration_email_domain_invalid' => 'Questo dominio della mail non ha accesso a questa applicazione',
    'register_success' => 'Grazie per la registrazione! Sei registrato e loggato.',


    // Password Reset
    'reset_password' => 'Reimposta Password',
    'reset_password_send_instructions' => 'Inserisci il tuo indirizzo sotto e ti verrà inviata una mail contenente un link per resettare la tua password.',
    'reset_password_send_button' => 'Invia Link Reset',
    'reset_password_sent' => 'Un link di reset della password verrà inviato a :email se la mail verrà trovata nel sistema.',
    'reset_password_success' => 'La tua password è stata resettata correttamente.',
    'email_reset_subject' => 'Reimposta la password di :appName',
    'email_reset_text' => 'Stai ricevendo questa mail perché abbiamo ricevuto una richiesta di reset della password per il tuo account.',
    'email_reset_not_requested' => 'Se non hai richiesto un reset della password, ignora questa mail.',


    // Email Confirmation
    'email_confirm_subject' => 'Conferma email per :appName',
    'email_confirm_greeting' => 'Grazie per esserti registrato a :appName!',
    'email_confirm_text' => 'Conferma il tuo indirizzo email cliccando il pulsante sotto:',
    'email_confirm_action' => 'Conferma Email',
    'email_confirm_send_error' => 'La conferma della mail è richiesta ma non è stato possibile mandare la mail. Contatta l\'amministratore.',
    'email_confirm_success' => 'La tua mail è stata confermata!',
    'email_confirm_resent' => 'Mail di conferma reinviata, controlla la tua posta.',

    'email_not_confirmed' => 'Indirizzo Email Non Confermato',
    'email_not_confirmed_text' => 'Il tuo indirizzo email non è ancora stato confermato.',
    'email_not_confirmed_click_link' => 'Clicca il link nella mail mandata subito dopo la tua registrazione.',
    'email_not_confirmed_resend' => 'Se non riesci a trovare la mail puoi rimandarla cliccando il pulsante sotto.',
    'email_not_confirmed_resend_button' => 'Reinvia Conferma',

    // User Invite
    'user_invite_email_subject' => 'Sei stato invitato a unirti a :appName!',
    'user_invite_email_greeting' => 'Un account è stato creato per te su :appName.',
    'user_invite_email_text' => 'Clicca sul pulsante qui sotto per impostare una password e ottenere l\'accesso:',
    'user_invite_email_action' => 'Imposta Password',
    'user_invite_page_welcome' => 'Benvenuto in :appName!',
    'user_invite_page_text' => 'Per completare il tuo account e ottenere l\'accesso devi impostare una password che verrà utilizzata per accedere a :appName in futuro.',
    'user_invite_page_confirm_button' => 'Conferma Password',
    'user_invite_success' => 'Password impostata, ora hai accesso a :appName!',

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