<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '您沒有權限進入所請求的頁面。',
    'permissionJson' => '您沒有權限執行所請求的操作。',

    // Auth
    'error_user_exists_different_creds' => 'Email為 :email 的使用者已經存在，但具有不同的憑據。',
    'email_already_confirmed' => 'Email已被確認，請嘗試登錄。',
    'email_confirmation_invalid' => '此確認 Session 無效或已被使用，請重新註冊。',
    'email_confirmation_expired' => '確認 Session 已過期，已發送新的確認電子郵件。',
    'email_confirmation_awaiting' => '用於此賬戶的電子郵箱需要認證',
    'ldap_fail_anonymous' => '使用匿名綁定的LDAP進入失敗。',
    'ldap_fail_authed' => '帶有標識名稱和密碼的LDAP進入失敗。',
    'ldap_extension_not_installed' => '未安裝LDAP PHP外掛程式',
    'ldap_cannot_connect' => '無法連接到ldap伺服器，第一次連接失敗',
    'saml_already_logged_in' => '已登陸',
    'saml_user_not_registered' => '用戶:name未註冊，自動註冊不可用',
    'saml_no_email_address' => '在外部認證系統提供的數據中找不到該用戶的電子郵件地址',
    'saml_invalid_response_id' => '該應用程序啟動的進程無法識別來自外部身份驗證系統的請求。 登錄後返回可能會導致此問題。',
    'saml_fail_authed' => '使用：system登錄失敗，系統未提供成功的授權',
    'social_no_action_defined' => '沒有定義行為',
    'social_login_bad_response' => "在 :socialAccount 登錄時遇到錯誤：\n:error",
    'social_account_in_use' => ':socialAccount 帳號已被使用，請嘗試透過 :socialAccount 選項登錄。',
    'social_account_email_in_use' => 'Email :email 已經被使用。如果您已有帳號，則可以在個人資料設定中綁定您的 :socialAccount。',
    'social_account_existing' => ':socialAccount已經被綁定到您的帳號。',
    'social_account_already_used_existing' => ':socialAccount帳號已經被其他使用者使用。',
    'social_account_not_used' => ':socialAccount帳號沒有綁定到任何使用者，請在您的個人資料設定中綁定。',
    'social_account_register_instructions' => '如果您還沒有帳號，您可以使用 :socialAccount 選項註冊帳號。',
    'social_driver_not_found' => '未找到社交驅動程式',
    'social_driver_not_configured' => '您的:socialAccount社交設定不正確。',
    'invite_token_expired' => '此邀請鏈接已過期，您可以嘗試重置您的賬戶密碼。',

    // System
    'path_not_writable' => '無法上傳到檔案路徑“:filePath”，請確保它可寫入伺服器。',
    'cannot_get_image_from_url' => '無法從 :url 中獲取圖片',
    'cannot_create_thumbs' => '伺服器無法建立縮圖，請檢查您是否安裝了GD PHP外掛。',
    'server_upload_limit' => '上傳的檔案大小超過伺服器允許上限。請嘗試較小的檔案。',
    'uploaded'  => '上傳的檔案大小超過伺服器允許上限。請嘗試較小的檔案。',
    'image_upload_error' => '上傳圖片時發生錯誤',
    'image_upload_type_error' => '上傳圖片類型錯誤',
    'file_upload_timeout' => '文件上傳已超時。',

    // Attachments
    'attachment_page_mismatch' => '附件更新期間的頁面不符合',
    'attachment_not_found' => '沒有找到附件',

    // Pages
    'page_draft_autosave_fail' => '無法儲存草稿，確保您在儲存頁面之前已經連接到互聯網',
    'page_custom_home_deletion' => '無法刪除一個被設定為首頁的頁面',

    // Entities
    'entity_not_found' => '未找到實體',
    'bookshelf_not_found' => '未找到書架',
    'book_not_found' => '未找到圖書',
    'page_not_found' => '未找到頁面',
    'chapter_not_found' => '未找到章節',
    'selected_book_not_found' => '選中的書未找到',
    'selected_book_chapter_not_found' => '未找到所選的圖書或章節',
    'guests_cannot_save_drafts' => '訪客不能儲存草稿',

    // Users
    'users_cannot_delete_only_admin' => '您不能刪除唯一的管理員帳號',
    'users_cannot_delete_guest' => '您不能刪除訪客使用者',

    // Roles
    'role_cannot_be_edited' => '無法編輯這個角色',
    'role_system_cannot_be_deleted' => '無法刪除系統角色',
    'role_registration_default_cannot_delete' => '無法刪除設定為預設註冊的角色',
    'role_cannot_remove_only_admin' => '該用戶是分配作為管理員職務的唯一用戶。 在嘗試在此處刪除管理員職務之前，請將其分配給其他用戶。',

    // Comments
    'comment_list' => '讀取評論時發生錯誤。',
    'cannot_add_comment_to_draft' => '您不能為草稿加入評論。',
    'comment_add' => '加入/更新評論時發生錯誤。',
    'comment_delete' => '刪除評論時發生錯誤。',
    'empty_comment' => '不能加入空的評論。',

    // Error pages
    '404_page_not_found' => '無法找到頁面',
    'sorry_page_not_found' => '對不起，無法找到您想進入的頁面。',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'return_home' => '返回首頁',
    'error_occurred' => '發生錯誤',
    'app_down' => ':appName現在正在關閉',
    'back_soon' => '請耐心等待網站的恢複。',

    // API errors
    'api_no_authorization_found' => '在請求上找不到授權令牌',
    'api_bad_authorization_format' => '在請求中找到授權令牌，但格式似乎不正確',
    'api_user_token_not_found' => '找不到提供的授權令牌的匹配API令牌',
    'api_incorrect_token_secret' => '給定使用的API令牌提供的密鑰不正確',
    'api_user_no_api_permission' => '使用的API令牌的擁有者者無權進行API調用',
    'api_user_token_expired' => '授權令牌已過期',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Error thrown when sending a test email:',

];
