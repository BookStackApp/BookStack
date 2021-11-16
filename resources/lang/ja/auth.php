<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'この資格情報は登録されていません。',
    'throttle' => 'ログイン試行回数が制限を超えました。:seconds秒後に再試行してください。',

    // Login & Register
    'sign_up' => '新規登録',
    'log_in' => 'ログイン',
    'log_in_with' => ':socialDriverでログイン',
    'sign_up_with' => ':socialDriverで登録',
    'logout' => 'ログアウト',

    'name' => '名前',
    'username' => 'ユーザ名',
    'email' => 'メールアドレス',
    'password' => 'パスワード',
    'password_confirm' => 'パスワード (確認)',
    'password_hint' => '7文字以上である必要があります',
    'forgot_password' => 'パスワードをお忘れですか？',
    'remember_me' => 'ログイン情報を保存する',
    'ldap_email_hint' => 'このアカウントで使用するEメールアドレスを入力してください。',
    'create_account' => 'アカウント作成',
    'already_have_account' => 'すでにアカウントをお持ちですか？',
    'dont_have_account' => '初めての登録ですか?',
    'social_login' => 'SNSログイン',
    'social_registration' => 'SNS登録',
    'social_registration_text' => '他のサービスで登録 / ログインする',

    'register_thanks' => '登録が完了しました！',
    'register_confirm' => 'メール内の確認ボタンを押して、:appNameへアクセスしてください。',
    'registrations_disabled' => '登録は現在停止中です。',
    'registration_email_domain_invalid' => 'このEmailドメインでの登録は許可されていません。',
    'register_success' => '登録が完了し、ログインできるようになりました！',

    // Password Reset
    'reset_password' => 'パスワードリセット',
    'reset_password_send_instructions' => '以下にEメールアドレスを入力すると、パスワードリセットリンクが記載されたメールが送信されます。',
    'reset_password_send_button' => 'リセットリンクを送信',
    'reset_password_sent' => 'メールアドレスがシステムで見つかった場合、パスワードリセットリンクが:emailに送信されます。',
    'reset_password_success' => 'パスワードがリセットされました。',
    'email_reset_subject' => ':appNameのパスワードをリセット',
    'email_reset_text' => 'このメールは、パスワードリセットがリクエストされたため送信されています。',
    'email_reset_not_requested' => 'もしパスワードリセットを希望しない場合、操作は不要です。',

    // Email Confirmation
    'email_confirm_subject' => ':appNameのメールアドレス確認',
    'email_confirm_greeting' => ':appNameへ登録してくださりありがとうございます！',
    'email_confirm_text' => '以下のボタンを押し、メールアドレスを確認してください:',
    'email_confirm_action' => 'メールアドレスを確認',
    'email_confirm_send_error' => 'Eメールの確認が必要でしたが、システム上でEメールの送信ができませんでした。管理者に連絡し、Eメールが正しく設定されていることを確認してください。',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => '確認メールを再送信しました。受信トレイを確認してください。',

    'email_not_confirmed' => 'Eメールアドレスが確認できていません',
    'email_not_confirmed_text' => 'Eメールアドレスの確認が完了していません。',
    'email_not_confirmed_click_link' => '登録時に受信したメールを確認し、確認リンクをクリックしてください。',
    'email_not_confirmed_resend' => 'Eメールが見つからない場合、以下のフォームから再送信してください。',
    'email_not_confirmed_resend_button' => '確認メールを再送信',

    // User Invite
    'user_invite_email_subject' => ':appNameに招待されました！',
    'user_invite_email_greeting' => ':appNameにあなたのアカウントが作成されました。',
    'user_invite_email_text' => 'アカウントのパスワードを設定してアクセスできるようにするため、下のボタンをクリックしてください：',
    'user_invite_email_action' => 'アカウントのパスワード設定',
    'user_invite_page_welcome' => ':appNameへようこそ！',
    'user_invite_page_text' => 'アカウントの設定を完了してアクセスするには、今後の訪問時に:appNameにログインするためのパスワードを設定する必要があります。',
    'user_invite_page_confirm_button' => 'パスワードを確定',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => '多要素認証を設定',
    'mfa_setup_desc' => 'アカウントのセキュリティを強化するために、多要素認証を設定してください。',
    'mfa_setup_configured' => '既に設定されています',
    'mfa_setup_reconfigure' => '再設定',
    'mfa_setup_remove_confirmation' => 'この多要素認証方法を削除してもよろしいですか？',
    'mfa_setup_action' => '設定',
    'mfa_backup_codes_usage_limit_warning' => 'バックアップコードは残り5つ以下です。アカウントのロックアウトを防ぐため、コードがなくなる前に新しいセットを生成して保存してください。',
    'mfa_option_totp_title' => 'モバイルアプリ',
    'mfa_option_totp_desc' => '多要素認証を使用するには、Google Authenticator、Authy、Microsoft AuthenticatorなどのTOTPをサポートするモバイルアプリケーションが必要です。',
    'mfa_option_backup_codes_title' => 'バックアップコード',
    'mfa_option_backup_codes_desc' => '本人確認のために入力する、一度しか使えないバックアップコードを安全に保存します。',
    'mfa_gen_confirm_and_enable' => '確認して有効化',
    'mfa_gen_backup_codes_title' => 'バックアップコードの設定',
    'mfa_gen_backup_codes_desc' => '以下のコードのリストを安全な場所に保管してください。システムにアクセスする際、コードのいずれかを第二の認証手段として使用できます。',
    'mfa_gen_backup_codes_download' => 'コードをダウンロード',
    'mfa_gen_backup_codes_usage_warning' => '各コードは一度だけ使用できます',
    'mfa_gen_totp_title' => 'モバイルアプリの設定',
    'mfa_gen_totp_desc' => '多要素認証を使用するには、Google Authenticator、Authy、Microsoft AuthenticatorなどのTOTPをサポートするモバイルアプリケーションが必要です。',
    'mfa_gen_totp_scan' => '利用したい認証アプリで以下のQRコードをスキャンしてください。',
    'mfa_gen_totp_verify_setup' => '設定を検証',
    'mfa_gen_totp_verify_setup_desc' => '認証アプリで生成されたコードを下の入力ボックスに入力し、すべてが機能していることを確認してください。',
    'mfa_gen_totp_provide_code_here' => 'アプリが生成したコードを入力',
    'mfa_verify_access' => 'アクセスを確認',
    'mfa_verify_access_desc' => 'このユーザーアカウントはアクセスを許可する前に追加の検証レベルで本人確認を行う必要があります。続行するには、設定されているいずれかの手段で検証してください。',
    'mfa_verify_no_methods' => '手段が設定されていません',
    'mfa_verify_no_methods_desc' => 'アカウントの多要素認証手段が見つかりませんでした。アクセスする前に、少なくとも1つの手段を設定する必要があります。',
    'mfa_verify_use_totp' => 'モバイルアプリを利用して確認',
    'mfa_verify_use_backup_codes' => 'バックアップコードを利用して確認',
    'mfa_verify_backup_code' => 'バックアップコード',
    'mfa_verify_backup_code_desc' => '残りのバックアップコードのいずれかを入力してください:',
    'mfa_verify_backup_code_enter_here' => 'バックアップコードを入力',
    'mfa_verify_totp_desc' => 'モバイルアプリを利用して生成されたコードを入力してください:',
    'mfa_setup_login_notification' => '多要素認証が構成されました。設定された手段を利用して再度ログインしてください。',
];
