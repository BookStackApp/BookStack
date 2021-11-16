<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Ovi pristupni podaci se ne slažu sa našom evidencijom.',
    'throttle' => 'Preveliki broj pokušaja prijave. Molimo vas da pokušate ponovo za :seconds sekundi.',

    // Login & Register
    'sign_up' => 'Registruj se',
    'log_in' => 'Prijavi se',
    'log_in_with' => 'Prijavi se sa :socialDriver',
    'sign_up_with' => 'Registruj se sa :socialDriver',
    'logout' => 'Odjavi se',

    'name' => 'Ime',
    'username' => 'Korisničko ime',
    'email' => 'E-mail',
    'password' => 'Lozinka',
    'password_confirm' => 'Potvrdi lozinku',
    'password_hint' => 'Mora imati više od 7 karaktera',
    'forgot_password' => 'Zaboravljena lozinka?',
    'remember_me' => 'Zapamti me',
    'ldap_email_hint' => 'Unesite e-mail koji će se koristiti za ovaj račun.',
    'create_account' => 'Napravi račun',
    'already_have_account' => 'Već imate račun?',
    'dont_have_account' => 'Nemate korisnički račun?',
    'social_login' => 'Prijava preko društvene mreže',
    'social_registration' => 'Registracija pomoću društvene mreže',
    'social_registration_text' => 'Registruj i prijavi se koristeći drugi servis.',

    'register_thanks' => 'Hvala na registraciji!',
    'register_confirm' => 'Provjerite vašu e-mail adresu i pritisnite dugme za potvrdu da bi dobili pristup :appName.',
    'registrations_disabled' => 'Registracije su trenutno onemogućene',
    'registration_email_domain_invalid' => 'Ta e-mail domena nema pristup ovoj aplikaciji',
    'register_success' => 'Hvala na registraciji! Sada ste registrovani i prijavljeni.',

    // Password Reset
    'reset_password' => 'Resetuj Lozinku',
    'reset_password_send_instructions' => 'Unesite vašu e-mail adresu ispod i na nju ćemo vam poslati e-mail sa linkom za promjenu lozinke.',
    'reset_password_send_button' => 'Pošalji link za promjenu',
    'reset_password_sent' => 'Link za promjenu lozinke će biti poslan na :email ako ta adresa postoji u sistemu.',
    'reset_password_success' => 'Vaša lozinka je uspješno promijenjena.',
    'email_reset_subject' => 'Resetujte vašu lozinku od :appName',
    'email_reset_text' => 'Primate ovaj e-mail jer smo dobili zahtjev za promjenu lozinke za vaš račun.',
    'email_reset_not_requested' => 'Ako niste zahtijevali promjenu lozinke ne trebate ništa više uraditi.',

    // Email Confirmation
    'email_confirm_subject' => 'Potvrdite vaš e-mail na :appName',
    'email_confirm_greeting' => 'Hvala na pristupanju :appName!',
    'email_confirm_text' => 'Potvrdite vašu e-mail adresu pritiskom na dugme ispod:',
    'email_confirm_action' => 'Potvrdi e-mail',
    'email_confirm_send_error' => 'Potvrda e-maila je obavezna ali sistem nije mogao poslati e-mail. Kontaktirajte administratora da biste bili sigurni da je e-mail postavljen ispravno.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'E-mail za potvrdu je ponovno poslan. Provjerite vaš e-mail.',

    'email_not_confirmed' => 'E-mail adresa nije potvrđena',
    'email_not_confirmed_text' => 'Vaša e-mail adresa nije još potvrđena.',
    'email_not_confirmed_click_link' => 'Kliknite na link u e-mailu koji vam je poslan nakon što ste se registrovali.',
    'email_not_confirmed_resend' => 'Ako ne možete naći e-mail možete ponovno poslati e-mail za potvrdu tako što ćete ispuniti formu ispod.',
    'email_not_confirmed_resend_button' => 'Ponovno pošalji e-mail za potvrdu',

    // User Invite
    'user_invite_email_subject' => 'Pozvani ste da se pridružite :appName!',
    'user_invite_email_greeting' => 'Račun je napravljen za vas na :appName.',
    'user_invite_email_text' => 'Pritisnite dugme ispod da niste postavili lozinku vašeg računa i tako dobili pristup:',
    'user_invite_email_action' => 'Postavi lozinku računa',
    'user_invite_page_welcome' => 'Dobrodošli na :appName!',
    'user_invite_page_text' => 'Da biste završili vaš račun i dobili pristup morate postaviti lozinku koju ćete koristiti da se prijavite na :appName tokom budućih posjeta.',
    'user_invite_page_confirm_button' => 'Potvrdi lozinku',
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
