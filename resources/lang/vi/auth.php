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
    'reset_password_sent_success' => 'Một liên kết đặt lại mật khẩu đã được gửi tới :email.',
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
    'user_invite_success' => 'Mật khẩu đã được thiết lập, bạn có quyền truy cập đến :appName!'
];