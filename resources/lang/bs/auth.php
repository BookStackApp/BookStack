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
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed!',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];