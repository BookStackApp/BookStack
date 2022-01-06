<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'مشخصات وارد شده با اطلاعات ما سازگار نیست.',
    'throttle' => 'دفعات تلاش شما برای ورود بیش از حد مجاز است. لطفا پس از :seconds ثانیه مجددا تلاش فرمایید.',

    // Login & Register
    'sign_up' => 'ثبت نام',
    'log_in' => 'ورود',
    'log_in_with' => 'ورود با :socialDriver',
    'sign_up_with' => 'ثبت نام با :socialDriver',
    'logout' => 'خروج',

    'name' => 'نام',
    'username' => 'نام کاربری',
    'email' => 'پست الکترونیک',
    'password' => 'کلمه عبور',
    'password_confirm' => 'تایید کلمه عبور',
    'password_hint' => 'Must be at least 8 characters',
    'forgot_password' => 'کلمه عبور خود را فراموش کرده اید؟',
    'remember_me' => 'مرا به خاطر بسپار',
    'ldap_email_hint' => 'لطفا برای استفاده از این حساب کاربری پست الکترونیک وارد نمایید.',
    'create_account' => 'ایجاد حساب کاربری',
    'already_have_account' => 'قبلا ثبت نام نموده اید؟',
    'dont_have_account' => 'حساب کاربری ندارید؟',
    'social_login' => 'ورود از طریق شبکه اجتماعی',
    'social_registration' => 'ثبت نام از طریق شبکه اجتماعی',
    'social_registration_text' => 'با استفاده از سرویس دیگری ثبت نام نموده و وارد سیستم شوید.',

    'register_thanks' => 'از ثبت نام شما متشکریم!',
    'register_confirm' => 'لطفا پست الکترونیک خود را بررسی نموده و برای دسترسی به:appName دکمه تایید را کلیک نمایید.',
    'registrations_disabled' => 'ثبت نام در حال حاضر غیر فعال است',
    'registration_email_domain_invalid' => 'دامنه پست الکترونیک به این برنامه دسترسی ندارد',
    'register_success' => 'از ثبت نام شما سپاسگزاریم! شما اکنون ثبت نام کرده و وارد سیستم شده اید.',

    // Password Reset
    'reset_password' => 'بازنشانی کلمه عبور',
    'reset_password_send_instructions' => 'پست الکترونیک خود را در کادر زیر وارد نموده تا یک پیام حاوی لینک بازنشانی کلمه عبور دریافت نمایید.',
    'reset_password_send_button' => 'ارسال لینک بازنشانی',
    'reset_password_sent' => 'در صورت موجود بودن پست الکترونیک، یک لینک بازنشانی کلمه عبور برای شما ارسال خواهد شد.',
    'reset_password_success' => 'کلمه عبور شما با موفقیت بازنشانی شد.',
    'email_reset_subject' => 'بازنشانی کلمه عبور :appName',
    'email_reset_text' => 'شما این پیام را به علت درخواست بازنشانی کلمه عبور دریافت می نمایید.',
    'email_reset_not_requested' => 'در صورتی که درخواست بازنشانی کلمه عبور از سمت شما نمی باشد، نیاز به انجام هیچ فعالیتی ندارید.',

    // Email Confirmation
    'email_confirm_subject' => 'پست الکترونیک خود را در:appName تایید نمایید',
    'email_confirm_greeting' => 'برای پیوستن به :appName متشکریم!',
    'email_confirm_text' => 'لطفا با کلیک بر روی دکمه زیر پست الکترونیک خود را تایید نمایید:',
    'email_confirm_action' => 'تایید پست الکترونیک',
    'email_confirm_send_error' => 'تایید پست الکترونیک الزامی می باشد، اما سیستم قادر به ارسال پیام نمی باشد.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'پیام تایید پست الکترونیک مجدد ارسال گردید، لطفا صندوق ورودی خود را بررسی نمایید.',

    'email_not_confirmed' => 'پست الکترونیک تایید نشده است',
    'email_not_confirmed_text' => 'پست الکترونیک شما هنوز تایید نشده است.',
    'email_not_confirmed_click_link' => 'لطفا بر روی لینک موجود در پیامی که بلافاصله پس از ثبت نام ارسال شده است کلیک نمایید.',
    'email_not_confirmed_resend' => 'در صورتی که نمی توانید پیام را پیدا کنید، می توانید با ارسال فرم زیر، پیام تایید را مجدد دریافت نمایید.',
    'email_not_confirmed_resend_button' => 'ارسال مجدد تایید پست الکترونیک',

    // User Invite
    'user_invite_email_subject' => 'از شما برای پیوستن به :appName دعوت شده است!',
    'user_invite_email_greeting' => 'حساب کاربری برای شما در :appName ایجاد شده است.',
    'user_invite_email_text' => 'برای تنظیم کلمه عبور و دسترسی به حساب کاربری بر روی دکمه زیر کلیک نمایید:',
    'user_invite_email_action' => 'تنظیم کلمه عبور حساب‌کاربری',
    'user_invite_page_welcome' => 'به :appName خوش آمدید!',
    'user_invite_page_text' => 'برای نهایی کردن حساب کاربری خود در :appName و دسترسی به آن، می بایست یک کلمه عبور تنظیم نمایید.',
    'user_invite_page_confirm_button' => 'تایید کلمه عبور',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'تنظیم احراز هویت چند مرحله‌ای',
    'mfa_setup_desc' => 'تنظیم احراز هویت چند مرحله ای یک لایه امنیتی دیگر به حساب شما اضافه میکند.',
    'mfa_setup_configured' => 'هم اکنون تنظیم شده است.',
    'mfa_setup_reconfigure' => 'تنظیم مجدد',
    'mfa_setup_remove_confirmation' => 'از حذف احراز هویت چند مرحله ای اطمینان دارید؟',
    'mfa_setup_action' => 'تنظیم',
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
