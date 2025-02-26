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
    'book_create_from_chapter'              => 'trosodd bennod i lyfr',
    'book_create_from_chapter_notification' => 'Pennod wedi\'i trosi\'n llwyddiannus i lyfr',
    'book_update'                 => 'llyfr wedi diweddaru',
    'book_update_notification'    => 'Llyfr wedi\'i diweddaru\'n llwyddiannus',
    'book_delete'                 => 'llyfr wedi\'i dileu',
    'book_delete_notification'    => 'Cafodd y llyfr ei dileu yn llwyddiannus',
    'book_sort'                   => 'llyfr wedi\'i ddidoli',
    'book_sort_notification'      => 'Ail-archebwyd y llyfr yn llwyddiannus',

    // Bookshelves
    'bookshelf_create'            => 'creodd silff',
    'bookshelf_create_notification'    => 'Silff wedi\'i chreu\'n llwyddiannus',
    'bookshelf_create_from_book'    => 'trosodd lyfr i silff',
    'bookshelf_create_from_book_notification'    => 'Llyfr wedi\'i trosi\'n llwyddiannus i silff',
    'bookshelf_update'                 => 'diweddarodd silff',
    'bookshelf_update_notification'    => 'Silff wedi\'i diweddaru\'n llwyddiannus',
    'bookshelf_delete'                 => 'dileodd silff',
    'bookshelf_delete_notification'    => 'Silff wedi\'i dileu\'n llwyddiannus',

    // Revisions
    'revision_restore' => 'adferodd y diwygiad',
    'revision_delete' => 'dileuodd y diwygiad',
    'revision_delete_notification' => 'Diwygiad wedi\'i dileu\'n llwyddiannus',

    // Favourites
    'favourite_add_notification' => 'Mae ":name" wedi\'i ychwanegu at eich ffefrynnau',
    'favourite_remove_notification' => 'Mae ":name" wedi\'i tynnu o\'ch ffefrynnau',

    // Watching
    'watch_update_level_notification' => 'Dewisiadau gwylio wedi’u diweddaru\'n llwyddiannus',

    // Auth
    'auth_login' => 'wedi\'u mewngofnodi',
    'auth_register' => 'wedi\'i cofrestru\'n ddefnyddiwr newydd',
    'auth_password_reset_request' => 'wedi ceisio ailosod gair pass defnyddiwr',
    'auth_password_reset_update' => 'ailosododd air pass defnyddiwr',
    'mfa_setup_method' => 'Dull Dilysu Aml-ffactor wedi’i ffurfweddu',
    'mfa_setup_method_notification' => 'Dull aml-ffactor wedi\'i ffurfweddu\'n llwyddiannus',
    'mfa_remove_method' => 'Dull Dilysu Aml-ffactor wedi\'i ddileu',
    'mfa_remove_method_notification' => 'Llwyddwyd i ddileu dull aml-ffactor',

    // Settings
    'settings_update' => 'diweddarodd osodiadau',
    'settings_update_notification' => 'Gosodiadau wedi\'i diweddaru\'n llwyddiannus',
    'maintenance_action_run' => 'rhedeg gweithred cynnal a chadw',

    // Webhooks
    'webhook_create' => 'webhook wedi creu',
    'webhook_create_notification' => 'Webhook wedi\'i creu\'n llwyddiannus',
    'webhook_update' => 'webhook wedi\'i diweddaru',
    'webhook_update_notification' => 'Webhook wedi\'i diweddaru\'n llwyddiannus',
    'webhook_delete' => 'webhook wedi\'i dileu',
    'webhook_delete_notification' => 'Webhook wedi\'i dileu\'n llwyddiannus',

    // Imports
    'import_create' => 'creodd fewnforyn',
    'import_create_notification' => 'Mewnforyn wedi\'i lwytho i fyny\'n llwyddiannus',
    'import_run' => 'diweddarodd fewnforyn',
    'import_run_notification' => 'Cynnwys wedi\'i fewnforio\'n llwyddiannus',
    'import_delete' => 'dileodd fewnforyn',
    'import_delete_notification' => 'Mewnforyn wedi\'i ddileu\'n llwyddiannus',

    // Users
    'user_create' => 'creodd ddefnyddiwr',
    'user_create_notification' => 'Defnyddiwr wedi\'i greu\'n llwyddiannus',
    'user_update' => 'diweddarodd ddefnyddiwr',
    'user_update_notification' => 'Diweddarwyd y defnyddiwr yn llwyddiannus',
    'user_delete' => 'dileodd ddefnyddiwr',
    'user_delete_notification' => 'Tynnwyd y defnyddiwr yn llwyddiannus',

    // API Tokens
    'api_token_create' => 'creodd docyn API',
    'api_token_create_notification' => 'Tocyn API wedi\'i greu\'n llwyddiannus',
    'api_token_update' => 'diweddarodd docyn API',
    'api_token_update_notification' => 'Tocyn API wedi\'i ddiweddaru\'n llwyddiannus',
    'api_token_delete' => 'dileodd docyn API',
    'api_token_delete_notification' => 'Tocyn API wedi\'i ddileu\'n llwyddiannus',

    // Roles
    'role_create' => 'creodd rôl',
    'role_create_notification' => 'Rôl wedi\'i creu\'n llwyddiannus',
    'role_update' => 'diweddarodd rôl',
    'role_update_notification' => 'Rôl wedi\'i diweddaru\'n llwyddiannus',
    'role_delete' => 'dileodd rôl',
    'role_delete_notification' => 'Rôl wedi\'i dileu\'n llwyddiannus',

    // Recycle Bin
    'recycle_bin_empty' => 'gwagodd fin ailgylchu',
    'recycle_bin_restore' => 'wedi\'i adfer o\'r bin ailgylchu',
    'recycle_bin_destroy' => 'symudwyd o’r bin ailgylchu',

    // Comments
    'commented_on'                => 'gwnaeth sylwadau ar',
    'comment_create'              => 'ychwanegodd sylw',
    'comment_update'              => 'diweddarodd sylw',
    'comment_delete'              => 'dileodd sylw',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'caniatadau wedi\'u diweddaru',
];
