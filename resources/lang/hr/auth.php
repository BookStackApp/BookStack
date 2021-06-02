<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Ove vjerodajnice ne podudaraju se s našim zapisima.'
    'throttle' => 'Previše pokušaja prijave. Molimo vas da pokušate za :seconds sekundi.'

    // Login & Register
    'sign_up' => 'Registrirajte se'
    'log_in' => 'Prijavite se'
    'log_in_with' => 'Prijavite se sa :socialDriver'
    'sign_up_with' => 'Registrirajte se sa :socialDriver'
    'logout' => 'Odjavite se'

    'name' => 'Ime'
    'username' => 'Korisničko ime'
    'email' => 'Email'
    'password' => 'Lozinka'
    'password_confirm' => 'Potvrdite lozinku'
    'password_hint' => 'Mora imati više od 7 znakova'
    'forgot_password' => 'Zaboravili ste lozinku?'
    'remember_me' => 'Zapamti me'
    'ldap_email_hint' => 'Molimo upišite mail korišten za ovaj račun.'
    'create_account' => 'Stvori račun'
    'already_have_account' => 'Imate li već račun?'
    'dont_have_account' => 'Nemate račun?'
    'social_login' => 'Social Login'
    'social_registration' => 'Social Registration'
    'social_registration_text' => 'Prijavite se putem drugih servisa.'

    'register_thanks' => 'Zahvaljujemo na registraciji!'
    'register_confirm' => 'Molimo, provjerite svoj email i kliknite gumb za potvrdu pristupa :appName.'
    'registrations_disabled' => 'Registracije su trenutno onemogućene'
    'registration_email_domain_invalid' => 'Ova e-mail adresa se ne može koristiti u ovoj aplikaciji'
    'register_success' => 'Hvala na prijavi! Sada ste registrirani i prijavljeni.'


    // Password Reset
    'reset_password' => 'Promijenite lozinku'
    'reset_password_send_instructions' => 'Upišite svoju e-mail adresu kako biste primili poveznicu za promjenu lozinke.'
    'reset_password_send_button' => 'Pošalji poveznicu za promjenu lozinke'
    'reset_password_sent' => 'Poveznica za promjenu lozinke poslat će se na :email adresu ako je u našem sustavu.'
    'reset_password_success' => 'Vaša lozinka je uspješno promijenjena.'
    'email_reset_subject' => 'Promijenite svoju :appName lozinku'
    'email_reset_text' => 'Primili ste ovu poruku jer je zatražena promjena lozinke za vaš račun.'
    'email_reset_not_requested' => 'Ako niste tražili promjenu lozinke slobodno zanemarite ovu poruku.'


    // Email Confirmation
    'email_confirm_subject' => 'Potvrdite svoju e-mail adresu na :appName'
    'email_confirm_greeting' => 'Hvala na prijavi :appName!'
    'email_confirm_text' => 'Molimo potvrdite svoju e-mail adresu klikom na donji gumb.'
    'email_confirm_action' => 'Potvrdi Email'
    'email_confirm_send_error' => 'Potvrda e-mail adrese je obavezna, ali sustav ne može poslati e-mail. Javite se administratoru kako bi provjerio vaš e-mail.'
    'email_confirm_success' => 'Vaš e-mail adresa je potvrđena!'
    'email_confirm_resent' => 'Ponovno je poslana potvrda. Molimo, provjerite svoj inbox.'

    'email_not_confirmed' => 'E-mail adresa nije potvrđena.'
    'email_not_confirmed_text' => 'Vaša e-mail adresa još nije potvrđena.'
    'email_not_confirmed_click_link' => 'Molimo, kliknite na poveznicu koju ste primili kratko nakon registracije.'
    'email_not_confirmed_resend' => 'Ako ne možete pronaći e-mail za postavljanje lozinke možete ga zatražiti ponovno ispunjavanjem ovog obrasca.'
    'email_not_confirmed_resend_button' => 'Ponovno pošalji e-mail potvrde'

    // User Invite
    'user_invite_email_subject' => 'Pozvani ste pridružiti se :appName!'
    'user_invite_email_greeting' => 'Vaš račun je kreiran za vas na :appName'
    'user_invite_email_text' => 'Kliknite ispod da biste postavili račun i dobili pristup.'
    'user_invite_email_action' => 'Postavite lozinku'
    'user_invite_page_welcome' => 'Dobrodošli u :appName!'
    'user_invite_page_text' => 'Da biste postavili račun i dobili pristup trebate unijeti lozinku kojom ćete se ubuduće prijaviti na :appName.'
    'user_invite_page_confirm_button' => 'Potvrdite lozinku'
    'user_invite_success' => 'Lozinka je postavljena, možete pristupiti :appName!'
];