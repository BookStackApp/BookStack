<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'ha creato la pagina',
    'page_create_notification'    => 'Pagina creata con successo',
    'page_update'                 => 'ha aggiornato la pagina',
    'page_update_notification'    => 'Pagina aggiornata con successo',
    'page_delete'                 => 'ha eliminato la pagina',
    'page_delete_notification'    => 'Pagina eliminata con successo',
    'page_restore'                => 'ha ripristinato la pagina',
    'page_restore_notification'   => 'Pagina ripristinata con successo',
    'page_move'                   => 'ha spostato la pagina',
    'page_move_notification'      => 'Pagina spostata con successo',

    // Chapters
    'chapter_create'              => 'ha creato il capitolo',
    'chapter_create_notification' => 'Capitolo creato con successo',
    'chapter_update'              => 'ha aggiornato il capitolo',
    'chapter_update_notification' => 'Capitolo aggiornato con successo',
    'chapter_delete'              => 'ha eliminato il capitolo',
    'chapter_delete_notification' => 'Capitolo eliminato con successo',
    'chapter_move'                => 'ha spostato il capitolo',
    'chapter_move_notification' => 'Capitolo spostato con successo',

    // Books
    'book_create'                 => 'ha creato il libro',
    'book_create_notification'    => 'Libro creato con successo',
    'book_create_from_chapter'              => 'ha convertito da capitolo a libro',
    'book_create_from_chapter_notification' => 'Capitolo convertito in libro con successo',
    'book_update'                 => 'ha aggiornato il libro',
    'book_update_notification'    => 'Libro aggiornato con successo',
    'book_delete'                 => 'ha eliminato il libro',
    'book_delete_notification'    => 'Libro eliminato con successo',
    'book_sort'                   => 'ha ordinato il libro',
    'book_sort_notification'      => 'Libro riordinato con successo',

    // Bookshelves
    'bookshelf_create'            => 'ha creato la libreria',
    'bookshelf_create_notification'    => 'Libreria creata con successo',
    'bookshelf_create_from_book'    => 'ha convertito il libro in libreria',
    'bookshelf_create_from_book_notification'    => 'Libro convertito in libreria con successo',
    'bookshelf_update'                 => 'ha aggiornato la libreria',
    'bookshelf_update_notification'    => 'Libreria aggiornata con successo',
    'bookshelf_delete'                 => 'ha eliminato la libreria',
    'bookshelf_delete_notification'    => 'Libreria eliminata con successo',

    // Revisions
    'revision_restore' => 'ha ripristinato la revisione',
    'revision_delete' => 'ha eliminato la revisione',
    'revision_delete_notification' => 'Revisione eliminata con successo',

    // Favourites
    'favourite_add_notification' => '":name" è stato aggiunto ai tuoi preferiti',
    'favourite_remove_notification' => '":name" è stato rimosso dai tuoi preferiti',

    // Watching
    'watch_update_level_notification' => 'Preferenze di monitoraggio aggiornate con successo',

    // Auth
    'auth_login' => 'connesso',
    'auth_register' => 'registrato come nuovo utente',
    'auth_password_reset_request' => 'ha richiesto di reimpostare la password utente',
    'auth_password_reset_update' => 'ha reimpostato la password utente',
    'mfa_setup_method' => 'ha configurato un metodo multi-fattore',
    'mfa_setup_method_notification' => 'Metodo multi-fattore impostato con successo',
    'mfa_remove_method' => 'ha rimosso un metodo multi-fattore',
    'mfa_remove_method_notification' => 'Metodo multi-fattore rimosso con successo',

    // Settings
    'settings_update' => 'ha aggiornato le impostazioni',
    'settings_update_notification' => 'Impostazioni aggiornate con successo',
    'maintenance_action_run' => 'ha eseguito un\'azione di manutenzione',

    // Webhooks
    'webhook_create' => 'ha creato un webhook',
    'webhook_create_notification' => 'Webhook creato con successo',
    'webhook_update' => 'ha aggiornato un webhook',
    'webhook_update_notification' => 'Webhook aggiornato con successo',
    'webhook_delete' => 'ha eliminato un webhook',
    'webhook_delete_notification' => 'Webhook eliminato con successo',

    // Imports
    'import_create' => 'importazione creata',
    'import_create_notification' => 'Importazione caricata con successo',
    'import_run' => 'importazione aggiornata',
    'import_run_notification' => 'Contenuto importato con successo',
    'import_delete' => 'importazione cancellata',
    'import_delete_notification' => 'Importazione eliminata con successo',

    // Users
    'user_create' => 'ha creato un utente',
    'user_create_notification' => 'Utente creato con successo',
    'user_update' => 'ha aggiornato un utente',
    'user_update_notification' => 'Utente aggiornato con successo',
    'user_delete' => 'ha eliminato un utente',
    'user_delete_notification' => 'Utente rimosso con successo',

    // API Tokens
    'api_token_create' => 'ha creato un token API',
    'api_token_create_notification' => 'Token API creato con successo',
    'api_token_update' => 'ha aggiornato un token API',
    'api_token_update_notification' => 'Token API aggiornato correttamente',
    'api_token_delete' => 'ha eliminato token API',
    'api_token_delete_notification' => 'Token API eliminato con successo',

    // Roles
    'role_create' => 'ha creato un ruolo',
    'role_create_notification' => 'Ruolo creato con successo',
    'role_update' => 'ha aggiornato un ruolo',
    'role_update_notification' => 'Ruolo aggiornato con successo',
    'role_delete' => 'ha eliminato un ruolo',
    'role_delete_notification' => 'Ruolo eliminato con successo',

    // Recycle Bin
    'recycle_bin_empty' => 'ha svuotato il cestino',
    'recycle_bin_restore' => 'ha ripristinato dal cestino',
    'recycle_bin_destroy' => 'ha rimosso dal cestino',

    // Comments
    'commented_on'                => 'ha commentato in',
    'comment_create'              => 'ha aggiunto un commento',
    'comment_update'              => 'ha aggiornato un commento',
    'comment_delete'              => 'ha rimosso un commento',

    // Sort Rules
    'sort_rule_create' => 'regola di ordinamento creata',
    'sort_rule_create_notification' => 'Ordina regola creata con successo',
    'sort_rule_update' => 'regola di ordinamento aggiornata',
    'sort_rule_update_notification' => 'Regola di ordinamento aggiornata con successo',
    'sort_rule_delete' => 'regola di ordinamento eliminata',
    'sort_rule_delete_notification' => 'Regola di ordinamento eliminata con successo',

    // Other
    'permissions_update'          => 'ha aggiornate le autorizzazioni',
];
