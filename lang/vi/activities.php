<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'đã tạo trang',
    'page_create_notification'    => 'Trang đã được tạo thành công',
    'page_update'                 => 'đã cập nhật trang',
    'page_update_notification'    => 'Trang đã được cập nhật thành công',
    'page_delete'                 => 'đã xóa trang',
    'page_delete_notification'    => 'Trang đã được xóa thành công',
    'page_restore'                => 'đã khôi phục trang',
    'page_restore_notification'   => 'Trang đã được khôi phục thành công',
    'page_move'                   => 'đã di chuyển trang',
    'page_move_notification'      => 'Đã di chuyển trang thành công',

    // Chapters
    'chapter_create'              => 'đã tạo chương',
    'chapter_create_notification' => 'Chương đã được tạo thành công',
    'chapter_update'              => 'đã cập nhật chương',
    'chapter_update_notification' => 'Chương đã được cập nhật thành công',
    'chapter_delete'              => 'đã xóa chương',
    'chapter_delete_notification' => 'Chương đã được xóa thành công',
    'chapter_move'                => 'đã di chuyển chương',
    'chapter_move_notification' => 'Đã chuyển chương thành công',

    // Books
    'book_create'                 => 'đã tạo sách',
    'book_create_notification'    => 'Sách đã được tạo thành công',
    'book_create_from_chapter'              => 'chuyển chương thành sách',
    'book_create_from_chapter_notification' => 'Chuyển chương thành sách thành công',
    'book_update'                 => 'đã cập nhật sách',
    'book_update_notification'    => 'Sách đã được cập nhật thành công',
    'book_delete'                 => 'đã xóa sách',
    'book_delete_notification'    => 'Sách đã được xóa thành công',
    'book_sort'                   => 'đã sắp xếp sách',
    'book_sort_notification'      => 'Sách đã được sắp xếp lại thành công',

    // Bookshelves
    'bookshelf_create'            => 'đã tạo giá sách',
    'bookshelf_create_notification'    => 'Giá sách đã được tạo thành công',
    'bookshelf_create_from_book'    => 'chuyển sách thành giá sách',
    'bookshelf_create_from_book_notification'    => 'Chuyển sách thành giá sách thành công',
    'bookshelf_update'                 => 'cập nhật giá sách',
    'bookshelf_update_notification'    => 'Giá sách được cập nhật thành công',
    'bookshelf_delete'                 => 'xoá giá sách',
    'bookshelf_delete_notification'    => 'Xoá giá sách thành công',

    // Revisions
    'revision_restore' => 'đã khôi phục sửa đổi',
    'revision_delete' => 'đã xóa bản sửa đổi',
    'revision_delete_notification' => 'Bản sửa đổi đã được xóa thành công',

    // Favourites
    'favourite_add_notification' => '":name" đã được thêm vào danh sách yêu thích của bạn',
    'favourite_remove_notification' => '":name" đã được gỡ khỏi danh sách yêu thích của bạn',

    // Watching
    'watch_update_level_notification' => 'Watch preferences successfully updated',

    // Auth
    'auth_login' => 'đăng nhập',
    'auth_register' => 'đã đăng ký như người dùng mới',
    'auth_password_reset_request' => 'yêu cầu người dùng đặt lại mật khẩu',
    'auth_password_reset_update' => 'đặt lại mật khẩu người dùng',
    'mfa_setup_method' => 'đã định cấu hình phương thức MFA',
    'mfa_setup_method_notification' => 'Cấu hình xác thực nhiều bước thành công',
    'mfa_remove_method' => 'loại bỏ phương thức MFA',
    'mfa_remove_method_notification' => 'Đã gỡ xác thực nhiều bước',

    // Settings
    'settings_update' => 'cập nhật cài đặt',
    'settings_update_notification' => 'Cài đặt đã cập nhật thành công',
    'maintenance_action_run' => 'chạy hành động bảo trì',

    // Webhooks
    'webhook_create' => 'đã tạo webhook',
    'webhook_create_notification' => 'Webhook đã được tạo thành công',
    'webhook_update' => 'đã cập nhật webhook',
    'webhook_update_notification' => 'Webhook đã được cập nhật thành công',
    'webhook_delete' => 'đã xóa webhook',
    'webhook_delete_notification' => 'Webhook đã được xóa thành công',

    // Users
    'user_create' => 'người dùng đã tạo',
    'user_create_notification' => 'Người dùng được tạo thành công',
    'user_update' => 'người dùng được cập nhật',
    'user_update_notification' => 'Người dùng được cập nhật thành công',
    'user_delete' => 'người dùng đã bị xóa',
    'user_delete_notification' => 'Người dùng đã được xóa thành công',

    // API Tokens
    'api_token_create' => 'created api token',
    'api_token_create_notification' => 'API token successfully created',
    'api_token_update' => 'updated api token',
    'api_token_update_notification' => 'API token successfully updated',
    'api_token_delete' => 'deleted api token',
    'api_token_delete_notification' => 'API token successfully deleted',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Vai trò mới đã được tạo thành công',
    'role_update' => 'updated role',
    'role_update_notification' => 'Vai trò đã được cập nhật thành công',
    'role_delete' => 'đã xóa vai trò',
    'role_delete_notification' => 'Vai trò đã được xóa thành công',

    // Recycle Bin
    'recycle_bin_empty' => 'làm trống thùng rác',
    'recycle_bin_restore' => 'khôi phục từ thùng rác',
    'recycle_bin_destroy' => 'đã xóa khỏi thùng rác',

    // Comments
    'commented_on'                => 'đã bình luận về',
    'comment_create'              => 'thêm bình luận',
    'comment_update'              => 'cập nhật bình luận',
    'comment_delete'              => 'đã xóa bình luận',

    // Other
    'permissions_update'          => 'các quyền đã được cập nhật',
];
