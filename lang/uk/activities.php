<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'створено сторінку',
    'page_create_notification'    => 'Сторінка успішно створена',
    'page_update'                 => 'оновив сторінку',
    'page_update_notification'    => 'Сторінка успішно оновлена',
    'page_delete'                 => 'видалив сторінку',
    'page_delete_notification'    => 'Сторінка успішно видалена',
    'page_restore'                => 'відновив сторінку',
    'page_restore_notification'   => 'Сторінка успішно відновлена',
    'page_move'                   => 'перемістив сторінку',
    'page_move_notification'      => 'Сторінку успішно перенесено',

    // Chapters
    'chapter_create'              => 'створив розділ',
    'chapter_create_notification' => 'Розділ успішно створено',
    'chapter_update'              => 'оновив розділ',
    'chapter_update_notification' => 'Розділ успішно оновлено',
    'chapter_delete'              => 'видалив розділ',
    'chapter_delete_notification' => 'Розділ успішно видалено',
    'chapter_move'                => 'перемістив розділ',
    'chapter_move_notification' => 'Розділ успішно перенесений',

    // Books
    'book_create'                 => 'створив книгу',
    'book_create_notification'    => 'Книгу успішно створено',
    'book_create_from_chapter'              => 'перетворений розділ в книгу',
    'book_create_from_chapter_notification' => 'Розділ успішно перетворений в книгу',
    'book_update'                 => 'оновив книгу',
    'book_update_notification'    => 'Книгу успішно оновлено',
    'book_delete'                 => 'видалив книгу',
    'book_delete_notification'    => 'Книгу успішно видалено',
    'book_sort'                   => 'sorted книгу',
    'book_sort_notification'      => 'Книгу успішно відновлено',

    // Bookshelves
    'bookshelf_create'            => 'створено полицю',
    'bookshelf_create_notification'    => 'Полиця успішно створена',
    'bookshelf_create_from_book'    => 'конвертовано книгу у полицю',
    'bookshelf_create_from_book_notification'    => 'Книжку успішно конвертовано на полицю',
    'bookshelf_update'                 => 'оновлено полицю',
    'bookshelf_update_notification'    => 'Полиця успішно оновлена',
    'bookshelf_delete'                 => 'видалена полиця',
    'bookshelf_delete_notification'    => 'Полиця успішно видалена',

    // Revisions
    'revision_restore' => 'відновлено версію',
    'revision_delete' => 'видалена версія',
    'revision_delete_notification' => 'Версію успішно видалено',

    // Favourites
    'favourite_add_notification' => '":ім\'я" було додане до ваших улюлених',
    'favourite_remove_notification' => '":ім\'я" було видалено з ваших улюблених',

    // Watching
    'watch_update_level_notification' => 'Налаштування перегляду успішно оновлено',

    // Auth
    'auth_login' => 'ввійшли',
    'auth_register' => 'зареєстрований як новий користувач',
    'auth_password_reset_request' => 'запит на скидання пароля користувача',
    'auth_password_reset_update' => 'скинути пароль користувача',
    'mfa_setup_method' => 'налаштований метод MFA',
    'mfa_setup_method_notification' => 'Багатофакторний метод успішно налаштований',
    'mfa_remove_method' => 'видалив метод MFA',
    'mfa_remove_method_notification' => 'Багатофакторний метод успішно видалений',

    // Settings
    'settings_update' => 'оновлені налаштування',
    'settings_update_notification' => 'Налаштування успішно оновлено',
    'maintenance_action_run' => 'виконуються дії щодо обслуговування',

    // Webhooks
    'webhook_create' => 'створений вебхук',
    'webhook_create_notification' => 'Веб хук успішно створений',
    'webhook_update' => 'оновлений вебхук',
    'webhook_update_notification' => 'Вебхуки успішно оновлено',
    'webhook_delete' => 'видалений вебхук',
    'webhook_delete_notification' => 'Вебхуки успішно видалено',

    // Imports
    'import_create' => 'створений імпорт',
    'import_create_notification' => 'Імпорт успішно завантажений',
    'import_run' => 'оновлений імпорт',
    'import_run_notification' => 'Контент успішно імпортовано',
    'import_delete' => 'видалений імпорт',
    'import_delete_notification' => 'Імпорт успішно видалено',

    // Users
    'user_create' => 'створений користувач',
    'user_create_notification' => 'Користувач успішно створений',
    'user_update' => 'оновлений користувач',
    'user_update_notification' => 'Користувача було успішно оновлено',
    'user_delete' => 'вилучений користувач',
    'user_delete_notification' => 'Користувача успішно видалено',

    // API Tokens
    'api_token_create' => 'створений APi токен',
    'api_token_create_notification' => 'API токен успішно створений',
    'api_token_update' => 'оновлено API токен',
    'api_token_update_notification' => 'Токен API успішно оновлено',
    'api_token_delete' => 'видалено API токен',
    'api_token_delete_notification' => 'API-токен успішно видалено',

    // Roles
    'role_create' => 'створену роль',
    'role_create_notification' => 'Роль успішно створена',
    'role_update' => 'оновлена роль',
    'role_update_notification' => 'Роль успішно оновлена',
    'role_delete' => 'видалена роль',
    'role_delete_notification' => 'Роль успішно видалена',

    // Recycle Bin
    'recycle_bin_empty' => 'очищено кошик',
    'recycle_bin_restore' => 'відновлено із кошику',
    'recycle_bin_destroy' => 'видалено з кошика',

    // Comments
    'commented_on'                => 'прокоментував',
    'comment_create'              => 'додано коментар',
    'comment_update'              => 'оновлено коментар',
    'comment_delete'              => 'видалений коментар',

    // Sort Rules
    'sort_rule_create' => 'створене правило сортування',
    'sort_rule_create_notification' => 'Правило сортування успішно створено',
    'sort_rule_update' => 'оновлено правило сортування',
    'sort_rule_update_notification' => 'Правило сортування успішно оновлено',
    'sort_rule_delete' => 'видалено правило сортування',
    'sort_rule_delete_notification' => 'Правило сортування успішно видалено',

    // Other
    'permissions_update'          => 'оновив дозволи',
];
