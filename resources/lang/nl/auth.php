<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => 'Deze inloggegevens zijn niet bij ons bekend.',
    'throttle' => 'Te veel loginpogingen! Probeer het opnieuw na :seconds seconden.',

    /**
     * Login & Register
     */
    'sign_up' => 'Registreren',
    'log_in' => 'Log in',
    'log_in_with' => 'Login met :socialDriver',
    'sign_up_with' => 'Registreer met :socialDriver',
    'logout' => 'Uitloggen',

    'name' => 'Naam',
    'username' => 'Gebruikersnaam',
    'email' => 'Email',
    'password' => 'Wachtwoord',
    'password_confirm' => 'Wachtwoord Bevestigen',
    'password_hint' => 'Minimaal 5 tekens',
    'forgot_password' => 'Wachtwoord vergeten?',
    'remember_me' => 'Mij onthouden',
    'ldap_email_hint' => 'Geef een email op waarmee je dit account wilt gebruiken.',
    'create_account' => 'Account Aanmaken',
    'social_login' => 'Social Login',
    'social_registration' => 'Social Registratie',
    'social_registration_text' => 'Registreer en log in met een andere dienst.',

    'register_thanks' => 'Bedankt voor het registreren!',
    'register_confirm' => 'Controleer je e-mail en bevestig je registratie om in te loggen op :appName.',
    'registrations_disabled' => 'Registratie is momenteel niet mogelijk',
    'registration_email_domain_invalid' => 'Dit e-maildomein is niet toegestaan',
    'register_success' => 'Bedankt voor het inloggen. Je bent ook geregistreerd.',


    /**
     * Password Reset
     */
    'reset_password' => 'Wachtwoord Herstellen',
    'reset_password_send_instructions' => 'Geef je e-mail en we sturen je een link om je wachtwoord te herstellen',
    'reset_password_send_button' => 'Link Sturen',
    'reset_password_sent_success' => 'Een link om je wachtwoord te herstellen is verstuurd naar :email.',
    'reset_password_success' => 'Je wachtwoord is succesvol hersteld.',

    'email_reset_subject' => 'Herstel je wachtwoord van :appName',
    'email_reset_text' => 'Je ontvangt deze e-mail zodat je je wachtwoord kunt herstellen.',
    'email_reset_not_requested' => 'Als je jouw wachtwoord niet wilt wijzigen, doe dan niets.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Bevestig je e-mailadres op :appName',
    'email_confirm_greeting' => 'Bedankt voor je aanmelding op :appName!',
    'email_confirm_text' => 'Bevestig je registratie door op onderstaande knop te drukken:',
    'email_confirm_action' => 'Bevestig je e-mail',
    'email_confirm_send_error' => 'E-mail bevestiging is vereisd maar het systeem kon geen mail verzenden. Neem contact op met de beheerder.',
    'email_confirm_success' => 'Je e-mailadres is bevestigt!',
    'email_confirm_resent' => 'De bevestigingse-mails is opnieuw verzonden. Controleer je inbox.',

    'email_not_confirmed' => 'E-mail nog niet bevestigd',
    'email_not_confirmed_text' => 'Je e-mailadres is nog niet bevestigd.',
    'email_not_confirmed_click_link' => 'Klik op de link in de e-mail die vlak na je registratie is verstuurd.',
    'email_not_confirmed_resend' => 'Als je deze e-mail niet kunt vinden kun je deze met onderstaande formulier opnieuw verzenden.',
    'email_not_confirmed_resend_button' => 'Bevestigingsmail Opnieuw Verzenden',
];