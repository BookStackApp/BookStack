<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles
     */

    'settings' => '設定',
    'settings_save' => '儲存設定',
    'settings_save_success' => '設定已儲存',

    /**
     * App settings
     */

    'app_settings' => 'App設定',
    'app_name' => 'App名',
    'app_name_desc' => '此名稱將在網頁頂端和Email中顯示。',
    'app_name_header' => '在網頁頂端顯示應用名稱？',
    'app_public_viewing' => '開放公開閱覽？',
    'app_secure_images' => '啟用更高安全性的圖片上傳？',
    'app_secure_images_desc' => '出於效能考量，所有圖片都是公開的。這個選項會在圖片的網址前加入一個隨機並難以猜測的字元串，從而使直接進入變得困難。',
    'app_editor' => '頁面編輯器',
    'app_editor_desc' => '選擇所有使用者將使用哪個編輯器來編輯頁面。',
    'app_custom_html' => '自訂HTML頂端內容',
    'app_custom_html_desc' => '此處加入的任何內容都將插入到每個頁面的<head>部分的底部，這對於覆蓋樣式或加入分析程式碼很方便。',
    'app_logo' => 'App Logo',
    'app_logo_desc' => '這個圖片的高度應該為43px。<br>大圖片將會被縮小。',
    'app_primary_color' => 'App主要配色',
    'app_primary_color_desc' => '請使用十六進位數值。<br>保留空白則重置回預設配色。',
    'app_homepage' => 'App首頁',
    'app_homepage_desc' => '選擇要做為首頁的頁面，這將會替換預設首頁，而且這個頁面的權限設定將被忽略。',
    'app_homepage_default' => '預設首頁選擇',
    'app_disable_comments' => '關閉評論',
    'app_disable_comments_desc' => '在App的所有頁面上關閉評論，已經存在的評論也不會顯示。',

    /**
     * Registration settings
     */

    'reg_settings' => '註冊設定',
    'reg_allow' => '開放註冊？',
    'reg_default_role' => '註冊後的預設使用者角色',
    'reg_confirm_email' => '需要Email驗證？',
    'reg_confirm_email_desc' => '如果使用網域名稱限制，則需要Email驗證，並且本設定將被忽略。',
    'reg_confirm_restrict_domain' => '網域名稱限制',
    'reg_confirm_restrict_domain_desc' => '輸入您想要限制註冊的Email域域名稱列表，用逗號隔開。在被允許與本系統連結之前，使用者會收到一封Email來確認他們的位址。<br>注意，使用者在註冊成功後可以修改他們的Email位址。',
    'reg_confirm_restrict_domain_placeholder' => '尚未設定限制的網域',

    /**
     * Role settings
     */

    'roles' => '角色',
    'role_user_roles' => '使用者角色',
    'role_create' => '建立角色',
    'role_create_success' => '角色建立成功',
    'role_delete' => '刪除角色',
    'role_delete_confirm' => '這將會刪除名為 \':roleName\' 的角色.',
    'role_delete_users_assigned' => '有:userCount位使用者屬於此角色。如果您想將此角色中的使用者遷移，請在下面選擇一個新角色。',
    'role_delete_no_migration' => "不要遷移使用者",
    'role_delete_sure' => '您確定要刪除這個角色？',
    'role_delete_success' => '角色刪除成功',
    'role_edit' => '編輯角色',
    'role_details' => '角色詳細資訊',
    'role_name' => '角色名',
    'role_desc' => '角色簡述',
    'role_system' => '系統權限',
    'role_manage_users' => '管理使用者',
    'role_manage_roles' => '管理角色與角色權限',
    'role_manage_entity_permissions' => '管理所有圖書，章節和頁面的權限',
    'role_manage_own_entity_permissions' => '管理自己的圖書，章節和頁面的權限',
    'role_manage_settings' => '管理App設定',
    'role_asset' => '資源項目',
    'role_asset_desc' => '對系統內資源的預設權限將由這裡的權限控制。若有單獨設定在書本、章節和頁面上的權限，將會覆蓋這裡的權限設定。',
    'role_all' => '全部',
    'role_own' => '擁有',
    'role_controlled_by_asset' => '依據隸屬的資源來決定',
    'role_save' => '儲存角色',
    'role_update_success' => '角色更新成功',
    'role_users' => '此角色的使用者',
    'role_users_none' => '目前沒有使用者被分配到這個角色',

    /**
     * Users
     */

    'users' => '使用者',
    'user_profile' => '使用者資料',
    'users_add_new' => '加入使用者',
    'users_search' => '搜尋使用者',
    'users_role' => '使用者角色',
    'users_external_auth_id' => '外部身份驗證ID',
    'users_password_warning' => '如果您想更改密碼，請填寫以下內容：',
    'users_system_public' => '此使用者代表進入您的App的任何訪客。它不能用於登入，而是自動分配。',
    'users_books_view_type' => '圖書瀏覽佈局偏好',
    'users_delete' => '刪除使用者',
    'users_delete_named' => '刪除使用者 :userName',
    'users_delete_warning' => '這將從系統中完全刪除名為 \':userName\' 的使用者。',
    'users_delete_confirm' => '您確定要刪除這個使用者？',
    'users_delete_success' => '使用者刪除成功。',
    'users_edit' => '編輯使用者',
    'users_edit_profile' => '編輯資料',
    'users_edit_success' => '使用者更新成功',
    'users_avatar' => '使用者大頭照',
    'users_avatar_desc' => '目前圖片應該為約256px的正方形。',
    'users_preferred_language' => '語言',
    'users_social_accounts' => '社群網站帳號',
    'users_social_accounts_info' => '在這里，您可以連結您的其他帳號，以便方便地登入。如果您選擇解除連結，之後將不能透過此社群網站帳號登入，請設定社群網站帳號來取消本系統p的進入權限。',
    'users_social_connect' => '連結帳號',
    'users_social_disconnect' => '解除連結帳號',
    'users_social_connected' => ':socialAccount 帳號已經成功連結到您的資料。',
    'users_social_disconnected' => ':socialAccount 帳號已經成功解除連結。',
];
