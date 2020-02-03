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
    'email' => 'Email',
    'password' => 'Heslo',
    'password_confirm' => 'Potvrdit heslo',
    'password_hint' => 'Musí mít víc než 7 znaků',
    'forgot_password' => 'Zapomněli jste heslo?',
    'remember_me' => 'Neodhlašovat',
    'ldap_email_hint' => 'Zadejte email, který chcete přiřadit k tomuto účtu.',
    'create_account' => 'Vytvořit účet',
    'already_have_account' => 'Already have an account?',
    'dont_have_account' => 'Don\'t have an account?',
    'social_login' => 'Přihlášení přes sociální sítě',
    'social_registration' => 'Registrace přes sociální sítě',
    'social_registration_text' => 'Registrovat a přihlásit se přes jinou službu',

    'register_thanks' => 'Díky za registraci!',
    'register_confirm' => 'Zkontrolujte prosím váš email a klikněte na potvrzovací tlačítko pro dokončení registrace do :appName.',
    'registrations_disabled' => 'Registrace jsou momentálně pozastaveny',
    'registration_email_domain_invalid' => 'Registrace z této emailové domény nejsou povoleny.',
    'register_success' => 'Díky za registraci! Jste registrovaní a přihlášení.',


    // Password Reset
    'reset_password' => 'Resetovat heslo',
    'reset_password_send_instructions' => 'Zadejte vaší emailovou adresu a bude vám zaslán odkaz na resetování hesla.',
    'reset_password_send_button' => 'Poslat odkaz pro reset hesla',
    'reset_password_sent_success' => 'Odkaz na resetování hesla vám byl zaslán na :email.',
    'reset_password_success' => 'Vaše heslo bylo úspěšně resetováno.',
    'email_reset_subject' => 'Reset hesla do :appName',
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
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];