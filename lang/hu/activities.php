<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'létrehozta az oldalt:',
    'page_create_notification'    => 'Oldal sikeresen létrehozva',
    'page_update'                 => 'frissítette az oldalt:',
    'page_update_notification'    => 'Oldal sikeresen frissítve',
    'page_delete'                 => 'törölte az oldalt:',
    'page_delete_notification'    => 'Oldal sikeresen törölve',
    'page_restore'                => 'visszaállította az oldalt:',
    'page_restore_notification'   => 'Oldal sikeresen visszaállítva',
    'page_move'                   => 'áthelyezte az oldalt:',
    'page_move_notification'      => 'Page successfully moved',

    // Chapters
    'chapter_create'              => 'létrehozta a fejezetet:',
    'chapter_create_notification' => 'Fejezet sikeresen létrehozva',
    'chapter_update'              => 'frissítette a fejezetet:',
    'chapter_update_notification' => 'Fejezet sikeresen frissítve',
    'chapter_delete'              => 'törölte a fejezetet:',
    'chapter_delete_notification' => 'Fejezet sikeresen törölve',
    'chapter_move'                => 'áthelyezte a fejezetet:',
    'chapter_move_notification' => 'Chapter successfully moved',

    // Books
    'book_create'                 => 'létrehozott egy könyvet:',
    'book_create_notification'    => 'Könyv sikeresen létrehozva',
    'book_create_from_chapter'              => 'converted chapter to book',
    'book_create_from_chapter_notification' => 'Chapter successfully converted to a book',
    'book_update'                 => 'frissítette a könyvet:',
    'book_update_notification'    => 'Könyv sikeresen frissítve',
    'book_delete'                 => 'törölte a könyvet:',
    'book_delete_notification'    => 'Könyv sikeresen törölve',
    'book_sort'                   => 'átrendezte a könyvet:',
    'book_sort_notification'      => 'Könyv sikeresen újrarendezve',

    // Bookshelves
    'bookshelf_create'            => 'created shelf',
    'bookshelf_create_notification'    => 'Shelf successfully created',
    'bookshelf_create_from_book'    => 'converted book to shelf',
    'bookshelf_create_from_book_notification'    => 'Book successfully converted to a shelf',
    'bookshelf_update'                 => 'updated shelf',
    'bookshelf_update_notification'    => 'Shelf successfully updated',
    'bookshelf_delete'                 => 'deleted shelf',
    'bookshelf_delete_notification'    => 'Shelf successfully deleted',

    // Revisions
    'revision_restore' => 'restored revision',
    'revision_delete' => 'deleted revision',
    'revision_delete_notification' => 'Revision successfully deleted',

    // Favourites
    'favourite_add_notification' => '":name" has been added to your favourites',
    'favourite_remove_notification' => '":name" has been removed from your favourites',

    // Auth
    'auth_login' => 'logged in',
    'auth_register' => 'registered as new user',
    'auth_password_reset_request' => 'requested user password reset',
    'auth_password_reset_update' => 'reset user password',
    'mfa_setup_method' => 'configured MFA method',
    'mfa_setup_method_notification' => 'Multi-factor method successfully configured',
    'mfa_remove_method' => 'removed MFA method',
    'mfa_remove_method_notification' => 'Multi-factor method successfully removed',

    // Settings
    'settings_update' => 'updated settings',
    'settings_update_notification' => 'Settings successfully updated',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'created webhook',
    'webhook_create_notification' => 'Webhook sikeresen létrehozva',
    'webhook_update' => 'updated webhook',
    'webhook_update_notification' => 'Webhook sikeresen frissítve',
    'webhook_delete' => 'deleted webhook',
    'webhook_delete_notification' => 'Webhook successfully deleted',

    // Users
    'user_create' => 'created user',
    'user_create_notification' => 'User successfully created',
    'user_update' => 'updated user',
    'user_update_notification' => 'Felhasználó sikeresen frissítve',
    'user_delete' => 'deleted user',
    'user_delete_notification' => 'Felhasználó sikeresen eltávolítva',

    // API Tokens
    'api_token_create' => 'created api token',
    'api_token_create_notification' => 'API token successfully created',
    'api_token_update' => 'updated api token',
    'api_token_update_notification' => 'API token successfully updated',
    'api_token_delete' => 'deleted api token',
    'api_token_delete_notification' => 'API token successfully deleted',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Role successfully created',
    'role_update' => 'updated role',
    'role_update_notification' => 'Role successfully updated',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Role successfully deleted',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Other
    'commented_on'                => 'megjegyzést fűzött hozzá:',
    'permissions_update'          => 'updated permissions',
];
