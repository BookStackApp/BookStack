<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Tieto údaje sa nezhodujú s našimi záznamami.',
    'throttle' => 'Priveľa pokusov o prihlásenie. Skúste znova o :seconds sekúnd.',

    // Login & Register
    'sign_up' => 'Registrácia',
    'log_in' => 'Prihlásenie',
    'log_in_with' => 'Prihlásiť sa cez :socialDriver',
    'sign_up_with' => 'Registrovať sa cez :socialDriver',
    'logout' => 'Odhlásenie',

    'name' => 'Meno',
    'username' => 'Používateľské meno',
    'email' => 'E-mail',
    'password' => 'Heslo',
    'password_confirm' => 'Potvrdiť heslo',
    'password_hint' => 'Musí mať viac ako 7 znakov',
    'forgot_password' => 'Zabudli ste heslo?',
    'remember_me' => 'Zapamätať si ma',
    'ldap_email_hint' => 'Zadajte prosím e-mail, ktorý sa má použiť pre tento účet.',
    'create_account' => 'Vytvoriť účet',
    'already_have_account' => 'Už máte svoj ​​účet?',
    'dont_have_account' => 'Nemáte účet?',
    'social_login' => 'Sociálne prihlásenie',
    'social_registration' => 'Sociálna registrácia',
    'social_registration_text' => 'Registrácia a prihlásenie pomocou inej služby.',

    'register_thanks' => 'Ďakujeme za registráciu!',
    'register_confirm' => 'Prosím, skontrolujte svoj e-mail a kliknite na potvrdzujúce tlačidlo pre prístup k :appName.',
    'registrations_disabled' => 'Registrácie sú momentálne zablokované',
    'registration_email_domain_invalid' => 'Táto e-mailová doména nemá prístup k tejto aplikácii',
    'register_success' => 'Ďakujeme za registráciu! Teraz ste registrovaný a prihlásený.',


    // Password Reset
    'reset_password' => 'Resetovanie hesla',
    'reset_password_send_instructions' => 'Nižšie zadajte svoj e-mail, na ktorý Vám zašleme odkaz pre resetovanie hesla.',
    'reset_password_send_button' => 'Poslať odkaz na resetovanie hesla',
    'reset_password_sent' => 'Odkaz na resetovanie hesla bude odoslaný na :email, ak sa táto e-mailová adresa nachádza v systéme.',
    'reset_password_success' => 'Vaše heslo bolo úspešne resetované.',
    'email_reset_subject' => 'Resetovanie Vášho hesla do :appName',
    'email_reset_text' => 'Tento e-mail ste obdržali, pretože sme dostali požiadavku na resetovanie hesla pre Váš účet.',
    'email_reset_not_requested' => 'Ak ste nepožiadali o resetovanie hesla, nemusíte robiť nič.',


    // Email Confirmation
    'email_confirm_subject' => 'Potvrdiť e-mail na :appName',
    'email_confirm_greeting' => 'Ďakujeme, že ste sa pridali k :appName!',
    'email_confirm_text' => 'Prosím, potvrďte Vašu e-mailovú adresu kliknutím na tlačidlo nižšie:',
    'email_confirm_action' => 'Potvrdiť e-mail',
    'email_confirm_send_error' => 'Je požadované overenie e-mailu, ale systém nemohol e-mail odoslať. Kontaktujte administrátora, aby ste sa uistili, že je e-mail nastavený správne.',
    'email_confirm_success' => 'Váš email bol overený!',
    'email_confirm_resent' => 'Potvrdzujúci e-mail bol poslaný znovu, skontrolujte prosím svoju e-mailovú schránku.',

    'email_not_confirmed' => 'E-mailová adresa nebola overená',
    'email_not_confirmed_text' => 'Vaša e-mailová adresa nebola zatiaľ overená.',
    'email_not_confirmed_click_link' => 'Prosím, kliknite na odkaz v e-maili, ktorý bol poslaný krátko po Vašej registrácii.',
    'email_not_confirmed_resend' => 'Ak nemôžete nájsť e-mail, môžete znova odoslať overovací e-mail odoslaním doleuvedeného formulára.',
    'email_not_confirmed_resend_button' => 'Znova odoslať overovací e-mail',

    // User Invite
    'user_invite_email_subject' => 'Dostali ste pozvánku na pripojenie sa k aplikácii :appName!',
    'user_invite_email_greeting' => 'Účet pre :appName bol pre vás vytvorený.',
    'user_invite_email_text' => 'Kliknutím na tlačidlo nižšie nastavíte heslo k účtu a získate prístup:',
    'user_invite_email_action' => 'Nastaviť heslo k účtu',
    'user_invite_page_welcome' => 'Vitajte v :appName!',
    'user_invite_page_text' => 'Ak chcete dokončiť svoj účet a získať prístup, musíte nastaviť heslo, ktoré sa použije na prihlásenie do aplikácie :appName pri budúcich návštevách.',
    'user_invite_page_confirm_button' => 'Potvrdiť heslo',
    'user_invite_success' => 'Heslo bolo nastavené, teraz máte prístup k :appName!'
];