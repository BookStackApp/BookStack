<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nie masz uprawnień do wyświetlenia tej strony.',
    'permissionJson' => 'Nie masz uprawnień do wykonania tej akcji.',

    // Auth
    'error_user_exists_different_creds' => 'Użytkownik o adresie :email już istnieje, ale używa innych poświadczeń.',
    'email_already_confirmed' => 'E-mail został potwierdzony, spróbuj się zalogować.',
    'email_confirmation_invalid' => 'Ten token jest nieprawidłowy lub został już wykorzystany. Spróbuj zarejestrować się ponownie.',
    'email_confirmation_expired' => 'Ten token potwierdzający wygasł. Wysłaliśmy Ci kolejny.',
    'email_confirmation_awaiting' => 'Adres e-mail dla używanego konta musi zostać potwierdzony',
    'ldap_fail_anonymous' => 'Dostęp LDAP przy użyciu anonimowego powiązania nie powiódł się',
    'ldap_fail_authed' => 'Dostęp LDAP przy użyciu tego DN i hasła nie powiódł się',
    'ldap_extension_not_installed' => 'Rozszerzenie LDAP PHP nie zostało zainstalowane',
    'ldap_cannot_connect' => 'Nie można połączyć z serwerem LDAP, połączenie nie zostało ustanowione',
    'saml_already_logged_in' => 'Już zalogowany',
    'saml_user_not_registered' => 'Użytkownik :name nie jest zarejestrowany i automatyczna rejestracja jest wyłączona',
    'saml_no_email_address' => 'Nie można odnaleźć adresu email dla tego użytkownika w danych dostarczonych przez zewnętrzny system uwierzytelniania',
    'saml_invalid_response_id' => 'Żądanie z zewnętrznego systemu uwierzytelniania nie zostało rozpoznane przez proces rozpoczęty przez tę aplikację. Cofnięcie po zalogowaniu mogło spowodować ten problem.',
    'saml_fail_authed' => 'Logowanie przy użyciu :system nie powiodło się, system nie mógł pomyślnie ukończyć uwierzytelniania',
    'oidc_already_logged_in' => 'Już zalogowany',
    'oidc_user_not_registered' => 'Użytkownik :name nie jest zarejestrowany, a automatyczna rejestracja jest wyłączona',
    'oidc_no_email_address' => 'Nie można odnaleźć adresu email dla tego użytkownika w danych dostarczonych przez zewnętrzny system uwierzytelniania',
    'oidc_fail_authed' => 'Logowanie przy użyciu :system nie powiodło się, system nie mógł pomyślnie ukończyć uwierzytelniania',
    'social_no_action_defined' => 'Brak zdefiniowanej akcji',
    'social_login_bad_response' => "Podczas próby logowania :socialAccount wystąpił błąd: \n:error",
    'social_account_in_use' => 'To konto :socialAccount jest już w użyciu. Spróbuj zalogować się za pomocą opcji :socialAccount.',
    'social_account_email_in_use' => 'E-mail :email jest już w użyciu. Jeśli masz już konto, połącz konto :socialAccount z poziomu ustawień profilu.',
    'social_account_existing' => 'Konto :socialAccount jest już połączone z Twoim profilem',
    'social_account_already_used_existing' => 'Konto :socialAccount jest już używane przez innego użytkownika.',
    'social_account_not_used' => 'To konto :socialAccount nie jest połączone z żadnym użytkownikiem. Połącz je ze swoim kontem w ustawieniach profilu. ',
    'social_account_register_instructions' => 'Jeśli nie masz jeszcze konta, możesz zarejestrować je używając opcji :socialAccount.',
    'social_driver_not_found' => 'Funkcja społecznościowa nie została odnaleziona',
    'social_driver_not_configured' => 'Ustawienia konta :socialAccount nie są poprawne.',
    'invite_token_expired' => 'Zaproszenie wygasło. Możesz spróować zresetować swoje hasło.',

    // System
    'path_not_writable' => 'Zapis do ścieżki :filePath jest niemożliwy. Upewnij się że aplikacja ma prawa do zapisu plików na serwerze.',
    'cannot_get_image_from_url' => 'Nie można pobrać obrazka z :url',
    'cannot_create_thumbs' => 'Serwer nie może utworzyć miniaturek. Upewnij się że rozszerzenie GD PHP zostało zainstalowane.',
    'server_upload_limit' => 'Serwer nie pozwala na przyjęcie pliku o tym rozmiarze. Spróbuj przesłać plik o mniejszym rozmiarze.',
    'uploaded'  => 'Serwer nie pozwala na przyjęcie pliku o tym rozmiarze. Spróbuj przesłać plik o mniejszym rozmiarze.',

    // Drawing & Images
    'image_upload_error' => 'Wystąpił błąd podczas przesyłania obrazka',
    'image_upload_type_error' => 'Typ przesłanego obrazka jest nieprwidłowy.',
    'drawing_data_not_found' => 'Nie można załadować danych rysunku. Plik rysunku może już nie istnieć lub nie masz uprawnień dostępu do niego.',

    // Attachments
    'attachment_not_found' => 'Nie znaleziono załącznika',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Zapis wersji roboczej nie powiódł się. Upewnij się, że posiadasz połączenie z internetem.',
    'page_custom_home_deletion' => 'Nie można usunąć strony, jeśli jest ona ustawiona jako strona główna',

    // Entities
    'entity_not_found' => 'Nie znaleziono obiektu',
    'bookshelf_not_found' => 'Nie znaleziono półki',
    'book_not_found' => 'Nie znaleziono książki',
    'page_not_found' => 'Nie znaleziono strony',
    'chapter_not_found' => 'Nie znaleziono rozdziału',
    'selected_book_not_found' => 'Wybrana książka nie została znaleziona',
    'selected_book_chapter_not_found' => 'Wybrana książka lub rozdział nie został znaleziony',
    'guests_cannot_save_drafts' => 'Goście nie mogą zapisywać wersji roboczych',

    // Users
    'users_cannot_delete_only_admin' => 'Nie możesz usunąć jedynego administratora',
    'users_cannot_delete_guest' => 'Nie możesz usunąć użytkownika-gościa',

    // Roles
    'role_cannot_be_edited' => 'Ta rola nie może być edytowana',
    'role_system_cannot_be_deleted' => 'Ta rola jest rolą systemową i nie może zostać usunięta',
    'role_registration_default_cannot_delete' => 'Ta rola nie może zostać usunięta, dopóki jest ustawiona jako domyślna rola użytkownika',
    'role_cannot_remove_only_admin' => 'Ten użytkownik jest jedynym użytkownikiem przypisanym do roli administratora. Przypisz rolę administratora innemu użytkownikowi przed próbą usunięcia.',

    // Comments
    'comment_list' => 'Wystąpił błąd podczas pobierania komentarzy.',
    'cannot_add_comment_to_draft' => 'Nie możesz dodawać komentarzy do wersji roboczej.',
    'comment_add' => 'Wystąpił błąd podczas dodwania / aktualizaowania komentarza.',
    'comment_delete' => 'Wystąpił błąd podczas usuwania komentarza.',
    'empty_comment' => 'Nie można dodać pustego komentarza.',

    // Error pages
    '404_page_not_found' => 'Strona nie została znaleziona',
    'sorry_page_not_found' => 'Przepraszamy, ale strona której szukasz nie została znaleziona.',
    'sorry_page_not_found_permission_warning' => 'Jeśli spodziewałeś się, że ta strona istnieje, prawdopodobnie nie masz uprawnień do jej wyświetlenia.',
    'image_not_found' => 'Nie znaleziono obrazu',
    'image_not_found_subtitle' => 'Przepraszamy, ale obraz którego szukasz nie został znaleziony.',
    'image_not_found_details' => 'Jeśli spodziewałeś się, że ten obraz istnieje, mógł on zostać usunięty.',
    'return_home' => 'Powrót do strony głównej',
    'error_occurred' => 'Wystąpił błąd',
    'app_down' => ':appName jest aktualnie wyłączona',
    'back_soon' => 'Niedługo zostanie uruchomiona ponownie.',

    // API errors
    'api_no_authorization_found' => 'Nie znaleziono tokenu autoryzacji dla żądania',
    'api_bad_authorization_format' => 'Token autoryzacji został znaleziony w żądaniu, ale format okazał się nieprawidłowy',
    'api_user_token_not_found' => 'Nie znaleziono pasującego tokenu API dla podanego tokenu autoryzacji',
    'api_incorrect_token_secret' => 'Podany sekret dla tego API jest nieprawidłowy',
    'api_user_no_api_permission' => 'Właściciel używanego tokenu API nie ma uprawnień do wykonywania zapytań do API',
    'api_user_token_expired' => 'Token uwierzytelniania wygasł',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Błąd podczas wysyłania testowej wiadomości e-mail:',

];
