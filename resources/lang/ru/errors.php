<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'У вас нет доступа к запрашиваемой странице.',
    'permissionJson' => 'У вас нет разрешения для запрашиваемого действия.',

    // Auth
    'error_user_exists_different_creds' => 'Пользователь с электронной почтой: :email уже существует, но с другими учетными данными.',
    'email_already_confirmed' => 'Электронная почта уже подтверждена, попробуйте войти в систему.',
    'email_confirmation_invalid' => 'Этот токен подтверждения недействителен или уже используется. Повторите попытку регистрации.',
    'email_confirmation_expired' => 'Истек срок действия токена. Отправлено новое письмо с подтверждением.',
    'ldap_fail_anonymous' => 'Недопустимый доступ LDAP с использованием анонимной привязки',
    'ldap_fail_authed' => 'Не удалось получить доступ к LDAP, используя данные dn & password',
    'ldap_extension_not_installed' => 'LDAP расширения для PHP не установлено',
    'ldap_cannot_connect' => 'Не удается подключиться к серверу ldap, не удалось выполнить начальное соединение',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'saml_email_exists' => 'Registration unsuccessful since a user already exists with email address ":email"',
    'social_no_action_defined' => 'Действие не определено',
    'social_login_bad_response' => "При попытке входа с :socialAccount произошла ошибка: \\n:error",
    'social_account_in_use' => 'Этот :socialAccount аккаунт уже исопльзуется, попробуйте войти с параметрами :socialAccount.',
    'social_account_email_in_use' => 'Электронный ящик :email уже используется. Если у вас уже есть учетная запись, вы можете подключить свою учетную запись :socialAccount из настроек своего профиля.',
    'social_account_existing' => 'Этот :socialAccount уже привязан к вашему профилю.',
    'social_account_already_used_existing' => 'Этот :socialAccount уже используется другим пользователем.',
    'social_account_not_used' => 'Этот :socialAccount не связан ни с какими пользователями. Прикрепите его в настройках вашего профиля.',
    'social_account_register_instructions' => 'Если у вас еще нет учетной записи, вы можете зарегистрироваться, используя параметр :socialAccount.',
    'social_driver_not_found' => 'Драйвер для Соцсети не найден',
    'social_driver_not_configured' => 'Настройки вашего :socialAccount заданы неправильно.',
    'invite_token_expired' => 'Срок действия приглашения истек. Вместо этого вы можете попытаться сбросить пароль своей учетной записи.',

    // System
    'path_not_writable' => 'Невозможно загрузить файл по пути :filePath . Убедитесь что сервер доступен для записи.',
    'cannot_get_image_from_url' => 'Не удается получить изображение из :url',
    'cannot_create_thumbs' => 'Сервер не может создавать эскизы. Убедитесь, что у вас установлено расширение GD PHP.',
    'server_upload_limit' => 'Сервер не разрешает загрузку такого размера. Попробуйте уменьшить размер файла.',
    'uploaded'  => 'Сервер не позволяет загружать файлы такого размера. Пожалуйста, попробуйте файл меньше.',
    'image_upload_error' => 'Произошла ошибка при загрузке изображения.',
    'image_upload_type_error' => 'Неправильный тип загружаемого изображения',
    'file_upload_timeout' => 'Выгрузка файла закончилась.',

    // Attachments
    'attachment_page_mismatch' => 'Несоответствие страницы во время обновления вложения',
    'attachment_not_found' => 'Вложение не найдено',

    // Pages
    'page_draft_autosave_fail' => 'Не удалось сохранить черновик. Перед сохранением этой страницы убедитесь, что у вас есть подключение к Интернету.',
    'page_custom_home_deletion' => 'Нельзя удалить страницу, установленную вместо главной страницы',

    // Entities
    'entity_not_found' => 'Объект не найден',
    'bookshelf_not_found' => 'Полка не найдена',
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
    'role_registration_default_cannot_delete' => 'Эта роль не может быть удалена, так как она устанолена в качестве роли по умолчанию',
    'role_cannot_remove_only_admin' => 'Этот пользователь единственный с правами администратора. Назначьте роль администратора другому пользователю, прежде чем удалить этого.',

    // Comments
    'comment_list' => 'При получении комментариев произошла ошибка.',
    'cannot_add_comment_to_draft' => 'Вы не можете добавлять комментарии к черновику.',
    'comment_add' => 'При добавлении / обновлении комментария произошла ошибка.',
    'comment_delete' => 'При удалении комментария произошла ошибка.',
    'empty_comment' => 'Нельзя добавить пустой комментарий.',

    // Error pages
    '404_page_not_found' => 'Страница не найдена',
    'sorry_page_not_found' => 'Извините, страница, которую вы искали, не найдена.',
    'return_home' => 'вернуться на главную страницу',
    'error_occurred' => 'Произошла ошибка',
    'app_down' => ':appName в данный момент не достпуно',
    'back_soon' => 'Скоро восстановится.',

];
