<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Disse detaljene samsvarer ikke med det vi har på bok.',
    'throttle' => 'For mange forsøk, prøv igjen om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Registrer deg',
    'log_in' => 'Logg inn',
    'log_in_with' => 'Logg inn med :socialDriver',
    'sign_up_with' => 'Registrer med :socialDriver',
    'logout' => 'Logg ut',

    'name' => 'Navn',
    'username' => 'Brukernavn',
    'email' => 'E-post',
    'password' => 'Passord',
    'password_confirm' => 'Bekreft passord',
    'password_hint' => 'Må inneholde 7 tegn',
    'forgot_password' => 'Glemt passord?',
    'remember_me' => 'Husk meg',
    'ldap_email_hint' => 'Oppgi en e-post for denne kontoen.',
    'create_account' => 'Opprett konto',
    'already_have_account' => 'Har du allerede en konto?',
    'dont_have_account' => 'Mangler du en konto?',
    'social_login' => 'Sosiale kontoer',
    'social_registration' => 'Registrer via sosiale kontoer',
    'social_registration_text' => 'Bruk en annen tjeneste for å registrere deg.',

    'register_thanks' => 'Takk for at du registrerte deg!',
    'register_confirm' => 'Sjekk e-posten din for informasjon som gir deg tilgang til :appName.',
    'registrations_disabled' => 'Registrering er deaktivert.',
    'registration_email_domain_invalid' => 'Du kan ikke bruke det domenet for å registrere en konto.',
    'register_success' => 'Takk for registreringen! Du kan nå logge inn på tjenesten.',


    // Password Reset
    'reset_password' => 'Nullstille passord',
    'reset_password_send_instructions' => 'Oppgi e-posten som er koblet til kontoen din, så sender vi en epost hvor du kan nullstille passordet.',
    'reset_password_send_button' => 'Send nullstillingslenke',
    'reset_password_sent' => 'En nullstillingslenke ble sendt til :email om den eksisterer i systemet.',
    'reset_password_success' => 'Passordet ble nullstilt.',
    'email_reset_subject' => 'Nullstill ditt :appName passord',
    'email_reset_text' => 'Du mottar denne eposten fordi det er blitt bedt om en nullstilling av passord på denne kontoen.',
    'email_reset_not_requested' => 'Om det ikke var deg, så trenger du ikke foreta deg noe.',


    // Email Confirmation
    'email_confirm_subject' => 'Bekreft epost-adressen for :appName',
    'email_confirm_greeting' => 'Takk for at du registrerte deg for :appName!',
    'email_confirm_text' => 'Bekreft e-posten din ved å trykke på knappen nedenfor:',
    'email_confirm_action' => 'Bekreft e-post',
    'email_confirm_send_error' => 'Bekreftelse er krevd av systemet, men systemet kan ikke sende disse. Kontakt admin for å løse problemet.',
    'email_confirm_success' => 'E-posten din er bekreftet!',
    'email_confirm_resent' => 'Bekreftelsespost ble sendt, sjekk innboksen din.',

    'email_not_confirmed' => 'E-posten er ikke bekreftet.',
    'email_not_confirmed_text' => 'Epost-adressen er ennå ikke bekreftet.',
    'email_not_confirmed_click_link' => 'Trykk på lenken i e-posten du fikk vedrørende din registrering.',
    'email_not_confirmed_resend' => 'Om du ikke finner den i innboksen eller søppelboksen, kan du få tilsendt ny ved å trykke på knappen under.',
    'email_not_confirmed_resend_button' => 'Send bekreftelsespost på nytt',

    // User Invite
    'user_invite_email_subject' => 'Du har blitt invitert til :appName!',
    'user_invite_email_greeting' => 'En konto har blitt opprettet for deg på :appName.',
    'user_invite_email_text' => 'Trykk på knappen under for å opprette et sikkert passord:',
    'user_invite_email_action' => 'Angi passord',
    'user_invite_page_welcome' => 'Velkommen til :appName!',
    'user_invite_page_text' => 'For å fullføre prosessen må du oppgi et passord som sikrer din konto på :appName for fremtidige besøk.',
    'user_invite_page_confirm_button' => 'Bekreft passord',
    'user_invite_success' => 'Passordet er angitt, du kan nå bruke :appName!',

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