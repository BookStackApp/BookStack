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
    'failed' => 'Credenziali errate.',
    'throttle' => 'Troppi tentativi di login. Riprova in :seconds secondi.',

    /**
     * Login & Register
     */
    'sign_up' => 'Registrati',
    'log_in' => 'Login',
    'log_in_with' => 'Login con :socialDriver',
    'sign_up_with' => 'Registrati con :socialDriver',
    'logout' => 'Esci',

    'name' => 'Nome',
    'username' => 'Username',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirm' => 'Conferma Password',
    'password_hint' => 'Deve essere più di 7 caratteri',
    'forgot_password' => 'Password dimenticata?',
    'remember_me' => 'Ricordami',
    'ldap_email_hint' => 'Inserisci un email per usare quest\'account.',
    'create_account' => 'Crea Account',
    'social_login' => 'Login Social',
    'social_registration' => 'Registrazione Social',
    'social_registration_text' => 'Registrati usando un altro servizio.',

    'register_thanks' => 'Grazie per esserti registrato!',
    'register_confirm' => 'Controlla la tua mail e clicca il bottone di conferma per accedere a :appName.',
    'registrations_disabled' => 'La registrazione è disabilitata',
    'registration_email_domain_invalid' => 'Questo dominio della mail non ha accesso a questa applicazione',
    'register_success' => 'Grazie per la registrazione! Sei registrato e loggato.',


    /**
     * Password Reset
     */
    'reset_password' => 'Reimposta Password',
    'reset_password_send_instructions' => 'Inserisci il tuo indirizzo sotto e ti verrà inviata una mail contenente un link per resettare la tua password.',
    'reset_password_send_button' => 'Invia Link Reset',
    'reset_password_sent_success' => 'Un link di reset è stato mandato a :email.',
    'reset_password_success' => 'La tua password è stata resettata correttamente.',

    'email_reset_subject' => 'Reimposta la password di :appName',
    'email_reset_text' => 'Stai ricevendo questa mail perché abbiamo ricevuto una richiesta di reset della password per il tuo account.',
    'email_reset_not_requested' => 'Se non hai richiesto un reset della password, ignora questa mail.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Conferma email per :appName',
    'email_confirm_greeting' => 'Grazie per esserti registrato a :appName!',
    'email_confirm_text' => 'Conferma il tuo indirizzo email cliccando il pulsante sotto:',
    'email_confirm_action' => 'Conferma Email',
    'email_confirm_send_error' => 'La conferma della mail è richiesta ma non è stato possibile mandare la mail. Contatta l\'amministratore.',
    'email_confirm_success' => 'La tua mail è stata confermata!',
    'email_confirm_resent' => 'Mail di conferma reinviata, controlla la tua posta.',

    'email_not_confirmed' => 'Indirizzo Email Non Confermato',
    'email_not_confirmed_text' => 'Il tuo indirizzo email non è ancora stato confermato.',
    'email_not_confirmed_click_link' => 'Clicca il link nella mail mandata subito dopo la tua registrazione.',
    'email_not_confirmed_resend' => 'Se non riesci a trovare la mail puoi rimandarla cliccando il pulsante sotto.',
    'email_not_confirmed_resend_button' => 'Reinvia Conferma',
];