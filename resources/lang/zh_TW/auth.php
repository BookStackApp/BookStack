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
    'log_in_with' => '以:socialDriver登入',
    'sign_up_with' => '註冊:socialDriver',
    'logout' => '登出',

    'name' => '名稱',
    'username' => '使用者名稱',
    'email' => '電子郵件',
    'password' => '密碼',
    'password_confirm' => '確認密碼',
    'password_hint' => '必須超過 7 個字元',
    'forgot_password' => '忘記密碼?',
    'remember_me' => '記住我',
    'ldap_email_hint' => '請輸入此帳號使用的電子郵件。',
    'create_account' => '建立帳戶',
    'already_have_account' => '已經擁有帳戶？',
    'dont_have_account' => '沒有帳戶？',
    'social_login' => '社群網站登入',
    'social_registration' => '社群網站帳戶註冊',
    'social_registration_text' => '使用其他服務註冊及登入。',

    'register_thanks' => '感謝您的註冊！',
    'register_confirm' => '請檢查您的電子郵件，並按下確認按鈕以使用 :appName 。',
    'registrations_disabled' => '目前已停用註冊',
    'registration_email_domain_invalid' => '這個電子郵件網域沒有權限使用',
    'register_success' => '感謝您註冊:appName，您現在已經登入。',


    // Password Reset
    'reset_password' => '重置密碼',
    'reset_password_send_instructions' => '在下方輸入您的電子郵件，您將收到一封帶有密碼重置連結的郵件。',
    'reset_password_send_button' => '發送重置連結',
    'reset_password_sent' => '重置密碼的連結會發送至電子郵件地址:email（如果系統記錄中存在此電子郵件地址）',
    'reset_password_success' => '您的密碼已成功重置。',
    'email_reset_subject' => '重置您的:appName密碼',
    'email_reset_text' => '您收到此電子郵件是因為我們收到了您的帳號的密碼重置請求。',
    'email_reset_not_requested' => '如果您沒有要求重置密碼，則不需要採取進一步的操作。',


    // Email Confirmation
    'email_confirm_subject' => '確認您在:appName的電子郵件',
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

    // User Invite
    'user_invite_email_subject' => '您受邀請加入:appName！',
    'user_invite_email_greeting' => '我們為您在:appName上創建了一個新賬戶。',
    'user_invite_email_text' => '請點擊下面的按鈕設置賬戶密碼并獲取訪問權限:',
    'user_invite_email_action' => '請設置賬戶密碼',
    'user_invite_page_welcome' => '歡迎使用:appName',
    'user_invite_page_text' => '要完成設置您的賬戶並獲取訪問權限，您需要設置一個密碼。該密碼將在以後訪問時用於登陸:appName',
    'user_invite_page_confirm_button' => '請確定密碼',
    'user_invite_success' => '密碼已設置，您現在可以進入:appName了啦！'
];