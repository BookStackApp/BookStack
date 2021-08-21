<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => '用户名或密码错误。',
    'throttle' => '您的登录次数过多，请在:seconds秒后重试。',

    // Login & Register
    'sign_up' => '注册',
    'log_in' => '登录',
    'log_in_with' => '以:socialDriver登录',
    'sign_up_with' => '通过 :socialDriver 账号登录',
    'logout' => '注销',

    'name' => '名称',
    'username' => '用户名',
    'email' => 'Email地址',
    'password' => '密码',
    'password_confirm' => '确认密码',
    'password_hint' => '必须超过7个字符',
    'forgot_password' => '忘记密码?',
    'remember_me' => '记住我',
    'ldap_email_hint' => '请输入用于此帐户的电子邮件。',
    'create_account' => '创建账户',
    'already_have_account' => '您已经有账号？',
    'dont_have_account' => '您还没有账号吗？',
    'social_login' => 'SNS登录',
    'social_registration' => '使用社交网站账号注册',
    'social_registration_text' => '使用其他服务注册并登录。',

    'register_thanks' => '注册完成！',
    'register_confirm' => '请点击查收您的Email，并点击确认。',
    'registrations_disabled' => '注册目前被禁用',
    'registration_email_domain_invalid' => '该Email域名无权访问此应用程序',
    'register_success' => '感谢您注册:appName，您现在已经登录。',


    // Password Reset
    'reset_password' => '重置密码',
    'reset_password_send_instructions' => '在下面输入您的Email地址，您将收到一封带有密码重置链接的邮件。',
    'reset_password_send_button' => '发送重置链接',
    'reset_password_sent' => '重置密码的链接将通过您的电子邮箱发送:email。',
    'reset_password_success' => '您的密码已成功重置。',
    'email_reset_subject' => '重置您的:appName密码',
    'email_reset_text' => '您收到此电子邮件是因为我们收到了您的帐户的密码重置请求。',
    'email_reset_not_requested' => '如果您没有要求重置密码，则不需要采取进一步的操作。',


    // Email Confirmation
    'email_confirm_subject' => '确认您在:appName的Email地址',
    'email_confirm_greeting' => '感谢您加入:appName！',
    'email_confirm_text' => '请点击下面的按钮确认您的Email地址：',
    'email_confirm_action' => '确认Email',
    'email_confirm_send_error' => '需要Email验证，但系统无法发送电子邮件，请联系网站管理员。',
    'email_confirm_success' => '您的Email地址已成功验证！',
    'email_confirm_resent' => '验证邮件已重新发送，请检查收件箱。',

    'email_not_confirmed' => 'Email地址未验证',
    'email_not_confirmed_text' => '您的电子邮件地址尚未确认。',
    'email_not_confirmed_click_link' => '请检查注册时收到的电子邮件，然后点击确认链接。',
    'email_not_confirmed_resend' => '如果找不到电子邮件，请通过下面的表单重新发送确认Email。',
    'email_not_confirmed_resend_button' => '重新发送确认Email',

    // User Invite
    'user_invite_email_subject' => '您已受邀加入 :appName！',
    'user_invite_email_greeting' => ' :appName 已为您创建了一个帐户。',
    'user_invite_email_text' => '点击下面的按钮以设置帐户密码并获得访问权限：',
    'user_invite_email_action' => '设置帐号密码',
    'user_invite_page_welcome' => '欢迎来到 :appName！',
    'user_invite_page_text' => '要完成您的帐户并获得访问权限，您需要设置一个密码，该密码将在以后访问时用于登录 :appName。',
    'user_invite_page_confirm_button' => '确认密码',
    'user_invite_success' => '已设置密码，您现在可以访问 :appName！',

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