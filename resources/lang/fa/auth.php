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
    'password_hint' => 'باید بیش از 7 کاراکتر باشد',
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
    'reset_password_send_instructions' => 'Enter your email below and you will be sent an email with a password reset link.',
    'reset_password_send_button' => 'Send Reset Link',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
    'reset_password_success' => 'Your password has been successfully reset.',
    'email_reset_subject' => 'Reset your :appName password',
    'email_reset_text' => 'You are receiving this email because we received a password reset request for your account.',
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