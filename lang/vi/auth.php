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
    'password_hint' => 'Phải có ít nhất 8 ký tự',
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

    // Login auto-initiation
    'auto_init_starting' => 'Đang thử đăng nhập',
    'auto_init_starting_desc' => 'Chúng tôi đang liên lạc với hệ thống xác thực của bạn để bắt đầu quá trình đăng nhập. Nếu sau 5 giây không có tiến triển, bạn có thể thử nhấp vào liên kết dưới đây.',
    'auto_init_start_link' => 'Tiến hành xác thực',

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
    'email_confirm_success' => 'Email của bạn đã được xác nhận! Bạn có thể đăng nhập với email này ngay bây giờ.',
    'email_confirm_resent' => 'Email xác nhận đã được gửi lại, Vui lòng kiểm tra hộp thư.',
    'email_confirm_thanks' => 'Cảm ơn bạn đã xác nhận!',
    'email_confirm_thanks_desc' => 'Vui lòng chờ trong giây lát khi yêu cầu xác nhận của bạn được xử lý. Nếu sau 3 giây bạn không được chuyển hướng, nhấp vào liên kết "Tiếp tục" dưới đây để tiếp tục.',

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
    'user_invite_success_login' => 'Đã đặt mật khẩu, bạn có thể đăng nhập với mật khẩu trên để truy cập :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Cài đặt xác thực nhiều bước',
    'mfa_setup_desc' => 'Cài đặt xác thực nhiều bước như một lớp bảo mật khác cho tài khoản của bạn.',
    'mfa_setup_configured' => 'Đã cài đặt',
    'mfa_setup_reconfigure' => 'Cài đặt lại',
    'mfa_setup_remove_confirmation' => 'Bạn có chắc muốn gỡ bỏ phương thức xác thực nhiều bước này?',
    'mfa_setup_action' => 'Cài đặt',
    'mfa_backup_codes_usage_limit_warning' => 'Bạn có ít hơn 5 mã dự phòng, Xin vui lòng tạo và lưu trữ bộ mã mới trước khi bạn dùng hết mã để tránh việc bị khóa quyền truy cập tài khoản.',
    'mfa_option_totp_title' => 'Ứng dụng di động',
    'mfa_option_totp_desc' => 'Để sử dụng xác thực đa lớp bạn cần ưng dụng trên điện thoại có hỗ trợ TOTP như Google Authenticator, Authy hoặc Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Mã dự phòng',
    'mfa_option_backup_codes_desc' => 'Bảo mật việc lưu trữ mã dự phòng dùng một lần mà bạn có thể sử dụng để xác định danh tính của mình.',
    'mfa_gen_confirm_and_enable' => 'Xác nhận và Mở',
    'mfa_gen_backup_codes_title' => 'Cài đặt Mã dự phòng',
    'mfa_gen_backup_codes_desc' => 'Lưu trữ các mã dưới đây ở một nơi an toàn. Khi truy cập vào hệ thống bạn sẽ có thể sử dụng được một trong các đoạn mã đó như là một phương thức xác thực dự phòng.',
    'mfa_gen_backup_codes_download' => 'Tải mã',
    'mfa_gen_backup_codes_usage_warning' => 'Mỗi mã chỉ có thể sử dụng một lần',
    'mfa_gen_totp_title' => 'Cài đặt ứng dụng di động',
    'mfa_gen_totp_desc' => 'Để sử dụng xác thực nhiều bước, bạn cần một ứng dụng di động hỗ trợ TOTP ví dụ như Google Authenticator, Authy hoặc Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Quét mã QR dưới đây bằng ứng dụng xác thực mà bạn muốn để bắt đầu.',
    'mfa_gen_totp_verify_setup' => 'Xác nhận cài đặt',
    'mfa_gen_totp_verify_setup_desc' => 'Xác nhận rằng tất cả hoạt động bằng cách nhập vào một mã, được tạo ra bởi ứng dụng xác thực của bạn vào ô dưới đây:',
    'mfa_gen_totp_provide_code_here' => 'Cung cấp mã bạn tạo được từ ứng dụng ở đây',
    'mfa_verify_access' => 'Xác thực truy cập',
    'mfa_verify_access_desc' => 'Tài khoản của bạn cần xác nhận danh tính của bạn thông qua một lớp xác thực bổ sung trước khi bạn được cấp quyền truy cập. Xác thực qua việc sử dụng một trong các phương thức để tiếp tục.',
    'mfa_verify_no_methods' => 'Không có phương pháp nào được cấu hình',
    'mfa_verify_no_methods_desc' => 'Tài khoản của bạn chưa đăng ký xác thực nhiều lớp. Bạn cần thiết lập ít nhất một phương pháp trước khi yêu cầu truy cập.',
    'mfa_verify_use_totp' => 'Xác thực sử dụng mã di động',
    'mfa_verify_use_backup_codes' => 'Xác thực sử dụng mã backup',
    'mfa_verify_backup_code' => 'Mã dự phòng',
    'mfa_verify_backup_code_desc' => 'Nhập một trong các mã dự phòng còn lại của bạn vào ô phía dưới:',
    'mfa_verify_backup_code_enter_here' => 'Nhập mã xác thực của bạn tại đây',
    'mfa_verify_totp_desc' => 'Nhập mã do ứng dụng di động của bạn tạo ra vào dưới đây:',
    'mfa_setup_login_notification' => 'Đã cài đặt xác thực nhiều bước, bạn vui lòng đăng nhập lại sử dụng phương thức đã cài đặt.',
];
