<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'ustvarjena stran',
    'page_create_notification'    => 'Stran uspešno ustvarjena',
    'page_update'                 => 'posodobljena stran',
    'page_update_notification'    => 'Stran uspešno posodobljena',
    'page_delete'                 => 'izbrisana stran',
    'page_delete_notification'    => 'Stran uspešno izbrisana',
    'page_restore'                => 'obnovljena stran',
    'page_restore_notification'   => 'Stran uspešno obnovljena',
    'page_move'                   => 'premaknjena stran',
    'page_move_notification'      => 'Stran uspešno premaknjena',

    // Chapters
    'chapter_create'              => 'ustvarjeno poglavje',
    'chapter_create_notification' => 'Poglavje uspešno ustvarjeno',
    'chapter_update'              => 'posodobljeno poglavje',
    'chapter_update_notification' => 'Poglavje uspešno posodobljeno',
    'chapter_delete'              => 'izbrisano poglavje',
    'chapter_delete_notification' => 'Poglavje uspešno izbrisano',
    'chapter_move'                => 'premaknjeno poglavje',
    'chapter_move_notification' => 'Poglavje uspešno premaknjeno',

    // Books
    'book_create'                 => 'knjiga ustvarjena',
    'book_create_notification'    => 'Knjiga uspešno ustvarjena',
    'book_create_from_chapter'              => 'poglavje pretvorjeno v knjigo',
    'book_create_from_chapter_notification' => 'Poglavje uspešno pretvorjeno v knjigo',
    'book_update'                 => 'knjiga posodobljena',
    'book_update_notification'    => 'Knjiga uspešno posodobljena',
    'book_delete'                 => 'izbrisana knjiga',
    'book_delete_notification'    => 'Knjiga uspešno izbrisana',
    'book_sort'                   => 'razvrščena knjiga',
    'book_sort_notification'      => 'Knjiga uspešno razvrščena',

    // Bookshelves
    'bookshelf_create'            => 'knjižna polica ustvarjena',
    'bookshelf_create_notification'    => 'Knjižna polica uspešno ustvarjena',
    'bookshelf_create_from_book'    => 'knjiga pretvorjena v knjižno polico',
    'bookshelf_create_from_book_notification'    => 'Knjiga uspešno pretvorjena v knjižno polico',
    'bookshelf_update'                 => 'knjižna polica posodobljena',
    'bookshelf_update_notification'    => 'Knjižna polica uspešno posodobljena',
    'bookshelf_delete'                 => 'knjižna polica izbrisana',
    'bookshelf_delete_notification'    => 'Knjižna polica uspešno izbrisana',

    // Revisions
    'revision_restore' => 'obnovljena različica',
    'revision_delete' => 'izbrisana različica',
    'revision_delete_notification' => 'Različica uspešno izbrisana',

    // Favourites
    'favourite_add_notification' => '":name" dodan med priljubljene',
    'favourite_remove_notification' => '":name" odstranjen iz priljubljenih',

    // Watching
    'watch_update_level_notification' => 'Nastavitve spremljanja uspešno posodobljene',

    // Auth
    'auth_login' => 'prijavljen',
    'auth_register' => 'registriran kot nov uporabnik',
    'auth_password_reset_request' => 'zahteva ponastavitev uporabniškega gesla',
    'auth_password_reset_update' => 'ponastavitev uporabniškega gesla',
    'mfa_setup_method' => 'nastavljena metoda MFA',
    'mfa_setup_method_notification' => 'Večfaktorska avtentikacija (MFA) uspešno nastavljena',
    'mfa_remove_method' => 'odstranjena metoda MFA',
    'mfa_remove_method_notification' => 'Večfaktorska avtentikacija (MFA) uspešno odstranjena',

    // Settings
    'settings_update' => 'posodobitev nastavitev',
    'settings_update_notification' => 'Nastavitve uspešno posodobljene',
    'maintenance_action_run' => 'zagnano vzdrževanje',

    // Webhooks
    'webhook_create' => 'ustvarjen webhook',
    'webhook_create_notification' => 'Webhook uspešno ustvarjen',
    'webhook_update' => 'posodobljen webhook',
    'webhook_update_notification' => 'Webhook uspešno posodobljen',
    'webhook_delete' => 'izbrisan webhook',
    'webhook_delete_notification' => 'Webhook uspešno izbrisan',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'ustvarjen uporabnik',
    'user_create_notification' => 'Uporabnik uspešno ustvarjen',
    'user_update' => 'posodobljen uporabnik',
    'user_update_notification' => 'Uporabnik uspešno posodobljen',
    'user_delete' => 'uporabnik izbrisan',
    'user_delete_notification' => 'Uporabnik uspešno izbrisan',

    // API Tokens
    'api_token_create' => 'ustvarjen žeton API',
    'api_token_create_notification' => 'Žeton API uspešno ustvarjen',
    'api_token_update' => 'posodobljen žeton API',
    'api_token_update_notification' => 'Žeton API uspešno posodobljen',
    'api_token_delete' => 'izbrisan žeton API',
    'api_token_delete_notification' => 'Žeton API uspešno izbrisan',

    // Roles
    'role_create' => 'ustvarjena vloga',
    'role_create_notification' => 'Vloga uspešno ustvarjena',
    'role_update' => 'posodobljena vloga',
    'role_update_notification' => 'Vloga uspešno posodobljena',
    'role_delete' => 'izbrisana vloga',
    'role_delete_notification' => 'Vloga uspešno izbrisana',

    // Recycle Bin
    'recycle_bin_empty' => 'izpraznjen koš',
    'recycle_bin_restore' => 'obnovljeno iz koša',
    'recycle_bin_destroy' => 'odstranjeno iz koša',

    // Comments
    'commented_on'                => 'komentar na',
    'comment_create'              => 'dodan komentar',
    'comment_update'              => 'posodobljen komentar',
    'comment_delete'              => 'izbrisan komentar',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'pravice so posodobljene',
];
