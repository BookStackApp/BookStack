<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Aceste credenţiale nu se potrivesc cu înregistrările noastre.',
    'throttle' => 'Prea multe încercări de conectare. Vă rugăm să încercați din nou în :seconds secunde.',

    // Login & Register
    'sign_up' => 'Inregistrează-te',
    'log_in' => 'Autentifică-te',
    'log_in_with' => 'Autentifică-te cu :socialDriver',
    'sign_up_with' => 'Inregistrează-te cu :socialDriver',
    'logout' => 'Deconectează-te',

    'name' => 'Nume',
    'username' => 'Nume utilizator',
    'email' => 'E-mail',
    'password' => 'Parolă',
    'password_confirm' => 'Confirmă parola',
    'password_hint' => 'Trebuie să aibă cel puțin 8 caractere',
    'forgot_password' => 'Ai uitat parola?',
    'remember_me' => 'Amintește-ți de mine',
    'ldap_email_hint' => 'Te rog să introduci o adresa de e-mail pentru a utiliza acest cont.',
    'create_account' => 'Crează cont',
    'already_have_account' => 'Ai deja cont?',
    'dont_have_account' => 'Nu ai cont?',
    'social_login' => 'Conectare folosind rețea socială',
    'social_registration' => 'Înregistrare cu rețea socială',
    'social_registration_text' => 'Înregistrați-vă și conectați-vă utilizând alt serviciu.',

    'register_thanks' => 'Mulțumesc pentru înregistrare!',
    'register_confirm' => 'Verifică-ți e-mailul și dă clic pe butonul de confirmare pentru a accesa :appName.',
    'registrations_disabled' => 'Înregistrările sunt momentan dezactivate',
    'registration_email_domain_invalid' => 'Acel domeniu de e-mail nu are acces la această aplicație',
    'register_success' => 'Mulțumesc pentru înscriere! Acum sunteți înregistrat și conectat.',

    // Login auto-initiation
    'auto_init_starting' => 'Se încearcă autentificarea',
    'auto_init_starting_desc' => 'Vă contactăm sistemul de autentificare pentru a începe procesul de conectare. Dacă nu există niciun progres după 5 secunde, puteți încerca să dați clic pe linkul de mai jos.',
    'auto_init_start_link' => 'Continuă cu autentificarea',

    // Password Reset
    'reset_password' => 'Resetează parola',
    'reset_password_send_instructions' => 'Introduceți adresa de e-mail mai jos și vi se va trimite un e-mail cu un link pentru resetarea parolei.',
    'reset_password_send_button' => 'Trimite linkul de resetare',
    'reset_password_sent' => 'Un link pentru resetarea parolei va fi trimis la :email dacă acea adresă de e-mail este găsită în sistem.',
    'reset_password_success' => 'Parola a fost resetată cu succes.',
    'email_reset_subject' => 'Resetează parola ta :appName',
    'email_reset_text' => 'Primești acest e-mail deoarece am primit o solicitare de resetare a parolei pentru contul tău.',
    'email_reset_not_requested' => 'Dacă nu ai solicitat resetarea parolei, nu este necesară nicio acțiune suplimentară.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmă e-mailul pentru :appName',
    'email_confirm_greeting' => 'Mulțumim că te-ai alăturat :appName!',
    'email_confirm_text' => 'Te rog să confirmi adresa ta de e-mail făcând clic pe butonul de mai jos:',
    'email_confirm_action' => 'Confirmă emailul',
    'email_confirm_send_error' => 'Este necesară confirmarea prin e-mail, dar sistemul nu a putut trimite e-mailul. Contactează administratorul pentru a te asigura că e-mailul este configurat corect.',
    'email_confirm_success' => 'E-mailul a fost confirmat! Acum ar trebui să te poți autentifica folosind această adresă de e-mail.',
    'email_confirm_resent' => 'E-mailul de confirmare a fost retrimis, te rugăm să îți verifici căsuța de e-mail.',

    'email_not_confirmed' => 'Adresa de e-mail neconfirmată',
    'email_not_confirmed_text' => 'Adresa ta de e-mail nu a fost încă confirmată.',
    'email_not_confirmed_click_link' => 'Accesează linkul din e-mailul care a fost trimis la scurt timp după ce te-ai înregistrat.',
    'email_not_confirmed_resend' => 'Dacă nu găsești e-mailul, poți retrimite e-mailul de confirmare completând formularul de mai jos.',
    'email_not_confirmed_resend_button' => 'Retrimite e-mail de confirmare',

    // User Invite
    'user_invite_email_subject' => 'Ai fost invitat să te alături :appName!',
    'user_invite_email_greeting' => 'A fost creat un cont pentru tine pe :appName.',
    'user_invite_email_text' => 'Fă clic pe butonul de mai jos pentru a seta o parolă de cont și a obține acces:',
    'user_invite_email_action' => 'Setează parola contului',
    'user_invite_page_welcome' => 'Bun venit la :appName!',
    'user_invite_page_text' => 'Pentru a vă finaliza configurarea contului și a obține acces, trebuie să setezi o parolă care va fi folosită pentru a te conecta la :appName la vizitele viitoare.',
    'user_invite_page_confirm_button' => 'Confirmă parola',
    'user_invite_success_login' => 'Parola setată, acum ar trebui să te poți autentifica folosind parola setată pentru a accesa :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configurează autentificarea cu mai mulți factori de autentificare',
    'mfa_setup_desc' => 'Configurează autentificarea cu mai mulți factori ca un nivel suplimentar de securitate pentru contul tău de utilizator.',
    'mfa_setup_configured' => 'Deja configurat',
    'mfa_setup_reconfigure' => 'Reconfigurează',
    'mfa_setup_remove_confirmation' => 'Sigur dorești să elimini această metodă de autentificare cu mai mulți factori?',
    'mfa_setup_action' => 'Configurare',
    'mfa_backup_codes_usage_limit_warning' => 'Mai ai mai puțin de 5 coduri de rezervă rămase. Vă rugăm să generați și să stocați un nou set înainte de a rămâne fără coduri pentru a preveni blocarea contului.',
    'mfa_option_totp_title' => 'Aplicație mobilă',
    'mfa_option_totp_desc' => 'Pentru a utiliza autentificarea multifactor, vei avea nevoie de o aplicație mobilă care acceptă TOTP, cum ar fi Google Authenticator, Authy sau Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Coduri de rezervă',
    'mfa_option_backup_codes_desc' => 'Stochează în siguranță un set de coduri de rezervă de unică folosință pe care le poți introduce pentru verificarea identității.',
    'mfa_gen_confirm_and_enable' => 'Confirmă și activează',
    'mfa_gen_backup_codes_title' => 'Configurarea codurilor de rezervă',
    'mfa_gen_backup_codes_desc' => 'Păstrează lista de coduri de mai jos într-un loc sigur. Când accesezi sistemul, vei putea folosi unul dintre coduri ca un al doilea mecanism de autentificare.',
    'mfa_gen_backup_codes_download' => 'Descarcă codurile',
    'mfa_gen_backup_codes_usage_warning' => 'Fiecare cod poate fi folosit o singură dată',
    'mfa_gen_totp_title' => 'Configurare aplicație mobilă',
    'mfa_gen_totp_desc' => 'Pentru a utiliza autentificarea cu mai mulți factori, vei avea nevoie de o aplicație mobilă care acceptă TOTP, cum ar fi Google Authenticator, Authy sau Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scanează codul QR de mai jos folosind aplicația de autentificare preferată pentru a începe.',
    'mfa_gen_totp_verify_setup' => 'Verifică configurarea',
    'mfa_gen_totp_verify_setup_desc' => 'Verifică dacă totul funcționează introducând un cod, generat în aplicația de autentificare, în caseta de introducere de mai jos:',
    'mfa_gen_totp_provide_code_here' => 'Furnizează aici codul generat de aplicație',
    'mfa_verify_access' => 'Verifică accesul',
    'mfa_verify_access_desc' => 'Contul tău de utilizator necesită să îți confirmi identitatea printr-un nivel suplimentar de verificare înainte de acordare acces. Verifică folosind una dintre metodele configurate pentru a continua.',
    'mfa_verify_no_methods' => 'Nicio metodă configurată',
    'mfa_verify_no_methods_desc' => 'Nu s-au găsit metode de autentificare cu mai mulți factori pentru contul tău. Va trebui să configurezi cel puțin o metodă înainte de a obține acces.',
    'mfa_verify_use_totp' => 'Verifică folosind o aplicație mobilă',
    'mfa_verify_use_backup_codes' => 'Verifică folosind un cod de rezervă',
    'mfa_verify_backup_code' => 'Cod de rezervă',
    'mfa_verify_backup_code_desc' => 'Introdu unul dintre codurile de rezervă rămase mai jos:',
    'mfa_verify_backup_code_enter_here' => 'Introdu codul de rezervă aici',
    'mfa_verify_totp_desc' => 'Introdu codul, generat folosind aplicația mobilă, mai jos:',
    'mfa_setup_login_notification' => 'Metodă multifactorială configurată. Te rog să te conectezi din nou folosind metoda configurată.',
];
