<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Credenziali errate.',
    'throttle' => 'Troppi tentativi di login. Riprova in :seconds secondi.',

    // Login & Register
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
    'password_hint' => 'Deve essere lunga almeno 8 caratteri',
    'forgot_password' => 'Password dimenticata?',
    'remember_me' => 'Ricordami',
    'ldap_email_hint' => 'Inserisci un email per usare quest\'account.',
    'create_account' => 'Crea Account',
    'already_have_account' => 'Hai già un account?',
    'dont_have_account' => 'Non hai un account?',
    'social_login' => 'Login Social',
    'social_registration' => 'Registrazione Social',
    'social_registration_text' => 'Registrati usando un altro servizio.',

    'register_thanks' => 'Grazie per esserti registrato!',
    'register_confirm' => 'Controlla la tua mail e clicca il bottone di conferma per accedere a :appName.',
    'registrations_disabled' => 'La registrazione è disabilitata',
    'registration_email_domain_invalid' => 'Questo dominio della mail non ha accesso a questa applicazione',
    'register_success' => 'Grazie per la registrazione! Sei registrato e loggato.',

    // Password Reset
    'reset_password' => 'Reimposta Password',
    'reset_password_send_instructions' => 'Inserisci il tuo indirizzo sotto e ti verrà inviata una mail contenente un link per resettare la tua password.',
    'reset_password_send_button' => 'Invia Link Reset',
    'reset_password_sent' => 'Un link di reset della password verrà inviato a :email se la mail verrà trovata nel sistema.',
    'reset_password_success' => 'La tua password è stata resettata correttamente.',
    'email_reset_subject' => 'Reimposta la password di :appName',
    'email_reset_text' => 'Stai ricevendo questa mail perché abbiamo ricevuto una richiesta di reset della password per il tuo account.',
    'email_reset_not_requested' => 'Se non hai richiesto un reset della password, ignora questa mail.',

    // Email Confirmation
    'email_confirm_subject' => 'Conferma email per :appName',
    'email_confirm_greeting' => 'Grazie per esserti registrato a :appName!',
    'email_confirm_text' => 'Conferma il tuo indirizzo email cliccando il pulsante sotto:',
    'email_confirm_action' => 'Conferma Email',
    'email_confirm_send_error' => 'La conferma della mail è richiesta ma non è stato possibile mandare la mail. Contatta l\'amministratore.',
    'email_confirm_success' => 'La tua email è stata confermata! Ora dovresti essere in grado di effettuare il login utilizzando questo indirizzo email.',
    'email_confirm_resent' => 'Mail di conferma reinviata, controlla la tua posta.',

    'email_not_confirmed' => 'Indirizzo Email Non Confermato',
    'email_not_confirmed_text' => 'Il tuo indirizzo email non è ancora stato confermato.',
    'email_not_confirmed_click_link' => 'Clicca il link nella mail mandata subito dopo la tua registrazione.',
    'email_not_confirmed_resend' => 'Se non riesci a trovare la mail puoi rimandarla cliccando il pulsante sotto.',
    'email_not_confirmed_resend_button' => 'Reinvia Conferma',

    // User Invite
    'user_invite_email_subject' => 'Sei stato invitato a unirti a :appName!',
    'user_invite_email_greeting' => 'Un account è stato creato per te su :appName.',
    'user_invite_email_text' => 'Clicca sul pulsante qui sotto per impostare una password e ottenere l\'accesso:',
    'user_invite_email_action' => 'Imposta Password',
    'user_invite_page_welcome' => 'Benvenuto in :appName!',
    'user_invite_page_text' => 'Per completare il tuo account e ottenere l\'accesso devi impostare una password che verrà utilizzata per accedere a :appName in futuro.',
    'user_invite_page_confirm_button' => 'Conferma Password',
    'user_invite_success_login' => 'Password impostata, ora dovresti essere in grado di effettuare il login utilizzando la password impostata per accedere a :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Imposta Autenticazione Multi-Fattore',
    'mfa_setup_desc' => 'Imposta l\'autenticazione multi-fattore come misura di sicurezza aggiuntiva per il tuo account.',
    'mfa_setup_configured' => 'Già configurata',
    'mfa_setup_reconfigure' => 'Riconfigura',
    'mfa_setup_remove_confirmation' => 'Sei sicuro di voler rimuovere questo metodo di autenticazione multi-fattore?',
    'mfa_setup_action' => 'Imposta',
    'mfa_backup_codes_usage_limit_warning' => 'Hai meno di 5 codici di backup rimanenti. Genera e memorizza un nuovo set prima di esaurire i codici per evitare di essere bloccato dal tuo account.',
    'mfa_option_totp_title' => 'App mobile',
    'mfa_option_totp_desc' => 'Per utilizzare l\'autenticazione multi-fattore avrai bisogno di un\'applicazione mobile che supporti TOTP come Google Authenticator, Authy o Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Codici di backup',
    'mfa_option_backup_codes_desc' => 'Salva in modo sicuro una serie di codici di backup monouso che puoi inserire per verificare la tua identità.',
    'mfa_gen_confirm_and_enable' => 'Conferma e abilita',
    'mfa_gen_backup_codes_title' => 'Configurazione codici di backup',
    'mfa_gen_backup_codes_desc' => 'Conserva l\'elenco di codici qui sotto in un luogo sicuro. Quando accedi al sistema potrai utilizzare uno dei codici come meccanismo di autenticazione secondario.',
    'mfa_gen_backup_codes_download' => 'Scarica codici',
    'mfa_gen_backup_codes_usage_warning' => 'Ogni codice può essere utilizzato solo una volta',
    'mfa_gen_totp_title' => 'Impostazione App Mobile',
    'mfa_gen_totp_desc' => 'Per utilizzare l\'autenticazione multi-fattore avrai bisogno di un\'applicazione mobile che supporti TOTP come Google Authenticator, Authy o Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scansiona il codice QR qui sotto utilizzando la tua app di autenticazione preferita per iniziare.',
    'mfa_gen_totp_verify_setup' => 'Verifica configurazione',
    'mfa_gen_totp_verify_setup_desc' => 'Verifica che tutto funzioni inserendo un codice, generato all\'interno della tua app di autenticazione, nella casella di testo sottostante:',
    'mfa_gen_totp_provide_code_here' => 'Inserisci qui il codice generato dall\'app',
    'mfa_verify_access' => 'Verifica accesso',
    'mfa_verify_access_desc' => 'Il tuo account utente richiede che tu confermi la tua identità tramite un ulteriore livello di verifica prima di ottenere l\'accesso. Verifica usando uno dei tuoi metodi configurati per continuare.',
    'mfa_verify_no_methods' => 'Nessun metodo configurato',
    'mfa_verify_no_methods_desc' => 'Non è stato possibile trovare metodi di autenticazione multi-fattore per il tuo account. Devi impostare almeno un metodo prima di ottenere l\'accesso.',
    'mfa_verify_use_totp' => 'Verifica utilizzando un\'app mobile',
    'mfa_verify_use_backup_codes' => 'Verifica utilizzando un codice di backup',
    'mfa_verify_backup_code' => 'Codice di backup',
    'mfa_verify_backup_code_desc' => 'Inserisci uno dei tuoi rimanenti codici di backup qui sotto:',
    'mfa_verify_backup_code_enter_here' => 'Inserisci qui il codice di backup',
    'mfa_verify_totp_desc' => 'Inserisci il codice, generato tramite la tua app mobile, qui sotto:',
    'mfa_setup_login_notification' => 'Metodo multi-fattore configurato, si prega di effettuare nuovamente il login utilizzando il metodo configurato.',
];
