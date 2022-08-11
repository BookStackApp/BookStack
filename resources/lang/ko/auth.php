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
    'password' => '패스워드',
    'password_confirm' => '패스워드 확인',
    'password_hint' => '여덟 글자를 넘어야 합니다.',
    'forgot_password' => '패스워드를 잊었나요?',
    'remember_me' => '로그인 유지',
    'ldap_email_hint' => '계정에 연결한 메일 주소를 입력하세요.',
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

    // Login auto-initiation
    'auto_init_starting' => '로그인 시도 중',
    'auto_init_starting_desc' => '로그인을 시작하기 위해 인증 시스템에 접근 중입니다. 5초 후에도 아무런 반응이 없다면 아래 링크를 클릭하세요.',
    'auto_init_start_link' => '인증 진행',

    // Password Reset
    'reset_password' => '패스워드 바꾸기',
    'reset_password_send_instructions' => '메일 주소를 입력하세요. 이 주소로 해당 과정을 위한 링크를 보낼 것입니다.',
    'reset_password_send_button' => '메일 보내기',
    'reset_password_sent' => '패스워드를 바꿀 수 있는 링크를 :email로 보낼 것입니다.',
    'reset_password_success' => '패스워드를 바꿨습니다.',
    'email_reset_subject' => ':appName 패스워드 바꾸기',
    'email_reset_text' => '패스워드를 바꿉니다.',
    'email_reset_not_requested' => '원하지 않는다면 이 과정은 필요 없습니다.',

    // Email Confirmation
    'email_confirm_subject' => ':appName 메일 인증',
    'email_confirm_greeting' => ':appName로 가입해 주셔서 감사합니다!',
    'email_confirm_text' => '다음 버튼을 눌러 인증하세요:',
    'email_confirm_action' => '메일 인증',
    'email_confirm_send_error' => '메일을 보낼 수 없었습니다.',
    'email_confirm_success' => '메일 인증을 성공했습니다. 이 메일 주소로 로그인할 수 있습니다.',
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
    'user_invite_email_action' => '패스워드 설정',
    'user_invite_page_welcome' => ':appName에 오신 것을 환영합니다!',
    'user_invite_page_text' => ':appName에 로그인할 때 입력할 패스워드를 설정하세요.',
    'user_invite_page_confirm_button' => '패스워드 확인',
    'user_invite_success_login' => '입력한 패스워드로 :appName에 로그인할 수 있습니다.',

    // Multi-factor Authentication
    'mfa_setup' => '다중 인증 설정',
    'mfa_setup_desc' => '추가 보안 계층으로 다중 인증을 설정합니다.',
    'mfa_setup_configured' => '설정되어 있습니다.',
    'mfa_setup_reconfigure' => '다시 설정',
    'mfa_setup_remove_confirmation' => '다중 인증을 해제할까요?',
    'mfa_setup_action' => '설정',
    'mfa_backup_codes_usage_limit_warning' => '남은 백업 코드가 다섯 개 미만입니다. 새 백업 코드 세트를 생성하지 않아 코드가 소진되면 계정이 잠길 수 있습니다.',
    'mfa_option_totp_title' => '모바일 앱',
    'mfa_option_totp_desc' => '다중 인증에는 Google Authenticator, Authy나 Microsoft Authenticator와 같은 TOTP 지원 모바일 앱이 필요합니다.',
    'mfa_option_backup_codes_title' => '백업 코드',
    'mfa_option_backup_codes_desc' => '일회성 백업 코드를 안전한 장소에 보관하세요.',
    'mfa_gen_confirm_and_enable' => '활성화',
    'mfa_gen_backup_codes_title' => '백업 코드 설정',
    'mfa_gen_backup_codes_desc' => '코드 목록을 안전한 장소에 보관하세요. 코드 중 하나를 2FA에 쓸 수 있습니다.',
    'mfa_gen_backup_codes_download' => '코드 받기',
    'mfa_gen_backup_codes_usage_warning' => '각 코드는 한 번씩만 유효합니다.',
    'mfa_gen_totp_title' => '모바일 앱 설정',
    'mfa_gen_totp_desc' => '다중 인증에는 Google Authenticator, Authy나 Microsoft Authenticator와 같은 TOTP 지원 모바일 앱이 필요합니다.',
    'mfa_gen_totp_scan' => '인증 앱으로 QR 코드를 스캔하세요.',
    'mfa_gen_totp_verify_setup' => '설정 확인',
    'mfa_gen_totp_verify_setup_desc' => '인증 앱에서 생성한 코드를 입력하세요:',
    'mfa_gen_totp_provide_code_here' => '백업 코드를 입력하세요.',
    'mfa_verify_access' => '접근 확인',
    'mfa_verify_access_desc' => '추가 인증으로 신원을 확인합니다. 설정한 방법 중 하나를 고르세요.',
    'mfa_verify_no_methods' => '설정한 방법이 없습니다.',
    'mfa_verify_no_methods_desc' => '다중 인증을 설정하지 않았습니다.',
    'mfa_verify_use_totp' => '모바일 앱으로 인증하기',
    'mfa_verify_use_backup_codes' => '백업 코드로 인증하세요.',
    'mfa_verify_backup_code' => '백업 코드',
    'mfa_verify_backup_code_desc' => '나머지 백업 코드 중 하나를 입력하세요:',
    'mfa_verify_backup_code_enter_here' => '백업 코드를 입력하세요.',
    'mfa_verify_totp_desc' => '모바일 앱에서 생성한 백업 코드를 입력하세요:',
    'mfa_setup_login_notification' => '다중 인증을 설정했습니다. 설정한 방법으로 다시 로그인하세요.',
];
