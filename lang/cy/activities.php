<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'creodd dudalen',
    'page_create_notification'    => 'Tudalen wedi\'i chreu\'n llwyddiannus',
    'page_update'                 => 'diweddarodd dudalen',
    'page_update_notification'    => 'Tudalen wedi\'i diweddaru\'n llwyddiannus',
    'page_delete'                 => 'dileodd dudalen',
    'page_delete_notification'    => 'Tudalen wedi\'i dileu\'n llwyddiannus',
    'page_restore'                => 'adferodd dudalen',
    'page_restore_notification'   => 'Tudalen wedi\'i hadfer yn llwyddiannus',
    'page_move'                   => 'symydodd dudalen',
    'page_move_notification'      => 'Tudalen wedi\'i symud yn llwyddianus',

    // Chapters
    'chapter_create'              => 'creodd bennod',
    'chapter_create_notification' => 'Pennod wedi\'i chreu\'n llwyddiannus',
    'chapter_update'              => 'pennod wedi diweddaru',
    'chapter_update_notification' => 'Pennod wedi\'i diweddaru\'n llwyddiannus',
    'chapter_delete'              => 'pennod wedi dileu',
    'chapter_delete_notification' => 'Pennod wedi\'i dileu\'n llwyddiannus',
    'chapter_move'                => 'pennod wedi symud',
    'chapter_move_notification' => 'Pennod wedi\'i symud yn llwyddianus',

    // Books
    'book_create'                 => 'llyfr wedi creu',
    'book_create_notification'    => 'Llyfr wedi\'i creu\'n llwyddiannus',
    'book_create_from_chapter'              => 'converted chapter to book',
    'book_create_from_chapter_notification' => 'Chapter successfully converted to a book',
    'book_update'                 => 'llyfr wedi diweddaru',
    'book_update_notification'    => 'Llyfr wedi\'i diweddaru\'n llwyddiannus',
    'book_delete'                 => 'llyfr wedi\'i dileu',
    'book_delete_notification'    => 'Cafodd y llyfr ei dileu yn llwyddiannus',
    'book_sort'                   => 'llyfr wedi\'i ddidoli',
    'book_sort_notification'      => 'Ail-archebwyd y llyfr yn llwyddiannus',

    // Bookshelves
    'bookshelf_create'            => 'creodd silff',
    'bookshelf_create_notification'    => 'Silff wedi\'i chreu\'n llwyddiannus',
    'bookshelf_create_from_book'    => 'converted book to shelf',
    'bookshelf_create_from_book_notification'    => 'Book successfully converted to a shelf',
    'bookshelf_update'                 => 'diweddarodd silff',
    'bookshelf_update_notification'    => 'Silff wedi\'i diweddaru\'n llwyddiannus',
    'bookshelf_delete'                 => 'dileodd silff',
    'bookshelf_delete_notification'    => 'Silff wedi\'i dileu\'n llwyddiannus',

    // Revisions
    'revision_restore' => 'restored revision',
    'revision_delete' => 'deleted revision',
    'revision_delete_notification' => 'Revision successfully deleted',

    // Favourites
    'favourite_add_notification' => 'Mae ":name" wedi\'i ychwanegu at eich ffefrynnau',
    'favourite_remove_notification' => 'Mae ":name" wedi\'i tynnu o\'ch ffefrynnau',

    // Watching
    'watch_update_level_notification' => 'Watch preferences successfully updated',

    // Auth
    'auth_login' => 'wedi\'u mewngofnodi',
    'auth_register' => 'registered as new user',
    'auth_password_reset_request' => 'requested user password reset',
    'auth_password_reset_update' => 'reset user password',
    'mfa_setup_method' => 'configured MFA method',
    'mfa_setup_method_notification' => 'Dull aml-ffactor wedi\'i ffurfweddu\'n llwyddiannus',
    'mfa_remove_method' => 'removed MFA method',
    'mfa_remove_method_notification' => 'Llwyddwyd i ddileu dull aml-ffactor',

    // Settings
    'settings_update' => 'updated settings',
    'settings_update_notification' => 'Settings successfully updated',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'webhook wedi creu',
    'webhook_create_notification' => 'Webhook wedi\'i creu\'n llwyddiannus',
    'webhook_update' => 'webhook wedi\'i diweddaru',
    'webhook_update_notification' => 'Webhook wedi\'i diweddaru\'n llwyddiannus',
    'webhook_delete' => 'webhook wedi\'i dileu',
    'webhook_delete_notification' => 'Webhook wedi\'i dileu\'n llwyddiannus',

    // Users
    'user_create' => 'creodd ddefnyddiwr',
    'user_create_notification' => 'Defnyddiwr wedi\'i greu\'n llwyddiannus',
    'user_update' => 'diweddarodd ddefnyddiwr',
    'user_update_notification' => 'Diweddarwyd y defnyddiwr yn llwyddiannus',
    'user_delete' => 'dileodd ddefnyddiwr',
    'user_delete_notification' => 'Tynnwyd y defnyddiwr yn llwyddiannus',

    // API Tokens
    'api_token_create' => 'created API token',
    'api_token_create_notification' => 'Tocyn API wedi\'i greu\'n llwyddiannus',
    'api_token_update' => 'updated API token',
    'api_token_update_notification' => 'Tocyn API wedi\'i ddiweddaru\'n llwyddiannus',
    'api_token_delete' => 'deleted API token',
    'api_token_delete_notification' => 'Tocyn API wedi\'i ddileu\'n llwyddiannus',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Role successfully created',
    'role_update' => 'updated role',
    'role_update_notification' => 'Role successfully updated',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Role successfully deleted',

    // Recycle Bin
    'recycle_bin_empty' => 'gwagodd fin ailgylchu',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Comments
    'commented_on'                => 'gwnaeth sylwadau ar',
    'comment_create'              => 'ychwanegodd sylw',
    'comment_update'              => 'diweddarodd sylw',
    'comment_delete'              => 'dileodd sylw',

    // Other
    'permissions_update'          => 'caniatadau wedi\'u diweddaru',
];
