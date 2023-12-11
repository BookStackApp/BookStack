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
    'password_confirm' => 'Potwierdź hasło',
    'password_hint' => 'Musi mieć co najmniej 8 znaków',
    'forgot_password' => 'Zapomniałeś hasła?',
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

    // Login auto-initiation
    'auto_init_starting' => 'Próba logowania',
    'auto_init_starting_desc' => 'Łączymy się z twoim systemem uwierzytelniania w celu rozpoczęcia procesu logowania. Jeśli po 5 sekundach nie ma żadnych postępów, możesz spróbować kliknąć poniższy link.',
    'auto_init_start_link' => 'Kontynuuj uwierzytelnianie',

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
    'email_confirm_success' => 'Twój e-mail został potwierdzony! Powinieneś teraz mieć możliwość zalogowania się za pomocą tego adresu e-mail.',
    'email_confirm_resent' => 'E-mail z potwierdzeniem został wysłany ponownie, sprawdź swoją skrzynkę odbiorczą.',
    'email_confirm_thanks' => 'Dzięki za potwierdzenie!',
    'email_confirm_thanks_desc' => 'Poczekaj chwilę, Twoje potwierdzenie jest obsługiwane. Jeśli nie zostaniesz przekierowany po 3 sekundach, naciśnij poniższy link "Kontynuuj", aby kontynuować.',

    'email_not_confirmed' => 'Adres e-mail nie został potwierdzony',
    'email_not_confirmed_text' => 'Twój adres e-mail nie został jeszcze potwierdzony.',
    'email_not_confirmed_click_link' => 'Aby potwierdzić swoje konto, kliknij link wysłany w wiadomości po rejestracji.',
    'email_not_confirmed_resend' => 'Jeśli wiadomość do Ciebie nie dotarła, możesz wysłać ją ponownie, wypełniając formularz poniżej.',
    'email_not_confirmed_resend_button' => 'Wyślij ponownie wiadomość z potwierdzeniem',

    // User Invite
    'user_invite_email_subject' => 'Zostałeś zaproszony do :appName!',
    'user_invite_email_greeting' => 'Zostało dla Ciebie utworzone konto w :appName.',
    'user_invite_email_text' => 'Kliknij przycisk poniżej, aby ustawić hasło do konta i uzyskać do niego dostęp:',
    'user_invite_email_action' => 'Ustaw hasło do konta',
    'user_invite_page_welcome' => 'Witaj w :appName!',
    'user_invite_page_text' => 'Aby zakończyć tworzenie konta musisz ustawić hasło, które będzie używane do logowania do :appName w przyszłości.',
    'user_invite_page_confirm_button' => 'Potwierdź hasło',
    'user_invite_success_login' => 'Hasło ustawione, teraz powinieneś mieć możliwość logowania się przy użyciu ustawionego hasła, aby uzyskać dostęp do :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Skonfiguruj uwierzytelnianie wieloskładnikowe',
    'mfa_setup_desc' => 'Skonfiguruj uwierzytelnianie wieloskładnikowe jako dodatkową warstwę bezpieczeństwa dla swojego konta użytkownika.',
    'mfa_setup_configured' => 'Już skonfigurowane',
    'mfa_setup_reconfigure' => 'Ponownie konfiguruj',
    'mfa_setup_remove_confirmation' => 'Czy na pewno chcesz usunąć tę metodę uwierzytelniania wieloskładnikowego?',
    'mfa_setup_action' => 'Konfiguracja',
    'mfa_backup_codes_usage_limit_warning' => 'Pozostało Ci mniej niż 5 kodów zapasowych, Wygeneruj i przechowuj nowy zestaw zanim skończysz kody, aby zapobiec zablokowaniu się z konta.',
    'mfa_option_totp_title' => 'Aplikacja mobilna',
    'mfa_option_totp_desc' => 'Aby korzystać z uwierzytelniania wieloskładnikowego, potrzebujesz aplikacji mobilnej, która obsługuje TOTP, takiej jak Google Authenticator, Authy lub Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Kody zapasowe',
    'mfa_option_backup_codes_desc' => 'Bezpiecznie przechowuj zestaw jednorazowych kodów zapasowych, które możesz wprowadzić, aby zweryfikować swoją tożsamość.',
    'mfa_gen_confirm_and_enable' => 'Potwierdź i włącz',
    'mfa_gen_backup_codes_title' => 'Ustawienia kopii zapasowych kodów',
    'mfa_gen_backup_codes_desc' => 'Przechowuj poniższą listę kodów w bezpiecznym miejscu. Przy dostępie do systemu będziesz mógł użyć jednego z kodów jako drugiego mechanizmu uwierzytelniania.',
    'mfa_gen_backup_codes_download' => 'Pobierz kody',
    'mfa_gen_backup_codes_usage_warning' => 'Każdy kod może być użyty tylko raz',
    'mfa_gen_totp_title' => 'Ustawienia aplikacji mobilnej',
    'mfa_gen_totp_desc' => 'Aby korzystać z uwierzytelniania wieloskładnikowego, potrzebujesz aplikacji mobilnej, która obsługuje TOTP, takiej jak Google Authenticator, Authy lub Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Zeskanuj poniższy kod QR za pomocą preferowanej aplikacji uwierzytelniającej, aby rozpocząć.',
    'mfa_gen_totp_verify_setup' => 'Sprawdź ustawienia',
    'mfa_gen_totp_verify_setup_desc' => 'Sprawdź, czy wszystko działa wprowadzając kod wygenerowany w twojej aplikacji uwierzytelniającej, w poniższym polu:',
    'mfa_gen_totp_provide_code_here' => 'Tutaj podaj kod wygenerowany przez aplikację',
    'mfa_verify_access' => 'Sprawdź dostęp',
    'mfa_verify_access_desc' => 'Twoje konto wymaga potwierdzenia tożsamości poprzez dodatkowy poziom weryfikacji, zanim uzyskasz dostęp. Zweryfikuj za pomocą jednej z skonfigurowanych metod, aby kontynuować.',
    'mfa_verify_no_methods' => 'Brak skonfigurowanych metod',
    'mfa_verify_no_methods_desc' => 'Nie można znaleźć metod uwierzytelniania wieloskładnikowego. Musisz skonfigurować co najmniej jedną metodę zanim uzyskasz dostęp.',
    'mfa_verify_use_totp' => 'Zweryfikuj używając aplikacji mobilnej',
    'mfa_verify_use_backup_codes' => 'Zweryfikuj używając kodu zapasowego',
    'mfa_verify_backup_code' => 'Kod zapasowy',
    'mfa_verify_backup_code_desc' => 'Wprowadź poniżej jeden z pozostałych kodów zapasowych:',
    'mfa_verify_backup_code_enter_here' => 'Wprowadź kod zapasowy tutaj',
    'mfa_verify_totp_desc' => 'Wprowadź kod, wygenerowany przy użyciu aplikacji mobilnej poniżej:',
    'mfa_setup_login_notification' => 'Metoda wieloskładnikowa skonfigurowana, zaloguj się ponownie za pomocą skonfigurowanej metody.',
];
