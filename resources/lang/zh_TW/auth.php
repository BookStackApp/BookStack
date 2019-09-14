<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => '使用者名稱或密碼錯誤。',
    'throttle' => '您的登入次數過多，請在:seconds秒後重試。',

    /**
     * Login & Register
     */
    'sign_up' => '註冊',
    'log_in' => '登入',
    'log_in_with' => '以:socialDriver登入',
    'sign_up_with' => '註冊:socialDriver',
    'logout' => '登出',

    'name' => '名稱',
    'username' => '使用者名稱',
    'email' => 'Email位址',
    'password' => '密碼',
    'password_confirm' => '確認密碼',
    'password_hint' => '必須超過7個字元',
    'forgot_password' => '忘記密碼?',
    'remember_me' => '記住我',
    'ldap_email_hint' => '請輸入用於此帳號的電子郵件。',
    'create_account' => '建立帳號',
    'social_login' => 'SNS登入',
    'social_registration' => 'SNS註冊',
    'social_registration_text' => '其他服務註冊/登入.',

    'register_thanks' => '註冊完成！',
    'register_confirm' => '請點選查收您的Email，並點選確認。',
    'registrations_disabled' => '註冊目前被禁用',
    'registration_email_domain_invalid' => '此Email域名沒有權限進入本系統',
    'register_success' => '感謝您註冊:appName，您現在已經登入。',


    /**
     * Password Reset
     */
    'reset_password' => '重置密碼',
    'reset_password_send_instructions' => '在下方輸入您的Email位址，您將收到一封帶有密碼重置連結的郵件。',
    'reset_password_send_button' => '發送重置連結',
    'reset_password_sent_success' => '密碼重置連結已發送到:email。',
    'reset_password_success' => '您的密碼已成功重置。',

    'email_reset_subject' => '重置您的:appName密碼',
    'email_reset_text' => '您收到此電子郵件是因為我們收到了您的帳號的密碼重置請求。',
    'email_reset_not_requested' => '如果您沒有要求重置密碼，則不需要採取進一步的操作。',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => '確認您在:appName的Email位址',
    'email_confirm_greeting' => '感謝您加入:appName！',
    'email_confirm_text' => '請點選下面的按鈕確認您的Email位址：',
    'email_confirm_action' => '確認Email',
    'email_confirm_send_error' => '需要Email驗證，但系統無法發送電子郵件，請聯繫網站管理員。',
    'email_confirm_success' => '您的Email位址已成功驗證！',
    'email_confirm_resent' => '驗證郵件已重新發送，請檢查收件箱。',

    'email_not_confirmed' => 'Email位址未驗證',
    'email_not_confirmed_text' => '您的電子郵件位址尚未確認。',
    'email_not_confirmed_click_link' => '請檢查註冊時收到的電子郵件，然後點選確認連結。',
    'email_not_confirmed_resend' => '如果找不到電子郵件，請透過下面的表單重新發送確認Email。',
    'email_not_confirmed_resend_button' => '重新發送確認Email',
];