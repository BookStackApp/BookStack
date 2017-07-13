<?php

return [

    /**
     * Error text strings.
     */

    // Permissions
    'permission' => 'Nie masz uprawnień do wyświetlenia tej strony.',
    'permissionJson' => 'Nie masz uprawnień do wykonania tej akcji.',

    // Auth
    'error_user_exists_different_creds' => 'Użytkownik o adresie :email już istnieje.',
    'email_already_confirmed' => 'Email został potwierdzony, spróbuj się zalogować.',
    'email_confirmation_invalid' => 'Ten token jest nieprawidłowy lub został już wykorzystany. Spróbuj zarejestrować się ponownie.',
    'email_confirmation_expired' => 'Ten token potwierdzający wygasł. Wysłaliśmy Ci kolejny.',
    'ldap_fail_anonymous' => 'Dostęp LDAP przy użyciu anonimowego powiązania nie powiódł się',
    'ldap_fail_authed' => 'Dostęp LDAP przy użyciu tego dn i hasła nie powiódł się',
    'ldap_extension_not_installed' => 'Rozszerzenie LDAP PHP nie zostało zainstalowane',
    'ldap_cannot_connect' => 'Nie można połączyć z serwerem LDAP, połączenie nie zostało ustanowione',
    'social_no_action_defined' => 'Brak zdefiniowanej akcji',
    'social_account_in_use' => 'To konto :socialAccount jest już w użyciu, spróbuj zalogować się za pomocą opcji :socialAccount.',
    'social_account_email_in_use' => 'Email :email jest już w użyciu. Jeśli masz już konto, połącz konto :socialAccount z poziomu ustawień profilu.',
    'social_account_existing' => 'Konto :socialAccount jest już połączone z Twoim profilem',
    'social_account_already_used_existing' => 'Konto :socialAccount jest już używane przez innego użytkownika.',
    'social_account_not_used' => 'To konto :socialAccount nie jest połączone z żadnym użytkownikiem. Połącz je ze swoim kontem w ustawieniach profilu. ',
    'social_account_register_instructions' => 'Jeśli nie masz jeszcze konta, możesz zarejestrować je używając opcji :socialAccount.',
    'social_driver_not_found' => 'Funkcja społecznościowa nie została odnaleziona',
    'social_driver_not_configured' => 'Ustawienia konta :socialAccount nie są poprawne.',

    // System
    'path_not_writable' => 'Zapis do ścieżki :filePath jest niemożliwy. Upewnij się że aplikacja ma prawa do zapisu w niej.',
    'cannot_get_image_from_url' => 'Nie można pobrać obrazka z :url',
    'cannot_create_thumbs' => 'Serwer nie może utworzyć miniaturek. Upewnij się że rozszerzenie GD PHP zostało zainstalowane.',
    'server_upload_limit' => 'Serwer nie pozwala na przyjęcie pliku o tym rozmiarze. Spróbuj udostępnić coś o mniejszym rozmiarze.',
    'image_upload_error' => 'Wystąpił błąd podczas udostępniania obrazka',

    // Attachments
    'attachment_page_mismatch' => 'Niezgodność stron podczas aktualizacji załącznika',

    // Pages
    'page_draft_autosave_fail' => 'Zapis szkicu nie powiódł się. Upewnij się że posiadasz połączenie z internetem.',

    // Entities
    'entity_not_found' => 'Encja nie została odnaleziona',
    'book_not_found' => 'Księga nie została odnaleziona',
    'page_not_found' => 'Strona nie została odnaleziona',
    'chapter_not_found' => 'Rozdział nie został odnaleziony',
    'selected_book_not_found' => 'Wybrana księga nie została odnaleziona',
    'selected_book_chapter_not_found' => 'Wybrana księga lub rozdział nie zostały odnalezione',
    'guests_cannot_save_drafts' => 'Goście nie mogą zapisywać szkiców',

    // Users
    'users_cannot_delete_only_admin' => 'Nie możesz usunąć jedynego administratora',
    'users_cannot_delete_guest' => 'Nie możesz usunąć użytkownika-gościa',

    // Roles
    'role_cannot_be_edited' => 'Ta rola nie może być edytowana',
    'role_system_cannot_be_deleted' => 'Ta rola jest rolą systemową i nie może zostać usunięta',
    'role_registration_default_cannot_delete' => 'Ta rola nie może zostać usunięta jeśli jest ustawiona jako domyślna rola użytkownika',

    // Error pages
    '404_page_not_found' => 'Strona nie została odnaleziona',
    'sorry_page_not_found' => 'Przepraszamy, ale strona której szukasz nie została odnaleziona.',
    'return_home' => 'Powrót do strony głównej',
    'error_occurred' => 'Wystąpił błąd',
    'app_down' => ':appName jest aktualnie wyłączona',
    'back_soon' => 'Niedługo zostanie uruchomiona ponownie.',
];