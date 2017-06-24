<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => '設定',
    'settings_save' => '設定を保存',
    'settings_save_success' => '設定を保存しました',

    /**
     * App settings
     */

    'app_settings' => 'アプリケーション設定',
    'app_name' => 'アプリケーション名',
    'app_name_desc' => 'この名前はヘッダーやEメール内で表示されます。',
    'app_name_header' => 'ヘッダーにアプリケーション名を表示する',
    'app_public_viewing' => 'アプリケーションを公開する',
    'app_secure_images' => '画像アップロード時のセキュリティを強化',
    'app_secure_images_desc' => 'パフォーマンスの観点から、全ての画像が公開になっています。このオプションを有効にすると、画像URLの先頭にランダムで推測困難な文字列が追加され、アクセスを困難にします。',
    'app_editor' => 'ページエディタ',
    'app_editor_desc' => 'ここで選択されたエディタを全ユーザが使用します。',
    'app_custom_html' => 'カスタムheadタグ',
    'app_custom_html_desc' => 'スタイルシートやアナリティクスコード追加したい場合、ここを編集します。これは<head>の最下部に挿入されます。',
    'app_logo' => 'ロゴ',
    'app_logo_desc' => '高さ43pxで表示されます。これを上回る場合、自動で縮小されます。',
    'app_primary_color' => 'プライマリカラー',
    'app_primary_color_desc' => '16進数カラーコードで入力します。空にした場合、デフォルトの色にリセットされます。',

    /**
     * Registration settings
     */

    'reg_settings' => '登録設定',
    'reg_allow' => '新規登録を許可',
    'reg_default_role' => '新規登録時のデフォルト役割',
    'reg_confirm_email' => 'Eメール認証を必須にする',
    'reg_confirm_email_desc' => 'ドメイン制限を有効にしている場合はEメール認証が必須となり、この項目は無視されます。',
    'reg_confirm_restrict_domain' => 'ドメイン制限',
    'reg_confirm_restrict_domain_desc' => '特定のドメインのみ登録できるようにする場合、以下にカンマ区切りで入力します。設定された場合、Eメール認証が必須になります。<br>登録後、ユーザは自由にEメールアドレスを変更できます。',
    'reg_confirm_restrict_domain_placeholder' => '制限しない',

    /**
     * Role settings
     */

    'roles' => '役割',
    'role_user_roles' => '役割',
    'role_create' => '役割を作成',
    'role_create_success' => '役割を作成しました',
    'role_delete' => '役割を削除',
    'role_delete_confirm' => '役割「:roleName」を削除します。',
    'role_delete_users_assigned' => 'この役割は:userCount人のユーザに付与されています。該当するユーザを他の役割へ移行できます。',
    'role_delete_no_migration' => "ユーザを移行しない",
    'role_delete_sure' => '本当に役割を削除してよろしいですか？',
    'role_delete_success' => '役割を削除しました',
    'role_edit' => '役割を編集',
    'role_details' => '概要',
    'role_name' => '役割名',
    'role_desc' => '役割の説明',
    'role_system' => 'システム権限',
    'role_manage_users' => 'ユーザ管理',
    'role_manage_roles' => '役割と権限の管理',
    'role_manage_entity_permissions' => '全てのブック, チャプター, ページに対する権限の管理',
    'role_manage_own_entity_permissions' => '自身のブック, チャプター, ページに対する権限の管理',
    'role_manage_settings' => 'アプリケーション設定の管理',
    'role_asset' => 'アセット権限',
    'role_asset_desc' => '各アセットに対するデフォルトの権限を設定します。ここで設定した権限が優先されます。',
    'role_all' => '全て',
    'role_own' => '自身',
    'role_controlled_by_asset' => 'このアセットに対し、右記の操作を許可:',
    'role_save' => '役割を保存',
    'role_update_success' => '役割を更新しました',
    'role_users' => 'この役割を持つユーザ',
    'role_users_none' => 'この役割が付与されたユーザは居ません',

    /**
     * Users
     */

    'users' => 'ユーザ',
    'user_profile' => 'ユーザプロフィール',
    'users_add_new' => 'ユーザを追加',
    'users_search' => 'ユーザ検索',
    'users_role' => 'ユーザ役割',
    'users_external_auth_id' => '外部認証ID',
    'users_password_warning' => 'パスワードを変更したい場合のみ入力してください',
    'users_system_public' => 'このユーザはアプリケーションにアクセスする全てのゲストを表します。ログインはできませんが、自動的に割り当てられます。',
    'users_delete' => 'ユーザを削除',
    'users_delete_named' => 'ユーザ「:userName」を削除',
    'users_delete_warning' => 'ユーザ「:userName」を完全に削除します。',
    'users_delete_confirm' => '本当にこのユーザを削除してよろしいですか？',
    'users_delete_success' => 'ユーザを削除しました',
    'users_edit' => 'ユーザ編集',
    'users_edit_profile' => 'プロフィール編集',
    'users_edit_success' => 'ユーザを更新しました',
    'users_avatar' => 'アバター',
    'users_avatar_desc' => '256pxの正方形である必要があります。',
    'users_preferred_language' => '使用言語',
    'users_social_accounts' => 'ソーシャルアカウント',
    'users_social_accounts_info' => 'アカウントを接続すると、ログインが簡単になります。ここでアカウントの接続を解除すると、そのアカウントを経由したログインを禁止できます。接続解除後、各ソーシャルアカウントの設定にてこのアプリケーションへのアクセス許可を解除してください。',
    'users_social_connect' => 'アカウントを接続',
    'users_social_disconnect' => 'アカウントを接続解除',
    'users_social_connected' => '「:socialAccount」がプロフィールに接続されました。',
    'users_social_disconnected' => '「:socialAccount」がプロフィールから接続解除されました。'
    
];
