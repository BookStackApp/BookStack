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
    'log_in_with' => '使用 :socialDriver 账户登录',
    'sign_up_with' => '通过 :socialDriver 账号登录',
    'logout' => '注销',

    'name' => '名称',
    'username' => '用户名',
    'email' => 'Email地址',
    'password' => '密码',
    'password_confirm' => '确认密码',
    'password_hint' => '必须至少有 8 个字符',
    'forgot_password' => '忘记密码?',
    'remember_me' => '记住我',
    'ldap_email_hint' => '请输入用于此帐户的电子邮件。',
    'create_account' => '创建账户',
    'already_have_account' => '已经有账号了？',
    'dont_have_account' => '您还没有账号吗？',
    'social_login' => 'SNS登录',
    'social_registration' => '使用社交网站账号注册',
    'social_registration_text' => '使用其他服务注册并登录。',

    'register_thanks' => '注册完成！',
    'register_confirm' => '请点击查收您的Email，并点击确认。',
    'registrations_disabled' => '注册目前被禁用',
    'registration_email_domain_invalid' => '该Email域名无权访问此应用程序',
    'register_success' => '感谢您注册:appName，您现在已经登录。',

    // Login auto-initiation
    'auto_init_starting' => 'Attempting Login',
    'auto_init_starting_desc' => 'We\'re contacting your authentication system to start the login process. If there\'s no progress after 5 seconds you can try clicking the link below.',
    'auto_init_start_link' => 'Proceed with authentication',

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
    'email_confirm_success' => '您已成功验证电子邮件地址！您现在可以使用此电子邮件地址登录。',
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
    'user_invite_success_login' => '密码已设置，您现在可以使用您设置的密码登录 :appName!',

    // Multi-factor Authentication
    'mfa_setup' => '设置多重身份认证',
    'mfa_setup_desc' => '设置多重身份认证能增加您账户的安全性。',
    'mfa_setup_configured' => '已经设置过了',
    'mfa_setup_reconfigure' => '重新配置',
    'mfa_setup_remove_confirmation' => '您确定想要移除多重身份认证吗？',
    'mfa_setup_action' => '设置',
    'mfa_backup_codes_usage_limit_warning' => '您剩余的备用认证码少于 5 个，请在用完认证码之前生成并保存新的认证码，以防止您的帐户被锁定。',
    'mfa_option_totp_title' => '移动设备 App',
    'mfa_option_totp_desc' => '要使用多重身份认证功能，您需要一个支持 TOTP（基于时间的一次性密码算法） 的移动设备 App，如谷歌身份验证器（Google Authenticator）、Authy 或微软身份验证器（Microsoft Authenticator）。',
    'mfa_option_backup_codes_title' => '备用认证码',
    'mfa_option_backup_codes_desc' => '请安全地保存这些一次性使用的备用认证码，您可以输入这些认证码来验证您的身份。',
    'mfa_gen_confirm_and_enable' => '确认并启用',
    'mfa_gen_backup_codes_title' => '备用认证码设置',
    'mfa_gen_backup_codes_desc' => '将下面的认证码存放在一个安全的地方。访问系统时，您可以使用其中的一个验证码进行二次认证。',
    'mfa_gen_backup_codes_download' => '下载认证码',
    'mfa_gen_backup_codes_usage_warning' => '每个认证码只能使用一次',
    'mfa_gen_totp_title' => '移动设备 App',
    'mfa_gen_totp_desc' => '要使用多重身份认证功能，您需要一个支持 TOTP（基于时间的一次性密码算法） 的移动设备 App，如谷歌身份验证器（Google Authenticator）、Authy 或微软身份验证器（Microsoft Authenticator）。',
    'mfa_gen_totp_scan' => '要开始操作，请使用您的身份验证 App 扫描下面的二维码。',
    'mfa_gen_totp_verify_setup' => '验证设置',
    'mfa_gen_totp_verify_setup_desc' => '请在下面的框中输入您在身份验证 App 中生成的认证码来验证一切是否正常：',
    'mfa_gen_totp_provide_code_here' => '在此输入您的 App 生成的认证码',
    'mfa_verify_access' => '认证访问',
    'mfa_verify_access_desc' => '您的账户要求您在访问前通过额外的验证确认您的身份。使用您设置的认证方法认证以继续。',
    'mfa_verify_no_methods' => '没有设置认证方法',
    'mfa_verify_no_methods_desc' => '您的账户没有设置多重身份认证。您需要至少设置一种才能访问。',
    'mfa_verify_use_totp' => '使用移动设备 App 进行认证',
    'mfa_verify_use_backup_codes' => '使用备用认证码进行认证',
    'mfa_verify_backup_code' => '备用认证码',
    'mfa_verify_backup_code_desc' => '在下面输入您的其中一个备用认证码：',
    'mfa_verify_backup_code_enter_here' => '在这里输入备用认证码',
    'mfa_verify_totp_desc' => '在下面输入您的移动 App 生成的认证码：',
    'mfa_setup_login_notification' => '多重身份认证已设置，请使用新配置的方法重新登录。',
];
