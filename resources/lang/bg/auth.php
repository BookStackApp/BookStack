<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Въведените удостоверителни данни не съвпадат с нашите записи.',
    'throttle' => 'Твърде много опити за влизане. Опитайте пак след :seconds секунди.',

    // Login & Register
    'sign_up' => 'Регистриране',
    'log_in' => 'Влизане',
    'log_in_with' => 'Влизане с :socialDriver',
    'sign_up_with' => 'Регистриране с :socialDriver',
    'logout' => 'Изход',

    'name' => 'Име',
    'username' => 'Потребителско име',
    'email' => 'Имейл',
    'password' => 'Парола',
    'password_confirm' => 'Потвърди паролата',
    'password_hint' => 'Трябва да бъде поне 8 символа',
    'forgot_password' => 'Забравена парола?',
    'remember_me' => 'Запомни ме',
    'ldap_email_hint' => 'Моля въведете емейл, който да използвате за дадения профил.',
    'create_account' => 'Създай Акаунт',
    'already_have_account' => 'Вече имате профил?',
    'dont_have_account' => 'Нямате акаунт?',
    'social_login' => 'Влизане по друг начин',
    'social_registration' => 'Регистрация по друг начин',
    'social_registration_text' => 'Регистрация и вписване чрез друга услуга.',

    'register_thanks' => 'Благодарим Ви за регистрацията!',
    'register_confirm' => 'Моля, провери своя имейл адрес и натисни бутона за потвърждение, за да достъпиш :appName.',
    'registrations_disabled' => 'Регистрациите към момента са забранени',
    'registration_email_domain_invalid' => 'Този емейл домейн към момента няма достъп до приложението',
    'register_success' => 'Благодарим Ви за регистрацията! В момента сте регистриран и сте вписани в приложението.',

    // Password Reset
    'reset_password' => 'Нулиране на паролата',
    'reset_password_send_instructions' => 'Въведете емейла си и ще ви бъде изпратен емейл с линк за нулиране на паролата.',
    'reset_password_send_button' => 'Изпращане на линк за възстановяване',
    'reset_password_sent' => 'Линк за нулиране на паролата ще Ви бъде изпратен на :email, ако емейлът Ви бъде открит в системата.',
    'reset_password_success' => 'Паролата Ви е променена успешно.',
    'email_reset_subject' => 'Възстанови паролата си за :appName',
    'email_reset_text' => 'Вие получихте този имейл, защото поискахте Вашата парола да бъде възстановена.',
    'email_reset_not_requested' => 'Ако Вие не сте поискали зануляването на паролата, няма нужда от други действия.',

    // Email Confirmation
    'email_confirm_subject' => 'Потвърди емейла си за :appName',
    'email_confirm_greeting' => 'Благодарим Ви, че се присъединихте към :appName!',
    'email_confirm_text' => 'Моля, потвърдете вашия имейл адрес, като следвате връзката по-долу:',
    'email_confirm_action' => 'Потвърдете имейл',
    'email_confirm_send_error' => 'Нужно ви е потвърждение чрез емейл, но системата не успя да го изпрати. Моля свържете се с администратора, за да проверите дали вашият емейл адрес е конфигуриран правилно.',
    'email_confirm_success' => 'Имейлът ти е потвърден! Вече би трябвало да можеш да се впишеш с този имейл адрес.',
    'email_confirm_resent' => 'Беше изпратен имейл с потвърждение, Моля, проверете кутията си.',

    'email_not_confirmed' => 'Имейл адресът не е потвърден',
    'email_not_confirmed_text' => 'Вашият емейл адрес все още не е потвърден.',
    'email_not_confirmed_click_link' => 'Моля да последвате линка, който ви беше изпратен непосредствено след регистрацията.',
    'email_not_confirmed_resend' => 'Ако не откривате писмото, може да го изпратите отново като попълните формуляра по-долу.',
    'email_not_confirmed_resend_button' => 'Изпрати отново емейла за потвърждение',

    // User Invite
    'user_invite_email_subject' => 'Вие бяхте поканен да се присъедините към :appName!',
    'user_invite_email_greeting' => 'Беше създаден акаунт за Вас във :appName.',
    'user_invite_email_text' => 'Натисните бутона по-долу за да определите парола и да получите достъп:',
    'user_invite_email_action' => 'Парола на акаунта',
    'user_invite_page_welcome' => 'Добре дошли в :appName!',
    'user_invite_page_text' => 'За да финализирате вашият акаунт и да получите достъп трябва да определите парола, която да бъде използвана за следващия влизания в :appName.',
    'user_invite_page_confirm_button' => 'Потвърди паролата',
    'user_invite_success_login' => 'Паролата е настроена, вече можеш да се впишеш с новата парола, за да достъпиш :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Настрой многофакторно удостоверяване',
    'mfa_setup_desc' => 'Настрой многофакторно удостверяване като втори слой сигурност на твоя профил.',
    'mfa_setup_configured' => 'Вече е конфигурирано',
    'mfa_setup_reconfigure' => 'Преконфигурирай',
    'mfa_setup_remove_confirmation' => 'Сигурен ли си, че желаеш да премахнеш този метод за многофакторно удостоверяване?',
    'mfa_setup_action' => 'Настройка',
    'mfa_backup_codes_usage_limit_warning' => 'Имаш по-малко от 5 останали резервни кода. Генерирай и съхрани нов набор, преди тези да са свършили, за да избегнеш да останеш без достъп до профила си.',
    'mfa_option_totp_title' => 'Мобилно приложение',
    'mfa_option_totp_desc' => 'За да използваш многофакторно удостоверяване, ще ти трябва мобилно приложение, което поддържа временни еднократни пароли (TOTP), като например Google Authenticator, Authy или Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Резервни кодове',
    'mfa_option_backup_codes_desc' => 'Запази на сигурно място набор от еднократни резервни кодове, с които можеш да устовериш идентичността си.',
    'mfa_gen_confirm_and_enable' => 'Потвърди и включи',
    'mfa_gen_backup_codes_title' => 'Настройка на резервни кодове',
    'mfa_gen_backup_codes_desc' => 'Запази този лист с кодове на сигурно място. Когато достъпваш системата, ще можеш да използваш един от тези кодове като вторичен механизъм за удостоверяване.',
    'mfa_gen_backup_codes_download' => 'Изтегли кодовете',
    'mfa_gen_backup_codes_usage_warning' => 'Всеки код може да бъде използван само веднъж',
    'mfa_gen_totp_title' => 'Настройка на мобилно приложение',
    'mfa_gen_totp_desc' => 'За да използваш многофакторно удостоверяване, ще ти трябва мобилно приложение, което поддържа временни еднократни пароли (TOTP), като например Google Authenticator, Authy или Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'За да започнеш, сканирай QR кода отдолу с предпочитано от теб приложение.',
    'mfa_gen_totp_verify_setup' => 'Потвърди настройката',
    'mfa_gen_totp_verify_setup_desc' => 'Потвърди, че всичко работи, като в кутията отдолу въведеш код, генериран от твоето приложение за удостоверяване:',
    'mfa_gen_totp_provide_code_here' => 'Въведи тук кода, генериран от мобилното ти приложение',
    'mfa_verify_access' => 'Потвърди достъпа',
    'mfa_verify_access_desc' => 'Твоят потребителски профил изисква да потвърдиш идентичността си чрез допълнително ниво проверка преди да получиш достъп. Потвърди чрез един от конфигурираните методи, за да продължиш.',
    'mfa_verify_no_methods' => 'Няма конфигурирани методи',
    'mfa_verify_no_methods_desc' => 'Няма намерени методи за многофакторно удостоверяване за твоя профил. Ще трябва да настроиш поне един метод, преди да получиш достъп.',
    'mfa_verify_use_totp' => 'Потвърди чрез мобилно приложение',
    'mfa_verify_use_backup_codes' => 'Потвърди чрез резервен код',
    'mfa_verify_backup_code' => 'Резервен код',
    'mfa_verify_backup_code_desc' => 'Въведи един от останалите ти резервни кодове отдолу:',
    'mfa_verify_backup_code_enter_here' => 'Въведи резервен код тук',
    'mfa_verify_totp_desc' => 'Въведи кода, генериран от мобилното ти приложение, отдолу:',
    'mfa_setup_login_notification' => 'Многофакторният метод е конфигуриран, моля да се впишете отново чрез конфигурирания метод.',
];
