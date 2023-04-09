<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => '자격 증명이 기록과 일치하지 않습니다.',
    'throttle' => '로그인 시도가 너무 많습니다. :seconds초 후에 다시 시도해주세요.',

    // Login & Register
    'sign_up' => '가입',
    'log_in' => '로그인',
    'log_in_with' => ':socialDriver 소셜 계정으로 로그인',
    'sign_up_with' => ':socialDriver 소셜 계정으로 가입',
    'logout' => '로그아웃',

    'name' => '이름',
    'username' => '사용자 이름',
    'email' => '전자우편 주소',
    'password' => '비밀번호',
    'password_confirm' => '비밀번호 확인',
    'password_hint' => '8 글자를 넘어야 합니다.',
    'forgot_password' => '비밀번호를 잊으셨나요?',
    'remember_me' => '로그인 유지',
    'ldap_email_hint' => '계정에 연결한 전자우편 주소를 입력하세요.',
    'create_account' => '가입',
    'already_have_account' => '계정이 있나요?',
    'dont_have_account' => '계정이 없나요?',
    'social_login' => '소셜 로그인',
    'social_registration' => '소셜 가입',
    'social_registration_text' => '소셜 계정으로 가입하고 로그인합니다.',

    'register_thanks' => '가입해 주셔서 감사합니다!',
    'register_confirm' => '전자우편을 확인한 후 버튼을 눌러 :appName에 접근하세요.',
    'registrations_disabled' => '가입 기능이 비활성화되어 있습니다',
    'registration_email_domain_invalid' => '이 전자우편 주소로는 이 사이트에 접근할 수 없습니다.',
    'register_success' => '가입했습니다! 이제 로그인할 수 있습니다.',

    // Login auto-initiation
    'auto_init_starting' => '로그인 시도 중',
    'auto_init_starting_desc' => '로그인을 시작하기 위해 인증 시스템에 접근 중입니다. 5초 후에도 아무런 반응이 없다면 아래 링크를 클릭하세요.',
    'auto_init_start_link' => '인증 진행',

    // Password Reset
    'reset_password' => '비밀번호 바꾸기',
    'reset_password_send_instructions' => '전자우편 주소를 입력하세요. 이 주소로 해당 과정을 위한 링크를 보낼 것입니다.',
    'reset_password_send_button' => '재설정 링크 보내기',
    'reset_password_sent' => '비밀번호를 바꿀 수 있는 링크를 :email 전자우편 주소로 보낼 것입니다.',
    'reset_password_success' => '비밀번호를 바꿨습니다.',
    'email_reset_subject' => ':appName 비밀번호 바꾸기',
    'email_reset_text' => '비밀번호를 바꿉니다.',
    'email_reset_not_requested' => '비밀번호 재설정을 요청하지 않으셨다면 추가 조치가 필요하지 않습니다.',

    // Email Confirmation
    'email_confirm_subject' => ':appName 전자우편 인증을 확인합니다',
    'email_confirm_greeting' => ':appName 서비스에 가입해 주셔서 감사합니다!',
    'email_confirm_text' => '다음 버튼을 눌러 전자우편 주소를 확인하세요:',
    'email_confirm_action' => '전자우편 확인',
    'email_confirm_send_error' => '전자우편 확인이 필요하지만 이 시스템에서 전자우편을 발송하지 못했습니다. 전자우편 주소가 제대로 설정되었는지 확인하려면 관리자에게 연락을 해주세요.',
    'email_confirm_success' => '전자우편 인증을 성공했습니다. 이 전자우편 주소로 로그인할 수 있습니다.',
    'email_confirm_resent' => '전자우편 확인을 다시 보냈습니다. 우편함을 확인해주세요.',
    'email_confirm_thanks' => '확인해 주셔서 감사합니다!',
    'email_confirm_thanks_desc' => '확인이 처리되는 동안 잠시 기다려주세요. 3초 안에 리디렉션되지 않는다면 아내에 있는 "계속" 링크를 눌러서 진행하세요.',

    'email_not_confirmed' => '전자우편 주소가 확인되지 않았습니다.',
    'email_not_confirmed_text' => '전자우편 확인이 아직 완료되지 않았습니다.',
    'email_not_confirmed_click_link' => '등록한 직후에 발송된 전자우편에 있는 확인 링크를 클릭하세요.',
    'email_not_confirmed_resend' => '전자우편 확인을 위해 발송된 전자우편을 찾을 수 없다면 아래의 폼을 다시 발행하여 전자우편 확인을 재발송할 수 있습니다.',
    'email_not_confirmed_resend_button' => '전자우편 확인 재전송',

    // User Invite
    'user_invite_email_subject' => ':appName 애플리케이션에서 초대를 받았습니다!',
    'user_invite_email_greeting' => ':appName 애플리케이션에 가입한 기록이 있습니다.',
    'user_invite_email_text' => '아래 버튼을 클릭하여 계정 비밀번호를 설정하고 접근 권한을 얻으세요:',
    'user_invite_email_action' => '비밀번호 설정',
    'user_invite_page_welcome' => ':appName 애플리케이션에 오신 것을 환영합니다!',
    'user_invite_page_text' => '계정 설정을 마치고 접근 권한을 얻으려면 :appName 애플리케이션에 로그인할 때 사용할 비밀번호를 설정해야 합니다.',
    'user_invite_page_confirm_button' => '비밀번호 확인',
    'user_invite_success_login' => '비밀번호를 설정했습니다, 이제 입력한 비밀번호로 :appName 애플리케이션에 로그인할 수 있습니다!',

    // Multi-factor Authentication
    'mfa_setup' => '다중 인증 설정',
    'mfa_setup_desc' => '추가 보안 계층으로 다중 인증을 설정합니다.',
    'mfa_setup_configured' => '이미 설정되었습니다',
    'mfa_setup_reconfigure' => '다시 설정',
    'mfa_setup_remove_confirmation' => '다중 인증을 해제할까요?',
    'mfa_setup_action' => '설정',
    'mfa_backup_codes_usage_limit_warning' => '남은 백업 코드가 다섯 개 미만입니다. 새 백업 코드 세트를 생성하지 않아 코드가 소진되면 계정이 잠길 수 있습니다.',
    'mfa_option_totp_title' => '모바일 앱',
    'mfa_option_totp_desc' => '다중 인증에는 Google Authenticator, Authy나 Microsoft Authenticator와 같은 TOTP 지원 모바일 앱이 필요합니다.',
    'mfa_option_backup_codes_title' => '백업 코드',
    'mfa_option_backup_codes_desc' => '일회성 백업 코드를 안전한 장소에 보관하세요.',
    'mfa_gen_confirm_and_enable' => '확인 및 활성화',
    'mfa_gen_backup_codes_title' => '백업 코드 설정',
    'mfa_gen_backup_codes_desc' => '코드 목록을 안전한 장소에 보관하세요. 코드 중 하나를 2FA에 쓸 수 있습니다.',
    'mfa_gen_backup_codes_download' => '코드 내려받기',
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
    'mfa_verify_no_methods_desc' => '다중 인증을 설정하지 않았습니다. 접근 권한을 얻기 전에 하나 이상의 다중 인증을 설정해야 합니다.',
    'mfa_verify_use_totp' => '모바일 앱으로 인증하기',
    'mfa_verify_use_backup_codes' => '백업 코드로 인증하세요.',
    'mfa_verify_backup_code' => '백업 코드',
    'mfa_verify_backup_code_desc' => '나머지 백업 코드 중 하나를 입력하세요:',
    'mfa_verify_backup_code_enter_here' => '백업 코드를 입력하세요.',
    'mfa_verify_totp_desc' => '모바일 앱에서 생성한 백업 코드를 입력하세요:',
    'mfa_setup_login_notification' => '다중 인증을 설정했습니다. 설정한 방법으로 다시 로그인하세요.',
];
