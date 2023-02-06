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
    'email_confirmation_awaiting' => '使用中のアカウントのメールアドレスを確認する必要があります',
    'ldap_fail_anonymous' => '匿名バインドを用いたLDAPアクセスに失敗しました',
    'ldap_fail_authed' => '識別名, パスワードを用いたLDAPアクセスに失敗しました',
    'ldap_extension_not_installed' => 'LDAP PHP extensionがインストールされていません',
    'ldap_cannot_connect' => 'LDAPサーバに接続できませんでした',
    'saml_already_logged_in' => '既にログインしています',
    'saml_user_not_registered' => 'ユーザー :name は登録されておらず、自動登録は無効になっています',
    'saml_no_email_address' => '外部認証システムから提供されたデータに、このユーザーのメールアドレスが見つかりませんでした',
    'saml_invalid_response_id' => '外部認証システムからの要求がアプリケーションによって開始されたプロセスによって認識されません。ログイン後に戻るとこの問題が発生する可能性があります。',
    'saml_fail_authed' => ':systemを利用したログインに失敗しました。システムは正常な認証を提供しませんでした。',
    'oidc_already_logged_in' => '既にログインしています',
    'oidc_user_not_registered' => 'ユーザー :name は登録されておらず、自動登録は無効になっています',
    'oidc_no_email_address' => '外部認証システムから提供されたデータに、このユーザーのメールアドレスが見つかりませんでした',
    'oidc_fail_authed' => ':systemを利用したログインに失敗しました。システムは正常な認証を提供しませんでした。',
    'social_no_action_defined' => 'アクションが定義されていません',
    'social_login_bad_response' => ":socialAccountのログイン中にエラーが発生しました:\n:error",
    'social_account_in_use' => ':socialAccountアカウントは既に使用されています。:socialAccountのオプションからログインを試行してください。',
    'social_account_email_in_use' => ':emailは既に使用されています。ログイン後、プロフィール設定から:socialAccountアカウントを接続できます。',
    'social_account_existing' => 'アカウント:socialAccountは既にあなたのプロフィールに接続されています。',
    'social_account_already_used_existing' => 'この:socialAccountアカウントは既に他のユーザが使用しています。',
    'social_account_not_used' => 'この:socialAccountアカウントはどのユーザにも接続されていません。プロフィール設定から接続できます。',
    'social_account_register_instructions' => 'まだアカウントをお持ちでない場合、:socialAccountオプションから登録できます。',
    'social_driver_not_found' => 'Social driverが見つかりません',
    'social_driver_not_configured' => 'あなたの:socialAccount設定は正しく構成されていません。',
    'invite_token_expired' => 'この招待リンクの有効期限が切れています。 代わりにアカウントのパスワードをリセットしてみてください。',

    // System
    'path_not_writable' => 'ファイルパス :filePath へアップロードできませんでした。サーバ上での書き込みが許可されているか確認してください。',
    'cannot_get_image_from_url' => ':url から画像を取得できませんでした。',
    'cannot_create_thumbs' => 'このサーバはサムネイルを作成できません。GD PHP extensionがインストールされていることを確認してください。',
    'server_upload_limit' => 'このサイズの画像をアップロードすることは許可されていません。ファイルサイズを小さくし、再試行してください。',
    'uploaded'  => 'このサイズの画像をアップロードすることは許可されていません。ファイルサイズを小さくし、再試行してください。',
    'file_upload_timeout' => 'ファイルのアップロードがタイムアウトしました。',

    // Drawing & Images
    'image_upload_error' => '画像アップロード時にエラーが発生しました。',
    'image_upload_type_error' => 'アップロード中の画像の種類が無効です',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => '添付ファイルが見つかりません',

    // Pages
    'page_draft_autosave_fail' => '下書きの保存に失敗しました。インターネットへ接続してください。',
    'page_custom_home_deletion' => 'ホームページに設定されているページは削除できません',

    // Entities
    'entity_not_found' => 'エンティティが見つかりません',
    'bookshelf_not_found' => '本棚が見つかりません',
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
    'role_cannot_remove_only_admin' => 'このユーザーは、管理者の役割に割り当てられている唯一のユーザーです。削除する前に別のユーザーに管理者の役割を割り当ててください。',

    // Comments
    'comment_list' => 'コメントを取得中にエラーが発生しました。',
    'cannot_add_comment_to_draft' => '下書きにコメントは追加できません。',
    'comment_add' => 'コメントの追加・更新中にエラーが発生しました。',
    'comment_delete' => 'コメントを削除中にエラーが発生しました。',
    'empty_comment' => '空のコメントは追加できません。',

    // Error pages
    '404_page_not_found' => 'ページが見つかりません',
    'sorry_page_not_found' => 'ページを見つけることができませんでした。',
    'sorry_page_not_found_permission_warning' => 'このページが存在すると思われる場合は、閲覧の権限がない可能性があります。',
    'image_not_found' => '画像が見つかりません',
    'image_not_found_subtitle' => '画像を見つけることができませんでした。',
    'image_not_found_details' => 'この画像が存在することを予期していた場合は、削除された可能性があります。',
    'return_home' => 'ホームに戻る',
    'error_occurred' => 'エラーが発生しました',
    'app_down' => ':appNameは現在停止しています',
    'back_soon' => '回復までしばらくお待ちください。',

    // API errors
    'api_no_authorization_found' => 'リクエストに認証トークンが見つかりません',
    'api_bad_authorization_format' => 'リクエストに認証トークンが見つかりましたが、形式が正しくないようです',
    'api_user_token_not_found' => '提供された認証トークンに一致するAPIトークンが見つかりませんでした',
    'api_incorrect_token_secret' => '利用されたAPIトークンに対して提供されたシークレットが正しくありません',
    'api_user_no_api_permission' => '使用されているAPIトークンの所有者には、API呼び出しを行う権限がありません',
    'api_user_token_expired' => '認証トークンが期限切れです。',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'テストメール送信時にエラーが発生しました:',

];
