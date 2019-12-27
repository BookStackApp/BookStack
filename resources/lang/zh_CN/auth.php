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
    'sign_up_with' => '注册:socialDriver',
    'logout' => '注销',

    'name' => '姓名',
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
    'dont_have_account' => '您还没注册？',
    'social_login' => 'SNS登录',
    'social_registration' => 'SNS注册',
    'social_registration_text' => '其他服务注册/登录。',

    'register_thanks' => '注册完成！',
    'register_confirm' => '请点击查收您的Email，并点击确认。',
    'registrations_disabled' => '注册目前被禁用',
    'registration_email_domain_invalid' => '该Email域名无权访问此应用程序',
    'register_success' => '感谢您注册:appName，您现在已经登录。',


    // Password Reset
    'reset_password' => '重置密码',
    'reset_password_send_instructions' => '在下面输入您的Email地址，您将收到一封带有密码重置链接的邮件。',
    'reset_password_send_button' => '发送重置链接',
    'reset_password_sent_success' => '密码重置链接已发送到:email。',
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
    'user_invite_success' => '已设置密码，您现在可以访问 :appName！'
];
