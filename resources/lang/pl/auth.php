<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Wprowadzone poświadczenia są nieprawidłowe.',
    'throttle' => 'Zbyt wiele prób logowania. Spróbuj ponownie za :seconds s.',

    // Login & Register
    'sign_up' => 'Zarejestruj się',
    'log_in' => 'Zaloguj się',
    'log_in_with' => 'Zaloguj się za pomocą :socialDriver',
    'sign_up_with' => 'Zarejestruj się za pomocą :socialDriver',
    'logout' => 'Wyloguj',

    'name' => 'Imię',
    'username' => 'Nazwa użytkownika',
    'email' => 'E-mail',
    'password' => 'Hasło',
    'password_confirm' => 'Potwierdzenie hasła',
    'password_hint' => 'Musi mieć więcej niż 7 znaków',
    'forgot_password' => 'Zapomniałem hasła',
    'remember_me' => 'Zapamiętaj mnie',
    'ldap_email_hint' => 'Wprowadź adres e-mail dla tego konta.',
    'create_account' => 'Utwórz konto',
    'already_have_account' => 'Masz już konto?',
    'dont_have_account' => 'Nie masz konta?',
    'social_login' => 'Logowanie za pomocą konta społecznościowego',
    'social_registration' => 'Rejestracja za pomocą konta społecznościowego',
    'social_registration_text' => 'Zarejestruj się za pomocą innej usługi.',

    'register_thanks' => 'Dziękujemy za rejestrację!',
    'register_confirm' => 'Sprawdź podany adres e-mail i kliknij w link, by uzyskać dostęp do :appName.',
    'registrations_disabled' => 'Rejestracja jest obecnie zablokowana.',
    'registration_email_domain_invalid' => 'Adresy e-mail z tej domeny nie mają dostępu do tej aplikacji',
    'register_success' => 'Dziękujemy za rejestrację! Zostałeś zalogowany automatycznie.',


    // Password Reset
    'reset_password' => 'Resetowanie hasła',
    'reset_password_send_instructions' => 'Wprowadź adres e-mail powiązany z Twoim kontem, by otrzymać link do resetowania hasła.',
    'reset_password_send_button' => 'Wyślij link do resetowania hasła',
    'reset_password_sent' => 'Link z resetem hasła zostanie wysłany na :email jeśli mamy ten adres w systemie.',
    'reset_password_success' => 'Hasło zostało zresetowane pomyślnie.',
    'email_reset_subject' => 'Resetowanie hasła do :appName',
    'email_reset_text' => 'Otrzymujesz tę wiadomość ponieważ ktoś zażądał zresetowania hasła do Twojego konta.',
    'email_reset_not_requested' => 'Jeśli to nie Ty złożyłeś żądanie zresetowania hasła, zignoruj tę wiadomość.',


    // Email Confirmation
    'email_confirm_subject' => 'Potwierdź swój adres e-mail w :appName',
    'email_confirm_greeting' => 'Dziękujemy za dołączenie do :appName!',
    'email_confirm_text' => 'Prosimy byś potwierdził swoje hasło klikając przycisk poniżej:',
    'email_confirm_action' => 'Potwierdź e-mail',
    'email_confirm_send_error' => 'Wymagane jest potwierdzenie hasła, lecz wiadomość nie mogła zostać wysłana. Skontaktuj się z administratorem w celu upewnienia się, że skrzynka została skonfigurowana prawidłowo.',
    'email_confirm_success' => 'Adres e-mail został potwierdzony!',
    'email_confirm_resent' => 'E-mail z potwierdzeniem został wysłany ponownie, sprawdź swoją skrzynkę odbiorczą.',

    'email_not_confirmed' => 'Adres e-mail nie został potwierdzony',
    'email_not_confirmed_text' => 'Twój adres e-mail nie został jeszcze potwierdzony.',
    'email_not_confirmed_click_link' => 'Aby potwierdzić swoje konto kliknij w link wysłany w wiadomości po rejestracji.',
    'email_not_confirmed_resend' => 'Jeśli wiadomość do Ciebie nie dotarła możesz wysłać ją ponownie wypełniając formularz poniżej.',
    'email_not_confirmed_resend_button' => 'Wyślij ponownie wiadomość z potwierdzeniem',

    // User Invite
    'user_invite_email_subject' => 'Zostałeś zaproszony do :appName!',
    'user_invite_email_greeting' => 'Zostało dla Ciebie utworzone konto w :appName.',
    'user_invite_email_text' => 'Kliknij przycisk poniżej, aby ustawić hasło do konta i uzyskać do niego dostęp:',
    'user_invite_email_action' => 'Ustaw hasło do konta',
    'user_invite_page_welcome' => 'Witaj w :appName!',
    'user_invite_page_text' => 'Aby zakończyć tworzenie konta musisz ustawić hasło, które będzie używane do logowania do :appName w przyszłości.',
    'user_invite_page_confirm_button' => 'Potwierdź hasło',
    'user_invite_success' => 'Hasło zostało ustawione, teraz masz dostęp do :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
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