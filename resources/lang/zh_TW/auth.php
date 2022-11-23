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
    'password_hint' => '密碼必須至少8個字元',
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

    // Login auto-initiation
    'auto_init_starting' => '嘗試登入中',
    'auto_init_starting_desc' => '正在與認證系統連線以開始流程，若 5 秒鐘仍無回應，請嘗試點擊以下的連結。',
    'auto_init_start_link' => '進行認證',

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
    'email_confirm_success' => '您的電子郵箱已確認成功！您可以使用該電子郵箱地址進行登入了。',
    'email_confirm_resent' => '確認電子郵件已重新傳送。請檢查您的收件匣。',
    'email_confirm_thanks' => '完成驗證，謝謝。',
    'email_confirm_thanks_desc' => '正在處理您的確認，請稍候。若三秒後沒有重新導向，請按下方的「繼續」連結繼續。',

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
    'user_invite_success_login' => '密碼已設定完成，您可以使用改密碼來登入 :appName!',

    // Multi-factor Authentication
    'mfa_setup' => '設定雙重身份驗證',
    'mfa_setup_desc' => '設定雙重身份驗證為您的帳戶多增加了一道防線',
    'mfa_setup_configured' => '設定完成',
    'mfa_setup_reconfigure' => '重新設定',
    'mfa_setup_remove_confirmation' => '您確定要移除雙重身份驗證嗎？',
    'mfa_setup_action' => '設置',
    'mfa_backup_codes_usage_limit_warning' => '您只剩下不到5組備用驗證碼了，請重新生成新的備用驗證碼並妥善保存，以免日後無法登入您的賬號。',
    'mfa_option_totp_title' => '手機App',
    'mfa_option_totp_desc' => '您必須在行動裝置上安裝了支援TOTP的身份驗證程式（例如Google Authenticator, Authy 或是 Microsoft Authenticator）才能使用雙重身份驗證。',
    'mfa_option_backup_codes_title' => '備用驗證碼',
    'mfa_option_backup_codes_desc' => '妥善保存好您的一次性備用驗證碼，以便日後驗證您的身份。',
    'mfa_gen_confirm_and_enable' => '確認並啟用',
    'mfa_gen_backup_codes_title' => '備援代碼設定',
    'mfa_gen_backup_codes_desc' => '將以下代碼列表儲存在安全的地方。存取系統時，您可以使用其中一個代碼作為第二個身份驗證機制。',
    'mfa_gen_backup_codes_download' => '下載代碼',
    'mfa_gen_backup_codes_usage_warning' => '每個代碼都只能使用一次',
    'mfa_gen_totp_title' => '行動裝置應用程式設定',
    'mfa_gen_totp_desc' => '您必須在行動裝置上安裝支援 TOTP 的身份驗證應用程式（例如 Google Authenticator、Authy 或 Microsoft Authenticator）。',
    'mfa_gen_totp_scan' => '使用您偏好的身份驗證應用程式掃描下方的 QR code 以開始流程。',
    'mfa_gen_totp_verify_setup' => '驗證設定',
    'mfa_gen_totp_verify_setup_desc' => '透過在下方的輸入方塊中輸入您的身份驗證應用程式中產生的代碼來驗證一切都正常：',
    'mfa_gen_totp_provide_code_here' => '在此處填入您的應用程式產生的代碼',
    'mfa_verify_access' => '驗證存取權',
    'mfa_verify_access_desc' => '您的使用者帳號在您取得存取權前需要您透過額外的驗證層級確認您的身份。使用您設定的其中一種驗證方式繼續。',
    'mfa_verify_no_methods' => '未設定任何方式',
    'mfa_verify_no_methods_desc' => '您的帳號中找不到多重步驟驗證方式。在取得存取權前，您必須設定至少一種方式。',
    'mfa_verify_use_totp' => '使用您的行動裝置進行驗證',
    'mfa_verify_use_backup_codes' => '使用您的備用驗證碼進行驗證',
    'mfa_verify_backup_code' => '備用驗證碼',
    'mfa_verify_backup_code_desc' => '在下方輸入您剩下的其中一個備援代碼：',
    'mfa_verify_backup_code_enter_here' => '在此處輸入備援代碼',
    'mfa_verify_totp_desc' => '在下方輸入使用您行動裝置應用程式產生的代碼：',
    'mfa_setup_login_notification' => '多因素認證已設定，請使用新的設定登入',
];
