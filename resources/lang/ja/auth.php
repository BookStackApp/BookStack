<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'failed' => 'この資格情報は登録されていません。',
    'throttle' => 'ログイン試行回数が制限を超えました。:seconds秒後に再試行してください。',

    /**
     * Login & Register
     */
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
    'password_hint' => '5文字以上である必要があります',
    'forgot_password' => 'パスワードをお忘れですか？',
    'remember_me' => 'ログイン情報を保存する',
    'ldap_email_hint' => 'このアカウントで使用するEメールアドレスを入力してください。',
    'create_account' => 'アカウント作成',
    'social_login' => 'SNSログイン',
    'social_registration' => 'SNS登録',
    'social_registration_text' => '他のサービスで登録 / ログインする',

    'register_thanks' => '登録が完了しました！',
    'register_confirm' => 'メール内の確認ボタンを押して、:appNameへアクセスしてください。',
    'registrations_disabled' => '登録は現在停止中です。',
    'registration_email_domain_invalid' => 'このEmailドメインでの登録は許可されていません。',
    'register_success' => '登録が完了し、ログインできるようになりました！',


    /**
     * Password Reset
     */
    'reset_password' => 'パスワードリセット',
    'reset_password_send_instructions' => '以下にEメールアドレスを入力すると、パスワードリセットリンクが記載されたメールが送信されます。',
    'reset_password_send_button' => 'リセットリンクを送信',
    'reset_password_sent_success' => ':emailへリセットリンクを送信しました。',
    'reset_password_success' => 'パスワードがリセットされました。',

    'email_reset_subject' => ':appNameのパスワードをリセット',
    'email_reset_text' => 'このメールは、パスワードリセットがリクエストされたため送信されています。',
    'email_reset_not_requested' => 'もしパスワードリセットを希望しない場合、操作は不要です。',


    /**
     * Email Confirmation
     */
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
];
