<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'loi sivun',
    'page_create_notification'    => 'Sivu luotiin onnistuneesti',
    'page_update'                 => 'päivitti sivun',
    'page_update_notification'    => 'Sivu päivitettiin onnistuneesti',
    'page_delete'                 => 'poisti sivun',
    'page_delete_notification'    => 'Sivu poistettiin onnistuneesti',
    'page_restore'                => 'palautti sivun',
    'page_restore_notification'   => 'Sivu palautettiin onnistuneesti',
    'page_move'                   => 'siirsi sivun',
    'page_move_notification'      => 'Sivu siirrettiin onnistuneesti',

    // Chapters
    'chapter_create'              => 'loi luvun',
    'chapter_create_notification' => 'Luku luotiin onnistuneesti',
    'chapter_update'              => 'päivitti luvun',
    'chapter_update_notification' => 'Luku päivitettiin onnistuneesti',
    'chapter_delete'              => 'poisti luvun',
    'chapter_delete_notification' => 'Sivu poistettiin onnistuneesti',
    'chapter_move'                => 'siirsi luvun',
    'chapter_move_notification' => 'Sivu siirrettiin onnistuneesti',

    // Books
    'book_create'                 => 'loi kirjan',
    'book_create_notification'    => 'Kirja luotiin onnistuneesti',
    'book_create_from_chapter'              => 'muunsi luvun kirjaksi',
    'book_create_from_chapter_notification' => 'Luku muunnettiin onnistuneesti kirjaksi',
    'book_update'                 => 'päivitti kirjan',
    'book_update_notification'    => 'Kirja päivitettiin onnistuneesti',
    'book_delete'                 => 'poisti kirjan',
    'book_delete_notification'    => 'Kirja poistettiin onnistuneesti',
    'book_sort'                   => 'järjesti kirjan',
    'book_sort_notification'      => 'Kirja järjestettiin uudelleen onnistuneesti',

    // Bookshelves
    'bookshelf_create'            => 'loi hyllyn',
    'bookshelf_create_notification'    => 'Hylly luotiin onnistuneesti',
    'bookshelf_create_from_book'    => 'muunsi kirjan hyllyksi',
    'bookshelf_create_from_book_notification'    => 'Kirja muunnettiin onnistuneesti hyllyksi',
    'bookshelf_update'                 => 'päivitti hyllyn',
    'bookshelf_update_notification'    => 'Hylly päivitettiin onnistuneesti',
    'bookshelf_delete'                 => 'poisti hyllyn',
    'bookshelf_delete_notification'    => 'Hylly poistettiin onnistuneesti',

    // Revisions
    'revision_restore' => 'palautti version',
    'revision_delete' => 'poisti version',
    'revision_delete_notification' => 'Versio poistettiin onnistuneesti',

    // Favourites
    'favourite_add_notification' => '":name" on lisätty suosikkeihisi',
    'favourite_remove_notification' => '":name" on poistettu suosikeistasi',

    // Watching
    'watch_update_level_notification' => 'Seurannan asetukset päivitetty onnistuneesti',

    // Auth
    'auth_login' => 'kirjautui sisään',
    'auth_register' => 'rekisteröityi uudeksi käyttäjäksi',
    'auth_password_reset_request' => 'pyysi käyttäjän salasanan nollausta',
    'auth_password_reset_update' => 'nollasi käyttäjän salasana',
    'mfa_setup_method' => 'määritti monivaiheisen tunnistaumisen menetelmän',
    'mfa_setup_method_notification' => 'Monivaiheisen tunnistautumisen määrittäminen onnistui',
    'mfa_remove_method' => 'poisti monivaiheisen tunnistaumisen menetelmän',
    'mfa_remove_method_notification' => 'Monivaiheisen tunnistautumisen menetelmä poistettiin onnistuneesti',

    // Settings
    'settings_update' => 'päivitti asetukset',
    'settings_update_notification' => 'Asetukset päivitettiin onnistuneesti',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'created webhook',
    'webhook_create_notification' => 'Webhook successfully created',
    'webhook_update' => 'updated webhook',
    'webhook_update_notification' => 'Webhook successfully updated',
    'webhook_delete' => 'deleted webhook',
    'webhook_delete_notification' => 'Webhook successfully deleted',

    // Users
    'user_create' => 'created user',
    'user_create_notification' => 'User successfully created',
    'user_update' => 'updated user',
    'user_update_notification' => 'User successfully updated',
    'user_delete' => 'deleted user',
    'user_delete_notification' => 'User successfully removed',

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

    // Comments
    'commented_on'                => 'commented on',
    'comment_create'              => 'added comment',
    'comment_update'              => 'updated comment',
    'comment_delete'              => 'deleted comment',

    // Other
    'permissions_update'          => 'updated permissions',
];
