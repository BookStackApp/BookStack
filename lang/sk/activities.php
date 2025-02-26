<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'vytvoril(a) stránku',
    'page_create_notification'    => 'Stránka úspešne vytvorená',
    'page_update'                 => 'aktualizoval(a) stránku',
    'page_update_notification'    => 'Stránka úspešne aktualizovaná',
    'page_delete'                 => 'odstránil(a) stránku',
    'page_delete_notification'    => 'Stránka úspešne odstránená',
    'page_restore'                => 'obnovil(a) stránku',
    'page_restore_notification'   => 'Stránka úspešne obnovená',
    'page_move'                   => 'presunul(a) stránku',
    'page_move_notification'      => 'Stránka bola úspešne presunutá',

    // Chapters
    'chapter_create'              => 'vytvoril(a) kapitolu',
    'chapter_create_notification' => 'Kapitola úspešne vytvorená',
    'chapter_update'              => 'aktualizoval(a) kapitolu',
    'chapter_update_notification' => 'Kapitola úspešne aktualizovaná',
    'chapter_delete'              => 'odstránil(a) kapitolu',
    'chapter_delete_notification' => 'Kapitola úspešne odstránená',
    'chapter_move'                => 'presunul(a) kapitolu',
    'chapter_move_notification' => 'Kapitola bola úspešne presunutá',

    // Books
    'book_create'                 => 'vytvoril(a) knihu',
    'book_create_notification'    => 'Kniha úspešne vytvorená',
    'book_create_from_chapter'              => 'kapitola konvertovaná na knihu',
    'book_create_from_chapter_notification' => 'Kapitola úspešne konvertovaná na knihu',
    'book_update'                 => 'aktualizoval(a) knihu',
    'book_update_notification'    => 'Kniha úspešne aktualizovaná',
    'book_delete'                 => 'odstránil(a) knihu',
    'book_delete_notification'    => 'Kniha úspešne odstránená',
    'book_sort'                   => 'zoradil(a) knihu',
    'book_sort_notification'      => 'Kniha úspešne znovu zoradená',

    // Bookshelves
    'bookshelf_create'            => 'vytvoril(a) policu',
    'bookshelf_create_notification'    => 'Polica úspešne vytvorená',
    'bookshelf_create_from_book'    => 'kniha bola prevedená na policu',
    'bookshelf_create_from_book_notification'    => 'Kniha úspešne konvertovaná na poličku',
    'bookshelf_update'                 => 'aktualizoval(a) policu',
    'bookshelf_update_notification'    => 'Polica bola úspešne aktualizovaná',
    'bookshelf_delete'                 => 'odstránená polica',
    'bookshelf_delete_notification'    => 'Polica bola úspešne odstránená',

    // Revisions
    'revision_restore' => 'restored revision',
    'revision_delete' => 'odstránil(a) revíziu',
    'revision_delete_notification' => 'Revízia úspešne odstránená',

    // Favourites
    'favourite_add_notification' => '":name" bol pridaný medzi obľúbené',
    'favourite_remove_notification' => '":name" bol odstránený z obľúbených',

    // Watching
    'watch_update_level_notification' => 'Watch preferences successfully updated',

    // Auth
    'auth_login' => 'sa prihlásil(a)',
    'auth_register' => 'registered as new user',
    'auth_password_reset_request' => 'requested user password reset',
    'auth_password_reset_update' => 'reset user password',
    'mfa_setup_method' => 'configured MFA method',
    'mfa_setup_method_notification' => 'Viacúrovňový spôsob overenia úspešne nastavený',
    'mfa_remove_method' => 'removed MFA method',
    'mfa_remove_method_notification' => 'Viacúrovňový spôsob overenia úspešne odstránený',

    // Settings
    'settings_update' => 'aktualizované nastavenia',
    'settings_update_notification' => 'Nastavenia boli úspešne aktualizované',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'vytvoril(a) si webhook',
    'webhook_create_notification' => 'Webhook úspešne vytvorený',
    'webhook_update' => 'aktualizoval(a) si webhook',
    'webhook_update_notification' => 'Webhook úspešne aktualizovaný',
    'webhook_delete' => 'odstránil(a) si webhook',
    'webhook_delete_notification' => 'Webhook úspešne odstránený',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'užívateľ vytvorený',
    'user_create_notification' => 'User successfully created',
    'user_update' => 'používateľ aktualizovaný',
    'user_update_notification' => 'Používateľ úspešne upravený',
    'user_delete' => 'odstránený používateľ',
    'user_delete_notification' => 'Používateľ úspešne zmazaný',

    // API Tokens
    'api_token_create' => 'created API token',
    'api_token_create_notification' => 'API token successfully created',
    'api_token_update' => 'updated API token',
    'api_token_update_notification' => 'API token successfully updated',
    'api_token_delete' => 'deleted API token',
    'api_token_delete_notification' => 'API token successfully deleted',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Rola úspešne vytvorená',
    'role_update' => 'updated role',
    'role_update_notification' => 'Rola úspešne aktualizovaná',
    'role_delete' => 'odstrániť rolu',
    'role_delete_notification' => 'Rola úspešne zmazaná',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Comments
    'commented_on'                => 'komentoval(a)',
    'comment_create'              => 'pridal(a) komentár',
    'comment_update'              => 'aktualizoval(a) komentár',
    'comment_delete'              => 'odstrániť komentár',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'aktualizované oprávnenia',
];
