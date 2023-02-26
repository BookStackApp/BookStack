<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Neplatné přihlašovací údaje.',
    'throttle' => 'Příliš mnoho pokusů o přihlášení. Zkuste to prosím znovu za :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registrace',
    'log_in' => 'Přihlášení',
    'log_in_with' => 'Přihlásit se přes :socialDriver',
    'sign_up_with' => 'Registrovat se přes :socialDriver',
    'logout' => 'Odhlásit',

    'name' => 'Jméno',
    'username' => 'Uživatelské jméno',
    'email' => 'E-mail',
    'password' => 'Heslo',
    'password_confirm' => 'Potvrzení hesla',
    'password_hint' => 'Musí mít alespoň 8 znaků',
    'forgot_password' => 'Zapomenuté heslo?',
    'remember_me' => 'Zapamatovat si mě',
    'ldap_email_hint' => 'Zadejte email, který chcete přiřadit k tomuto účtu.',
    'create_account' => 'Vytvořit účet',
    'already_have_account' => 'Již máte účet?',
    'dont_have_account' => 'Nemáte učet?',
    'social_login' => 'Přihlášení přes sociální sítě',
    'social_registration' => 'Registrace přes sociální sítě',
    'social_registration_text' => 'Registrovat a přihlásit se přes jinou službu',

    'register_thanks' => 'Děkujeme za registraci!',
    'register_confirm' => 'Zkontrolujte prosím svůj e-mail a klikněte na potvrzovací tlačítko pro přístup do :appName.',
    'registrations_disabled' => 'Registrace jsou momentálně pozastaveny',
    'registration_email_domain_invalid' => 'Registrace z této e-mailové domény nejsou povoleny',
    'register_success' => 'Děkujeme za registraci! Nyní jste zaregistrováni a přihlášeni.',

    // Login auto-initiation
    'auto_init_starting' => 'Pokus o přihlášení',
    'auto_init_starting_desc' => 'Kontaktujeme váš ověřovací systém pro zahájení procesu přihlášení. Pokud po 5 sekundách nedojde k žádnému pokroku, můžete zkusit kliknout na odkaz níže.',
    'auto_init_start_link' => 'Pokračovat s ověřováním',

    // Password Reset
    'reset_password' => 'Obnovit heslo',
    'reset_password_send_instructions' => 'Níže zadejte svou e-mailovou adresu a bude vám zaslán e-mail s odkazem na obnovení hesla.',
    'reset_password_send_button' => 'Zaslat odkaz na obnovení hesla',
    'reset_password_sent' => 'Odkaz pro obnovení hesla bude odeslán na :email, pokud bude tato e-mailová adresa nalezena v systému.',
    'reset_password_success' => 'Vaše heslo bylo obnoveno.',
    'email_reset_subject' => 'Obnovit heslo do :appName',
    'email_reset_text' => 'Tento e-mail jste obdrželi, protože jsme obdrželi žádost o obnovení hesla k vašemu účtu.',
    'email_reset_not_requested' => 'Pokud jste o obnovení hesla nežádali, není vyžadována žádná další akce.',

    // Email Confirmation
    'email_confirm_subject' => 'Potvrďte svůj e-mail pro :appName',
    'email_confirm_greeting' => 'Díky že jste se přidali do :appName!',
    'email_confirm_text' => 'Prosíme potvrďte svou e-mailovou adresu kliknutím na níže uvedené tlačítko:',
    'email_confirm_action' => 'Potvrdit e-mail',
    'email_confirm_send_error' => 'Potvrzení e-mailu je vyžadováno, ale systém nemohl odeslat e-mail. Obraťte se na správce, abyste se ujistili, že je e-mail správně nastaven.',
    'email_confirm_success' => 'Váš email byl ověřen! Nyní byste měli být schopni se touto emailovou adresou přihlásit.',
    'email_confirm_resent' => 'E-mail s potvrzením byl znovu odeslán. Zkontrolujte svou příchozí poštu.',
    'email_confirm_thanks' => 'Děkujeme za potvrzení!',
    'email_confirm_thanks_desc' => 'Počkejte prosím chvíli, než se vaše potvrzení vyřizuje. Pokud nebudete po 3 sekundách přesměrováni, klikněte na odkaz "Pokračovat" níže pro pokračování.',

    'email_not_confirmed' => 'E-mailová adresa nebyla potvrzena',
    'email_not_confirmed_text' => 'Vaše e-mailová adresa nebyla dosud potvrzena.',
    'email_not_confirmed_click_link' => 'Klikněte prosím na odkaz v e-mailu, který byl odeslán krátce po registraci.',
    'email_not_confirmed_resend' => 'Pokud nemůžete e-mail nalézt, můžete znovu odeslat potvrzovací e-mail odesláním níže uvedeného formuláře.',
    'email_not_confirmed_resend_button' => 'Znovu odeslat potvrzovací e-mail',

    // User Invite
    'user_invite_email_subject' => 'Byli jste pozváni do :appName!',
    'user_invite_email_greeting' => 'Byl pro vás vytvořen účet na :appName.',
    'user_invite_email_text' => 'Klikněte na níže uvedené tlačítko pro nastavení hesla k účtu a získání přístupu:',
    'user_invite_email_action' => 'Nastavit heslo k účtu',
    'user_invite_page_welcome' => 'Vítejte v :appName!',
    'user_invite_page_text' => 'Pro dokončení vašeho účtu a získání přístupu musíte nastavit heslo, které bude použito k přihlášení do :appName při dalších návštěvách.',
    'user_invite_page_confirm_button' => 'Potvrdit heslo',
    'user_invite_success_login' => 'Heslo bylo nasteaveno, nyní byste měli být schopni přihlásit se nastaveným heslem do aplikace :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Nastavit vícefaktorové ověření',
    'mfa_setup_desc' => 'Nastavit vícefaktorové ověřování jako další vrstvu zabezpečení vašeho uživatelského účtu.',
    'mfa_setup_configured' => 'Již nastaveno',
    'mfa_setup_reconfigure' => 'Přenastavit',
    'mfa_setup_remove_confirmation' => 'Opravdu chcete odstranit tuto metodu vícefaktorového ověřování?',
    'mfa_setup_action' => 'Nastavit',
    'mfa_backup_codes_usage_limit_warning' => 'Zbývá vám méně než 5 záložních kódů. Před vypršením kódu si prosím vygenerujte a uložte novou sadu, abyste se vyhnuli zablokování vašeho účtu.',
    'mfa_option_totp_title' => 'Mobilní aplikace',
    'mfa_option_totp_desc' => 'Pro použití vícefaktorového ověření budete potřebovat mobilní aplikaci, která podporuje TOTP jako např. Google Authenticator, Authy nebo Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Záložní kódy',
    'mfa_option_backup_codes_desc' => 'Bezpečně si uložte sadu jednorázových záložních kódů, které můžete použít pro ověření vaší identity.',
    'mfa_gen_confirm_and_enable' => 'Potvrdit a povolit',
    'mfa_gen_backup_codes_title' => 'Nastavení záložních kódů',
    'mfa_gen_backup_codes_desc' => 'Uložte níže uvedený seznam kódů na bezpečné místo. Při přístupu k systému budete moci použít jeden z kódů jako druhou metodu ověření.',
    'mfa_gen_backup_codes_download' => 'Stáhnout kódy',
    'mfa_gen_backup_codes_usage_warning' => 'Každý kód může být použit pouze jednou',
    'mfa_gen_totp_title' => 'Nastavení mobilní aplikace',
    'mfa_gen_totp_desc' => 'Pro použití vícefaktorového ověření budete potřebovat mobilní aplikaci, která podporuje TOTP jako např. Google Authenticator, Authy nebo Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Naskenujte QR kód níže pomocí vaší preferované ověřovací aplikace.',
    'mfa_gen_totp_verify_setup' => 'Ověřit nastavení',
    'mfa_gen_totp_verify_setup_desc' => 'Ověřte, že vše funguje zadáním kódu, generovaného v ověřovací aplikaci, do níže uvedeného vstupního pole:',
    'mfa_gen_totp_provide_code_here' => 'Zde zadejte kód vygenerovaný vaší aplikací',
    'mfa_verify_access' => 'Ověřit přístup',
    'mfa_verify_access_desc' => 'Váš uživatelský účet vyžaduje, abyste před udělením přístupu potvrdili svou totožnost prostřednictvím další úrovně ověření. Ověřte pomocí jedné z vašich nakonfigurovaných metod, abyste mohli pokračovat.',
    'mfa_verify_no_methods' => 'Nejsou nastaveny žádné metody',
    'mfa_verify_no_methods_desc' => 'Pro váš účet nebyly nalezeny žádné vícefázové metody ověřování. Před získáním přístupu budete muset nastavit alespoň jednu metodu.',
    'mfa_verify_use_totp' => 'Ověřit pomocí mobilní aplikace',
    'mfa_verify_use_backup_codes' => 'Ověřit pomocí záložního kódu',
    'mfa_verify_backup_code' => 'Záložní kód',
    'mfa_verify_backup_code_desc' => 'Níže zadejte jeden z vašich zbývajících záložních kódů:',
    'mfa_verify_backup_code_enter_here' => 'Zde zadejte záložní kód',
    'mfa_verify_totp_desc' => 'Níže zadejte kód, který jste si vygenerovali pomocí mobilní aplikace:',
    'mfa_setup_login_notification' => 'Vícefázová metoda nastavena, nyní se prosím znovu přihlaste pomocí konfigurované metody.',
];
