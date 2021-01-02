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
    'password_hint' => 'Må inneholde 7 tegn',
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
    'email_confirm_success' => 'E-posten din er bekreftet!',
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
    'user_invite_success' => 'Passordet er angitt, du kan nå bruke :appName!'
];