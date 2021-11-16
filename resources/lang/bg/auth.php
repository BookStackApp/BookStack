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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

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
