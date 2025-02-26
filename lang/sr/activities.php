<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'креирана страница',
    'page_create_notification'    => 'Страница је успешно креирана',
    'page_update'                 => 'ажурирана страница',
    'page_update_notification'    => 'Страница је успешно ажурирана',
    'page_delete'                 => 'обрисана страница',
    'page_delete_notification'    => 'Страница је успешно обрисана',
    'page_restore'                => 'обновљена страна',
    'page_restore_notification'   => 'Страница је успешно обновљена',
    'page_move'                   => 'премештена страна',
    'page_move_notification'      => 'Страница је успешно померена',

    // Chapters
    'chapter_create'              => 'креирано поглавље',
    'chapter_create_notification' => 'Поглавље је успешно креирано',
    'chapter_update'              => 'ажурирано поглавље',
    'chapter_update_notification' => 'Поглавље је успешно обновљено',
    'chapter_delete'              => 'обрисано поглавље',
    'chapter_delete_notification' => 'Поглавље је успешно обрисано',
    'chapter_move'                => 'премештено поглавље',
    'chapter_move_notification' => 'Поглавље успешно премештено',

    // Books
    'book_create'                 => 'креирана књига',
    'book_create_notification'    => 'Књига је успешно креирана',
    'book_create_from_chapter'              => 'конвертовано поглавље у књигу',
    'book_create_from_chapter_notification' => 'Поглавље успешно конвертовано у књигу',
    'book_update'                 => 'књига је ажурирана',
    'book_update_notification'    => 'Књига је успешно ажурирана',
    'book_delete'                 => 'књига је обрисана',
    'book_delete_notification'    => 'Књига је успешно обрисана',
    'book_sort'                   => 'сортирана књига',
    'book_sort_notification'      => 'Књига је успешно поновно сортирана',

    // Bookshelves
    'bookshelf_create'            => 'креирана полица',
    'bookshelf_create_notification'    => 'Полица је успешно креирана',
    'bookshelf_create_from_book'    => 'конвертована је књига у полицу',
    'bookshelf_create_from_book_notification'    => 'Књига је успешно конвертована у полицу',
    'bookshelf_update'                 => 'ажурирана полица',
    'bookshelf_update_notification'    => 'Полица је успешно ажурирана',
    'bookshelf_delete'                 => 'обрисана је полица',
    'bookshelf_delete_notification'    => 'Полица је успешно обрисана',

    // Revisions
    'revision_restore' => 'повраћена ревизија',
    'revision_delete' => 'обрисана ревизија',
    'revision_delete_notification' => 'Ревизија је успешно обрисана',

    // Favourites
    'favourite_add_notification' => '":name" је додато као ваше омиљено',
    'favourite_remove_notification' => '":name" је уклоњено као ваше омиљено',

    // Watching
    'watch_update_level_notification' => 'Подешавање праћених предмета је успешно ажурирано',

    // Auth
    'auth_login' => 'пријављени',
    'auth_register' => 'регистрован као нови корисник',
    'auth_password_reset_request' => 'захтевано ресетовање корисничке лозинке',
    'auth_password_reset_update' => 'ресетуј корисничку лозинку',
    'mfa_setup_method' => 'конфигурисан МФА метод',
    'mfa_setup_method_notification' => 'Вишефакторска метода је успешно конфигурисана',
    'mfa_remove_method' => 'уклоњен МФА метод',
    'mfa_remove_method_notification' => 'Вишефакторска метода је успешно уклоњена',

    // Settings
    'settings_update' => 'ажурирана подешавања',
    'settings_update_notification' => 'Подешавања су успешно ажурирана',
    'maintenance_action_run' => 'покренуо акцију одржавања',

    // Webhooks
    'webhook_create' => 'креиран вебхоок',
    'webhook_create_notification' => 'Вебхоок је успешно креиран',
    'webhook_update' => 'ажуриран вебхоок',
    'webhook_update_notification' => 'Вебхоок је успешно ажуриран',
    'webhook_delete' => 'обрисан вебхоок',
    'webhook_delete_notification' => 'Вебхоок је успешно обрисан',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'креирао корисника',
    'user_create_notification' => 'Корисник је успешно креиран',
    'user_update' => 'ажуриран корисник',
    'user_update_notification' => 'Корисник је успешно ажуриран',
    'user_delete' => 'избрисан корисника',
    'user_delete_notification' => 'Корисник је успешно уклоњен',

    // API Tokens
    'api_token_create' => 'креирао апи токен',
    'api_token_create_notification' => 'АПИ токен је успешно креиран',
    'api_token_update' => 'ажуриран апи токен',
    'api_token_update_notification' => 'АПИ токен је успешно ажуриран',
    'api_token_delete' => 'обрисан апи токен',
    'api_token_delete_notification' => 'АПИ токен је успешно избрисан',

    // Roles
    'role_create' => 'створена улога',
    'role_create_notification' => 'Улога је успешно направљена',
    'role_update' => 'ажурирана улога',
    'role_update_notification' => 'Улога је успешно ажурирана',
    'role_delete' => 'обрисана улога',
    'role_delete_notification' => 'Улога је успешно избрисана',

    // Recycle Bin
    'recycle_bin_empty' => 'испражњена корпа за отпатке',
    'recycle_bin_restore' => 'враћен из корпе за отпатке',
    'recycle_bin_destroy' => 'уклоњен из корпе за отпатке',

    // Comments
    'commented_on'                => 'коментарисао',
    'comment_create'              => 'додао/ла коментар',
    'comment_update'              => 'ажуриран коментар',
    'comment_delete'              => 'обрисан коментар',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'ажуриране дозволе',
];
