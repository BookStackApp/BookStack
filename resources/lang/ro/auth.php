<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Aceste credenţiale nu se potrivesc cu înregistrările noastre.',
    'throttle' => 'Prea multe încercări de conectare. Vă rugăm să încercați din nou în :seconds secunde.',

    // Login & Register
    'sign_up' => 'Inregistrează-te',
    'log_in' => 'Autentifică-te',
    'log_in_with' => 'Autentifică-te cu :socialDriver',
    'sign_up_with' => 'Inregistrează-te cu :socialDriver',
    'logout' => 'Deconectează-te',

    'name' => 'Nume',
    'username' => 'Nume utilizator',
    'email' => 'E-mail',
    'password' => 'Parolă',
    'password_confirm' => 'Confirmă parola',
    'password_hint' => 'Trebuie să aibă cel puțin 8 caractere',
    'forgot_password' => 'Ai uitat parola?',
    'remember_me' => 'Amintește-ți de mine',
    'ldap_email_hint' => 'Te rog să introduci o adresa de e-mail pentru a utiliza acest cont.',
    'create_account' => 'Crează cont',
    'already_have_account' => 'Ai deja cont?',
    'dont_have_account' => 'Nu ai cont?',
    'social_login' => 'Conectare folosind rețea socială',
    'social_registration' => 'Înregistrare cu rețea socială',
    'social_registration_text' => 'Înregistrați-vă și conectați-vă utilizând alt serviciu.',

    'register_thanks' => 'Mulțumesc pentru înregistrare!',
    'register_confirm' => 'Verifică-ți e-mailul și dă clic pe butonul de confirmare pentru a accesa :appName.',
    'registrations_disabled' => 'Înregistrările sunt momentan dezactivate',
    'registration_email_domain_invalid' => 'Acel domeniu de e-mail nu are acces la această aplicație',
    'register_success' => 'Mulțumesc pentru înscriere! Acum sunteți înregistrat și conectat.',

    // Login auto-initiation
    'auto_init_starting' => 'Se încearcă autentificarea',
    'auto_init_starting_desc' => 'Vă contactăm sistemul de autentificare pentru a începe procesul de conectare. Dacă nu există niciun progres după 5 secunde, puteți încerca să dați clic pe linkul de mai jos.',
    'auto_init_start_link' => 'Continuă cu autentificarea',

    // Password Reset
    'reset_password' => 'Resetează parola',
    'reset_password_send_instructions' => 'Introduceți adresa de e-mail mai jos și vi se va trimite un e-mail cu un link pentru resetarea parolei.',
    'reset_password_send_button' => 'Trimite linkul de resetare',
    'reset_password_sent' => 'Un link pentru resetarea parolei va fi trimis la :email dacă acea adresă de e-mail este găsită în sistem.',
    'reset_password_success' => 'Parola a fost resetată cu succes.',
    'email_reset_subject' => 'Resetează parola ta :appName',
    'email_reset_text' => 'Primești acest e-mail deoarece am primit o solicitare de resetare a parolei pentru contul tău.',
    'email_reset_not_requested' => 'Dacă nu ai solicitat resetarea parolei, nu este necesară nicio acțiune suplimentară.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmă e-mailul pentru :appName',
    'email_confirm_greeting' => 'Mulțumim că te-ai alăturat :appName!',
    'email_confirm_text' => 'Te rog să confirmi adresa ta de e-mail făcând clic pe butonul de mai jos:',
    'email_confirm_action' => 'Confirmă emailul',
    'email_confirm_send_error' => 'Este necesară confirmarea prin e-mail, dar sistemul nu a putut trimite e-mailul. Contactează administratorul pentru a te asigura că e-mailul este configurat corect.',
    'email_confirm_success' => 'E-mailul a fost confirmat! Acum ar trebui să te poți autentifica folosind această adresă de e-mail.',
    'email_confirm_resent' => 'E-mailul de confirmare a fost retrimis, te rugăm să îți verifici căsuța de e-mail.',

    'email_not_confirmed' => 'Adresa de e-mail neconfirmată',
    'email_not_confirmed_text' => 'Adresa ta de e-mail nu a fost încă confirmată.',
    'email_not_confirmed_click_link' => 'Accesează linkul din e-mailul care a fost trimis la scurt timp după ce te-ai înregistrat.',
    'email_not_confirmed_resend' => 'Dacă nu găsești e-mailul, poți retrimite e-mailul de confirmare completând formularul de mai jos.',
    'email_not_confirmed_resend_button' => 'Retrimite e-mail de confirmare',

    // User Invite
    'user_invite_email_subject' => 'Ai fost invitat să te alături :appName!',
    'user_invite_email_greeting' => 'A fost creat un cont pentru tine pe :appName.',
    'user_invite_email_text' => 'Fă clic pe butonul de mai jos pentru a seta o parolă de cont și a obține acces:',
    'user_invite_email_action' => 'Setează parola contului',
    'user_invite_page_welcome' => 'Bun venit la :appName!',
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
