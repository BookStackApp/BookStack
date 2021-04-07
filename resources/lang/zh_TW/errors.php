<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => '您沒有權限進入所請求的頁面。',
    'permissionJson' => '您沒有權限執行所請求的動作。',

    // Auth
    'error_user_exists_different_creds' => '電子郵件為 :email 已存在，但帳號密碼不同。',
    'email_already_confirmed' => '已確認電子郵件，請嘗試登入。',
    'email_confirmation_invalid' => '這個確認權杖無效或已被使用，請嘗試重新註冊。',
    'email_confirmation_expired' => '這個確認權杖無效或已被使用，已傳送新的確認電子郵件。',
    'email_confirmation_awaiting' => '用於此帳號的電子郵件地址需要確認',
    'ldap_fail_anonymous' => '使用匿名綁定的 LDAP 存取失敗',
    'ldap_fail_authed' => '使用指定的 DN 與密碼詳細資訊的 LDAP 存取失敗',
    'ldap_extension_not_installed' => '未安裝 PHP 的 LDAP 擴充程式',
    'ldap_cannot_connect' => '無法連線至 LDAP 伺服器，初始化連線失敗',
    'saml_already_logged_in' => '已登入',
    'saml_user_not_registered' => '使用者 :name 未註冊，並已停用自動註冊',
    'saml_no_email_address' => '在外部認證系統提供的資料中找不到該使用者的電子郵件地址',
    'saml_invalid_response_id' => '此應用程式啟動的處理程序無法識別來自外部認證系統的請求。登入後回上一頁可能會造成此問題。',
    'saml_fail_authed' => '使用 :system 登入失敗，系統未提供成功的授權',
    'social_no_action_defined' => '未定義動作',
    'social_login_bad_response' => "在 :socialAccount 登入時遇到錯誤： \n:error",
    'social_account_in_use' => ':socialAccount 帳號已被使用，請嘗試透過 :socialAccount 選項登入。',
    'social_account_email_in_use' => '電子郵件 :email 已被使用。如果您已有帳號，您可以在您的個人設定中連結您的 :socialAccount 帳號。',
    'social_account_existing' => '此 :socialAccount 已附加至您的個人資料。',
    'social_account_already_used_existing' => '此 :socialAccount 帳號已經被其他使用者使用。',
    'social_account_not_used' => '此 :socialAccount 帳號未連結至任何使用者。請至您的個人設定中連結。 ',
    'social_account_register_instructions' => '如果您還沒有帳號，您可以使用 :socialAccount 選項註冊帳號。',
    'social_driver_not_found' => '找不到社交驅動程式',
    'social_driver_not_configured' => '您的 :socialAccount 社交設定不正確。',
    'invite_token_expired' => '此邀請連結已過期。您可以嘗試重設您的帳號密碼。',

    // System
    'path_not_writable' => '無法上傳到 :filePath 檔案路徑。請確定其對伺服器來說是可寫入的。',
    'cannot_get_image_from_url' => '無法從 :url 取得圖片',
    'cannot_create_thumbs' => '伺服器無法建立縮圖。請檢查您是否安裝了 PHP 的 GD 擴充程式。',
    'server_upload_limit' => '伺服器不允許上傳這個大的檔案。請嘗試較小的檔案。',
    'uploaded'  => '伺服器不允許上傳這個大的檔案。請嘗試較小的檔案。',
    'image_upload_error' => '上傳圖片時發生錯誤',
    'image_upload_type_error' => '上傳圖片類型無效',
    'file_upload_timeout' => '檔案上傳逾時。',

    // Attachments
    'attachment_not_found' => '找不到附件',

    // Pages
    'page_draft_autosave_fail' => '無法儲存草稿。請確保您在儲存此頁面前已連線至網際網路',
    'page_custom_home_deletion' => '無法刪除被設定為首頁的頁面',

    // Entities
    'entity_not_found' => '找不到實體',
    'bookshelf_not_found' => '找不到書架',
    'book_not_found' => '找不到書本',
    'page_not_found' => '找不到頁面',
    'chapter_not_found' => '找不到章節',
    'selected_book_not_found' => '找不到選定的書本',
    'selected_book_chapter_not_found' => '找不到選定的書本或章節',
    'guests_cannot_save_drafts' => '訪客無法儲存草稿',

    // Users
    'users_cannot_delete_only_admin' => '您不能刪除唯一的管理員帳號',
    'users_cannot_delete_guest' => '您不能刪除訪客使用者',

    // Roles
    'role_cannot_be_edited' => '無法編輯這個角色',
    'role_system_cannot_be_deleted' => '無法刪除系統角色',
    'role_registration_default_cannot_delete' => '無法刪除設定預設註冊的角色',
    'role_cannot_remove_only_admin' => '此使用者是唯一被指派為管理員角色的使用者。在試圖移除這裡前，請將管理員角色指派給其他使用者。',

    // Comments
    'comment_list' => '擷取評論時發生錯誤。',
    'cannot_add_comment_to_draft' => '您無法新增評論到草稿中。',
    'comment_add' => '新增／更新評論時發生錯誤。',
    'comment_delete' => '刪除評論時發生錯誤。',
    'empty_comment' => '無法新增空評論。',

    // Error pages
    '404_page_not_found' => '找不到頁面',
    'sorry_page_not_found' => '抱歉，找不到您在尋找的頁面。',
    'sorry_page_not_found_permission_warning' => '如果您確認這個頁面存在，則代表可能沒有查看它的權限。',
    'return_home' => '回到首頁',
    'error_occurred' => '發生錯誤',
    'app_down' => ':appName 離線中',
    'back_soon' => '它應該很快就會重新上線。',

    // API errors
    'api_no_authorization_found' => '在請求上找不到授權權杖',
    'api_bad_authorization_format' => '在請求中找到授權權杖，但格式似乎不正確',
    'api_user_token_not_found' => '找不到與提供的授權權杖相符的 API 權杖',
    'api_incorrect_token_secret' => '給定使用的 API 權杖的密碼錯誤',
    'api_user_no_api_permission' => '使用的 API 權杖擁有者無權呼叫 API',
    'api_user_token_expired' => '使用的授權權杖已過期',

    // Settings & Maintenance
    'maintenance_test_email_failure' => '寄送測試電子郵件時發生錯誤:',

];
