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
    'reset_password_sent_success' => 'A password reset link has been sent to :email.',
    'reset_password_success' => 'Your password has been successfully reset.',

    'email_reset_subject' => 'Reset your :appName password',
    'email_reset_text' => 'You are receiving this email because we received a password reset request for your account.',
    'email_reset_not_requested' => 'If you did not request a password reset, no further action is required.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Confirm Email',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed!',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',
];