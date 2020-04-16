<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Tieto údaje nesedia s našimi záznamami.',
    'throttle' => 'Priveľa pokusov o prihlásenie. Skúste znova o :seconds sekúnd.',

    // Login & Register
    'sign_up' => 'Registrácia',
    'log_in' => 'Prihlásenie',
    'log_in_with' => 'Prihlásiť sa cez :socialDriver',
    'sign_up_with' => 'Registrovať sa cez :socialDriver',
    'logout' => 'Odhlásenie',

    'name' => 'Meno',
    'username' => 'Používateľské meno',
    'email' => 'Email',
    'password' => 'Heslo',
    'password_confirm' => 'Potvrdiť heslo',
    'password_hint' => 'Musí mať viac ako 7 znakov',
    'forgot_password' => 'Zabudli ste heslo?',
    'remember_me' => 'Zapamätať si ma',
    'ldap_email_hint' => 'Zadajte prosím email, ktorý sa má použiť pre tento účet.',
    'create_account' => 'Vytvoriť účet',
    'already_have_account' => 'Already have an account?',
    'dont_have_account' => 'Don\'t have an account?',
    'social_login' => 'Sociálne prihlásenie',
    'social_registration' => 'Sociálna registrácia',
    'social_registration_text' => 'Registrovať sa a prihlásiť sa použitím inej služby.',

    'register_thanks' => 'Ďakujeme zaregistráciu!',
    'register_confirm' => 'Skontrolujte prosím svoj email a kliknite na potvrdzujúce tlačidlo pre prístup k :appName.',
    'registrations_disabled' => 'Registrácie sú momentálne zablokované',
    'registration_email_domain_invalid' => 'Táto emailová doména nemá prístup k tejto aplikácii',
    'register_success' => 'Ďakujeme za registráciu! Teraz ste registrovaný a prihlásený.',


    // Password Reset
    'reset_password' => 'Reset hesla',
    'reset_password_send_instructions' => 'Zadajte svoj email nižšie a bude Vám odoslaný email s odkazom pre reset hesla.',
    'reset_password_send_button' => 'Poslať odkaz na reset hesla',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
    'reset_password_success' => 'Vaše heslo bolo úspešne resetované.',
    'email_reset_subject' => 'Reset Vášho :appName hesla',
    'email_reset_text' => 'Tento email Ste dostali pretože sme dostali požiadavku na reset hesla pre Váš účet.',
    'email_reset_not_requested' => 'Ak ste nepožiadali o reset hesla, nemusíte nič robiť.',


    // Email Confirmation
    'email_confirm_subject' => 'Potvrdiť email na :appName',
    'email_confirm_greeting' => 'Ďakujeme za pridanie sa k :appName!',
    'email_confirm_text' => 'Prosím potvrďte Vašu emailovú adresu kliknutím na tlačidlo nižšie:',
    'email_confirm_action' => 'Potvrdiť email',
    'email_confirm_send_error' => 'Je požadované overenie emailu, ale systém nemohol odoslať email. Kontaktujte administrátora by ste sa uistili, že email je nastavený správne.',
    'email_confirm_success' => 'Váš email bol overený!',
    'email_confirm_resent' => 'Potvrdzujúci email bol poslaný znovu, skontrolujte prosím svoju emailovú schránku.',

    'email_not_confirmed' => 'Emailová adresa nebola overená',
    'email_not_confirmed_text' => 'Vaša emailová adresa nebola zatiaľ overená.',
    'email_not_confirmed_click_link' => 'Prosím, kliknite na odkaz v emaili, ktorý bol poslaný krátko po Vašej registrácii.',
    'email_not_confirmed_resend' => 'Ak nemôžete násť email, môžete znova odoslať overovací email odoslaním doleuvedeného formulára.',
    'email_not_confirmed_resend_button' => 'Znova odoslať overovací email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];