<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Ove vjerodajnice ne podudaraju se s našim zapisima.',
    'throttle' => 'Previše pokušaja prijave. Molimo vas da pokušate za :seconds sekundi.',

    // Login & Register
    'sign_up' => 'Registrirajte se',
    'log_in' => 'Prijavite se',
    'log_in_with' => 'Prijavite se sa :socialDriver',
    'sign_up_with' => 'Registrirajte se sa :socialDriver',
    'logout' => 'Odjavite se',

    'name' => 'Ime',
    'username' => 'Korisničko ime',
    'email' => 'E-pošta',
    'password' => 'Lozinka',
    'password_confirm' => 'Potvrdite lozinku',
    'password_hint' => 'Mora biti najmanje 8 znakova',
    'forgot_password' => 'Zaboravili ste lozinku?',
    'remember_me' => 'Zapamti me',
    'ldap_email_hint' => 'Molimo upišite mail korišten za ovaj račun.',
    'create_account' => 'Stvori račun',
    'already_have_account' => 'Imate li već račun?',
    'dont_have_account' => 'Nemate račun?',
    'social_login' => 'Mrežna Prijava',
    'social_registration' => 'Mrežna Registracija',
    'social_registration_text' => 'Prijavite se putem drugih servisa.',

    'register_thanks' => 'Zahvaljujemo na registraciji!',
    'register_confirm' => 'Molimo, provjerite svoj email i kliknite gumb za potvrdu pristupa :appName.',
    'registrations_disabled' => 'Registracije su trenutno onemogućene',
    'registration_email_domain_invalid' => 'Ova e-mail adresa se ne može koristiti u ovoj aplikaciji',
    'register_success' => 'Hvala na prijavi! Sada ste registrirani i prijavljeni.',

    // Login auto-initiation
    'auto_init_starting' => 'Pokušaj Prijave',
    'auto_init_starting_desc' => 'Kontaktiramo vaš sustav za autentifikaciju kako bismo započeli postupak prijave. Ako ne postoji napredak nakon 5 sekundi, možete pokušati kliknuti donju poveznicu.',
    'auto_init_start_link' => 'Nastavite s autentifikacijom',

    // Password Reset
    'reset_password' => 'Promijenite lozinku',
    'reset_password_send_instructions' => 'Upišite svoju e-mail adresu kako biste primili poveznicu za promjenu lozinke.',
    'reset_password_send_button' => 'Pošalji poveznicu za promjenu lozinke',
    'reset_password_sent' => 'Poveznica za promjenu lozinke poslat će se na :email adresu ako je u našem sustavu.',
    'reset_password_success' => 'Vaša lozinka je uspješno promijenjena.',
    'email_reset_subject' => 'Promijenite svoju :appName lozinku',
    'email_reset_text' => 'Primili ste ovu poruku jer je zatražena promjena lozinke za vaš račun.',
    'email_reset_not_requested' => 'Ako niste tražili promjenu lozinke slobodno zanemarite ovu poruku.',

    // Email Confirmation
    'email_confirm_subject' => 'Potvrdite svoju e-mail adresu na :appName',
    'email_confirm_greeting' => 'Hvala na prijavi :appName!',
    'email_confirm_text' => 'Molimo potvrdite svoju e-mail adresu klikom na donji gumb.',
    'email_confirm_action' => 'Potvrdi Email',
    'email_confirm_send_error' => 'Potvrda e-mail adrese je obavezna, ali sustav ne može poslati e-mail. Javite se administratoru kako bi provjerio vaš e-mail.',
    'email_confirm_success' => 'Vaša e-pošta je potvrđena! Sada biste se trebali moći prijaviti koristeći danu e-poštu.',
    'email_confirm_resent' => 'Ponovno je poslana potvrda. Molimo, provjerite svoj inbox.',
    'email_confirm_thanks' => 'Zahvaljujemo na potvrdi!',
    'email_confirm_thanks_desc' => 'Molimo pričekajte trenutak dok se obrađuje vaša potvrda. Ako ne budete preusmjereni nakon 3 sekunde, pritisnite "Nastavi" poveznicu ispod kako biste nastavili.',

    'email_not_confirmed' => 'E-mail adresa nije potvrđena.',
    'email_not_confirmed_text' => 'Vaša e-mail adresa još nije potvrđena.',
    'email_not_confirmed_click_link' => 'Molimo, kliknite na poveznicu koju ste primili kratko nakon registracije.',
    'email_not_confirmed_resend' => 'Ako ne možete pronaći e-mail za postavljanje lozinke možete ga zatražiti ponovno ispunjavanjem ovog obrasca.',
    'email_not_confirmed_resend_button' => 'Ponovno pošalji e-mail potvrde',

    // User Invite
    'user_invite_email_subject' => 'Pozvani ste pridružiti se :appName!',
    'user_invite_email_greeting' => 'Vaš račun je kreiran za vas na :appName',
    'user_invite_email_text' => 'Kliknite ispod da biste postavili račun i dobili pristup.',
    'user_invite_email_action' => 'Postavite lozinku',
    'user_invite_page_welcome' => 'Dobrodošli u :appName!',
    'user_invite_page_text' => 'Da biste postavili račun i dobili pristup trebate unijeti lozinku kojom ćete se ubuduće prijaviti na :appName.',
    'user_invite_page_confirm_button' => 'Potvrdite lozinku',
    'user_invite_success_login' => 'Lozinka je postavljena, sada biste se trebali moći prijaviti koristeći postavljenu lozinku kako biste pristupili aplikaciji :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Postavite Višestruku Autentifikaciju',
    'mfa_setup_desc' => 'Postavite višestruku provjeru autentičnosti kao dodatni sloj sigurnosti za svoj korisnički račun.',
    'mfa_setup_configured' => 'Već postavljeno',
    'mfa_setup_reconfigure' => 'Ponovno postavite',
    'mfa_setup_remove_confirmation' => 'Jeste li sigurni da želite ukloniti ovu metodu višestruke provjere autentičnosti?',
    'mfa_setup_action' => 'Postavke',
    'mfa_backup_codes_usage_limit_warning' => 'Imate manje od 5 preostalih rezervnih kodova. Molimo generirajte i pohranite novi set prije nego što vam kodovi ponestanu kako biste izbjegli blokadu pristupa vašem računu.',
    'mfa_option_totp_title' => 'Mobilna Aplikacija',
    'mfa_option_totp_desc' => 'Da biste koristili višestruku provjeru autentičnosti, trebat će vam mobilna aplikacija koja podržava TOTP (Time-Based One-Time Password) kao što su Google Authenticator, Authy ili Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Rezervni Kodovi',
    'mfa_option_backup_codes_desc' => 'Sigurno pohranite set jednokratnih rezervnih kodova koje možete unijeti kako biste potvrdili svoj identitet.',
    'mfa_gen_confirm_and_enable' => 'Potvrdi i Omogući',
    'mfa_gen_backup_codes_title' => 'Postavke Rezervnih Kodova',
    'mfa_gen_backup_codes_desc' => 'Spremite sljedeći popis kodova na sigurno mjesto. Prilikom pristupa sustavu, moći ćete koristiti jedan od ovih kodova kao drugi mehanizam autentifikacije.',
    'mfa_gen_backup_codes_download' => 'Preuzmi Kodove',
    'mfa_gen_backup_codes_usage_warning' => 'Pojedinačni kod se može koristiti samo jednom',
    'mfa_gen_totp_title' => 'Postavka Mobilne Aplikacije',
    'mfa_gen_totp_desc' => 'Da biste koristili višestruku provjeru autentičnosti, trebat će vam mobilna aplikacija koja podržava TOTP (Time-Based One-Time Password) kao što su Google Authenticator, Authy ili Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Skenirajte QR kod u nastavku koristeći svoju preferiranu aplikaciju za autentifikaciju kako biste započeli.',
    'mfa_gen_totp_verify_setup' => 'Potvrdite Postavke',
    'mfa_gen_totp_verify_setup_desc' => 'Potvrdite da sve radi unosom koda koji je generiran unutar vaše aplikacije za autentifikaciju u donje polje za unos:',
    'mfa_gen_totp_provide_code_here' => 'Dostavite generirani kod ovdje',
    'mfa_verify_access' => 'Potvrdite Pristup',
    'mfa_verify_access_desc' => 'Vaš korisnički račun zahtijeva potvrdu vašeg identiteta putem dodatne razine provjere prije nego vam se omogući pristup. Molimo potvrdite korištenjem jedne od konfiguriranih metoda kako biste nastavili.',
    'mfa_verify_no_methods' => 'Nema Postavljenih Metoda',
    'mfa_verify_no_methods_desc' => 'Nisu pronađene metode višestruke provjere autentičnosti za vaš korisnički račun. Morat ćete postaviti barem jednu metodu prije nego dobijete pristup.',
    'mfa_verify_use_totp' => 'Potvrda korištenjem mobilne aplikacije',
    'mfa_verify_use_backup_codes' => 'Potvrda korištenjem rezervnog koda',
    'mfa_verify_backup_code' => 'Rezervni Kod',
    'mfa_verify_backup_code_desc' => 'Unesite jedan od preostalih rezervnih kodova u nastavku:',
    'mfa_verify_backup_code_enter_here' => 'Ovdje unesite rezervni kod',
    'mfa_verify_totp_desc' => 'Unesite kod generiran pomoću vaše mobilne aplikacije u nastavku:',
    'mfa_setup_login_notification' => 'Višestruka metoda autentifikacije konfigurirana. Molimo prijavite se ponovno koristeći konfiguriranu metodu.',
];
