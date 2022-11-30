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
    'password_hint' => 'Must be at least 8 characters',
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

    // Login auto-initiation
    'auto_init_starting' => 'Attempting Login',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Potvrdzujúci e-mail bol poslaný znovu, skontrolujte prosím svoju e-mailovú schránku.',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Nastaviť viacúrovňové prihlasovanie',
    'mfa_setup_desc' => 'Pre vyššiu úroveň bezpečnosti si nastavte viacúrovňové prihlasovanie.',
    'mfa_setup_configured' => 'Už nastavené',
    'mfa_setup_reconfigure' => 'Znovunastavenie',
    'mfa_setup_remove_confirmation' => 'Ste si istý, že chcete odstrániť tento spôsob viacúrovňového overenia?',
    'mfa_setup_action' => 'Nastaveine',
    'mfa_backup_codes_usage_limit_warning' => 'Ostáva vám menej ako 5 záložných kódov. Vygenerujte a uložte si novú súpravu skôr, ako sa vám minú kódy, aby ste sa vyhli vymknutiu z vášho účtu.',
    'mfa_option_totp_title' => 'Mobilná aplikácia',
    'mfa_option_totp_desc' => 'Pre používanie viacúrovňového prihlasovania budete potrebovať mobilnú aplikáciu, ktorá podporuje TOPS ako napríklad Google Authenticator, Authy alebo Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Záložné kódy',
    'mfa_option_backup_codes_desc' => 'Bezpečne uložte jednorázové záložné kódy pre overenie vačej identity.',
    'mfa_gen_confirm_and_enable' => 'Potvrdiť a zapnúť',
    'mfa_gen_backup_codes_title' => 'Nastavenie záložných kódov',
    'mfa_gen_backup_codes_desc' => 'Uložte si tieto kódy na bezpečné miesto. Jeden z kódov budete môcť použiť ako druhý faktor overenia identiy na prihlásenie sa.',
    'mfa_gen_backup_codes_download' => 'Stiahnuť kódy',
    'mfa_gen_backup_codes_usage_warning' => 'Každý kód môže byť použitý len jeden krát',
    'mfa_gen_totp_title' => 'Nastavenie mobilnej aplikácie',
    'mfa_gen_totp_desc' => 'Pre používanie viacúrovňového prihlasovania budete potrebovať mobilnú aplikáciu, ktorá podporuje TOPS ako napríklad Google Authenticator, Authy alebo Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Naskenujte 1R k\'d pomocou vašej mobilnej aplikácie.',
    'mfa_gen_totp_verify_setup' => 'Overiť nastavenie',
    'mfa_gen_totp_verify_setup_desc' => 'Overte, či všetko funguje zadaním kódu vygenerovaného vo vašej autentifikačnej aplikácii do vstupného poľa nižšie:',
    'mfa_gen_totp_provide_code_here' => 'Sem vložte kód vygenerovaný vašou mobilnou aplikáciou',
    'mfa_verify_access' => 'Overiť prístup',
    'mfa_verify_access_desc' => 'Váš používateľský účet vyžaduje, aby ste pred udelením prístupu potvrdili svoju identitu prostredníctvom ďalšej úrovne overenia. Pokračujte overením pomocou jednej z vašich nakonfigurovaných metód.',
    'mfa_verify_no_methods' => 'Žiadny spôsob nebol nastavený',
    'mfa_verify_no_methods_desc' => 'Pre váš účet sa nenašli žiadne metódy viacfaktorovej autentifikácie. Pred získaním prístupu budete musieť nastaviť aspoň jednu metódu.',
    'mfa_verify_use_totp' => 'Overiť pomocou mobilnej aplikácie',
    'mfa_verify_use_backup_codes' => 'Overiť pomocou záložného kódu',
    'mfa_verify_backup_code' => 'Záložný kód',
    'mfa_verify_backup_code_desc' => 'Zadajte jeden zo zostávajúcich záložných kódov:',
    'mfa_verify_backup_code_enter_here' => 'Zadajte záložný kód',
    'mfa_verify_totp_desc' => 'Zadajte kód vygenerovaný vašou mobilnou aplikáciou:',
    'mfa_setup_login_notification' => 'Viacfaktorová metóda je nakonfigurovaná. Teraz sa znova prihláste pomocou nakonfigurovanej metódy.',
];
