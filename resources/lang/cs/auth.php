<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Tyto přihlašovací údaje neodpovídají našim záznamům.',
    'throttle' => 'Příliš mnoho pokusů o přihlášení. Zkuste to prosím znovu za :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registrace',
    'log_in' => 'Přihlášení',
    'log_in_with' => 'Přihlásit se pomocí :socialDriver',
    'sign_up_with' => 'Registrovat se pomocí :socialDriver',
    'logout' => 'Odhlásit',

    'name' => 'Jméno',
    'username' => 'Uživatelské jméno',
    'email' => 'E-mail',
    'password' => 'Heslo',
    'password_confirm' => 'Oveření hesla',
    'password_hint' => 'Musí mít více než 7 znaků',
    'forgot_password' => 'Zapomněli jste heslo?',
    'remember_me' => 'Zapamatovat si mě',
    'ldap_email_hint' => 'Zadejte email, který chcete přiřadit k tomuto účtu.',
    'create_account' => 'Vytvořit účet',
    'already_have_account' => 'Již máte účet?',
    'dont_have_account' => 'Nemáte účet?',
    'social_login' => 'Přihlášení pomocí sociálních sítí',
    'social_registration' => 'Přihlášení pomocí sociálních sítí',
    'social_registration_text' => 'Registrovat a přihlásit se pomocí jiné služby.',

    'register_thanks' => 'Děkujeme za registraci!',
    'register_confirm' => 'Zkontrolujte prosím svůj e-mail a klikněte na potvrzovací tlačítko pro přístup do :appName.',
    'registrations_disabled' => 'Registrace jsou aktuálně zakázány',
    'registration_email_domain_invalid' => 'Tato e-mailová doména nemá přístup k této aplikaci',
    'register_success' => 'Děkujeme za registraci! Nyní jste zaregistrováni a přihlášeni.',


    // Password Reset
    'reset_password' => 'Obnovit heslo',
    'reset_password_send_instructions' => 'Níže zadejte svou e-mailovou adresu a bude vám zaslán e-mail s odkazem pro obnovení hesla.',
    'reset_password_send_button' => 'Zaslat odkaz pro obnovení',
    'reset_password_sent' => 'Odkaz pro obnovení hesla bude odeslán na :email, pokud bude tato e-mailová adresa nalezena v systému.',
    'reset_password_success' => 'Vaše heslo bylo úspěšně obnoveno.',
    'email_reset_subject' => 'Obnovit heslo do :appName',
    'email_reset_text' => 'Tento e-mail jste obdrželi, protože jsme obdrželi žádost o obnovení hesla k vašemu účtu.',
    'email_reset_not_requested' => 'Pokud jste o obnovení hesla nežádali, není vyžadována žádná další akce.',


    // Email Confirmation
    'email_confirm_subject' => 'Potvrďte svůj e-mail pro :appName',
    'email_confirm_greeting' => 'Díky že jste se přidali do :appName!',
    'email_confirm_text' => 'Prosíme potvrďte svou e-mailovou adresu kliknutím na níže uvedené tlačítko:',
    'email_confirm_action' => 'Potvrdit e-mail',
    'email_confirm_send_error' => 'Potvrzení e-mailu je vyžadováno, ale systém nemohl odeslat e-mail. Obraťte se na správce, abyste se ujistili, že je e-mail správně nastaven.',
    'email_confirm_success' => 'Váš e-mail byla potvrzen!',
    'email_confirm_resent' => 'E-mail s potvrzením byl znovu odeslán. Zkontrolujte svou příchozí poštu.',

    'email_not_confirmed' => 'E-mailová adresa nebyla potvrzena',
    'email_not_confirmed_text' => 'Vaše e-mailová adresa nebyla dosud potvrzena.',
    'email_not_confirmed_click_link' => 'Klikněte prosím na odkaz v e-mailu, který byl odeslán krátce po registraci.',
    'email_not_confirmed_resend' => 'Pokud nemůžete e-mail nalézt, můžete znovu odeslat potvrzovací e-mail odesláním níže uvedeného formuláře.',
    'email_not_confirmed_resend_button' => 'Znovu odeslat potvrzovací e-mail',

    // User Invite
    'user_invite_email_subject' => 'Byli jste pozváni přidat se do :appName!',
    'user_invite_email_greeting' => 'Byl pro vás vytvořen účet na :appName.',
    'user_invite_email_text' => 'Klikněte na níže uvedené tlačítko pro nastavení hesla k účtu a získání přístupu:',
    'user_invite_email_action' => 'Nastavit heslo k účtu',
    'user_invite_page_welcome' => 'Vítejte v :appName!',
    'user_invite_page_text' => 'Pro dokončení vašeho účtu a získání přístupu musíte nastavit heslo, které bude použito k přihlášení do :appName při budoucích návštěvách.',
    'user_invite_page_confirm_button' => 'Potvrdit heslo',
    'user_invite_success' => 'Heslo nastaveno, nyní máte přístup k :appName!'
];
