<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'создал страницу',
    'page_create_notification'    => 'Страница успешно создана',
    'page_update'                 => 'обновил страницу',
    'page_update_notification'    => 'Страница успешно обновлена',
    'page_delete'                 => 'удалил страницу',
    'page_delete_notification'    => 'Страница успешно удалена',
    'page_restore'                => 'восстановил страницу',
    'page_restore_notification'   => 'Страница успешно восстановлена',
    'page_move'                   => 'переместил страницу',
    'page_move_notification'      => 'Страница успешно перемещена',

    // Chapters
    'chapter_create'              => 'создал главу',
    'chapter_create_notification' => 'Глава успешно создана',
    'chapter_update'              => 'обновил главу',
    'chapter_update_notification' => 'Глава успешно обновлена',
    'chapter_delete'              => 'удалил главу',
    'chapter_delete_notification' => 'Глава успешно удалена',
    'chapter_move'                => 'переместил главу',
    'chapter_move_notification' => 'Глава успешно перемещена',

    // Books
    'book_create'                 => 'создал книгу',
    'book_create_notification'    => 'Книга успешно создана',
    'book_create_from_chapter'              => 'преобразовал главу в книгу',
    'book_create_from_chapter_notification' => 'Глава успешно преобразована в книгу',
    'book_update'                 => 'обновил книгу',
    'book_update_notification'    => 'Книга успешно обновлена',
    'book_delete'                 => 'удалил книгу',
    'book_delete_notification'    => 'Книга успешно удалена',
    'book_sort'                   => 'отсортировал книгу',
    'book_sort_notification'      => 'Книга успешно отсортирована',

    // Bookshelves
    'bookshelf_create'            => 'создал полку',
    'bookshelf_create_notification'    => 'Полка успешно создана',
    'bookshelf_create_from_book'    => 'преобразовал книгу в полку',
    'bookshelf_create_from_book_notification'    => 'Книга успешно преобразована в полку',
    'bookshelf_update'                 => 'обновил полку',
    'bookshelf_update_notification'    => 'Полка успешно обновлена',
    'bookshelf_delete'                 => 'удалил полку',
    'bookshelf_delete_notification'    => 'Полка успешно удалена',

    // Revisions
    'revision_restore' => 'restored revision',
    'revision_delete' => 'deleted revision',
    'revision_delete_notification' => 'Версия успешно удалена',

    // Favourites
    'favourite_add_notification' => '":name" добавлено в избранное',
    'favourite_remove_notification' => '":name" удалено из избранного',

    // Auth
    'auth_login' => 'logged in',
    'auth_register' => 'зарегистрировался как новый пользователь',
    'auth_password_reset_request' => 'requested user password reset',
    'auth_password_reset_update' => 'reset user password',
    'mfa_setup_method' => 'configured MFA method',
    'mfa_setup_method_notification' => 'Двухфакторный метод авторизации успешно настроен',
    'mfa_remove_method' => 'removed MFA method',
    'mfa_remove_method_notification' => 'Двухфакторный метод авторизации успешно удален',

    // Settings
    'settings_update' => 'updated settings',
    'settings_update_notification' => 'Настройки успешно обновлены',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'создал вебхук',
    'webhook_create_notification' => 'Вебхук успешно создан',
    'webhook_update' => 'обновил вебхук',
    'webhook_update_notification' => 'Вебхук успешно обновлен',
    'webhook_delete' => 'удалил вебхук',
    'webhook_delete_notification' => 'Вебхук успешно удален',

    // Users
    'user_create' => 'created user',
    'user_create_notification' => 'Пользователь успешно создан',
    'user_update' => 'updated user',
    'user_update_notification' => 'Пользователь успешно обновлен',
    'user_delete' => 'deleted user',
    'user_delete_notification' => 'Пользователь успешно удален',

    // API Tokens
    'api_token_create' => 'created api token',
    'api_token_create_notification' => 'API токен успешно создан',
    'api_token_update' => 'updated api token',
    'api_token_update_notification' => 'API токен успешно обновлен',
    'api_token_delete' => 'deleted api token',
    'api_token_delete_notification' => 'API токен успешно удален',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Роль успешно создана',
    'role_update' => 'updated role',
    'role_update_notification' => 'Роль успешно обновлена',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Роль успешно удалена',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Other
    'commented_on'                => 'прокомментировал',
    'permissions_update'          => 'обновил разрешения',
];
