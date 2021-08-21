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
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
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
    'email_confirm_success' => 'Eメールアドレスが確認されました。',
    'email_confirm_resent' => '確認メールを再送信しました。受信トレイを確認してください。',

    'email_not_confirmed' => 'Eメールアドレスが確認できていません',
    'email_not_confirmed_text' => 'Eメールアドレスの確認が完了していません。',
    'email_not_confirmed_click_link' => '登録時に受信したメールを確認し、確認リンクをクリックしてください。',
    'email_not_confirmed_resend' => 'Eメールが見つからない場合、以下のフォームから再送信してください。',
    'email_not_confirmed_resend_button' => '確認メールを再送信',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];