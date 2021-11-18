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
    'password_hint' => 'Musí mít víc než 7 znaků',
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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'E-mail s potvrzením byl znovu odeslán. Zkontrolujte svou příchozí poštu.',

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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobilní aplikace',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Potvrdit a povolit',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Nastavení mobilní aplikace',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
