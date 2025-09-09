<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'erstellte Seite',
    'page_create_notification'    => 'Seite erfolgreich erstellt',
    'page_update'                 => 'aktualisierte Seite',
    'page_update_notification'    => 'Seite erfolgreich aktualisiert',
    'page_delete'                 => 'löschte Seite',
    'page_delete_notification'    => 'Seite erfolgreich gelöscht',
    'page_restore'                => 'stellte Seite wieder her',
    'page_restore_notification'   => 'Seite erfolgreich wiederhergestellt',
    'page_move'                   => 'verschob Seite',
    'page_move_notification'      => 'Seite erfolgreich verschoben',

    // Chapters
    'chapter_create'              => 'erstellte Kapitel',
    'chapter_create_notification' => 'Kapitel erfolgreich erstellt',
    'chapter_update'              => 'aktualisierte Kapitel',
    'chapter_update_notification' => 'Kapitel erfolgreich aktualisiert',
    'chapter_delete'              => 'löschte Kapitel',
    'chapter_delete_notification' => 'Kapitel erfolgreich gelöscht',
    'chapter_move'                => 'verschob Kapitel',
    'chapter_move_notification' => 'Kapitel erfolgreich verschoben',

    // Books
    'book_create'                 => 'erstellte Buch',
    'book_create_notification'    => 'Buch erfolgreich erstellt',
    'book_create_from_chapter'              => 'wandelte Kapitel zu Buch um',
    'book_create_from_chapter_notification' => 'Kapitel erfolgreich in ein Buch umgewandelt',
    'book_update'                 => 'aktualisierte Buch',
    'book_update_notification'    => 'Buch erfolgreich aktualisiert',
    'book_delete'                 => 'löschte Buch',
    'book_delete_notification'    => 'Buch erfolgreich gelöscht',
    'book_sort'                   => 'sortierte Buch',
    'book_sort_notification'      => 'Buch erfolgreich umsortiert',

    // Bookshelves
    'bookshelf_create'            => 'erstellte Regal',
    'bookshelf_create_notification'    => 'Regal erfolgreich erstellt',
    'bookshelf_create_from_book'    => 'wandelte Buch zu Regal um',
    'bookshelf_create_from_book_notification'    => 'Buch erfolgreich zu einem Regal umgewandelt',
    'bookshelf_update'                 => 'aktualisierte Regal',
    'bookshelf_update_notification'    => 'Regal erfolgreich aktualisiert',
    'bookshelf_delete'                 => 'löschte Regal',
    'bookshelf_delete_notification'    => 'Regal erfolgreich gelöscht',

    // Revisions
    'revision_restore' => 'stellte Revision wieder her',
    'revision_delete' => 'Revision gelöscht',
    'revision_delete_notification' => 'Revision erfolgreich gelöscht',

    // Favourites
    'favourite_add_notification' => '":name" wurde zu deinen Favoriten hinzugefügt',
    'favourite_remove_notification' => '":name" wurde aus deinen Favoriten entfernt',

    // Watching
    'watch_update_level_notification' => 'Beobachtungseinstellungen erfolgreich aktualisiert',

    // Auth
    'auth_login' => 'hat sich eingeloggt',
    'auth_register' => 'hat sich als neuer Benutzer registriert',
    'auth_password_reset_request' => 'hat eine Rücksetzung des Benutzerpassworts beantragt',
    'auth_password_reset_update' => 'Benutzerpasswort zurückgesetzt',
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

    // Imports
    'import_create' => 'erstellter Import',
    'import_create_notification' => 'Import erfolgreich hochgeladen',
    'import_run' => 'aktualisierter Import',
    'import_run_notification' => 'Inhalt erfolgreich importiert',
    'import_delete' => 'gelöschter Import',
    'import_delete_notification' => 'Import erfolgreich gelöscht',

    // Users
    'user_create' => 'hat Benutzer erzeugt:',
    'user_create_notification' => 'Benutzer erfolgreich erstellt',
    'user_update' => 'hat Benutzer aktualisiert:',
    'user_update_notification' => 'Benutzer erfolgreich aktualisiert',
    'user_delete' => 'hat Benutzer gelöscht: ',
    'user_delete_notification' => 'Benutzer erfolgreich entfernt',

    // API Tokens
    'api_token_create' => 'API Token wurde erstellt',
    'api_token_create_notification' => 'API-Token erfolgreich erstellt',
    'api_token_update' => 'API Token wurde aktualisiert',
    'api_token_update_notification' => 'API-Token erfolgreich aktualisiert',
    'api_token_delete' => 'API Token gelöscht',
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

    // Comments
    'commented_on'                => 'kommentiert',
    'comment_create'              => 'Kommentar hinzugefügt',
    'comment_update'              => 'Kommentar aktualisiert',
    'comment_delete'              => 'Kommentar gelöscht',

    // Sort Rules
    'sort_rule_create' => 'hat eine Sortierregel erstellt',
    'sort_rule_create_notification' => 'Sortierregel erfolgreich angelegt',
    'sort_rule_update' => 'hat eine Sortierregel aktualisiert',
    'sort_rule_update_notification' => 'Sortierregel erfolgreich aktualisiert',
    'sort_rule_delete' => 'hat eine Sortierregel gelöscht',
    'sort_rule_delete_notification' => 'Sortierregel erfolgreich gelöscht',

    // Other
    'permissions_update'          => 'aktualisierte Berechtigungen',
];
