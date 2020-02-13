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
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'Không có hành động được xác định',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => 'This :socialAccount account is already in use, Try logging in via the :socialAccount option.',
    'social_account_email_in_use' => 'The email :email is already in use. If you already have an account you can connect your :socialAccount account from your profile settings.',
    'social_account_existing' => 'This :socialAccount is already attached to your profile.',
    'social_account_already_used_existing' => 'This :socialAccount account is already used by another user.',
    'social_account_not_used' => 'This :socialAccount account is not linked to any users. Please attach it in your profile settings. ',
    'social_account_register_instructions' => 'If you do not yet have an account, You can register an account using the :socialAccount option.',
    'social_driver_not_found' => 'Social driver not found',
    'social_driver_not_configured' => 'Your :socialAccount social settings are not configured correctly.',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'File path :filePath could not be uploaded to. Ensure it is writable to the server.',
    'cannot_get_image_from_url' => 'Cannot get image from :url',
    'cannot_create_thumbs' => 'The server cannot create thumbnails. Please check you have the GD PHP extension installed.',
    'server_upload_limit' => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'uploaded'  => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'image_upload_error' => 'An error occurred uploading the image',
    'image_upload_type_error' => 'The image type being uploaded is invalid',
    'file_upload_timeout' => 'The file upload has timed out.',

    // Attachments
    'attachment_page_mismatch' => 'Page mismatch during attachment update',
    'attachment_not_found' => 'Attachment not found',

    // Pages
    'page_draft_autosave_fail' => 'Failed to save draft. Ensure you have internet connection before saving this page',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Không tìm thấy giá sách',
    'book_not_found' => 'Không tìm thấy sách',
    'page_not_found' => 'Không tìm thấy trang',
    'chapter_not_found' => 'Không tìm thấy chương',
    'selected_book_not_found' => 'The selected book was not found',
    'selected_book_chapter_not_found' => 'The selected Book or Chapter was not found',
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
    'comment_add' => 'An error occurred while adding / updating the comment.',
    'comment_delete' => 'An error occurred while deleting the comment.',
    'empty_comment' => 'Cannot add an empty comment.',

    // Error pages
    '404_page_not_found' => 'Không Tìm Thấy Trang',
    'sorry_page_not_found' => 'Xin lỗi, Không tìm thấy trang bạn đang tìm kiếm.',
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

];
