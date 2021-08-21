<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Цей обліковий запис не знайдено.',
    'throttle' => 'Забагато спроб входу в систему. Будь ласка, спробуйте ще раз через :seconds секунд.',

    // Login & Register
    'sign_up' => 'Реєстрація',
    'log_in' => 'Увійти',
    'log_in_with' => 'Увійти через :socialDriver',
    'sign_up_with' => 'Зареєструватися через :socialDriver',
    'logout' => 'Вихід',

    'name' => 'Ім\'я',
    'username' => 'Логін',
    'email' => 'Адреса електронної пошти',
    'password' => 'Пароль',
    'password_confirm' => 'Підтвердження пароля',
    'password_hint' => 'Має бути більше ніж 7 символів',
    'forgot_password' => 'Забули пароль?',
    'remember_me' => 'Запам\'ятати мене',
    'ldap_email_hint' => 'Введіть email для цього облікового запису.',
    'create_account' => 'Створити обліковий запис',
    'already_have_account' => 'Вже є обліковий запис?',
    'dont_have_account' => 'Немає облікового запису?',
    'social_login' => 'Вхід через соціальну мережу',
    'social_registration' => 'Реєстрація через соціальну мережу',
    'social_registration_text' => 'Реєстрація і вхід через інший сервіс.',

    'register_thanks' => 'Дякуємо за реєстрацію!',
    'register_confirm' => 'Будь ласка, перевірте свою електронну пошту та натисніть кнопку підтвердження, щоб отримати доступ до :appName.',
    'registrations_disabled' => 'Реєстрацію вимкнено',
    'registration_email_domain_invalid' => 'Цей домен електронної пошти заборонений для реєстрації',
    'register_success' => 'Дякуємо за реєстрацію! Ви зареєстровані та ввійшли в систему.',


    // Password Reset
    'reset_password' => 'Скинути пароль',
    'reset_password_send_instructions' => 'Введіть адресу електронної пошти нижче, і вам буде надіслано електронне повідомлення з посиланням на зміну пароля.',
    'reset_password_send_button' => 'Надіслати посилання для скидання пароля',
    'reset_password_sent' => 'Посилання для скидання пароля буде надіслано на :email, якщо ця електронна адреса вказана в системі.',
    'reset_password_success' => 'Ваш пароль успішно скинуто.',
    'email_reset_subject' => 'Скинути ваш пароль :appName',
    'email_reset_text' => 'Ви отримали цей електронний лист, оскільки до нас надійшов запит на скидання пароля для вашого облікового запису.',
    'email_reset_not_requested' => 'Якщо ви не надсилали запит на скидання пароля, подальші дії не потрібні.',


    // Email Confirmation
    'email_confirm_subject' => 'Підтвердьте свою електронну пошту на :appName',
    'email_confirm_greeting' => 'Дякуємо, що приєдналися до :appName!',
    'email_confirm_text' => 'Будь ласка, підтвердьте свою адресу електронної пошти, натиснувши кнопку нижче:',
    'email_confirm_action' => 'Підтвердити Email',
    'email_confirm_send_error' => 'Необхідно підтвердження електронною поштою, але система не змогла надіслати електронний лист. Зверніться до адміністратора, щоб правильно налаштувати електронну пошту.',
    'email_confirm_success' => 'Ваш електронну адресу підтверджено!',
    'email_confirm_resent' => 'Лист з підтвердженням надіслано, перевірте свою пошту.',

    'email_not_confirmed' => 'Адресу електронної скриньки не підтверджено',
    'email_not_confirmed_text' => 'Ваша електронна адреса ще не підтверджена.',
    'email_not_confirmed_click_link' => 'Будь-ласка, натисніть на посилання в електронному листі, яке було надіслано після реєстрації.',
    'email_not_confirmed_resend' => 'Якщо ви не можете знайти електронний лист, ви можете повторно надіслати підтвердження електронною поштою, на формі нижче.',
    'email_not_confirmed_resend_button' => 'Повторне підтвердження електронної пошти',

    // User Invite
    'user_invite_email_subject' => 'Вас запросили приєднатися до :appName!',
    'user_invite_email_greeting' => 'Для вас створено обліковий запис на :appName.',
    'user_invite_email_text' => 'Натисніть кнопку нижче, щоб встановити пароль облікового запису та отримати доступ:',
    'user_invite_email_action' => 'Встановити пароль облікового запису',
    'user_invite_page_welcome' => 'Ласкаво просимо до :appName!',
    'user_invite_page_text' => 'Для завершення процесу створення облікового запису та отримання доступу вам потрібно задати пароль, який буде використовуватися для входу в :appName в майбутньому.',
    'user_invite_page_confirm_button' => 'Підтвердити пароль',
    'user_invite_success' => 'Встановлено пароль, тепер у вас є доступ до :appName!',

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