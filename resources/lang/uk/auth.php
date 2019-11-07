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
    'reset_password_sent_success' => 'Посилання для скидання пароля було надіслано на :email.',
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
    'user_invite_success' => 'Встановлено пароль, тепер у вас є доступ до :appName!'
];