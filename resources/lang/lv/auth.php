<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Šie reģistrācijas dati neatbilst mūsu ierakstiem.',
    'throttle' => 'Pārāk daudz pieteikšanās mēģinājumu. Lūdzu, mēģiniet vēlreiz pēc :seconds seconds.',

    // Login & Register
    'sign_up' => 'Reģistrēties',
    'log_in' => 'Ielogoties',
    'log_in_with' => 'Ielogoties ar :socialDriver',
    'sign_up_with' => 'Pieteikties ar :socialDriver',
    'logout' => 'Iziet',

    'name' => 'Vārds',
    'username' => 'Lietotājvārds',
    'email' => 'E-pasts',
    'password' => 'Parole',
    'password_confirm' => 'Apstiprināt paroli',
    'password_hint' => 'Jābūt vismaz 8 rakstzīmēm',
    'forgot_password' => 'Aizmirsta parole?',
    'remember_me' => 'Atcerēties mani',
    'ldap_email_hint' => 'Lūdzu ievadiet e-pastu, kuru izmantosiet šim profilam.',
    'create_account' => 'Izveidot profilu',
    'already_have_account' => 'Jau ir profils?',
    'dont_have_account' => 'Nav profila?',
    'social_login' => 'Pieteikšanās ar sociālo tīklu profilu',
    'social_registration' => 'Reģistrēšanās ar sociālo profilu',
    'social_registration_text' => 'Reģistrēties vai pieteikties izmantojot citu servisu.',

    'register_thanks' => 'Paldies par reģistrāciju!',
    'register_confirm' => 'Lūdzu, pārbaudiet savu e-pastu un nospiediet apstiprināšanas pogu, lai piekļūtu :appName.',
    'registrations_disabled' => 'Reģistrācija ir izslēgta',
    'registration_email_domain_invalid' => 'E-pasta domēnam nav piekļuves pie šīs aplikācijas',
    'register_success' => 'Paldies par reģistrēšanos! Tagad varat pieslēgties.',


    // Password Reset
    'reset_password' => 'Atiestatīt paroli',
    'reset_password_send_instructions' => 'Ievadiet savu e-pastu zemāk un nosūtīsim e-pastu ar paroles atiestatīšanas saiti.',
    'reset_password_send_button' => 'Nosūtīt atiestatīšanas saiti',
    'reset_password_sent' => 'Paroles atiestatīšanas saite tiks nosūtīta uz :email, ja šāds e-pasts būs derīgs.',
    'reset_password_success' => 'Jūsu parole ir veiksmīgi atiestatīta.',
    'email_reset_subject' => 'Atiestatīt :appName paroli',
    'email_reset_text' => 'Jūs saņemat šo e-pastu, jo mēs saņēmām Jūsu profila paroles atiestatīšanas pieprasījumu.',
    'email_reset_not_requested' => 'Ja Jūs nepieprasījāt paroles atiestatīšanu, tad tālākas darbības nav nepieciešamas.',


    // Email Confirmation
    'email_confirm_subject' => 'Apstiprinat savu :appName e-pastu',
    'email_confirm_greeting' => 'Paldies, ka pievienojāties :appName!',
    'email_confirm_text' => 'Lūdzu apstipriniet savu e-pastu nospiežot zemāk redzamo pogu:',
    'email_confirm_action' => 'Apstiprināt e-pastu',
    'email_confirm_send_error' => 'E-pasta apriprināšana ir nepieciešama, bet sistēma nevarēja e-pastu nosūtīt. Lūdzu sazinaties ar administratoru, lai pārliecinātos, ka e-pasts ir iestatīts pareizi.',
    'email_confirm_success' => 'Jūsu e-pasts ir apstiprināts!',
    'email_confirm_resent' => 'Apstiprinājuma vēstule tika nosūtīta. Lūdzu, pārbaudiet jūsu e-pastu.',

    'email_not_confirmed' => 'E-pasts nav apstiprināts',
    'email_not_confirmed_text' => 'Jūsu e-pasta adrese vēl nav apstiprināta.',
    'email_not_confirmed_click_link' => 'Lūdzu, noklikšķiniet uz saiti nosūtītajā e-pastā pēc reģistrēšanās.',
    'email_not_confirmed_resend' => 'Ja neredzi e-pastu, tad vari atkārtoti nosūtīt apstiprinājuma e-pastu iesniedzot zemāk redzamo formu.',
    'email_not_confirmed_resend_button' => 'Atkārtoti nosūtīt apstiprinājuma e-pastu',

    // User Invite
    'user_invite_email_subject' => 'Tu esi uzaicināts pievienoties :appName!',
    'user_invite_email_greeting' => 'Jūsu :appName profils ir izveidots.',
    'user_invite_email_text' => 'Lūdzu, nospiediet zemāk redzamo pogu, lai izveidotu paroli un iegūtu piekļuvi:',
    'user_invite_email_action' => 'Iestatīt profila paroli',
    'user_invite_page_welcome' => 'Sveicināti :appName!',
    'user_invite_page_text' => 'Lai pabeigtu profila izveidi un piekļūtu :appName ir jāizveido parole.',
    'user_invite_page_confirm_button' => 'Apstiprināt paroli',
    'user_invite_success' => 'Parole iestatīta, tagad varat piekļūt :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Iestati divfaktoru autentifikāciju (2FA)',
    'mfa_setup_desc' => 'Iestati divfaktoru autentifikāciju kā papildus drošību tavam lietotāja kontam.',
    'mfa_setup_configured' => 'Divfaktoru autentifikācija jau ir nokonfigurēta',
    'mfa_setup_reconfigure' => 'Mainīt 2FA konfigurāciju',
    'mfa_setup_remove_confirmation' => 'Vai esi drošs, ka vēlies noņemt divfaktoru autentifikāciju?',
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