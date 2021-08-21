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
    'reset_password_sent' => 'Ссылка для сброса пароля будет выслана на :email, если этот адрес находится в системе.',
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
    'user_invite_success' => 'Пароль установлен, теперь у вас есть доступ к :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];