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
    'password_confirm' => 'Potvrdit heslo',
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
    'registrations_disabled' => 'Registrace jsou momentálně pozastaveny',
    'registration_email_domain_invalid' => 'Tato emailová doména nemá přístup k této aplikaci',
    'register_success' => 'Děkujeme za registraci! Jste registrovaní a přihlášení.',


    // Password Reset
    'reset_password' => 'Resetovat heslo',
    'reset_password_send_instructions' => 'Zadejte vaší emailovou adresu a bude vám zaslán odkaz na resetování hesla.',
    'reset_password_send_button' => 'Poslat odkaz pro resetování hesla',
    'reset_password_sent' => 'Na vaši emailovou adresu bude poslán odkaz na resetování hesla, pokud tento email je v systému.',
    'reset_password_success' => 'Vaše heslo bylo úspěšně resetováno.',
    'email_reset_subject' => 'Resetovat heslo do :appName',
    'email_reset_text' => 'Tento email jste obdrželi, protože jsme dostali žádost o resetování vašeho hesla k účtu v :appName.',
    'email_reset_not_requested' => 'Pokud jste o reset vašeho hesla nežádali, prostě tento dopis smažte a je to.',


    // Email Confirmation
    'email_confirm_subject' => 'Potvrďte vaši emailovou adresu pro :appName',
    'email_confirm_greeting' => 'Díky že jste se přidali do :appName!',
    'email_confirm_text' => 'Prosíme potvrďte funkčnost vaší emailové adresy kliknutím na tlačítko níže:',
    'email_confirm_action' => 'Potvrdit emailovou adresu',
    'email_confirm_send_error' => 'Potvrzení emailové adresy je vyžadováno, ale systém vám nedokázal odeslat email. Kontaktujte správce aby to dal do kupy a potvrzovací email vám dorazil.',
    'email_confirm_success' => 'Vaše emailová adresa byla potvrzena!',
    'email_confirm_resent' => 'Email s žádostí o potvrzení vaší emailové adresy byl odeslán. Podívejte se do příchozí pošty.',

    'email_not_confirmed' => 'Emailová adresa nebyla potvrzena',
    'email_not_confirmed_text' => 'Vaše emailová adresa nebyla dosud potvrzena.',
    'email_not_confirmed_click_link' => 'Klikněte na odkaz v emailu který jsme vám zaslali ihned po registraci.',
    'email_not_confirmed_resend' => 'Pokud nemůžete nalézt email v příchozí poště, můžete si jej nechat poslat znovu pomocí formuláře níže.',
    'email_not_confirmed_resend_button' => 'Znovu poslat email pro potvrzení emailové adresy',

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