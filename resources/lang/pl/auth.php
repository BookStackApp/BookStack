<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => 'Wprowadzone poświadczenia są nieprawidłowe.',
    'throttle' => 'Zbyt wiele prób logowania. Spróbuj ponownie za :seconds s.',

    /**
     * Login & Register
     */
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
    'password_hint' => 'Musi mieć więcej niż 5 znaków',
    'forgot_password' => 'Zapomniałem hasła',
    'remember_me' => 'Zapamiętaj mnie',
    'ldap_email_hint' => 'Wprowadź adres e-mail dla tego konta.',
    'create_account' => 'Utwórz konto',
    'social_login' => 'Logowanie za pomocą konta społecznościowego',
    'social_registration' => 'Rejestracja za pomocą konta społecznościowego',
    'social_registration_text' => 'Zarejestruj się za pomocą innej usługi.',

    'register_thanks' => 'Dziękujemy za rejestrację!',
    'register_confirm' => 'Sprawdź podany adres e-mail i kliknij w link, by uzyskać dostęp do :appName.',
    'registrations_disabled' => 'Rejestracja jest obecnie zablokowana.',
    'registration_email_domain_invalid' => 'Adresy e-mail z tej domeny nie mają dostępu do tej aplikacji',
    'register_success' => 'Dziękujemy za rejestrację! Zostałeś zalogowany automatycznie.',


    /**
     * Password Reset
     */
    'reset_password' => 'Resetowanie hasła',
    'reset_password_send_instructions' => 'Wprowadź adres e-mail powiązany z Twoim kontem, by otrzymać link do resetowania hasła.',
    'reset_password_send_button' => 'Wyślij link do resetowania hasła',
    'reset_password_sent_success' => 'Wysłano link do resetowania hasła na adres :email.',
    'reset_password_success' => 'Hasło zostało zresetowane pomyślnie.',

    'email_reset_subject' => 'Resetowanie hasła do :appName',
    'email_reset_text' => 'Otrzymujesz tę wiadomość ponieważ ktoś zażądał zresetowania hasła do Twojego konta.',
    'email_reset_not_requested' => 'Jeśli to nie Ty złożyłeś żądanie zresetowania hasła, zignoruj tę wiadomość.',


    /**
     * Email Confirmation
     */
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
];
