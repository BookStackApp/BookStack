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
    'email_reset_not_requested' => 'If you did not request a password reset, no further action is required.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirm your email on :appName',
    'email_confirm_greeting' => 'Thanks for joining :appName!',
    'email_confirm_text' => 'Please confirm your email address by clicking the button below:',
    'email_confirm_action' => 'Confirm Email',
    'email_confirm_send_error' => 'Email confirmation required but the system could not send the email. Contact the admin to ensure email is set up correctly.',
    'email_confirm_success' => 'Your email has been confirmed!',
    'email_confirm_resent' => 'Confirmation email resent, Please check your inbox.',

    'email_not_confirmed' => 'Email Address Not Confirmed',
    'email_not_confirmed_text' => 'Your email address has not yet been confirmed.',
    'email_not_confirmed_click_link' => 'Please click the link in the email that was sent shortly after you registered.',
    'email_not_confirmed_resend' => 'If you cannot find the email you can re-send the confirmation email by submitting the form below.',
    'email_not_confirmed_resend_button' => 'Resend Confirmation Email',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];