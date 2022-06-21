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

    // Login auto-initiation
    'auto_init_starting' => 'Attempting Login',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

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
    'email_confirm_success' => 'Jūsu epasta adrese ir apstiprināta! Jums tagad jābūt iespējai pieslēgties, izmantojot šo epasta adresi.',
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
    'user_invite_success_login' => 'Parole ir uzstādīta, jums tagad jābūt iespējai pieslēgties izmantojot uzstādīto paroli, lai piekļūtu :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Iestati divfaktoru autentifikāciju (2FA)',
    'mfa_setup_desc' => 'Iestati divfaktoru autentifikāciju kā papildus drošību tavam lietotāja kontam.',
    'mfa_setup_configured' => 'Divfaktoru autentifikācija jau ir nokonfigurēta',
    'mfa_setup_reconfigure' => 'Mainīt 2FA konfigurāciju',
    'mfa_setup_remove_confirmation' => 'Vai esi drošs, ka vēlies noņemt divfaktoru autentifikāciju?',
    'mfa_setup_action' => 'Iestatījumi',
    'mfa_backup_codes_usage_limit_warning' => 'Jums atlikuši mazāk kā 5 rezerves kodi. Lūdzu izveidojiet jaunu kodu komplektu pirms tie visi izlietoti, lai izvairītos no izslēgšanas no jūsu konta.',
    'mfa_option_totp_title' => 'Mobilā aplikācija',
    'mfa_option_totp_desc' => 'Lai lietotu vairākfaktoru autentifikāciju, jums būs nepieciešama mobilā aplikācija, kas atbalsta TOTP, piemēram, Google Authenticator, Authy vai Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Rezerves kodi',
    'mfa_option_backup_codes_desc' => 'Droši noglabājiet vienreizlietojamu rezerves kodu komplektu, ko varēsiet izmantot, lai verificētu savu identitāti.',
    'mfa_gen_confirm_and_enable' => 'Apstiprināt un ieslēgt',
    'mfa_gen_backup_codes_title' => 'Rezerves kodu iestatījumi',
    'mfa_gen_backup_codes_desc' => 'Noglabājiet zemāk esošo kodu sarakstu drošā vietā. Kad piekļūsiet sistēmai, jūs varēsiet izmantot vienu no kodiem kā papildus autentifikācijas mehānismu.',
    'mfa_gen_backup_codes_download' => 'Lejupielādēt kodus',
    'mfa_gen_backup_codes_usage_warning' => 'Katru kodu var izmantot tikai vienreiz',
    'mfa_gen_totp_title' => 'Mobilās aplikācijas iestatījumi',
    'mfa_gen_totp_desc' => 'Lai lietotu vairākfaktoru autentifikāciju, jums būs nepieciešama mobilā aplikācija, kas atbalsta TOTP, piemēram, Google Authenticator, Authy vai Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Skenējiet zemāk esošo kvadrātkodu (QR) izmantojot savu autentifikācijas aplikāciju.',
    'mfa_gen_totp_verify_setup' => 'Verificēt iestatījumus',
    'mfa_gen_totp_verify_setup_desc' => 'Pārbaudiet, ka viss darbojas, zemāk esošajā laukā ievadot kodu, ko izveidojusi jūsu autentifikācijas aplikācijā:',
    'mfa_gen_totp_provide_code_here' => 'Norādīet jūsu aplikācijā izveidoto kodu šeit',
    'mfa_verify_access' => 'Verificēt piekļuvi',
    'mfa_verify_access_desc' => 'Jūsu lietotāja kontam nepieciešams verificēt jūsu identitāti ar papildus pārbaudes līmeni pirms piešķirta piekļuve. Verificējiet, izmantojot vienu no uzstādītajām metodēm, lai turpinātu.',
    'mfa_verify_no_methods' => 'Nav iestatīta neviena metode',
    'mfa_verify_no_methods_desc' => 'Jūsu kontam nav iestatīta neviena vairākfaktoru autentifikācijas metode. Jums būs nepieciešams iestatīt vismaz vienu metodi, lai iegūtu piekļuvi.',
    'mfa_verify_use_totp' => 'Verificēt, izmantojot mobilo aplikāciju',
    'mfa_verify_use_backup_codes' => 'Verificēt, izmantojot rezerves kodu',
    'mfa_verify_backup_code' => 'Rezerves kods',
    'mfa_verify_backup_code_desc' => 'Zemāk ievadiet vienu no jūsu atlikušajiem rezerves kodiem:',
    'mfa_verify_backup_code_enter_here' => 'Ievadiet rezerves kodu šeit',
    'mfa_verify_totp_desc' => 'Zemāk ievadiet kodu, kas izveidots mobilajā aplikācijā:',
    'mfa_setup_login_notification' => 'Vairākfaktoru metode iestatīta, lūdzu pieslēdzieties atkal izmantojot iestatīto metodi.',
];
