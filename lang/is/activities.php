<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'stofna síðu',
    'page_create_notification'    => 'Síða stofnuð',
    'page_update'                 => 'nafnlaus síða',
    'page_update_notification'    => 'Síða uppfærð',
    'page_delete'                 => 'síðu eytt',
    'page_delete_notification'    => 'Tókst að eyða síðu',
    'page_restore'                => 'endurvirkja síðu',
    'page_restore_notification'   => 'Síða endurvirkjuð',
    'page_move'                   => 'síða færð',
    'page_move_notification'      => 'Tókst að færa síðu',

    // Chapters
    'chapter_create'              => 'kafli búinn til',
    'chapter_create_notification' => 'Tókst að búa til kafla',
    'chapter_update'              => 'kafli uppfærður',
    'chapter_update_notification' => 'Tókst að uppfæra kafla',
    'chapter_delete'              => 'eyddur kafli',
    'chapter_delete_notification' => 'Tókst að eyða kafla',
    'chapter_move'                => 'færður kafli',
    'chapter_move_notification' => 'Tókst að færa kafla',

    // Books
    'book_create'                 => 'stofnuð bók',
    'book_create_notification'    => 'Tókst að stofna bók',
    'book_create_from_chapter'              => 'kafla breytt í bók',
    'book_create_from_chapter_notification' => 'Tókst að breyta kafla í bók',
    'book_update'                 => 'uppfærð bók',
    'book_update_notification'    => 'Tókst að uppfæra bók',
    'book_delete'                 => 'eydd bók',
    'book_delete_notification'    => 'Tókst að eyða bók',
    'book_sort'                   => 'flokkuð bók',
    'book_sort_notification'      => 'Tókst að endurflokka bók',

    // Bookshelves
    'bookshelf_create'            => 'stofna hillu',
    'bookshelf_create_notification'    => 'Tókst að stofna hillu',
    'bookshelf_create_from_book'    => 'bók breytt i hillu',
    'bookshelf_create_from_book_notification'    => 'Tókst að breyta bók í hillu',
    'bookshelf_update'                 => 'uppærð hilla',
    'bookshelf_update_notification'    => 'Tókst að uppfæra hillu',
    'bookshelf_delete'                 => 'eydd hilla',
    'bookshelf_delete_notification'    => 'Tókst að eyða hillu',

    // Revisions
    'revision_restore' => 'útgáfa bakfærð',
    'revision_delete' => 'útgáfu eytt',
    'revision_delete_notification' => 'Tókst að eyða útgáfu',

    // Favourites
    'favourite_add_notification' => 'hefur verið bætt í eftirlæti',
    'favourite_remove_notification' => 'hefur verið eytt úr eftirlæti',

    // Watching
    'watch_update_level_notification' => 'Fylgjast með hefur verið uppfært',

    // Auth
    'auth_login' => 'skráður inn',
    'auth_register' => 'skráður sem nýr notandi',
    'auth_password_reset_request' => 'bað um nýtt lykilorð',
    'auth_password_reset_update' => 'endurstilla lykilorð',
    'mfa_setup_method' => 'valin MFA aðferð',
    'mfa_setup_method_notification' => 'Fjölauðkenningar aðferð stillt',
    'mfa_remove_method' => 'fjarlægja MFA aðferð',
    'mfa_remove_method_notification' => 'Fjölauðkenningar aðferð fjarlægð',

    // Settings
    'settings_update' => 'uppfæra stillingar',
    'settings_update_notification' => 'Tókst að uppfæra stillingar',
    'maintenance_action_run' => 'keyrði uppfærslu',

    // Webhooks
    'webhook_create' => 'webhook búin til',
    'webhook_create_notification' => 'Tókst að búa til Webhook',
    'webhook_update' => 'webhook uppfærður',
    'webhook_update_notification' => 'Tókst að uppfæra Webhook',
    'webhook_delete' => 'eyða Webhook',
    'webhook_delete_notification' => 'Tókst að eyða Webhook',

    // Imports
    'import_create' => 'búa til innlestur',
    'import_create_notification' => 'Innlestur tókst',
    'import_run' => 'uppfæra innlestur',
    'import_run_notification' => 'Tókst að lesa inn',
    'import_delete' => 'innlestri eytt',
    'import_delete_notification' => 'Tókst að eyða innlestri',

    // Users
    'user_create' => 'stofnaður notandi',
    'user_create_notification' => 'Tókst að stofna notanda',
    'user_update' => 'uppfærður notandi',
    'user_update_notification' => 'Tókst að uppfæra notanda',
    'user_delete' => 'eyddur notandi',
    'user_delete_notification' => 'Tókst að eyða notanda',

    // API Tokens
    'api_token_create' => 'API token búið til',
    'api_token_create_notification' => 'Tókst að búa til API tóka',
    'api_token_update' => 'API tóki uppfærður',
    'api_token_update_notification' => 'Tókst að uppfæra API tóka',
    'api_token_delete' => 'eyddur API tóki',
    'api_token_delete_notification' => 'Tókst að eyða API tóka',

    // Roles
    'role_create' => 'stofnað hlutverk',
    'role_create_notification' => 'Tókst að stofna hlutverk',
    'role_update' => 'hlutverk uppfært',
    'role_update_notification' => 'Tókst að uppfæra hlutverk',
    'role_delete' => 'eytt hlutverk',
    'role_delete_notification' => 'Tókst að eyða hlutverki',

    // Recycle Bin
    'recycle_bin_empty' => 'tæmd ruslatunna',
    'recycle_bin_restore' => 'endurheimt úr ruslatunnu',
    'recycle_bin_destroy' => 'fjarlægt úr ruslatunnu',

    // Comments
    'commented_on'                => 'athugasemd á',
    'comment_create'              => 'athugasemd bætt við',
    'comment_update'              => 'athugasemd uppfærð',
    'comment_delete'              => 'athugasemd eytt',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'uppfærðar heimildir',
];
