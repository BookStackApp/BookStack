<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Thông tin đăng nhập này không khớp với dữ liệu của chúng tôi.',
    'throttle' => 'Quá nhiều lần đăng nhập sai. Vui lòng thử lại sau :seconds giây.',

    // Login & Register
    'sign_up' => 'Đăng ký',
    'log_in' => 'Đăng nhập',
    'log_in_with' => 'Đăng nhập với :socialDriver',
    'sign_up_with' => 'Đăng kí với :socialDriver',
    'logout' => 'Đăng xuất',

    'name' => 'Tên',
    'username' => 'Tên đăng nhập',
    'email' => 'Email',
    'password' => 'Mật khẩu',
    'password_confirm' => 'Xác nhận mật khẩu',
    'password_hint' => 'Cần tối thiểu 7 kí tự',
    'forgot_password' => 'Quên Mật khẩu?',
    'remember_me' => 'Ghi nhớ đăng nhập',
    'ldap_email_hint' => 'Vui lòng điền một địa chỉ email để sử dụng tài khoản này.',
    'create_account' => 'Tạo Tài khoản',
    'already_have_account' => 'Bạn đã có tài khoản?',
    'dont_have_account' => 'Bạn không có tài khoản?',
    'social_login' => 'Đăng nhập bằng MXH',
    'social_registration' => 'Đăng kí bằng MXH',
    'social_registration_text' => 'Đăng kí và đăng nhập bằng dịch vụ khác.',

    'register_thanks' => 'Cảm ơn bạn đã đăng ký!',
    'register_confirm' => 'Vui lòng kiểm tra email và bấm vào nút xác nhận để truy cập :appName.',
    'registrations_disabled' => 'Việc đăng kí đang bị tắt',
    'registration_email_domain_invalid' => 'Tên miền của email không có quyền truy cập tới ứng dụng này',
    'register_success' => 'Cảm ơn bạn đã đăng kí! Bạn đã được xác nhận và đăng nhập.',


    // Password Reset
    'reset_password' => 'Đặt lại mật khẩu',
    'reset_password_send_instructions' => 'Nhập email vào ô dưới đây và bạn sẽ nhận được một email với liên kết để đặt lại mật khẩu.',
    'reset_password_send_button' => 'Gửi liên kết đặt lại mật khẩu',
    'reset_password_sent' => 'Một đường dẫn đặt lại mật khẩu sẽ được gửi tới :email nếu địa chỉ email đó tồn tại trong hệ thống.',
    'reset_password_success' => 'Mật khẩu đã được đặt lại thành công.',
    'email_reset_subject' => 'Đặt lại mật khẩu của :appName',
    'email_reset_text' => 'Bạn nhận được email này bởi vì chúng tôi nhận được một yêu cầu đặt lại mật khẩu cho tài khoản của bạn.',
    'email_reset_not_requested' => 'Nếu bạn không yêu cầu đặt lại mật khẩu, không cần có bất cứ hành động nào khác.',


    // Email Confirmation
    'email_confirm_subject' => 'Xác nhận email trên :appName',
    'email_confirm_greeting' => 'Cảm ơn bạn đã tham gia :appName!',
    'email_confirm_text' => 'Xin hãy xác nhận địa chỉa email bằng cách bấm vào nút dưới đây:',
    'email_confirm_action' => 'Xác nhận Email',
    'email_confirm_send_error' => 'Email xác nhận cần gửi nhưng hệ thống đã không thể gửi được email. Liên hệ với quản trị viên để chắc chắn email được thiết lập đúng.',
    'email_confirm_success' => 'Email của bạn đã được xác nhận!',
    'email_confirm_resent' => 'Email xác nhận đã được gửi lại, Vui lòng kiểm tra hộp thư.',

    'email_not_confirmed' => 'Địa chỉ email chưa được xác nhận',
    'email_not_confirmed_text' => 'Địa chỉ email của bạn hiện vẫn chưa được xác nhận.',
    'email_not_confirmed_click_link' => 'Vui lòng bấm vào liên kết trong mail được gửi trong thời gian ngắn ngay sau khi bạn đăng kí.',
    'email_not_confirmed_resend' => 'Nếu bạn không tìm thấy email bạn có thể yêu cầu gửi lại email xác nhận bằng cách gửi mẫu dưới đây.',
    'email_not_confirmed_resend_button' => 'Gửi lại email xác nhận',

    // User Invite
    'user_invite_email_subject' => 'Bạn được mời tham gia :appName!',
    'user_invite_email_greeting' => 'Một tài khoản đã được tạo dành cho bạn trên :appName.',
    'user_invite_email_text' => 'Bấm vào nút dưới đây để đặt lại mật khẩu tài khoản và lấy quyền truy cập:',
    'user_invite_email_action' => 'Đặt mật khẩu tài khoản',
    'user_invite_page_welcome' => 'Chào mừng đến với :appName!',
    'user_invite_page_text' => 'Để hoàn tất tài khoản và lấy quyền truy cập bạn cần đặt mật khẩu để sử dụng cho các lần đăng nhập sắp tới tại :appName.',
    'user_invite_page_confirm_button' => 'Xác nhận Mật khẩu',
    'user_invite_success' => 'Mật khẩu đã được thiết lập, bạn có quyền truy cập đến :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Cài đặt xác thực nhiều bước',
    'mfa_setup_desc' => 'Cài đặt xác thực nhiều bước như một lớp bảo mật khác cho tài khoản của bạn.',
    'mfa_setup_configured' => 'Đã cài đặt',
    'mfa_setup_reconfigure' => 'Cài đặt lại',
    'mfa_setup_remove_confirmation' => 'Bạn có chắc muốn gỡ bỏ phương thức xác thực nhiều bước này?',
    'mfa_setup_action' => 'Cài đặt',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Ứng dụng di động',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Mã dự phòng',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Xác nhận và Mở',
    'mfa_gen_backup_codes_title' => 'Cài đặt Mã dự phòng',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Tải mã',
    'mfa_gen_backup_codes_usage_warning' => 'Mỗi mã chỉ có thể sử dụng một lần',
    'mfa_gen_totp_title' => 'Cài đặt ứng dụng di động',
    'mfa_gen_totp_desc' => 'Để sử dụng xác thực nhiều bước, bạn cần một ứng dụng di động hỗ trợ TOTP ví dụ như Google Authenticator, Authy hoặc Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Quét mã QR dưới đây bằng ứng dụng xác thực mà bạn muốn để bắt đầu.',
    'mfa_gen_totp_verify_setup' => 'Xác nhận cài đặt',
    'mfa_gen_totp_verify_setup_desc' => 'Xác nhận rằng tất cả hoạt động bằng cách nhập vào một mã, được tạo ra bởi ứng dụng xác thực của bạn vào ô dưới đây:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Mã dự phòng',
    'mfa_verify_backup_code_desc' => 'Nhập một trong các mã dự phòng còn lại của bạn vào ô phía dưới:',
    'mfa_verify_backup_code_enter_here' => 'Nhập mã xác thực của bạn tại đây',
    'mfa_verify_totp_desc' => 'Nhập mã do ứng dụng di động của bạn tạo ra vào dưới đây:',
    'mfa_setup_login_notification' => 'Đã cài đặt xác thực nhiều bước, bạn vui lòng đăng nhập lại sử dụng phương thức đã cài đặt.',
];