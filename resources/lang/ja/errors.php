<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'リクエストされたページへの権限がありません。',
    'permissionJson' => '要求されたアクションを実行する権限がありません。',

    // Auth
    'error_user_exists_different_creds' => ':emailを持つユーザは既に存在しますが、資格情報が異なります。',
    'email_already_confirmed' => 'Eメールは既に確認済みです。ログインしてください。',
    'email_confirmation_invalid' => 'この確認トークンは無効か、または既に使用済みです。登録を再試行してください。',
    'email_confirmation_expired' => '確認トークンは有効期限切れです。確認メールを再送しました。',
    'email_confirmation_awaiting' => 'The email address for the account in use needs to be confirmed',
    'ldap_fail_anonymous' => '匿名バインドを用いたLDAPアクセスに失敗しました',
    'ldap_fail_authed' => '識別名, パスワードを用いたLDAPアクセスに失敗しました',
    'ldap_extension_not_installed' => 'LDAP PHP extensionがインストールされていません',
    'ldap_cannot_connect' => 'LDAPサーバに接続できませんでした',
    'saml_already_logged_in' => '既にログインしています',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'アクションが定義されていません',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => ':socialAccountアカウントは既に使用されています。:socialAccountのオプションからログインを試行してください。',
    'social_account_email_in_use' => ':emailは既に使用されています。ログイン後、プロフィール設定から:socialAccountアカウントを接続できます。',
    'social_account_existing' => 'アカウント:socialAccountは既にあなたのプロフィールに接続されています。',
    'social_account_already_used_existing' => 'この:socialAccountアカウントは既に他のユーザが使用しています。',
    'social_account_not_used' => 'この:socialAccountアカウントはどのユーザにも接続されていません。プロフィール設定から接続できます。',
    'social_account_register_instructions' => 'まだアカウントをお持ちでない場合、:socialAccountオプションから登録できます。',
    'social_driver_not_found' => 'Social driverが見つかりません。',
    'social_driver_not_configured' => 'あなたの:socialAccount設定は正しく構成されていません。',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'ファイルパス :filePath へアップロードできませんでした。サーバ上での書き込みが許可されているか確認してください。',
    'cannot_get_image_from_url' => ':url から画像を取得できませんでした。',
    'cannot_create_thumbs' => 'このサーバはサムネイルを作成できません。GD PHP extensionがインストールされていることを確認してください。',
    'server_upload_limit' => 'このサイズの画像をアップロードすることは許可されていません。ファイルサイズを小さくし、再試行してください。',
    'uploaded'  => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'image_upload_error' => '画像アップロード時にエラーが発生しました。',
    'image_upload_type_error' => 'The image type being uploaded is invalid',
    'file_upload_timeout' => 'ファイルのアップロードがタイムアウトしました。',

    // Attachments
    'attachment_not_found' => '添付ファイルが見つかりません。',

    // Pages
    'page_draft_autosave_fail' => '下書きの保存に失敗しました。インターネットへ接続してください。',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'エンティティが見つかりません',
    'bookshelf_not_found' => 'Bookshelf not found',
    'book_not_found' => 'ブックが見つかりません',
    'page_not_found' => 'ページが見つかりません',
    'chapter_not_found' => 'チャプターが見つかりません',
    'selected_book_not_found' => '選択されたブックが見つかりません',
    'selected_book_chapter_not_found' => '選択されたブック、またはチャプターが見つかりません',
    'guests_cannot_save_drafts' => 'ゲストは下書きを保存できません',

    // Users
    'users_cannot_delete_only_admin' => '唯一の管理者を削除することはできません',
    'users_cannot_delete_guest' => 'ゲストユーザを削除することはできません',

    // Roles
    'role_cannot_be_edited' => 'この役割は編集できません',
    'role_system_cannot_be_deleted' => 'この役割はシステムで管理されているため、削除できません',
    'role_registration_default_cannot_delete' => 'この役割を登録時のデフォルトに設定することはできません',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'An error occurred while fetching the comments.',
    'cannot_add_comment_to_draft' => '下書きにコメントは追加できません。',
    'comment_add' => 'An error occurred while adding / updating the comment.',
    'comment_delete' => 'An error occurred while deleting the comment.',
    'empty_comment' => 'Cannot add an empty comment.',

    // Error pages
    '404_page_not_found' => 'ページが見つかりません',
    'sorry_page_not_found' => 'ページを見つけることができませんでした。',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'image_not_found' => 'Image Not Found',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'ホームに戻る',
    'error_occurred' => 'エラーが発生しました',
    'app_down' => ':appNameは現在停止しています',
    'back_soon' => '回復までしばらくお待ちください。',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => '認証トークンが期限切れです。',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Error thrown when sending a test email:',

];
