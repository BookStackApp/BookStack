<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Bạn không có quyền truy cập đến trang này.',
    'permissionJson' => 'Bạn không có quyền để thực hiện hành động này.',

    // Auth
    'error_user_exists_different_creds' => 'Đã có người sử dụng email :email nhưng với thông tin định danh khác.',
    'email_already_confirmed' => 'Email đã được xác nhận trước đó, Đang đăng nhập.',
    'email_confirmation_invalid' => 'Token xác nhận này không hợp lệ hoặc đã được sử dụng trước đó, Xin hãy thử đăng ký lại.',
    'email_confirmation_expired' => 'Token xác nhận đã hết hạn, Một email xác nhận mới đã được gửi.',
    'email_confirmation_awaiting' => 'Địa chỉ email của tài khoản bạn đang sử dụng cần phải được xác nhận',
    'ldap_fail_anonymous' => 'Truy cập đến LDAP sử dụng gán ẩn danh thất bại',
    'ldap_fail_authed' => 'Truy cập đến LDAP sử dụng dn và mật khẩu thất bại',
    'ldap_extension_not_installed' => 'Tiện ích mở rộng LDAP PHP chưa được cài đặt',
    'ldap_cannot_connect' => 'Không thể kết nối đến máy chủ LDAP, mở đầu kết nối thất bại',
    'saml_already_logged_in' => 'Đã đăng nhập',
    'saml_user_not_registered' => 'Người dùng :name chưa được đăng ký và tự động đăng ký đang bị tắt',
    'saml_no_email_address' => 'Không tìm thấy địa chỉ email cho người dùng này trong dữ liệu được cung cấp bới hệ thống xác thực ngoài',
    'saml_invalid_response_id' => 'Yêu cầu từ hệ thống xác thực bên ngoài không được nhận diện bởi quy trình chạy cho ứng dụng này. Điều hướng trở lại sau khi đăng nhập có thể đã gây ra vấn đề này.',
    'saml_fail_authed' => 'Đăng nhập sử dụng :system thất bại, hệ thống không cung cấp được sự xác thực thành công',
    'oidc_already_logged_in' => 'Đã đăng nhập',
    'oidc_user_not_registered' => 'Người dùng :name chưa được đăng ký và tự động đăng ký đang bị tắt',
    'oidc_no_email_address' => 'Không tìm thấy địa chỉ email cho người dùng này, trong dữ liệu được cung cấp bới hệ thống xác thực ngoài',
    'oidc_fail_authed' => 'Đăng nhập sử dụng :system thất bại, hệ thống không cung cấp được sự xác thực thành công',
    'social_no_action_defined' => 'Không có hành động được xác định',
    'social_login_bad_response' => "Xảy ra lỗi trong lúc đăng nhập :socialAccount: \n:error",
    'social_account_in_use' => 'Tài khoản :socialAccount này đang được sử dụng, Vui lòng thử đăng nhập bằng tùy chọn :socialAccount.',
    'social_account_email_in_use' => 'Địa chỉ email :email đã được sử dụng. Nếu bạn đã có tài khoản bạn có thể kết nối đến tài khoản :socialAccount của mình từ cài đặt cá nhân của bạn.',
    'social_account_existing' => ':socialAccount đã được gắn với hồ sơ của bạn từ trước.',
    'social_account_already_used_existing' => 'Tài khoản :socialAccount đã được sử dụng bởi một người dùng khác.',
    'social_account_not_used' => 'Tài khoản :socialAccount này chưa được liên kết bởi bất cứ người dùng nào. Vui lòng liên kết nó tại cài đặt cá nhân của bạn. ',
    'social_account_register_instructions' => 'Nếu bạn chưa có tài khoản, Bạn có thể đăng ký một tài khoản bằng tùy chọn :socialAccount.',
    'social_driver_not_found' => 'Không tìm thấy driver cho MXH',
    'social_driver_not_configured' => 'Cài đặt MXH :socialAccount của bạn đang không được cấu hình hợp lệ.',
    'invite_token_expired' => 'Liên kết mời này đã hết hạn. Bạn có thể thử đặt lại mật khẩu của tài khoản.',

    // System
    'path_not_writable' => 'Đường dẫn tệp tin :filePath không thể tải đến được. Đảm bảo rằng đường dẫn này có thể ghi được ở trên máy chủ.',
    'cannot_get_image_from_url' => 'Không thể lấy ảnh từ :url',
    'cannot_create_thumbs' => 'Máy chủ không thể tạo ảnh nhỏ. Vui lòng kiểm tra bạn đã cài đặt tiện ích mở rộng GD PHP.',
    'server_upload_limit' => 'Máy chủ không cho phép tải lên kích thước này. Vui lòng thử lại với tệp tin nhỏ hơn.',
    'uploaded'  => 'Máy chủ không cho phép tải lên kích thước này. Vui lòng thử lại với tệp tin nhỏ hơn.',
    'image_upload_error' => 'Đã xảy ra lỗi khi đang tải lên ảnh',
    'image_upload_type_error' => 'Ảnh đang được tải lên không hợp lệ',
    'file_upload_timeout' => 'Đã quá thời gian tải lên tệp tin.',

    // Attachments
    'attachment_not_found' => 'Không tìm thấy đính kèm',

    // Pages
    'page_draft_autosave_fail' => 'Lưu bản nháp thất bại. Đảm bảo rằng bạn có kết nối đến internet trước khi lưu trang này',
    'page_custom_home_deletion' => 'Không thể xóa trang khi nó đang được đặt là trang chủ',

    // Entities
    'entity_not_found' => 'Không tìm thấy thực thể',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Không tìm thấy sách',
    'page_not_found' => 'Không tìm thấy trang',
    'chapter_not_found' => 'Không tìm thấy chương',
    'selected_book_not_found' => 'Không tìm thấy sách được chọn',
    'selected_book_chapter_not_found' => 'Không tìm thấy Sách hoặc Chương được chọn',
    'guests_cannot_save_drafts' => 'Khách không thể lưu bản nháp',

    // Users
    'users_cannot_delete_only_admin' => 'Bạn không thể xóa quản trị viên duy nhất',
    'users_cannot_delete_guest' => 'Bạn không thể xóa người dùng khách',

    // Roles
    'role_cannot_be_edited' => 'Không thể chỉnh sửa quyền này',
    'role_system_cannot_be_deleted' => 'Quyền này là quyền hệ thống và không thể bị xóa',
    'role_registration_default_cannot_delete' => 'Quyền này không thể bị xóa trong khi đang đặt là quyền mặc định khi đăng ký',
    'role_cannot_remove_only_admin' => 'Người dùng này là người dùng duy nhất được chỉ định quyền quản trị viên. Gán quyền quản trị viên cho người dùng khác trước khi thử xóa người dùng này.',

    // Comments
    'comment_list' => 'Đã có lỗi xảy ra khi tải bình luận.',
    'cannot_add_comment_to_draft' => 'Bạn không thể thêm bình luận vào bản nháp.',
    'comment_add' => 'Đã xảy ra lỗi khi thêm / sửa bình luận.',
    'comment_delete' => 'Đã xảy ra lỗi khi xóa bình luận.',
    'empty_comment' => 'Không thể thêm bình luận bị bỏ trống.',

    // Error pages
    '404_page_not_found' => 'Không Tìm Thấy Trang',
    'sorry_page_not_found' => 'Xin lỗi, Không tìm thấy trang bạn đang tìm kiếm.',
    'sorry_page_not_found_permission_warning' => 'Nếu trang bạn tìm kiếm tồn tại, có thể bạn đang không có quyền truy cập.',
    'image_not_found' => 'Không tìm thấy Ảnh',
    'image_not_found_subtitle' => 'Rất tiếc, không thể tìm thấy Ảnh bạn đang tìm kiếm.',
    'image_not_found_details' => 'Nếu bạn hi vọng ảnh này tồn tại, rất có thể nó đã bị xóa.',
    'return_home' => 'Quay lại trang chủ',
    'error_occurred' => 'Đã xảy ra lỗi',
    'app_down' => ':appName hiện đang ngoại tuyến',
    'back_soon' => 'Nó sẽ sớm hoạt động trở lại.',

    // API errors
    'api_no_authorization_found' => 'Không tìm thấy token ủy quyền trong yêu cầu',
    'api_bad_authorization_format' => 'Đã tìm thấy một token ủy quyền trong yêu cầu nhưng định dạng hiển thị không hợp lệ',
    'api_user_token_not_found' => 'Không tìm thấy token API nào khớp với token ủy quyền được cung cấp',
    'api_incorrect_token_secret' => 'Mã bí mật được cung cấp cho token API đang được sử dụng không hợp lệ',
    'api_user_no_api_permission' => 'Chủ của token API đang sử dụng không có quyền gọi API',
    'api_user_token_expired' => 'Token sử dụng cho việc ủy quyền đã hết hạn',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Lỗi khi gửi email thử:',

];
