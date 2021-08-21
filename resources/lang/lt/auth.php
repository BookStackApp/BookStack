<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Šie įgaliojimai neatitinka mūsų įrašų.',
    'throttle' => 'Per daug prisijungimo bandymų. Prašome pabandyti dar kartą po :seconds sekundžių.',

    // Login & Register
    'sign_up' => 'Užsiregistruoti',
    'log_in' => 'Prisijungti',
    'log_in_with' => 'Prisijungti su :socialDriver',
    'sign_up_with' => 'Užsiregistruoti su :socialDriver',
    'logout' => 'Atsijungti',

    'name' => 'Pavadinimas',
    'username' => 'Vartotojo vardas',
    'email' => 'Elektroninis paštas',
    'password' => 'Slaptažodis',
    'password_confirm' => 'Patvirtinti slaptažodį',
    'password_hint' => 'Privalo būti daugiau nei 7 simboliai',
    'forgot_password' => 'Pamiršote slaptažodį?',
    'remember_me' => 'Prisimink mane',
    'ldap_email_hint' => 'Prašome įvesti elektroninį paštą, kad galėtume naudotis šia paskyra.',
    'create_account' => 'Sukurti paskyrą',
    'already_have_account' => 'Jau turite paskyrą?',
    'dont_have_account' => 'Neturite paskyros?',
    'social_login' => 'Socialinis prisijungimas',
    'social_registration' => 'Socialinė registracija',
    'social_registration_text' => 'Užsiregistruoti ir prisijungti naudojantis kita paslauga.',

    'register_thanks' => 'Ačiū, kad užsiregistravote!',
    'register_confirm' => 'Prašome patikrinti savo elektroninį paštą ir paspausti patvirtinimo mygtuką, kad gautumėte leidimą į :appName.',
    'registrations_disabled' => 'Registracijos šiuo metu negalimos',
    'registration_email_domain_invalid' => 'Elektroninio pašto domenas neturi prieigos prie šios programos',
    'register_success' => 'Ačiū už prisijungimą! Dabar jūs užsiregistravote ir prisijungėte.',


    // Password Reset
    'reset_password' => 'Pakeisti slaptažodį',
    'reset_password_send_instructions' => 'Įveskite savo elektroninį paštą žemiau ir jums bus išsiųstas elektroninis laiškas su slaptažodžio nustatymo nuoroda.',
    'reset_password_send_button' => 'Atsiųsti atsatymo nuorodą',
    'reset_password_sent' => 'Slaptažodžio nustatymo nuoroda bus išsiųsta :email jeigu elektroninio pašto adresas bus rastas sistemoje.',
    'reset_password_success' => 'Jūsų slaptažodis buvo sėkmingai atnaujintas.',
    'email_reset_subject' => 'Atnaujinti jūsų :appName slaptažodį',
    'email_reset_text' => 'Šį laišką gaunate, nes mes gavome slaptažodžio atnaujinimo užklausą iš jūsų paskyros.',
    'email_reset_not_requested' => 'Jeigu jums nereikia slaptažodžio atnaujinimo, tolimesnių veiksmų atlikti nereikia.',


    // Email Confirmation
    'email_confirm_subject' => 'Patvirtinkite savo elektroninį paštą :appName',
    'email_confirm_greeting' => 'Ačiū už prisijungimą prie :appName!',
    'email_confirm_text' => 'Prašome patvirtinti savo elektroninio pašto adresą paspaudus mygtuką žemiau:',
    'email_confirm_action' => 'Patvirtinkite elektroninį paštą',
    'email_confirm_send_error' => 'Būtinas elektroninio laiško patviritnimas, bet sistema negali išsiųsti laiško. Susisiekite su administratoriumi, kad užtikrintumėte, jog elektroninis paštas atsinaujino teisingai.',
    'email_confirm_success' => 'Jūsų elektroninis paštas buvo patvirtintas!',
    'email_confirm_resent' => 'Elektroninio pašto patvirtinimas persiųstas, prašome patikrinti pašto dėžutę.',

    'email_not_confirmed' => 'Elektroninis paštas nepatvirtintas',
    'email_not_confirmed_text' => 'Jūsų elektroninis paštas dar vis nepatvirtintas.',
    'email_not_confirmed_click_link' => 'Prašome paspausti nuorodą elektroniniame pašte, kuri buvo išsiųsta iš karto po registracijos.',
    'email_not_confirmed_resend' => 'Jeigu nerandate elektroninio laiško, galite dar kartą išsiųsti patvirtinimo elektroninį laišką, pateikdami žemiau esančią formą.',
    'email_not_confirmed_resend_button' => 'Persiųsti patvirtinimo laišką',

    // User Invite
    'user_invite_email_subject' => 'Jūs buvote pakviestas prisijungti prie :appName!',
    'user_invite_email_greeting' => 'Paskyra buvo sukurta jums :appName.',
    'user_invite_email_text' => 'Paspauskite mygtuką žemiau, kad sukurtumėte paskyros slaptažodį ir gautumėte prieigą:',
    'user_invite_email_action' => 'Sukurti paskyros slaptažodį',
    'user_invite_page_welcome' => 'Sveiki atvykę į :appName!',
    'user_invite_page_text' => 'Norėdami galutinai pabaigti paskyrą ir gauti prieigą jums reikia nustatyti slaptažodį, kuris bus naudojamas prisijungiant prie :appName ateities vizitų metu.',
    'user_invite_page_confirm_button' => 'Patvirtinti slaptažodį',
    'user_invite_success' => 'Slaptažodis nustatytas, dabar turite prieigą prie :appName!',

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