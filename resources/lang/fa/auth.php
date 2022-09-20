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
    'password_hint' => 'باید بیش از 8 کاراکتر باشد',
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

    // Login auto-initiation
    'auto_init_starting' => 'تلاش برای ورود',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

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
    'email_confirm_success' => 'ایمیل شما تایید شد! اکنون باید بتوانید با استفاده از این آدرس ایمیل وارد شوید.',
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
    'user_invite_success_login' => 'رمز عبور تنظیم شده است، اکنون باید بتوانید با استفاده از رمز عبور تعیین شده خود وارد شوید تا به :appName دسترسی پیدا کنید!',

    // Multi-factor Authentication
    'mfa_setup' => 'تنظیم احراز هویت چند مرحله‌ای',
    'mfa_setup_desc' => 'تنظیم احراز هویت چند مرحله ای یک لایه امنیتی دیگر به حساب شما اضافه میکند.',
    'mfa_setup_configured' => 'هم اکنون تنظیم شده است.',
    'mfa_setup_reconfigure' => 'تنظیم مجدد',
    'mfa_setup_remove_confirmation' => 'از حذف احراز هویت چند مرحله ای اطمینان دارید؟',
    'mfa_setup_action' => 'تنظیم',
    'mfa_backup_codes_usage_limit_warning' => 'کمتر از 5 کد پشتیبان باقی مانده است، لطفاً قبل از تمام شدن کدها یک مجموعه جدید ایجاد و ذخیره کنید تا از قفل شدن حساب خود جلوگیری کنید.',
    'mfa_option_totp_title' => 'برنامه ی موبایل',
    'mfa_option_totp_desc' => 'برای استفاده از احراز هویت چند عاملی به یک برنامه موبایلی نیاز دارید که از TOTP پشتیبانی کند، مانند Google Authenticator، Authy یا Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'کدهای پشتیبان',
    'mfa_option_backup_codes_desc' => 'مجموعه ای از کدهای پشتیبان یکبار مصرف را ایمن ذخیره کنید که می توانید برای تأیید هویت خود وارد کنید.',
    'mfa_gen_confirm_and_enable' => 'تایید و فعال کنید',
    'mfa_gen_backup_codes_title' => 'راه اندازی کدهای پشتیبان',
    'mfa_gen_backup_codes_desc' => 'لیست کدهای زیر را در مکانی امن ذخیره کنید. هنگام دسترسی به سیستم، می توانید از یکی از کدها به عنوان مکانیزم احراز هویت دوم استفاده کنید.',
    'mfa_gen_backup_codes_download' => 'دانلود کدها',
    'mfa_gen_backup_codes_usage_warning' => 'هر کد فقط یک بار قابل استفاده است',
    'mfa_gen_totp_title' => 'راه اندازی اپلیکیشن موبایل',
    'mfa_gen_totp_desc' => 'برای استفاده از احراز هویت چند عاملی به یک برنامه موبایلی نیاز دارید که از TOTP پشتیبانی کند، مانند Google Authenticator، Authy یا Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'برای شروع، کد QR زیر را با استفاده از برنامه احراز هویت ترجیحی خود اسکن کنید.',
    'mfa_gen_totp_verify_setup' => 'تأیید تنظیمات',
    'mfa_gen_totp_verify_setup_desc' => 'با وارد کردن کدی که در برنامه احراز هویت شما ایجاد شده است، در کادر ورودی زیر، مطمئن شوید که همه کار می کنند:',
    'mfa_gen_totp_provide_code_here' => 'کد تولید شده برنامه خود را در اینجا ارائه دهید',
    'mfa_verify_access' => 'تأیید دسترسی',
    'mfa_verify_access_desc' => 'قبل از اینکه به شما اجازه دسترسی داده شود، حساب کاربری شما از شما می خواهد که هویت خود را از طریق یک سطح تأیید اضافی تأیید کنید. برای ادامه، با استفاده از یکی از روش های پیکربندی شده خود، تأیید کنید.',
    'mfa_verify_no_methods' => 'هیچ روشی پیکربندی نشده است',
    'mfa_verify_no_methods_desc' => 'هیچ روش احراز هویت چند عاملی برای حساب شما یافت نشد. قبل از دسترسی، باید حداقل یک روش را تنظیم کنید.',
    'mfa_verify_use_totp' => 'با استفاده از یک برنامه تلفن همراه تأیید کنید',
    'mfa_verify_use_backup_codes' => 'با استفاده از یک کد پشتیبان تأیید کنید',
    'mfa_verify_backup_code' => 'کد پشتیبان',
    'mfa_verify_backup_code_desc' => 'یکی از کدهای پشتیبان باقی مانده خود را در زیر وارد کنید:',
    'mfa_verify_backup_code_enter_here' => 'کد پشتیبان را در اینجا وارد کنید',
    'mfa_verify_totp_desc' => 'کد ایجاد شده با استفاده از برنامه تلفن همراه خود را در زیر وارد کنید:',
    'mfa_setup_login_notification' => 'روش چند عاملی پیکربندی شد، لطفاً اکنون دوباره با استفاده از روش پیکربندی شده وارد شوید.',
];
