<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Desse detaljane samsvarar ikkje med det me har på bok.',
    'throttle' => 'For mange forsøk, prøv på nytt om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Registrer deg',
    'log_in' => 'Logg inn',
    'log_in_with' => 'Logg inn med :socialDriver',
    'sign_up_with' => 'Registrer med :socialDriver',
    'logout' => 'Logg ut',

    'name' => 'Namn',
    'username' => 'Brukarnamn',
    'email' => 'E-post',
    'password' => 'Passord',
    'password_confirm' => 'Stadfest passord',
    'password_hint' => 'Må vere minst 8 teikn',
    'forgot_password' => 'Gløymt passord?',
    'remember_me' => 'Hugs meg',
    'ldap_email_hint' => 'Oppgi ein e-post for denne kontoen.',
    'create_account' => 'Opprett konto',
    'already_have_account' => 'Har du allereie ein konto?',
    'dont_have_account' => 'Manglar du ein konto?',
    'social_login' => 'Sosiale kontoar',
    'social_registration' => 'Registrer via sosiale kontoar',
    'social_registration_text' => 'Bruk ei anna teneste for å registrere deg.',

    'register_thanks' => 'Takk for at du registrerte deg!',
    'register_confirm' => 'Sjekk e-posten din for informasjon som gir deg tilgang til :appName.',
    'registrations_disabled' => 'Registrering er deaktivert.',
    'registration_email_domain_invalid' => 'Du kan ikkje bruke det domenet for å registrere ein konto',
    'register_success' => 'Takk for registreringa! Du kan no logge inn på tenesta.',

    // Login auto-initiation
    'auto_init_starting' => 'Prøver innlogging',
    'auto_init_starting_desc' => 'Me kontaktar autentiseringssystemet ditt for å starte innloggingsprosessen. Dersom det ikkje er noko framdrift i løpet av fem sekunder kan du trykke på lenka under.',
    'auto_init_start_link' => 'Fortsett med autentisering',

    // Password Reset
    'reset_password' => 'Nullstill passord',
    'reset_password_send_instructions' => 'Oppgi e-posten kobla til kontoen din, så sender me ein e-post der du kan nullstille passordet.',
    'reset_password_send_button' => 'Send nullstillingslenke',
    'reset_password_sent' => 'Ei nullstillingslenke vart sendt til :email om den eksisterer i systemet.',
    'reset_password_success' => 'Passordet vart nullstilt.',
    'email_reset_subject' => 'Nullstill ditt :appName passord',
    'email_reset_text' => 'Du mottar denne e-posten fordi det er blitt bedt om ei nullstilling av passord for denne kontoen.',
    'email_reset_not_requested' => 'Om det ikkje var deg, så treng du ikkje gjere noko.',

    // Email Confirmation
    'email_confirm_subject' => 'Stadfest e-postadressa for :appName',
    'email_confirm_greeting' => 'Takk for at du registrerte deg på :appName!',
    'email_confirm_text' => 'Stadfest e-posten din ved å trykke på knappen under:',
    'email_confirm_action' => 'Stadfest e-post',
    'email_confirm_send_error' => 'Stadfesting er krevd av systemet, men systemet kan ikkje sende desse. Kontakt admin for å løyse problemet.',
    'email_confirm_success' => 'E-postadressa di er verifisert! Du kan no logge inn ved å bruke denne ved innlogging.',
    'email_confirm_resent' => 'Stadfesting sendt på e-post, sjekk innboksen din.',
    'email_confirm_thanks' => 'Takk for verifiseringa!',
    'email_confirm_thanks_desc' => 'Vent litt medan me verifiserer. Om du ikkje vert sendt vidare i løpet av tre sekunder, kan du klikke på "Fortsett" under.',

    'email_not_confirmed' => 'E-posten er ikkje stadfesta',
    'email_not_confirmed_text' => 'E-postadressa er ennå ikkje stadfesta.',
    'email_not_confirmed_click_link' => 'Trykk på lenka i e-posten du fekk då du registrerte deg.',
    'email_not_confirmed_resend' => 'Finner du den ikkje i innboks eller useriøs e-post? Trykk på knappen under for å få ny.',
    'email_not_confirmed_resend_button' => 'Send stadfesting på e-post på nytt',

    // User Invite
    'user_invite_email_subject' => 'Du har blitt invitert til :appName!',
    'user_invite_email_greeting' => 'Ein konto har blitt oppretta for deg på :appName.',
    'user_invite_email_text' => 'Trykk på knappen under for å opprette eit sikkert passord:',
    'user_invite_email_action' => 'Skriv inn passord',
    'user_invite_page_welcome' => 'Velkommen til :appName!',
    'user_invite_page_text' => 'For å fullføre prosessen må du oppgi eit passord som sikrar din konto på :appName for neste besøk.',
    'user_invite_page_confirm_button' => 'Stadfest passord',
    'user_invite_success_login' => 'Passordet vart lagra, du skal nå kunne logge inn med ditt nye passord for å få tilgang til :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Konfigurer fleirfaktor-autentisering',
    'mfa_setup_desc' => 'Konfigurer fleirfaktor-autentisering som eit ekstra lag med tryggleik for brukarkontoen din.',
    'mfa_setup_configured' => 'Allereie konfigurert',
    'mfa_setup_reconfigure' => 'Konfigurer på nytt',
    'mfa_setup_remove_confirmation' => 'Er du sikker på at du vil deaktivere denne fleirfaktor-autentiseringsmetoden?',
    'mfa_setup_action' => 'Konfigurasjon',
    'mfa_backup_codes_usage_limit_warning' => 'Du har mindre enn 5 tryggleikskodar igjen; generer gjerne nye og lagre eit nytt sett før du går tom for kodar. Då slepper du å bli låst ute frå kontoen din.',
    'mfa_option_totp_title' => 'Mobilapplikasjon',
    'mfa_option_totp_desc' => 'For å bruka fleirfaktorautentisering treng du ein mobilapplikasjon som støttar TOTP-teknologien, slik som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Tryggleikskodar',
    'mfa_option_backup_codes_desc' => 'Genererer et sett med engangs sikkerhetskoder som du skal logge inn for å bekrefte identiteten din. Sørg for å lagre disse på et sikkert og sikkert sted.',
    'mfa_gen_confirm_and_enable' => 'Stadfest og aktiver',
    'mfa_gen_backup_codes_title' => 'Konfigurasjon av tryggleikskodar',
    'mfa_gen_backup_codes_desc' => 'Lagre lista under med kodar på ein trygg stad. Når du skal ha tilgang til systemet kan du bruka ein av desse som ein faktor under innlogging.',
    'mfa_gen_backup_codes_download' => 'Last ned kodar',
    'mfa_gen_backup_codes_usage_warning' => 'Kvar kode kan berre brukast ein gong',
    'mfa_gen_totp_title' => 'Oppsett for mobilapplikasjon',
    'mfa_gen_totp_desc' => 'For å bruka fleirfaktorautentisering treng du ein mobilapplikasjon som støttar TOTP-teknologien, slik som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan QR-koden nedanfor med vald TOTP-applikasjon for å starta.',
    'mfa_gen_totp_verify_setup' => 'Stadfest oppsett',
    'mfa_gen_totp_verify_setup_desc' => 'Stadfest at oppsettet fungerer ved å skrive inn koden fra TOTP-applikasjonen i boksen nedanfor:',
    'mfa_gen_totp_provide_code_here' => 'Skriv inn den genererte koden her',
    'mfa_verify_access' => 'Stadfest tilgang',
    'mfa_verify_access_desc' => 'Brukarkontoen din krev at du stadfestar identiteten din med ein ekstra autentiseringsfaktor før du får tilgang. Stadfest identiteten med ein av dine konfigurerte metodar for å halda fram.',
    'mfa_verify_no_methods' => 'Ingen metodar er konfigurert',
    'mfa_verify_no_methods_desc' => 'Ingen fleirfaktorautentiseringsmetoder er satt opp for din konto. Du må setje opp minst ein metode for å få tilgang.',
    'mfa_verify_use_totp' => 'Stadfest med mobilapplikasjon',
    'mfa_verify_use_backup_codes' => 'Stadfest med tryggleikskode',
    'mfa_verify_backup_code' => 'Tryggleikskode',
    'mfa_verify_backup_code_desc' => 'Skriv inn ein av dei ubrukte tryggleikskodane dine under:',
    'mfa_verify_backup_code_enter_here' => 'Skriv inn tryggleikskode her',
    'mfa_verify_totp_desc' => 'Skriv inn koden, generert ved hjelp av mobilapplikasjonen, nedanfor:',
    'mfa_setup_login_notification' => 'Fleirfaktorautentisering er konfigurert, vennlegast logg inn på nytt med denne metoden.',
];
