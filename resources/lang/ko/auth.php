<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => '자격 증명이 기록과 일치하지 않습니다.',
    'throttle' => '로그인 시도가 너무 많습니다. :seconds초 후에 다시 시도하세요.',

    // Login & Register
    'sign_up' => '가입',
    'log_in' => '로그인',
    'log_in_with' => ':socialDriver로 로그인',
    'sign_up_with' => ':socialDriver로 가입',
    'logout' => '로그아웃',

    'name' => '이름',
    'username' => '사용자 이름',
    'email' => '메일 주소',
    'password' => '비밀번호',
    'password_confirm' => '비밀번호 확인',
    'password_hint' => '일곱 글자를 넘어야 합니다.',
    'forgot_password' => '비밀번호를 잊었나요?',
    'remember_me' => '로그인 유지',
    'ldap_email_hint' => '이 계정에 대한 메일 주소를 입력하세요.',
    'create_account' => '가입',
    'already_have_account' => '계정이 있나요?',
    'dont_have_account' => '계정이 없나요?',
    'social_login' => '소셜 로그인',
    'social_registration' => '소셜 가입',
    'social_registration_text' => '소셜 계정으로 가입하고 로그인합니다.',

    'register_thanks' => '가입해 주셔서 감사합니다!',
    'register_confirm' => '메일을 확인한 후 버튼을 눌러 :appName에 접근하세요.',
    'registrations_disabled' => '가입할 수 없습니다.',
    'registration_email_domain_invalid' => '이 메일 주소로는 이 사이트에 접근할 수 없습니다.',
    'register_success' => '가입했습니다! 이제 로그인할 수 있습니다.',


    // Password Reset
    'reset_password' => '비밀번호 바꾸기',
    'reset_password_send_instructions' => '메일 주소를 입력하세요. 이 주소로 해당 과정을 위한 링크를 보낼 것입니다.',
    'reset_password_send_button' => '메일 보내기',
    'reset_password_sent' => '시스템에서 이메일 주소가 발견되면, 암호 재설정 링크가 :email로 전송된다.',
    'reset_password_success' => '비밀번호를 바꿨습니다.',
    'email_reset_subject' => ':appName 비밀번호 바꾸기',
    'email_reset_text' => '비밀번호를 바꿉니다.',
    'email_reset_not_requested' => '원하지 않는다면 이 과정은 필요 없습니다.',


    // Email Confirmation
    'email_confirm_subject' => ':appName 메일 인증',
    'email_confirm_greeting' => ':appName로 가입해 주셔서 감사합니다!',
    'email_confirm_text' => '다음 버튼을 눌러 인증하세요:',
    'email_confirm_action' => '메일 인증',
    'email_confirm_send_error' => '메일을 보낼 수 없었습니다.',
    'email_confirm_success' => '인증했습니다!',
    'email_confirm_resent' => '다시 보냈습니다. 메일함을 확인하세요.',

    'email_not_confirmed' => '인증하지 않았습니다.',
    'email_not_confirmed_text' => '인증을 완료하지 않았습니다.',
    'email_not_confirmed_click_link' => '메일을 확인하고 인증 링크를 클릭하세요.',
    'email_not_confirmed_resend' => '메일 주소가 없다면 다음을 입력하세요.',
    'email_not_confirmed_resend_button' => '다시 보내기',

    // User Invite
    'user_invite_email_subject' => ':appName에서 권유를 받았습니다.',
    'user_invite_email_greeting' => ':appName에서 가입한 기록이 있습니다.',
    'user_invite_email_text' => '다음 버튼을 눌러 확인하세요:',
    'user_invite_email_action' => '비밀번호 설정',
    'user_invite_page_welcome' => ':appName에 오신 것을 환영합니다!',
    'user_invite_page_text' => ':appName에 로그인할 때 입력할 비밀번호를 설정하세요.',
    'user_invite_page_confirm_button' => '비밀번호 확인',
    'user_invite_success' => '암호가 설정되었고, 이제 :appName에 접근할 수 있습니다.',

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