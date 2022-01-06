<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Disse detaljene samsvarer ikke med det vi har på bok.',
    'throttle' => 'For mange forsøk, prøv igjen om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Registrer deg',
    'log_in' => 'Logg inn',
    'log_in_with' => 'Logg inn med :socialDriver',
    'sign_up_with' => 'Registrer med :socialDriver',
    'logout' => 'Logg ut',

    'name' => 'Navn',
    'username' => 'Brukernavn',
    'email' => 'E-post',
    'password' => 'Passord',
    'password_confirm' => 'Bekreft passord',
    'password_hint' => 'Must be at least 8 characters',
    'forgot_password' => 'Glemt passord?',
    'remember_me' => 'Husk meg',
    'ldap_email_hint' => 'Oppgi en e-post for denne kontoen.',
    'create_account' => 'Opprett konto',
    'already_have_account' => 'Har du allerede en konto?',
    'dont_have_account' => 'Mangler du en konto?',
    'social_login' => 'Sosiale kontoer',
    'social_registration' => 'Registrer via sosiale kontoer',
    'social_registration_text' => 'Bruk en annen tjeneste for å registrere deg.',

    'register_thanks' => 'Takk for at du registrerte deg!',
    'register_confirm' => 'Sjekk e-posten din for informasjon som gir deg tilgang til :appName.',
    'registrations_disabled' => 'Registrering er deaktivert.',
    'registration_email_domain_invalid' => 'Du kan ikke bruke det domenet for å registrere en konto.',
    'register_success' => 'Takk for registreringen! Du kan nå logge inn på tjenesten.',

    // Password Reset
    'reset_password' => 'Nullstille passord',
    'reset_password_send_instructions' => 'Oppgi e-posten som er koblet til kontoen din, så sender vi en epost hvor du kan nullstille passordet.',
    'reset_password_send_button' => 'Send nullstillingslenke',
    'reset_password_sent' => 'En nullstillingslenke ble sendt til :email om den eksisterer i systemet.',
    'reset_password_success' => 'Passordet ble nullstilt.',
    'email_reset_subject' => 'Nullstill ditt :appName passord',
    'email_reset_text' => 'Du mottar denne eposten fordi det er blitt bedt om en nullstilling av passord på denne kontoen.',
    'email_reset_not_requested' => 'Om det ikke var deg, så trenger du ikke foreta deg noe.',

    // Email Confirmation
    'email_confirm_subject' => 'Bekreft epost-adressen for :appName',
    'email_confirm_greeting' => 'Takk for at du registrerte deg for :appName!',
    'email_confirm_text' => 'Bekreft e-posten din ved å trykke på knappen nedenfor:',
    'email_confirm_action' => 'Bekreft e-post',
    'email_confirm_send_error' => 'Bekreftelse er krevd av systemet, men systemet kan ikke sende disse. Kontakt admin for å løse problemet.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Bekreftelsespost ble sendt, sjekk innboksen din.',

    'email_not_confirmed' => 'E-posten er ikke bekreftet.',
    'email_not_confirmed_text' => 'Epost-adressen er ennå ikke bekreftet.',
    'email_not_confirmed_click_link' => 'Trykk på lenken i e-posten du fikk vedrørende din registrering.',
    'email_not_confirmed_resend' => 'Om du ikke finner den i innboksen eller søppelboksen, kan du få tilsendt ny ved å trykke på knappen under.',
    'email_not_confirmed_resend_button' => 'Send bekreftelsespost på nytt',

    // User Invite
    'user_invite_email_subject' => 'Du har blitt invitert til :appName!',
    'user_invite_email_greeting' => 'En konto har blitt opprettet for deg på :appName.',
    'user_invite_email_text' => 'Trykk på knappen under for å opprette et sikkert passord:',
    'user_invite_email_action' => 'Angi passord',
    'user_invite_page_welcome' => 'Velkommen til :appName!',
    'user_invite_page_text' => 'For å fullføre prosessen må du oppgi et passord som sikrer din konto på :appName for fremtidige besøk.',
    'user_invite_page_confirm_button' => 'Bekreft passord',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Konfigurer flerfaktor-autentisering',
    'mfa_setup_desc' => 'Konfigurer flerfaktor-autentisering som et ekstra lag med sikkerhet for brukerkontoen din.',
    'mfa_setup_configured' => 'Allerede konfigurert',
    'mfa_setup_reconfigure' => 'Omkonfigurer',
    'mfa_setup_remove_confirmation' => 'Er du sikker på at du vil deaktivere denne flerfaktor-autentiseringsmetoden?',
    'mfa_setup_action' => 'Konfigurasjon',
    'mfa_backup_codes_usage_limit_warning' => 'Du har mindre enn 5 sikkerhetskoder igjen; vennligst generer og lagre ett nytt sett før du går tom for koder, for å unngå å bli låst ute av kontoen din.',
    'mfa_option_totp_title' => 'Mobilapplikasjon',
    'mfa_option_totp_desc' => 'For å bruke flerfaktorautentisering trenger du en mobilapplikasjon som støtter TOTP-teknologien, slik som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Sikkerhetskoder',
    'mfa_option_backup_codes_desc' => 'Lagre sikkerhetskoder til engangsbruk på et trygt sted, disse kan du bruke for å verifisere identiteten din.',
    'mfa_gen_confirm_and_enable' => 'Bekreft og aktiver',
    'mfa_gen_backup_codes_title' => 'Konfigurasjon av sikkerhetskoder',
    'mfa_gen_backup_codes_desc' => 'Lagre nedeforstående liste med koder på et trygt sted. Når du skal ha tilgang til systemet kan du bruke en av disse som en faktor under innlogging.',
    'mfa_gen_backup_codes_download' => 'Last ned koder',
    'mfa_gen_backup_codes_usage_warning' => 'Hver kode kan kun brukes en gang',
    'mfa_gen_totp_title' => 'Oppsett for mobilapplikasjon',
    'mfa_gen_totp_desc' => 'For å bruke flerfaktorautentisering trenger du en mobilapplikasjon som støtter TOTP-teknologien, slik som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan QR-koden nedenfor med valgt TOTP-applikasjon for å starte.',
    'mfa_gen_totp_verify_setup' => 'Bekreft oppsett',
    'mfa_gen_totp_verify_setup_desc' => 'Bekreft at oppsettet fungerer ved å skrive inn koden fra TOTP-applikasjonen i boksen nedenfor:',
    'mfa_gen_totp_provide_code_here' => 'Skriv inn den genererte koden her',
    'mfa_verify_access' => 'Bekreft tilgang',
    'mfa_verify_access_desc' => 'Brukerkontoen din krever at du bekrefter din identitet med en ekstra autentiseringsfaktor før du får tilgang. Bekreft identiteten med en av dine konfigurerte metoder for å fortsette.',
    'mfa_verify_no_methods' => 'Ingen metoder er konfigurert',
    'mfa_verify_no_methods_desc' => 'Ingen flerfaktorautentiseringsmetoder er satt opp for din konto. Du må sette opp minst en metode for å få tilgang.',
    'mfa_verify_use_totp' => 'Bekreft med mobilapplikasjon',
    'mfa_verify_use_backup_codes' => 'Bekreft med sikkerhetskode',
    'mfa_verify_backup_code' => 'Sikkerhetskode',
    'mfa_verify_backup_code_desc' => 'Skriv inn en av dine ubrukte sikkerhetskoder under:',
    'mfa_verify_backup_code_enter_here' => 'Skriv inn sikkerhetskode her',
    'mfa_verify_totp_desc' => 'Skriv inn koden, generert ved hjelp av mobilapplikasjonen, nedenfor:',
    'mfa_setup_login_notification' => 'Flerfaktorautentisering er konfigurert, vennligst logg inn på nytt med denne metoden.',
];
