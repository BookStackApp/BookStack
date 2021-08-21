<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => '使用者名稱或密碼錯誤。',
    'throttle' => '您的登入次數過多，請在:seconds秒後重試。',

    // Login & Register
    'sign_up' => '註冊',
    'log_in' => '登入',
    'log_in_with' => '以 :socialDriver 登入',
    'sign_up_with' => '以 :socialDriver 註冊',
    'logout' => '登出',

    'name' => '名稱',
    'username' => '使用者名稱',
    'email' => '電子郵件',
    'password' => '密碼',
    'password_confirm' => '確認密碼',
    'password_hint' => '必須超過 7 個字元',
    'forgot_password' => '忘記密碼？',
    'remember_me' => '記住我',
    'ldap_email_hint' => '請輸入此帳號使用的電子郵件。',
    'create_account' => '建立帳號',
    'already_have_account' => '已有帳號？',
    'dont_have_account' => '沒有帳號？',
    'social_login' => '社群網站登入',
    'social_registration' => '使用社群網站帳號註冊',
    'social_registration_text' => '使用其他服務註冊及登入。',

    'register_thanks' => '感謝您的註冊！',
    'register_confirm' => '請檢查您的電子郵件，並按下確認按鈕以使用 :appName 。',
    'registrations_disabled' => '目前已停用註冊',
    'registration_email_domain_invalid' => '這個電子郵件網域沒有權限使用',
    'register_success' => '感謝您註冊！您已註冊完成並可登入。',


    // Password Reset
    'reset_password' => '重設密碼',
    'reset_password_send_instructions' => '在下方輸入您的電子郵件，您將收到一封帶有密碼重設連結的郵件。',
    'reset_password_send_button' => '發送重設連結',
    'reset_password_sent' => '重設密碼的連結會發送至電子郵件 :email（如果此電子郵件在我們的系統中存在）',
    'reset_password_success' => '您的密碼已成功重設。',
    'email_reset_subject' => '重設您的 :appName 密碼',
    'email_reset_text' => '您收到此電子郵件是因為我們收到了您的帳號的密碼重設請求。',
    'email_reset_not_requested' => '如果您沒有要求重設密碼，則不需要採取進一步的操作。',


    // Email Confirmation
    'email_confirm_subject' => '確認您在 :appName 的電子郵件',
    'email_confirm_greeting' => '感謝您加入 :appName！',
    'email_confirm_text' => '請點選下面的按鈕來確認您的電子郵件地址：',
    'email_confirm_action' => '確認電子郵件',
    'email_confirm_send_error' => '需要電子郵件驗證，但系統無法傳送電子郵件。請與管理員聯絡以確保電子郵件正確設定。',
    'email_confirm_success' => '您的電子郵件已成功驗證！',
    'email_confirm_resent' => '確認電子郵件已重新傳送。請檢查您的收件匣。',

    'email_not_confirmed' => '電子郵件地址未確認',
    'email_not_confirmed_text' => '您的電子郵件位址尚未確認。',
    'email_not_confirmed_click_link' => '請檢查註冊時收到的電子郵件，然後點選確認連結。',
    'email_not_confirmed_resend' => '如果找不到電子郵件，請透過下面的表單重新發送確認電子郵件。',
    'email_not_confirmed_resend_button' => '重新傳送確認電子郵件',

    // User Invite
    'user_invite_email_subject' => '您被邀請加入 :appName！',
    'user_invite_email_greeting' => '我們為您在 :appName 上建立了一個新帳號。',
    'user_invite_email_text' => '請點擊下方按鈕來設定帳號密碼並取得存取權：',
    'user_invite_email_action' => '設定帳號密碼',
    'user_invite_page_welcome' => '歡迎使用 :appName！',
    'user_invite_page_text' => '要完成設定您的帳號並取得存取權，您必須設定密碼，此密碼將用於登入 :appName。',
    'user_invite_page_confirm_button' => '確認密碼',
    'user_invite_success' => '密碼已設定，您現在可以存取 :appName 了！',

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