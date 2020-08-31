<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Neplatné přihlašovací údaje.',
    'throttle' => 'Příliš pokusů o přihlášení. Zkuste to prosím znovu za :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registrace',
    'log_in' => 'Přihlášení',
    'log_in_with' => 'Přihlásit přes :socialDriver',
    'sign_up_with' => 'Registrovat se přes :socialDriver',
    'logout' => 'Odhlásit',

    'name' => 'Jméno',
    'username' => 'Jméno účtu',
    'email' => 'E-mail',
    'password' => 'Heslo',
    'password_confirm' => 'Oveření hesla',
    'password_hint' => 'Musí mít víc než 7 znaků',
    'forgot_password' => 'Zapomněli jste heslo?',
    'remember_me' => 'Neodhlašovat',
    'ldap_email_hint' => 'Zadejte e-mail, který chcete přiřadit k tomuto účtu.',
    'create_account' => 'Vytvořit účet',
    'already_have_account' => 'Máte už založený účet?',
    'dont_have_account' => 'Nemáte učet?',
    'social_login' => 'Přihlášení přes sociální sítě',
    'social_registration' => 'Registrace přes sociální sítě',
    'social_registration_text' => 'Registrovat a přihlásit se přes jinou službu',

    'register_thanks' => 'Díky za registraci!',
    'register_confirm' => 'Zkontrolujte prosím váš e-mail a klikněte na potvrzovací tlačítko pro dokončení registrace do :appName.',
    'registrations_disabled' => 'Registrace jsou momentálně pozastaveny',
    'registration_email_domain_invalid' => 'Registrace z této e-mailové domény nejsou povoleny.',
    'register_success' => 'Díky za registraci! Jste registrovaní a přihlášení.',


    // Password Reset
    'reset_password' => 'Resetovat heslo',
    'reset_password_send_instructions' => 'Zadejte vaší e-mailovou adresu a bude vám zaslán odkaz na resetování hesla.',
    'reset_password_send_button' => 'Poslat odkaz pro reset hesla',
    'reset_password_sent' => 'E-mail s odkazen na reset hesla bude zaslán na Vaši adresu :email, pokud bude tato adresa nalezena v systému.',
    'reset_password_success' => 'Vaše heslo bylo úspěšně resetováno.',
    'email_reset_subject' => 'Reset hesla do :appName',
    'email_reset_text' => 'Tento e-mail jste obdrželi, protože jsme dostali žádost o resetování vašeho hesla k účtu v :appName.',
    'email_reset_not_requested' => 'Pokud jste o reset vašeho hesla nežádali, prostě tento dopis smažte a je to.',


    // Email Confirmation
    'email_confirm_subject' => 'Potvrďte vaši e-mailovou adresu pro :appName',
    'email_confirm_greeting' => 'Díky že jste se přidali do :appName!',
    'email_confirm_text' => 'Prosíme potvrďte funkčnost vaší e-mailové adresy kliknutím na tlačítko níže:',
    'email_confirm_action' => 'Potvrdit e-mailovou adresu',
    'email_confirm_send_error' => 'Potvrzení e-mailové adresy je vyžadováno, ale systém vám nedokázal odeslat e-mail. Kontaktujte správce aby to dal do kupy a potvrzovací e-mail vám dorazil.',
    'email_confirm_success' => 'Vaše e-mailová adresa byla potvrzena!',
    'email_confirm_resent' => 'E-mail s žádostí o potvrzení vaší e-mailové adresy byl odeslán. Podívejte se do příchozí pošty.',

    'email_not_confirmed' => 'E-mailová adresa nebyla potvrzena',
    'email_not_confirmed_text' => 'Vaše e-mailová adresa nebyla dosud potvrzena.',
    'email_not_confirmed_click_link' => 'Klikněte na odkaz v e-mailu který jsme vám zaslali ihned po registraci.',
    'email_not_confirmed_resend' => 'Pokud nemůžete nalézt e-mail v příchozí poště, můžete si jej nechat poslat znovu pomocí formuláře níže.',
    'email_not_confirmed_resend_button' => 'Znovu poslat e-mail pro potvrzení e-mailové adresy',

    // User Invite
    'user_invite_email_subject' => 'Byl jste pozván do :appName!',
    'user_invite_email_greeting' => 'Byl pro vás vytvořen účet na :appName.',
    'user_invite_email_text' => 'Klikněte na tlačítko níže pro nastavení hesla k účtu a získání přístupu:',
    'user_invite_email_action' => 'Nastavit heslo účtu',
    'user_invite_page_welcome' => 'Vítejte v :appName!',
    'user_invite_page_text' => 'Chcete-li dokončit svůj účet a získat přístup, musíte nastavit heslo, které bude použito k přihlášení do :appName při budoucích návštěvách.',
    'user_invite_page_confirm_button' => 'Potvrdit heslo',
    'user_invite_success' => 'Heslo nastaveno, nyní máte přístup k :appName!'
];