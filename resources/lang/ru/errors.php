<?php

return [

    /**
     * Error text strings.
     */

    // Permissions
    'permission' => 'У вас нет доступа к запрашиваемой странице.',
    'permissionJson' => 'У вас нет разрешения для запрашиваемого действия.',

    // Auth
    'error_user_exists_different_creds' => 'Пользователь с электронной почтой: :email уже существует, но с другими учетными данными.',
    'email_already_confirmed' => 'Электронная почта уже подтверждена, попробуйте войти в систему.',
    'email_confirmation_invalid' => 'Этот токен подтверждения недействителен или уже используется. Повторите попытку регистрации.',
    'email_confirmation_expired' => 'Идентификатор подтверждения истек. Отправлено новое письмо с подтверждением.',
    'ldap_fail_anonymous' => 'Недопустимый доступ LDAP с использованием анонимной привязки',
    'ldap_fail_authed' => 'Не удалось получить доступ к LDAP, используя данные dn & password',
    'ldap_extension_not_installed' => 'LDAP расширения для PHP не установлено',
    'ldap_cannot_connect' => 'Не удается подключиться к серверу ldap, Не удалось выполнить начальное соединение',
    'social_no_action_defined' => 'Действие не определено',
    'social_account_in_use' => 'Этот :socialAccount аккаунт уже исопльзуется, Попробуйте войти с параматрами :socialAccount.',
    'social_account_email_in_use' => 'Электронный ящик :email уже используется. Если у вас уже есть учетная запись, вы можете подключить свою учетную запись :socialAccount из настроек своего профиля.',
    'social_account_existing' => 'Этот :socialAccount уже привязан к вашему профилю.',
    'social_account_already_used_existing' => 'Этот :socialAccount уже используется другим пользователем.',
    'social_account_not_used' => 'Эта :socialAccount учетная запись не связана ни с какими пользователями. Прикрепите его в настройках вашего профиля.',
    'social_account_register_instructions' => 'Если у вас еще нет учетной записи, вы можете зарегистрироваться, используя параметр :socialAccount.',
    'social_driver_not_found' => 'Драйдер для Соцсети не найден',
    'social_driver_not_configured' => 'Настройки вашего :socialAccount сконфигурированы неправильно.',

    // System
    'path_not_writable' => 'Невозможно загрузить файл по пути :filePath . Убедитесь что сервер доступен для записи.',
    'cannot_get_image_from_url' => 'Не удается получить изображение из :url',
    'cannot_create_thumbs' => 'Сервер не может создавать эскизы. Убедитесь, что у вас установлено расширение GD PHP.',
    'server_upload_limit' => 'Сервер не разрешает загрузку такого размера. Попробуйте уменьшить размер файла.',
    'image_upload_error' => 'Произошла ошибка при загрузке изображения.',

    // Attachments
    'attachment_page_mismatch' => 'Несоответствие страницы во время обновления вложения',

    // Pages
    'page_draft_autosave_fail' => 'Не удалось сохранить черновик. Перед сохранением этой страницы убедитесь, что у вас есть подключение к Интернету.',

    // Entities
    'entity_not_found' => 'Объект не найден',
    'book_not_found' => 'Книга не найдена',
    'page_not_found' => 'Страница не найдена',
    'chapter_not_found' => 'Глава не найдена',
    'selected_book_not_found' => 'Выбранная книга не найдена',
    'selected_book_chapter_not_found' => 'Выбранная книга или глава не найдена',
    'guests_cannot_save_drafts' => 'Гости не могут сохранить черновики',

    // Users
    'users_cannot_delete_only_admin' => 'Вы не можете удалить единственного администратора',
    'users_cannot_delete_guest' => 'Вы не можете удалить гостевого пользователя',

    // Roles
    'role_cannot_be_edited' => 'Невозможно отредактировать данную роль',
    'role_system_cannot_be_deleted' => 'Эта роль является системной и не может быть удалена',
    'role_registration_default_cannot_delete' => 'Эта роль не может быть удалена, так как она устанолена в качестве роли регистрации по-умолчанию',

    // Comments
    'comment_list' => 'При получении комментариев произошла ошибка.',
    'cannot_add_comment_to_draft' => 'Вы не можете добавлять комментарии к черновику.',
    'comment_add' => 'При добавлении / обновлении комментария произошла ошибка.',
    'comment_delete' => 'При удалении комментария произошла ошибка.',
    'empty_comment' => 'Нельзя добавить пустой комментарий.',

    // Error pages
    '404_page_not_found' => 'Старница не найдена',
    'sorry_page_not_found' => 'Извините, страница, которую вы искали, не найдена.',
    'return_home' => 'вернуться на главную страницу',
    'error_occurred' => 'Произошла ошибка',
    'app_down' => ':appName в данный момент не достпуно',
    'back_soon' => 'Скоро восстановится.',
];