<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Учетная запись не найдена.',
    'throttle' => 'Слишком много попыток входа. Пожалуйста, повторите попытку через :seconds секунд.',

    // Login & Register
    'sign_up' => 'Регистрация',
    'log_in' => 'Вход',
    'log_in_with' => 'Вход с :socialDriver',
    'sign_up_with' => 'Регистрация с :socialDriver',
    'logout' => 'Выход',

    'name' => 'Имя',
    'username' => 'Логин',
    'email' => 'Адрес электронной почты',
    'password' => 'Пароль',
    'password_confirm' => 'Подтверждение пароля',
    'password_hint' => 'Минимум 8 символов',
    'forgot_password' => 'Забыли пароль?',
    'remember_me' => 'Запомнить меня',
    'ldap_email_hint' => 'Введите адрес электронной почты для этой учетной записи.',
    'create_account' => 'Создать аккаунт',
    'already_have_account' => 'Уже есть аккаунт?',
    'dont_have_account' => 'У вас нет аккаунта?',
    'social_login' => 'Вход через Соцсеть',
    'social_registration' => 'Регистрация через Соцсеть',
    'social_registration_text' => 'Регистрация и вход через другой сервис.',

    'register_thanks' => 'Благодарим за регистрацию!',
    'register_confirm' => 'Проверьте свою электронную почту и нажмите кнопку подтверждения для доступа к :appName.',
    'registrations_disabled' => 'Регистрация отключена',
    'registration_email_domain_invalid' => 'Данный домен электронной почты недоступен для регистрации',
    'register_success' => 'Спасибо за регистрацию! Регистрация и вход в систему выполнены.',


    // Password Reset
    'reset_password' => 'Сброс пароля',
    'reset_password_send_instructions' => 'Введите свой адрес электронной почты ниже, и вам будет отправлено письмо со ссылкой для сброса пароля.',
    'reset_password_send_button' => 'Сбросить пароль',
    'reset_password_sent_success' => 'Ссылка для сброса пароля была отправлена на :email.',
    'reset_password_success' => 'Ваш пароль был успешно сброшен.',
    'email_reset_subject' => 'Сброс пароля от :appName',
    'email_reset_text' => 'Вы получили это письмо, потому что запросили сброс пароля для вашей учетной записи.',
    'email_reset_not_requested' => 'Если вы не запрашивали сброса пароля, то никаких дополнительных действий не требуется.',


    // Email Confirmation
    'email_confirm_subject' => 'Подтвердите ваш почтовый адрес на :appName',
    'email_confirm_greeting' => 'Благодарим за участие :appName!',
    'email_confirm_text' => 'Пожалуйста, подтвердите свой адрес электронной почты нажав на кнопку ниже:',
    'email_confirm_action' => 'Подтвердить адрес электронной почты',
    'email_confirm_send_error' => 'Требуется подтверждение электронной почты, но система не может отправить письмо. Свяжитесь с администратором, чтобы убедиться, что адрес электронной почты настроен правильно.',
    'email_confirm_success' => 'Ваш адрес подтвержден!',
    'email_confirm_resent' => 'Письмо с подтверждение выслано снова. Пожалуйста, проверьте ваш почтовый ящик.',

    'email_not_confirmed' => 'Адрес электронной почты не подтвержден',
    'email_not_confirmed_text' => 'Ваш email адрес все еще не подтвержден.',
    'email_not_confirmed_click_link' => 'Пожалуйста, нажмите на ссылку в письме, которое было отправлено при регистрации.',
    'email_not_confirmed_resend' => 'Если вы не можете найти электронное письмо, вы можете снова отправить его с подтверждением по форме ниже.',
    'email_not_confirmed_resend_button' => 'Переотправить письмо с подтверждением',

    // User Invite
    'user_invite_email_subject' => 'Вас приглашают присоединиться к :appName!',
    'user_invite_email_greeting' => 'Для вас создан аккаунт в :appName.',
    'user_invite_email_text' => 'Нажмите кнопку ниже, чтобы задать пароль и получить доступ:',
    'user_invite_email_action' => 'Установить пароль для аккаунта',
    'user_invite_page_welcome' => 'Добро пожаловать в :appName!',
    'user_invite_page_text' => 'Завершите настройку аккаунта, установите пароль для дальнейшего входа в :appName.',
    'user_invite_page_confirm_button' => 'Подтвердите пароль',
    'user_invite_success' => 'Пароль установлен, теперь у вас есть доступ к :appName!'
];
