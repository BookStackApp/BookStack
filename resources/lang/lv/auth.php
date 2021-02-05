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
    'ldap_email_hint' => 'Please enter an email to use for this account.',
    'create_account' => 'Izveidot profilu',
    'already_have_account' => 'Jau ir profils?',
    'dont_have_account' => 'Nav profila?',
    'social_login' => 'Pieteikšanās ar sociālo tīklu profilu',
    'social_registration' => 'Social Registration',
    'social_registration_text' => 'Register and sign in using another service.',

    'register_thanks' => 'Paldies par reģistrāciju!',
    'register_confirm' => 'Please check your email and click the confirmation button to access :appName.',
    'registrations_disabled' => 'Reģistrācija ir izslēgta',
    'registration_email_domain_invalid' => 'E-pasta domēnam nav piekļuves pie šīs aplikācijas',
    'register_success' => 'Thanks for signing up! You are now registered and signed in.',


    // Password Reset
    'reset_password' => 'Atiestatīt paroli',
    'reset_password_send_instructions' => 'Enter your email below and you will be sent an email with a password reset link.',
    'reset_password_send_button' => 'Nosūtīt atjaunošanas saiti',
    'reset_password_sent' => 'Paroles atiestatīšanas saite tiks nosūtīta uz :email, ja šāds e-pasts eksistē.',
    'reset_password_success' => 'Jūsu parole ir veiksmīgi atiestatīta.',
    'email_reset_subject' => 'Atiestatīt :appName paroli',
    'email_reset_text' => 'Jūs saņemat šo e-pastu, jo mēs saņēmām Jūsu profila paroles atiestatīšanas pieprasījumu.',
    'email_reset_not_requested' => 'Ja Jūs nepieprasījāt paroles atiestatīšanu, tad tālākas darbības nav nepieciešamas.',


    // Email Confirmation
    'email_confirm_subject' => 'Apstiprinat savu :appName e-pastu',
    'email_confirm_greeting' => 'Paldies, ka pievienojāties :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Apstiprināt e-pastu',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Jūsu e-pasts ir apstiprināts!',
    'email_confirm_resent' => 'Apstiprinājuma vēstule tika nosūtīta. Lūdzu, pārbaudiet jūsu e-pastu.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Iestatīt profila paroli',
    'user_invite_page_welcome' => 'Sveicināti :appName!',
    'user_invite_page_text' => 'Lai pabeigtu profila izveidi un piekļūtu :appName ir jāizveido parole.',
    'user_invite_page_confirm_button' => 'Apstiprināt paroli',
    'user_invite_success' => 'Parole iestatīta, tagad varat piekļūt :appName!'
];