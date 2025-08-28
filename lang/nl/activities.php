<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'maakte pagina',
    'page_create_notification'    => 'Pagina succesvol aangemaakt',
    'page_update'                 => 'wijzigde pagina',
    'page_update_notification'    => 'Pagina succesvol bijgewerkt',
    'page_delete'                 => 'verwijderde pagina',
    'page_delete_notification'    => 'Pagina succesvol verwijderd',
    'page_restore'                => 'herstelde pagina',
    'page_restore_notification'   => 'Pagina succesvol hersteld',
    'page_move'                   => 'verplaatste pagina',
    'page_move_notification'      => 'Pagina succesvol verplaatst',

    // Chapters
    'chapter_create'              => 'maakte hoofdstuk',
    'chapter_create_notification' => 'Hoofdstuk succesvol aangemaakt',
    'chapter_update'              => 'wijzigde hoofdstuk',
    'chapter_update_notification' => 'Hoofdstuk succesvol bijgewerkt',
    'chapter_delete'              => 'verwijderde hoofdstuk',
    'chapter_delete_notification' => 'Hoofdstuk succesvol verwijderd',
    'chapter_move'                => 'verplaatste hoofdstuk',
    'chapter_move_notification' => 'Hoofdstuk succesvol verplaatst',

    // Books
    'book_create'                 => 'maakte boek',
    'book_create_notification'    => 'Boek succesvol aangemaakt',
    'book_create_from_chapter'              => 'converteerde hoofdstuk naar boek',
    'book_create_from_chapter_notification' => 'Hoofdstuk succesvol geconverteerd naar een boek',
    'book_update'                 => 'wijzigde boek',
    'book_update_notification'    => 'Boek succesvol bijgewerkt',
    'book_delete'                 => 'verwijderde boek',
    'book_delete_notification'    => 'Boek succesvol verwijderd',
    'book_sort'                   => 'sorteerde boek',
    'book_sort_notification'      => 'Boek succesvol opnieuw gesorteerd',

    // Bookshelves
    'bookshelf_create'            => 'maakte boekenplank aan',
    'bookshelf_create_notification'    => 'Boekenplank succesvol aangemaakt',
    'bookshelf_create_from_book'    => 'converteerde boek naar boekenplank',
    'bookshelf_create_from_book_notification'    => 'Boek succesvol geconverteerd naar boekenplank',
    'bookshelf_update'                 => 'werkte boekenplank bij',
    'bookshelf_update_notification'    => 'Boekenplank succesvol bijgewerkt',
    'bookshelf_delete'                 => 'verwijderde boekenplank',
    'bookshelf_delete_notification'    => 'Boekenplank succesvol verwijderd',

    // Revisions
    'revision_restore' => 'herstelde revisie',
    'revision_delete' => 'verwijderde revisie',
    'revision_delete_notification' => 'Revisie succesvol verwijderd',

    // Favourites
    'favourite_add_notification' => '":name" is toegevoegd aan je favorieten',
    'favourite_remove_notification' => '":name" is verwijderd uit je favorieten',

    // Watching
    'watch_update_level_notification' => 'Volg voorkeuren succesvol aangepast',

    // Auth
    'auth_login' => 'logde in',
    'auth_register' => 'registreerde als nieuwe gebruiker',
    'auth_password_reset_request' => 'vraagde een nieuw gebruikerswachtwoord aan',
    'auth_password_reset_update' => 'stelde gebruikerswachtwoord opnieuw in',
    'mfa_setup_method' => 'stelde meervoudige verificatie methode in',
    'mfa_setup_method_notification' => 'Meervoudige verificatie methode succesvol geconfigureerd',
    'mfa_remove_method' => 'verwijderde meervoudige verificatie methode',
    'mfa_remove_method_notification' => 'Meervoudige verificatie methode is succesvol verwijderd',

    // Settings
    'settings_update' => 'werkte instellingen bij',
    'settings_update_notification' => 'Instellingen succesvol bijgewerkt',
    'maintenance_action_run' => 'voerde onderhoudsactie uit',

    // Webhooks
    'webhook_create' => 'maakte webhook aan',
    'webhook_create_notification' => 'Webhook succesvol aangemaakt',
    'webhook_update' => 'werkte webhook bij',
    'webhook_update_notification' => 'Webhook succesvol bijgewerkt',
    'webhook_delete' => 'verwijderde webhook',
    'webhook_delete_notification' => 'Webhook succesvol verwijderd',

    // Imports
    'import_create' => 'maakte import',
    'import_create_notification' => 'Import succesvol geüpload',
    'import_run' => 'wijzigde import',
    'import_run_notification' => 'Inhoud succesvol geïmporteerd',
    'import_delete' => 'verwijderde import',
    'import_delete_notification' => 'Import succesvol verwijderd',

    // Users
    'user_create' => 'maakte gebruiker aan',
    'user_create_notification' => 'Gebruiker succesvol aangemaakt',
    'user_update' => 'werkte gebruiker bij',
    'user_update_notification' => 'Gebruiker succesvol bijgewerkt',
    'user_delete' => 'verwijderde gebruiker',
    'user_delete_notification' => 'Gebruiker succesvol verwijderd',

    // API Tokens
    'api_token_create' => 'API-token aangemaakt',
    'api_token_create_notification' => 'API-token succesvol aangemaakt',
    'api_token_update' => 'wijzigde API-token',
    'api_token_update_notification' => 'API-token succesvol bijgewerkt',
    'api_token_delete' => 'verwijderde API-token',
    'api_token_delete_notification' => 'API-token succesvol verwijderd',

    // Roles
    'role_create' => 'maakte rol aan',
    'role_create_notification' => 'Rol succesvol aangemaakt',
    'role_update' => 'werkte rol bij',
    'role_update_notification' => 'Rol succesvol bijgewerkt',
    'role_delete' => 'verwijderde rol',
    'role_delete_notification' => 'Rol succesvol verwijderd',

    // Recycle Bin
    'recycle_bin_empty' => 'leegde prullenbak',
    'recycle_bin_restore' => 'herstelde van prullenbak',
    'recycle_bin_destroy' => 'verwijderde van prullenbak',

    // Comments
    'commented_on'                => 'plaatste opmerking in',
    'comment_create'              => 'voegde opmerking toe',
    'comment_update'              => 'paste opmerking aan',
    'comment_delete'              => 'verwijderde opmerking',

    // Sort Rules
    'sort_rule_create' => 'maakte soorteerregel',
    'sort_rule_create_notification' => 'Sorteerregel succesvol aangemaakt',
    'sort_rule_update' => 'wijzigde sorteerregel',
    'sort_rule_update_notification' => 'Sorteerregel succesvol bijgewerkt',
    'sort_rule_delete' => 'verwijderde sorteerregel',
    'sort_rule_delete_notification' => 'Sorteerregel succesvol verwijderd',

    // Other
    'permissions_update'          => 'wijzigde machtigingen',
];
