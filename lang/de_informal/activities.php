<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'erstellt Seite',
    'page_create_notification'    => 'Seite erfolgreich erstellt',
    'page_update'                 => 'aktualisiert Seite',
    'page_update_notification'    => 'Seite erfolgreich aktualisiert',
    'page_delete'                 => 'löscht Seite',
    'page_delete_notification'    => 'Seite erfolgreich gelöscht',
    'page_restore'                => 'stellt Seite wieder her',
    'page_restore_notification'   => 'Seite erfolgreich wiederhergestellt',
    'page_move'                   => 'verschiebt Seite',
    'page_move_notification'      => 'Seite erfolgreich verschoben',

    // Chapters
    'chapter_create'              => 'erstellt Kapitel',
    'chapter_create_notification' => 'Kapitel erfolgreich erstellt',
    'chapter_update'              => 'aktualisiert Kapitel',
    'chapter_update_notification' => 'Kapitel erfolgreich aktualisiert',
    'chapter_delete'              => 'löscht Kapitel',
    'chapter_delete_notification' => 'Kapitel erfolgreich gelöscht',
    'chapter_move'                => 'verschiebt Kapitel',
    'chapter_move_notification' => 'Kapitel erfolgreich verschoben',

    // Books
    'book_create'                 => 'erstellt Buch',
    'book_create_notification'    => 'Buch erfolgreich erstellt',
    'book_create_from_chapter'              => 'Kapitel zu Buch umgewandelt',
    'book_create_from_chapter_notification' => 'Kapitel erfolgreich in ein Buch umgewandelt',
    'book_update'                 => 'aktualisiert Buch',
    'book_update_notification'    => 'Buch erfolgreich aktualisiert',
    'book_delete'                 => 'löscht Buch',
    'book_delete_notification'    => 'Buch erfolgreich gelöscht',
    'book_sort'                   => 'sortiert Buch',
    'book_sort_notification'      => 'Buch erfolgreich umsortiert',

    // Bookshelves
    'bookshelf_create'            => 'Regal erstellt',
    'bookshelf_create_notification'    => 'Regal erfolgreich erstellt',
    'bookshelf_create_from_book'    => 'Buch zu Regal umgewandelt',
    'bookshelf_create_from_book_notification'    => 'Buch erfolgreich zu einem Regal umgewandelt',
    'bookshelf_update'                 => 'Regal aktualisiert',
    'bookshelf_update_notification'    => 'Regal erfolgreich aktualisiert',
    'bookshelf_delete'                 => 'Regal gelöscht',
    'bookshelf_delete_notification'    => 'Regal erfolgreich gelöscht',

    // Revisions
    'revision_restore' => 'stellte Revision wieder her:',
    'revision_delete' => 'löschte Revision',
    'revision_delete_notification' => 'Revision erfolgreich gelöscht',

    // Favourites
    'favourite_add_notification' => '":name" wurde zu deinen Favoriten hinzugefügt',
    'favourite_remove_notification' => '":name" wurde aus deinen Favoriten entfernt',

    // Auth
    'auth_login' => 'hat sich eingeloggt',
    'auth_register' => 'hat sich als neuer Benutzer registriert',
    'auth_password_reset_request' => 'hat eine Rücksetzung des Benutzerpassworts beantragt',
    'auth_password_reset_update' => 'hat Benutzerpasswort zurückgesetzt',
    'mfa_setup_method' => 'hat MFA-Methode konfiguriert',
    'mfa_setup_method_notification' => 'Multi-Faktor-Methode erfolgreich konfiguriert',
    'mfa_remove_method' => 'hat MFA-Methode entfernt',
    'mfa_remove_method_notification' => 'Multi-Faktor-Methode erfolgreich entfernt',

    // Settings
    'settings_update' => 'hat Einstellungen aktualisiert',
    'settings_update_notification' => 'Einstellungen erfolgreich aktualisiert',
    'maintenance_action_run' => 'hat Wartungsarbeiten ausgeführt',

    // Webhooks
    'webhook_create' => 'erstellter Webhook',
    'webhook_create_notification' => 'Webhook erfolgreich eingerichtet',
    'webhook_update' => 'aktualisierter Webhook',
    'webhook_update_notification' => 'Webhook erfolgreich aktualisiert',
    'webhook_delete' => 'gelöschter Webhook',
    'webhook_delete_notification' => 'Webhook erfolgreich gelöscht',

    // Users
    'user_create' => 'hat Benutzer erzeugt:',
    'user_create_notification' => 'Benutzer erfolgreich erstellt',
    'user_update' => 'hat Benutzer aktualisiert:',
    'user_update_notification' => 'Benutzer erfolgreich aktualisiert',
    'user_delete' => 'hat Benutzer gelöscht: ',
    'user_delete_notification' => 'Benutzer erfolgreich entfernt',

    // API Tokens
    'api_token_create' => 'hat API-Token erzeugt:',
    'api_token_create_notification' => 'API-Token erfolgreich erstellt',
    'api_token_update' => 'hat API-Token aktualisiert:',
    'api_token_update_notification' => 'API-Token erfolgreich aktualisiert',
    'api_token_delete' => 'hat API-Token gelöscht:',
    'api_token_delete_notification' => 'API-Token erfolgreich gelöscht',

    // Roles
    'role_create' => 'hat Rolle erzeugt:',
    'role_create_notification' => 'Rolle erfolgreich erstellt',
    'role_update' => 'hat Rolle aktualisiert:',
    'role_update_notification' => 'Rolle erfolgreich aktualisiert',
    'role_delete' => 'hat Rolle gelöscht:',
    'role_delete_notification' => 'Rolle erfolgreich gelöscht',

    // Recycle Bin
    'recycle_bin_empty' => 'hat den Papierkorb geleert',
    'recycle_bin_restore' => 'aus dem Papierkorb wiederhergestellt',
    'recycle_bin_destroy' => 'aus dem Papierkorb gelöscht',

    // Other
    'commented_on'                => 'kommentiert',
    'permissions_update'          => 'aktualisierte Berechtigungen',
];
