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
    'username' => 'Потребител',
    'email' => 'Имейл',
    'password' => 'Парола',
    'password_confirm' => 'Потвърди паролата',
    'password_hint' => 'Трябва да бъде поне 7 символа',
    'forgot_password' => 'Забравена парола?',
    'remember_me' => 'Запомни ме',
    'ldap_email_hint' => 'Моля въведете емейл, който да използвате за дадения акаунт.',
    'create_account' => 'Създай Акаунт',
    'already_have_account' => 'Вече имате акаунт?',
    'dont_have_account' => 'Нямате акаунт?',
    'social_login' => 'Влизане по друг начин',
    'social_registration' => 'Регистрация по друг начин',
    'social_registration_text' => 'Регистрация и влизане използвайки друг начин.',

    'register_thanks' => 'Благодарим Ви за регистрацията!',
    'register_confirm' => 'Моля проверете своя емейл и натиснете върху бутона за потвърждение, за да влезете в :appName.',
    'registrations_disabled' => 'Регистрациите към момента са забранени',
    'registration_email_domain_invalid' => 'Този емейл домейн към момента няма достъп до приложението',
    'register_success' => 'Благодарим Ви за регистрацията! В момента сте регистриран и сте вписани в приложението.',


    // Password Reset
    'reset_password' => 'Нулиране на паролата',
    'reset_password_send_instructions' => 'Въведете емейла си и ще ви бъде изпратен емейл с линк за нулиране на паролата.',
    'reset_password_send_button' => 'Изпращане на линк за нулиране',
    'reset_password_sent' => 'Линк за нулиране на паролата ще Ви бъде изпратен на :email, ако емейлът Ви бъде открит в системата.',
    'reset_password_success' => 'Паролата Ви е променена успешно.',
    'email_reset_subject' => 'Възстановете паролата си за :appName',
    'email_reset_text' => 'Вие получихте този емейл, защото поискахте вашата парола да бъде занулена.',
    'email_reset_not_requested' => 'Ако Вие не сте поискали зануляването на паролата, няма нужда от други действия.',


    // Email Confirmation
    'email_confirm_subject' => 'Потвърди емейла си за :appName',
    'email_confirm_greeting' => 'Благодарим Ви, че се присъединихте към :appName!',
    'email_confirm_text' => 'Моля, потвърдете вашия имейл адрес, като следвате връзката по-долу:',
    'email_confirm_action' => 'Потвърдете имейл',
    'email_confirm_send_error' => 'Нужно ви е потвърждение чрез емейл, но системата не успя да го изпрати. Моля свържете се с администратора, за да проверите дали вашият емейл адрес е конфигуриран правилно.',
    'email_confirm_success' => 'Адресът на електронната ви поща е потвърден!',
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
    'user_invite_success' => 'Паролата е потвърдена и вече имате достъп до :appName!'
];