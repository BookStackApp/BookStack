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
    'ldap_fail_anonymous' => '使用匿名綁定的LDAP進入失敗。',
    'ldap_fail_authed' => '帶有標識名稱和密碼的LDAP進入失敗。',
    'ldap_extension_not_installed' => '未安裝LDAP PHP外掛程式',
    'ldap_cannot_connect' => '無法連接到ldap伺服器，第一次連接失敗',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'saml_email_exists' => 'Registration unsuccessful since a user already exists with email address ":email"',
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
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

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
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => '讀取評論時發生錯誤。',
    'cannot_add_comment_to_draft' => '您不能為草稿加入評論。',
    'comment_add' => '加入/更新評論時發生錯誤。',
    'comment_delete' => '刪除評論時發生錯誤。',
    'empty_comment' => '不能加入空的評論。',

    // Error pages
    '404_page_not_found' => '無法找到頁面',
    'sorry_page_not_found' => '對不起，無法找到您想進入的頁面。',
    'return_home' => '返回首頁',
    'error_occurred' => '發生錯誤',
    'app_down' => ':appName現在正在關閉',
    'back_soon' => '請耐心等待網站的恢複。',

];
