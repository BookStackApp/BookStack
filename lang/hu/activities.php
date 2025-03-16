<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'létrehozta az oldalt',
    'page_create_notification'    => 'Oldal sikeresen létrehozva',
    'page_update'                 => 'frissítette az oldalt',
    'page_update_notification'    => 'Oldal sikeresen frissítve',
    'page_delete'                 => 'törölte az oldalt',
    'page_delete_notification'    => 'Oldal sikeresen törölve',
    'page_restore'                => 'visszaállította az oldalt',
    'page_restore_notification'   => 'Oldal sikeresen visszaállítva',
    'page_move'                   => 'áthelyezte az oldalt',
    'page_move_notification'      => 'Oldal sikeresen áthelyezve',

    // Chapters
    'chapter_create'              => 'létrehozta a fejezetet',
    'chapter_create_notification' => 'Fejezet sikeresen létrehozva',
    'chapter_update'              => 'frissítette a fejezetet',
    'chapter_update_notification' => 'Fejezet sikeresen frissítve',
    'chapter_delete'              => 'törölte a fejezetet',
    'chapter_delete_notification' => 'Fejezet sikeresen törölve',
    'chapter_move'                => 'áthelyezte a fejezetet',
    'chapter_move_notification' => 'Fejezet sikeresen áthelyezve',

    // Books
    'book_create'                 => 'létrehozott egy könyvet',
    'book_create_notification'    => 'Könyv sikeresen létrehozva',
    'book_create_from_chapter'              => 'könyvvé alakította a fejezetet',
    'book_create_from_chapter_notification' => 'Fejezet sikeresen könyvvé lett alakítva',
    'book_update'                 => 'frissítette a könyvet',
    'book_update_notification'    => 'Könyv sikeresen frissítve',
    'book_delete'                 => 'törölte a könyvet',
    'book_delete_notification'    => 'Könyv sikeresen törölve',
    'book_sort'                   => 'átrendezte a könyvet',
    'book_sort_notification'      => 'Könyv sikeresen újrarendezve',

    // Bookshelves
    'bookshelf_create'            => 'létrehozta a polcot',
    'bookshelf_create_notification'    => 'Könyvespolc sikeresen létrehozva',
    'bookshelf_create_from_book'    => 'könyvet polccá alakított',
    'bookshelf_create_from_book_notification'    => 'Könyv sikeresen polccá lett alakítva',
    'bookshelf_update'                 => 'polcot frissített',
    'bookshelf_update_notification'    => 'Polc sikeresen frissítve',
    'bookshelf_delete'                 => 'polcot törölt',
    'bookshelf_delete_notification'    => 'Polc sikeresen törölve',

    // Revisions
    'revision_restore' => 'visszaállította a változatot',
    'revision_delete' => 'törölte a változatot',
    'revision_delete_notification' => 'Változat sikeresen törölve',

    // Favourites
    'favourite_add_notification' => '":name" hozzáadva a kedvencekhez',
    'favourite_remove_notification' => '":name" törölve a kedvencek közül',

    // Watching
    'watch_update_level_notification' => 'A megfigyelési beállítások sikeresen frissültek',

    // Auth
    'auth_login' => 'bejelentkezve',
    'auth_register' => 'új felhasználóként regisztrált',
    'auth_password_reset_request' => 'jelszó visszaállítást kért',
    'auth_password_reset_update' => 'felhasználói jelszó visszaállítás',
    'mfa_setup_method' => 'MFA módszert állított be',
    'mfa_setup_method_notification' => 'Többfaktoros azonosítás sikeresen beállítva',
    'mfa_remove_method' => 'MFA módszert törölt',
    'mfa_remove_method_notification' => 'Többfaktoros azonosítás sikeresen törölve',

    // Settings
    'settings_update' => 'frissítette a beállításokat',
    'settings_update_notification' => 'Beállítások sikeresen frissítve',
    'maintenance_action_run' => 'karbantartási műveletet futtatott',

    // Webhooks
    'webhook_create' => 'webhookot hozott létre',
    'webhook_create_notification' => 'Webhook sikeresen létrehozva',
    'webhook_update' => 'webhookot frissített',
    'webhook_update_notification' => 'Webhook sikeresen frissítve',
    'webhook_delete' => 'webhookot törölt',
    'webhook_delete_notification' => 'Webhook sikeresen törölve',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'létrehozta a felhasználót',
    'user_create_notification' => 'Felhasználó sikeresen létrehozva',
    'user_update' => 'frissítette a felhasználót',
    'user_update_notification' => 'Felhasználó sikeresen frissítve',
    'user_delete' => 'felhasználót törölt',
    'user_delete_notification' => 'Felhasználó sikeresen eltávolítva',

    // API Tokens
    'api_token_create' => 'létrehozta az API tokent',
    'api_token_create_notification' => 'API token sikeresen létrehozva',
    'api_token_update' => 'frissítette az API tokent',
    'api_token_update_notification' => 'API token sikeresen frissítve',
    'api_token_delete' => 'törölte az API tokent',
    'api_token_delete_notification' => 'API token sikeresen törölve',

    // Roles
    'role_create' => 'szerepkört hozott létre',
    'role_create_notification' => 'Szerepkör sikeresen létrehozva',
    'role_update' => 'frissítette a szerepkört',
    'role_update_notification' => 'Szerepkör sikeresen frissítve',
    'role_delete' => 'törölte a szerepkört',
    'role_delete_notification' => 'Szerepkör sikeresen törölve',

    // Recycle Bin
    'recycle_bin_empty' => 'kiürítette a lomtárat',
    'recycle_bin_restore' => 'lomtárból visszaállítva',
    'recycle_bin_destroy' => 'lomtárból törölve',

    // Comments
    'commented_on'                => 'megjegyzést fűzött hozzá:',
    'comment_create'              => 'hozzáadott hozzászólás',
    'comment_update'              => 'frissített hozzászólás',
    'comment_delete'              => 'megjegyzés törlése',

    // Sort Rules
    'sort_rule_create' => 'létrehozta a rendezési szabályt',
    'sort_rule_create_notification' => 'Rendezési szabály sikeresen létrehozva',
    'sort_rule_update' => 'frissítette a rendezési szabályt',
    'sort_rule_update_notification' => 'Rendezési szabály sikeresen frissítve',
    'sort_rule_delete' => 'törölte a rendezési szabályt',
    'sort_rule_delete_notification' => 'Rendezési szabály sikeresen törölve',

    // Other
    'permissions_update'          => 'engedélyek frissítve',
];
