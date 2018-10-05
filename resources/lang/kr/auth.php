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
    'failed' => '이 자격 증명은 등록되어 있지 않습니다.',
    'throttle' => '로그인 시도 횟수 제한을 초과했습니다. :seconds초 후에 다시 시도하십시오.',

    /**
     * Login & Register
     */
    'sign_up' => '신규등록',
    'log_in' => '로그인',
    'log_in_with' => ':socialDriver에 로그인',
    'sign_up_with' => ':socialDriver로 등록',
    'logout' => '로그아웃',

    'name' => '이름',
    'username' => '사용자이름',
    'email' => '이메일',
    'password' => '비밀번호',
    'password_confirm' => '비밀번호 (확인)',
    'password_hint' => '5자 이상이어야 합니다.',
    'forgot_password' => '비밀번호를 잊으셨습니까?',
    'remember_me' => '자동로그인',
    'ldap_email_hint' => '이 계정에서 사용하는 이메일을 입력해 주세요.',
    'create_account' => '계정 만들기',
    'social_login' => 'SNS로그인',
    'social_registration' => 'SNS등록',
    'social_registration_text' => '다른 서비스를 사용하여 등록하고 로그인.',

    'register_thanks' => '등록이완료되었습니다!',
    'register_confirm' => '당신의 이메일을 확인하신후 확인 버튼을 눌러 :appName에 액세스하십시오.',
    'registrations_disabled' => '현재 등록이 불가합니다.',
    'registration_email_domain_invalid' => '해당 이메일 도메인으로 액세스 할 수 없습니다.',
    'register_success' => '등록을 완료하고 로그인 할 수 있습니다!',


    /**
     * Password Reset
     */
    'reset_password' => '암호 재설정',
    'reset_password_send_instructions' => '다음에 메일 주소를 입력하면 비밀번호 재설정 링크가 포함 된 이메일이 전송됩니다.',
    'reset_password_send_button' => '재설정 링크 보내기',
    'reset_password_sent_success' => ':email로 재설정 링크를 보냈습니다.',
    'reset_password_success' => '비밀번호가 재설정되었습니다.',

    'email_reset_subject' => ':appName 암호를 재설정',
    'email_reset_text' => '귀하의 계정에 대한 비밀번호 재설정 요청을 받았기 때문에 본 이메일이 발송되었습니다.',
    'email_reset_not_requested' => '암호 재설정을 요청하지 않은 경우 더 이상의 조치는 필요하지 않습니다.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => ':appName의 이메일 주소 확인',
    'email_confirm_greeting' => ':appName에 가입 ​​해 주셔서 감사합니다!',
    'email_confirm_text' => '다음 버튼을 눌러 이메일 주소를 확인하십시오',
    'email_confirm_action' => '이메일 주소를 확인',
    'email_confirm_send_error' => 'E메일 확인이 필요하지만 시스템에서 메일을 보낼 수 없습니다. 관리자에게 문의하여 메일이 제대로 설정되어 있는지 확인하십시오.',
    'email_confirm_success' => '메일 주소가 확인되었습니다.',
    'email_confirm_resent' => '확인 메일을 다시 보냈습니다. 받은 편지함을 확인하십시오.',

    'email_not_confirmed' => '메일 주소가 확인되지 않습니다',
    'email_not_confirmed_text' => '메일 주소 확인이 완료되지 않습니다.',
    'email_not_confirmed_click_link' => '등록시 받은 이메일을 확인하고 확인 링크를 클릭하십시오.',
    'email_not_confirmed_resend' => '메일이 없으면 아래 양식을 통해 다시 제출하십시오.',
    'email_not_confirmed_resend_button' => '확인 메일을 다시 전송',
];
