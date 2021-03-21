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
    'user_invite_success' => 'Parole iestatīta, tagad varat piekļūt :appName!'
];