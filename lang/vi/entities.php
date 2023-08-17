<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Được tạo gần đây',
    'recently_created_pages' => 'Trang được tạo gần đây',
    'recently_updated_pages' => 'Trang được cập nhật gần đây',
    'recently_created_chapters' => 'Chương được tạo gần đây',
    'recently_created_books' => 'Sách được tạo gần đây',
    'recently_created_shelves' => 'Giá sách được tạo gần đây',
    'recently_update' => 'Được cập nhật gần đây',
    'recently_viewed' => 'Được xem gần đây',
    'recent_activity' => 'Hoạt động gần đây',
    'create_now' => 'Tạo ngay',
    'revisions' => 'Phiên bản',
    'meta_revision' => 'Phiên bản #:revisionCount',
    'meta_created' => 'Được tạo :timeLength',
    'meta_created_name' => 'Được tạo :timeLength bởi :user',
    'meta_updated' => 'Được cập nhật :timeLength',
    'meta_updated_name' => 'Được cập nhật :timeLength bởi :user',
    'meta_owned_name' => 'Được sở hữu bởi :user',
    'meta_reference_page_count' => 'Được tham chiếu trên :count page|Được tham chiếu trên :count pages',
    'entity_select' => 'Chọn thực thể',
    'entity_select_lack_permission' => 'Bạn không có quyền để chọn mục này',
    'images' => 'Ảnh',
    'my_recent_drafts' => 'Bản nháp gần đây của tôi',
    'my_recently_viewed' => 'Xem gần đây',
    'my_most_viewed_favourites' => 'Yêu thích được tôi xem nhiều nhất',
    'my_favourites' => 'Danh sách yêu thích của tôi',
    'no_pages_viewed' => 'Bạn chưa xem bất cứ trang nào',
    'no_pages_recently_created' => 'Không có trang nào được tạo gần đây',
    'no_pages_recently_updated' => 'Không có trang nào được cập nhật gần đây',
    'export' => 'Kết xuất',
    'export_html' => 'Đang chứa tệp tin Web',
    'export_pdf' => 'Tệp PDF',
    'export_text' => 'Tệp văn bản thuần túy',
    'export_md' => 'Tệp Markdown',

    // Permissions and restrictions
    'permissions' => 'Quyền',
    'permissions_desc' => 'Đặt quyền ở đây để ghi đè các quyền mặc định do vai trò người dùng cung cấp.',
    'permissions_book_cascade' => 'Quyền được đặt trên sách sẽ tự động xếp tầng cho các chương và trang con, trừ khi chúng được xác định quyền riêng.',
    'permissions_chapter_cascade' => 'Quyền được đặt trên các chương sẽ tự động xếp tầng cho các trang con, trừ khi chúng được xác định quyền riêng.',
    'permissions_save' => 'Lưu quyền hạn',
    'permissions_owner' => 'Chủ sở hữu',
    'permissions_role_everyone_else' => 'Những người khác',
    'permissions_role_everyone_else_desc' => 'Đặt quyền cho tất cả vai trò không được ghi đè cụ thể.',
    'permissions_role_override' => 'Ghi đè quyền cho vai trò',
    'permissions_inherit_defaults' => 'Kế thừa giá trị mặc định',

    // Search
    'search_results' => 'Kết quả Tìm kiếm',
    'search_total_results_found' => 'Tìm thấy :count kết quả|:count tổng kết quả',
    'search_clear' => 'Xoá tìm kiếm',
    'search_no_pages' => 'Không trang nào khớp với tìm kiếm này',
    'search_for_term' => 'Tìm kiếm cho :term',
    'search_more' => 'Thêm kết quả',
    'search_advanced' => 'Tìm kiếm Nâng cao',
    'search_terms' => 'Cụm từ Tìm kiếm',
    'search_content_type' => 'Kiểu Nội dung',
    'search_exact_matches' => 'Hoàn toàn trùng khớp',
    'search_tags' => 'Tìm kiếm Tag',
    'search_options' => 'Tuỳ chọn',
    'search_viewed_by_me' => 'Được xem bởi tôi',
    'search_not_viewed_by_me' => 'Không được xem bởi tôi',
    'search_permissions_set' => 'Phân quyền',
    'search_created_by_me' => 'Được tạo bởi tôi',
    'search_updated_by_me' => 'Được cập nhật bởi tôi',
    'search_owned_by_me' => 'Của tôi',
    'search_date_options' => 'Tùy chọn ngày',
    'search_updated_before' => 'Đã được cập nhật trước đó',
    'search_updated_after' => 'Đã được cập nhật sau',
    'search_created_before' => 'Đã được tạo trước',
    'search_created_after' => 'Đã được tạo sau',
    'search_set_date' => 'Đặt ngày',
    'search_update' => 'Cập nhật tìm kiếm',

    // Shelves
    'shelf' => 'Giá',
    'shelves' => 'Giá',
    'x_shelves' => ':count Giá |:count Giá',
    'shelves_empty' => 'Không có giá nào được tạo',
    'shelves_create' => 'Tạo Giá mới',
    'shelves_popular' => 'Các Giá phổ biến',
    'shelves_new' => 'Các Giá mới',
    'shelves_new_action' => 'Giá mới',
    'shelves_popular_empty' => 'Các giá phổ biến sẽ xuất hiện ở đây.',
    'shelves_new_empty' => 'Các Giá được tạo gần đây sẽ xuất hiện ở đây.',
    'shelves_save' => 'Lưu Giá',
    'shelves_books' => 'Sách trên Giá này',
    'shelves_add_books' => 'Thêm sách vào Giá này',
    'shelves_drag_books' => 'Kéo sách bên dưới để thêm vào kệ sách này',
    'shelves_empty_contents' => 'Giá này không có sách nào',
    'shelves_edit_and_assign' => 'Chỉnh sửa kệ để gán sách',
    'shelves_edit_named' => 'Chỉnh sửa kệ :name',
    'shelves_edit' => 'Chỉnh sửa kệ',
    'shelves_delete' => 'Xóa kệ',
    'shelves_delete_named' => 'Xóa kệ :name',
    'shelves_delete_explain' => "Thao tác này sẽ xóa kệ có tên ':name'. Sách chứa sẽ không bị xóa.",
    'shelves_delete_confirmation' => 'Are you sure you want to delete this shelf?',
    'shelves_permissions' => 'Shelf Permissions',
    'shelves_permissions_updated' => 'Shelf Permissions Updated',
    'shelves_permissions_active' => 'Shelf Permissions Active',
    'shelves_permissions_cascade_warning' => 'Permissions on shelves do not automatically cascade to contained books. This is because a book can exist on multiple shelves. Permissions can however be copied down to child books using the option found below.',
    'shelves_permissions_create' => 'Shelf create permissions are only used for copying permissions to child books using the action below. They do not control the ability to create books.',
    'shelves_copy_permissions_to_books' => 'Sao chép các quyền cho sách',
    'shelves_copy_permissions' => 'Sao chép các quyền',
    'shelves_copy_permissions_explain' => 'This will apply the current permission settings of this shelf to all books contained within. Before activating, ensure any changes to the permissions of this shelf have been saved.',
    'shelves_copy_permission_success' => 'Shelf permissions copied to :count books',

    // Books
    'book' => 'Sách',
    'books' => 'Tất cả sách',
    'x_books' => ':count Sách|:count Tất cả sách',
    'books_empty' => 'Không có cuốn sách nào được tạo',
    'books_popular' => 'Những cuốn sách phổ biến',
    'books_recent' => 'Những cuốn sách gần đây',
    'books_new' => 'Những cuốn sách mới',
    'books_new_action' => 'Sách mới',
    'books_popular_empty' => 'Những cuốn sách phổ biến nhất sẽ xuất hiện ở đây.',
    'books_new_empty' => 'Những cuốn sách tạo gần đây sẽ được xuất hiện ở đây.',
    'books_create' => 'Tạo cuốn sách mới',
    'books_delete' => 'Xóa sách',
    'books_delete_named' => 'Xóa sách :bookName',
    'books_delete_explain' => 'Điều này sẽ xóa cuốn sách với tên \':bookName\'. Tất cả các trang và các chương sẽ bị xóa.',
    'books_delete_confirmation' => 'Bạn có chắc chắn muốn xóa cuốn sách này?',
    'books_edit' => 'Sửa sách',
    'books_edit_named' => 'Sửa sách :bookName',
    'books_form_book_name' => 'Tên sách',
    'books_save' => 'Lưu sách',
    'books_permissions' => 'Các quyền của cuốn sách',
    'books_permissions_updated' => 'Các quyền của cuốn sách đã được cập nhật',
    'books_empty_contents' => 'Không có trang hay chương nào được tạo cho cuốn sách này.',
    'books_empty_create_page' => 'Tao một trang mới',
    'books_empty_sort_current_book' => 'Sắp xếp cuốn sách này',
    'books_empty_add_chapter' => 'Thêm một chương mới',
    'books_permissions_active' => 'Đang bật các quyền hạn từ Sách',
    'books_search_this' => 'Tìm cuốn sách này',
    'books_navigation' => 'Điều hướng cuốn sách',
    'books_sort' => 'Sắp xếp nội dung cuốn sách',
    'books_sort_desc' => 'Move chapters and pages within a book to reorganise its contents. Other books can be added which allows easy moving of chapters and pages between books.',
    'books_sort_named' => 'Sắp xếp sách :bookName',
    'books_sort_name' => 'Sắp xếp theo tên',
    'books_sort_created' => 'Sắp xếp theo ngày tạo',
    'books_sort_updated' => 'Sắp xếp theo ngày cập nhật',
    'books_sort_chapters_first' => 'Các Chương đầu',
    'books_sort_chapters_last' => 'Các Chương cuối',
    'books_sort_show_other' => 'Hiển thị các Sách khác',
    'books_sort_save' => 'Lưu thứ tự mới',
    'books_sort_show_other_desc' => 'Add other books here to include them in the sort operation, and allow easy cross-book reorganisation.',
    'books_sort_move_up' => 'Move Up',
    'books_sort_move_down' => 'Move Down',
    'books_sort_move_prev_book' => 'Move to Previous Book',
    'books_sort_move_next_book' => 'Move to Next Book',
    'books_sort_move_prev_chapter' => 'Move Into Previous Chapter',
    'books_sort_move_next_chapter' => 'Move Into Next Chapter',
    'books_sort_move_book_start' => 'Move to Start of Book',
    'books_sort_move_book_end' => 'Move to End of Book',
    'books_sort_move_before_chapter' => 'Move to Before Chapter',
    'books_sort_move_after_chapter' => 'Move to After Chapter',
    'books_copy' => 'Copy Book',
    'books_copy_success' => 'Book successfully copied',

    // Chapters
    'chapter' => 'Chương',
    'chapters' => 'Các chương',
    'x_chapters' => ':count Chương|:count Chương',
    'chapters_popular' => 'Các Chương phổ biến',
    'chapters_new' => 'Chương mới',
    'chapters_create' => 'Tạo Chương mới',
    'chapters_delete' => 'Xóa Chương',
    'chapters_delete_named' => 'Xóa Chương :chapterName',
    'chapters_delete_explain' => 'Hành động này sẽ xoá chương \':chapterName\'. Tất cả các trang trong chương này cũng sẽ bị xoá.',
    'chapters_delete_confirm' => 'Bạn có chắc chắn muốn xóa chương này?',
    'chapters_edit' => 'Sửa Chương',
    'chapters_edit_named' => 'Sửa chương :chapterName',
    'chapters_save' => 'Lưu Chương',
    'chapters_move' => 'Di chuyển Chương',
    'chapters_move_named' => 'Di chuyển Chương :chapterName',
    'chapters_copy' => 'Copy Chapter',
    'chapters_copy_success' => 'Chapter successfully copied',
    'chapters_permissions' => 'Quyền hạn Chương',
    'chapters_empty' => 'Không có trang nào hiện có trong chương này.',
    'chapters_permissions_active' => 'Đang bật các quyền hạn từ Chương',
    'chapters_permissions_success' => 'Quyền hạn Chương được cập nhật',
    'chapters_search_this' => 'Tìm kiếm trong Chương này',
    'chapter_sort_book' => 'Sort Book',

    // Pages
    'page' => 'Trang',
    'pages' => 'Các trang',
    'x_pages' => ':count Trang|:count Trang',
    'pages_popular' => 'Các Trang phổ biến',
    'pages_new' => 'Trang Mới',
    'pages_attachments' => 'Các đính kèm',
    'pages_navigation' => 'Điều hướng Trang',
    'pages_delete' => 'Xóa Trang',
    'pages_delete_named' => 'Xóa Trang :pageName',
    'pages_delete_draft_named' => 'Xóa Trang Nháp :pageName',
    'pages_delete_draft' => 'Xóa Trang Nháp',
    'pages_delete_success' => 'Đã xóa Trang',
    'pages_delete_draft_success' => 'Đã xóa trang Nháp',
    'pages_delete_confirm' => 'Bạn có chắc chắn muốn xóa trang này?',
    'pages_delete_draft_confirm' => 'Bạn có chắc chắn muốn xóa trang nháp này?',
    'pages_editing_named' => 'Đang chỉnh sửa Trang :pageName',
    'pages_edit_draft_options' => 'Tùy chọn bản nháp',
    'pages_edit_save_draft' => 'Lưu Nháp',
    'pages_edit_draft' => 'Sửa trang nháp',
    'pages_editing_draft' => 'Đang chỉnh sửa Nháp',
    'pages_editing_page' => 'Đang chỉnh sửa Trang',
    'pages_edit_draft_save_at' => 'Bản nháp đã lưu lúc ',
    'pages_edit_delete_draft' => 'Xóa Bản nháp',
    'pages_edit_delete_draft_confirm' => 'Are you sure you want to delete your draft page changes? All of your changes, since the last full save, will be lost and the editor will be updated with the latest page non-draft save state.',
    'pages_edit_discard_draft' => 'Hủy bỏ Bản nháp',
    'pages_edit_switch_to_markdown' => 'Switch to Markdown Editor',
    'pages_edit_switch_to_markdown_clean' => '(Clean Content)',
    'pages_edit_switch_to_markdown_stable' => '(Stable Content)',
    'pages_edit_switch_to_wysiwyg' => 'Switch to WYSIWYG Editor',
    'pages_edit_set_changelog' => 'Đặt Changelog',
    'pages_edit_enter_changelog_desc' => 'Viết mô tả ngắn gọn cho các thay đổi mà bạn tạo',
    'pages_edit_enter_changelog' => 'Viết Changelog',
    'pages_editor_switch_title' => 'Switch Editor',
    'pages_editor_switch_are_you_sure' => 'Are you sure you want to change the editor for this page?',
    'pages_editor_switch_consider_following' => 'Consider the following when changing editors:',
    'pages_editor_switch_consideration_a' => 'Once saved, the new editor option will be used by any future editors, including those that may not be able to change editor type themselves.',
    'pages_editor_switch_consideration_b' => 'This can potentially lead to a loss of detail and syntax in certain circumstances.',
    'pages_editor_switch_consideration_c' => 'Tag or changelog changes, made since last save, won\'t persist across this change.',
    'pages_save' => 'Lưu Trang',
    'pages_title' => 'Tiêu đề Trang',
    'pages_name' => 'Tên Trang',
    'pages_md_editor' => 'Trình chỉnh sửa',
    'pages_md_preview' => 'Xem trước',
    'pages_md_insert_image' => 'Chèn hình ảnh',
    'pages_md_insert_link' => 'Chèn liên kết thực thể',
    'pages_md_insert_drawing' => 'Chèn bản vẽ',
    'pages_md_show_preview' => 'Show preview',
    'pages_md_sync_scroll' => 'Sync preview scroll',
    'pages_not_in_chapter' => 'Trang không nằm trong một chương',
    'pages_move' => 'Di chuyển Trang',
    'pages_copy' => 'Sao chép Trang',
    'pages_copy_desination' => 'Sao lưu đến',
    'pages_copy_success' => 'Trang được sao chép thành công',
    'pages_permissions' => 'Quyền hạn Trang',
    'pages_permissions_success' => 'Quyền hạn Trang được cập nhật',
    'pages_revision' => 'Phiên bản',
    'pages_revisions' => 'Phiên bản Trang',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'Phiên bản Trang cho :pageName',
    'pages_revision_named' => 'Phiên bản Trang cho :pageName',
    'pages_revision_restored_from' => 'Khôi phục từ #:id; :summary',
    'pages_revisions_created_by' => 'Tạo bởi',
    'pages_revisions_date' => 'Ngày của Phiên bản',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'Phiên bản #:id',
    'pages_revisions_numbered_changes' => 'Các thay đổi của phiên bản #:id',
    'pages_revisions_editor' => 'Editor Type',
    'pages_revisions_changelog' => 'Nhật ký thay đổi',
    'pages_revisions_changes' => 'Các thay đổi',
    'pages_revisions_current' => 'Phiên bản hiện tại',
    'pages_revisions_preview' => 'Xem trước',
    'pages_revisions_restore' => 'Khôi phục',
    'pages_revisions_none' => 'Trang này không có phiên bản nào',
    'pages_copy_link' => 'Sao chép Liên kết',
    'pages_edit_content_link' => 'Jump to section in editor',
    'pages_pointer_enter_mode' => 'Enter section select mode',
    'pages_pointer_label' => 'Tùy chọn phần trang',
    'pages_pointer_permalink' => 'Phần trang Liên kết cố định',
    'pages_pointer_include_tag' => 'Phần trang bao gồm thẻ',
    'pages_pointer_toggle_link' => 'Chế độ Liên kết cố định, Nhấn để hiển thị thẻ bao gồm',
    'pages_pointer_toggle_include' => 'Bao gồm chế độ thẻ, Nhấn để hiển thị liên kết cố định',
    'pages_permissions_active' => 'Đang bật các quyền hạn từ Trang',
    'pages_initial_revision' => 'Đăng bài mở đầu',
    'pages_references_update_revision' => 'System auto-update of internal links',
    'pages_initial_name' => 'Trang mới',
    'pages_editing_draft_notification' => 'Bạn hiện đang chỉnh sửa một bản nháp được lưu cách đây :timeDiff.',
    'pages_draft_edited_notification' => 'Trang này đã được cập nhật từ lúc đó. Bạn nên loại bỏ bản nháp này.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count người dùng đang bắt đầu chỉnh sửa trang này',
        'start_b' => ':userName đang bắt đầu chỉnh sửa trang này',
        'time_a' => 'kể từ khi thang được cập nhật lần cuối',
        'time_b' => 'trong :minCount phút cuối',
        'message' => ':start :time. Hãy cẩn thận đừng ghi đè vào các bản cập nhật của nhau!',
    ],
    'pages_draft_discarded' => 'Draft discarded! The editor has been updated with the current page content',
    'pages_draft_deleted' => 'Draft deleted! The editor has been updated with the current page content',
    'pages_specific' => 'Trang cụ thể',
    'pages_is_template' => 'Biểu mẫu trang',

    // Editor Sidebar
    'page_tags' => 'Các Thẻ Trang',
    'chapter_tags' => 'Các Thẻ Chương',
    'book_tags' => 'Các Thẻ Sách',
    'shelf_tags' => 'Các Thẻ Kệ',
    'tag' => 'Nhãn',
    'tags' =>  'Các Thẻ',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'Tên Nhãn',
    'tag_value' => 'Giá trị Thẻ (Tùy chọn)',
    'tags_explain' => "Thêm vài thẻ để phân loại nội dung của bạn tốt hơn. \n Bạn có thể đặt giá trị cho thẻ để quản lí kĩ càng hơn.",
    'tags_add' => 'Thêm thẻ khác',
    'tags_remove' => 'Xóa thẻ này',
    'tags_usages' => 'Total tag usages',
    'tags_assigned_pages' => 'Assigned to Pages',
    'tags_assigned_chapters' => 'Assigned to Chapters',
    'tags_assigned_books' => 'Assigned to Books',
    'tags_assigned_shelves' => 'Assigned to Shelves',
    'tags_x_unique_values' => ':count unique values',
    'tags_all_values' => 'All values',
    'tags_view_tags' => 'View Tags',
    'tags_view_existing_tags' => 'View existing tags',
    'tags_list_empty_hint' => 'Tags can be assigned via the page editor sidebar or while editing the details of a book, chapter or shelf.',
    'attachments' => 'Các Đính kèm',
    'attachments_explain' => 'Cập nhật một số tập tin và đính một số liên kết để hiển thị trên trang của bạn. Chúng được hiện trong sidebar của trang.',
    'attachments_explain_instant_save' => 'Các thay đổi ở đây sẽ được lưu ngay lập tức.',
    'attachments_upload' => 'Tải lên Tập tin',
    'attachments_link' => 'Đính kèm Liên kết',
    'attachments_upload_drop' => 'Alternatively you can drag and drop a file here to upload it as an attachment.',
    'attachments_set_link' => 'Đặt Liên kết',
    'attachments_delete' => 'Bạn có chắc chắn muốn xóa tập tin đính kèm này?',
    'attachments_dropzone' => 'Drop files here to upload',
    'attachments_no_files' => 'Không có tập tin nào được tải lên',
    'attachments_explain_link' => 'Bạn có thể đính kèm một liên kết nếu bạn lựa chọn không tải lên tập tin. Liên kết này có thể trỏ đến một trang khác hoặc một tập tin ở trên mạng (đám mây).',
    'attachments_link_name' => 'Tên Liên kết',
    'attachment_link' => 'Liên kết đính kèm',
    'attachments_link_url' => 'Liên kết đến tập tin',
    'attachments_link_url_hint' => 'URL của trang hoặc tập tin',
    'attach' => 'Đính kèm',
    'attachments_insert_link' => 'Thêm Đường dẫn Tập tin đính kèm vào Trang',
    'attachments_edit_file' => 'Sửa tập tin',
    'attachments_edit_file_name' => 'Tên tệp tin',
    'attachments_edit_drop_upload' => 'Thả tập tin hoặc bấm vào đây để tải lên và ghi đè',
    'attachments_order_updated' => 'Đã cập nhật thứ tự đính kèm',
    'attachments_updated_success' => 'Đã cập nhật chi tiết đính kèm',
    'attachments_deleted' => 'Đính kèm đã được xóa',
    'attachments_file_uploaded' => 'Tập tin tải lên thành công',
    'attachments_file_updated' => 'Tập tin cập nhật thành công',
    'attachments_link_attached' => 'Liên kết được đính kèm đến trang thành công',
    'templates' => 'Các Mẫu',
    'templates_set_as_template' => 'Trang là một mẫu',
    'templates_explain_set_as_template' => 'Bạn có thể đặt trang này làm mẫu, nội dung của nó sẽ được sử dụng lại khi tạo các trang mới. Người dùng khác có thể sử dụng mẫu này nếu học có quyền hạn xem trang này.',
    'templates_replace_content' => 'Thay thế nội dung trang',
    'templates_append_content' => 'Viết vào nội dung trang',
    'templates_prepend_content' => 'Thêm vào đầu nội dung trang',

    // Profile View
    'profile_user_for_x' => 'Đã là người dùng trong :time',
    'profile_created_content' => 'Đã tạo nội dung',
    'profile_not_created_pages' => ':userName chưa tạo bất kỳ trang nào',
    'profile_not_created_chapters' => ':userName chưa tạo bất kì chương nào',
    'profile_not_created_books' => ':userName chưa tạo bất cứ sách nào',
    'profile_not_created_shelves' => ':userName chưa tạo bất kỳ giá sách nào',

    // Comments
    'comment' => 'Bình luận',
    'comments' => 'Các bình luận',
    'comment_add' => 'Thêm bình luận',
    'comment_placeholder' => 'Đăng bình luận tại đây',
    'comment_count' => '{0} Không có bình luận|{1} 1 Bình luận|[2,*] :count Bình luận',
    'comment_save' => 'Lưu bình luận',
    'comment_new' => 'Bình luận mới',
    'comment_created' => 'đã bình luận :createDiff',
    'comment_updated' => 'Đã cập nhật :updateDiff bởi :username',
    'comment_updated_indicator' => 'Updated',
    'comment_deleted_success' => 'Bình luận đã bị xóa',
    'comment_created_success' => 'Đã thêm bình luận',
    'comment_updated_success' => 'Bình luận đã được cập nhật',
    'comment_delete_confirm' => 'Bạn có chắc bạn muốn xóa bình luận này?',
    'comment_in_reply_to' => 'Trả lời cho :commentId',
    'comment_editor_explain' => 'Here are the comments that have been left on this page. Comments can be added & managed when viewing the saved page.',

    // Revision
    'revision_delete_confirm' => 'Bạn có chắc bạn muốn xóa phiên bản này?',
    'revision_restore_confirm' => 'Bạn có chắc bạn muốn khôi phục phiên bản này? Nội dung trang hiện tại sẽ được thay thế.',
    'revision_cannot_delete_latest' => 'Không thể xóa phiên bản mới nhất.',

    // Copy view
    'copy_consider' => 'Please consider the below when copying content.',
    'copy_consider_permissions' => 'Custom permission settings will not be copied.',
    'copy_consider_owner' => 'You will become the owner of all copied content.',
    'copy_consider_images' => 'Page image files will not be duplicated & the original images will retain their relation to the page they were originally uploaded to.',
    'copy_consider_attachments' => 'Page attachments will not be copied.',
    'copy_consider_access' => 'A change of location, owner or permissions may result in this content being accessible to those previously without access.',

    // Conversions
    'convert_to_shelf' => 'Convert to Shelf',
    'convert_to_shelf_contents_desc' => 'You can convert this book to a new shelf with the same contents. Chapters contained within this book will be converted to new books. If this book contains any pages, that are not in a chapter, this book will be renamed and contain such pages, and this book will become part of the new shelf.',
    'convert_to_shelf_permissions_desc' => 'Any permissions set on this book will be copied to the new shelf and to all new child books that don\'t have their own permissions enforced. Note that permissions on shelves do not auto-cascade to content within, as they do for books.',
    'convert_book' => 'Convert Book',
    'convert_book_confirm' => 'Are you sure you want to convert this book?',
    'convert_undo_warning' => 'This cannot be as easily undone.',
    'convert_to_book' => 'Convert to Book',
    'convert_to_book_desc' => 'You can convert this chapter to a new book with the same contents. Any permissions set on this chapter will be copied to the new book but any inherited permissions, from the parent book, will not be copied which could lead to a change of access control.',
    'convert_chapter' => 'Convert Chapter',
    'convert_chapter_confirm' => 'Are you sure you want to convert this chapter?',

    // References
    'references' => 'References',
    'references_none' => 'There are no tracked references to this item.',
    'references_to_desc' => 'Shown below are all the known pages in the system that link to this item.',

    // Watch Options
    'watch' => 'Watch',
    'watch_title_default' => 'Default Preferences',
    'watch_desc_default' => 'Revert watching to just your default notification preferences.',
    'watch_title_ignore' => 'Ignore',
    'watch_desc_ignore' => 'Ignore all notifications, including those from user-level preferences.',
    'watch_title_new' => 'New Pages',
    'watch_desc_new' => 'Notify when any new page is created within this item.',
    'watch_title_updates' => 'All Page Updates',
    'watch_desc_updates' => 'Notify upon all new pages and page changes.',
    'watch_desc_updates_page' => 'Notify upon all page changes.',
    'watch_title_comments' => 'All Page Updates & Comments',
    'watch_desc_comments' => 'Notify upon all new pages, page changes and new comments.',
    'watch_desc_comments_page' => 'Notify upon page changes and new comments.',
    'watch_change_default' => 'Change default notification preferences',
    'watch_detail_ignore' => 'Ignoring notifications',
    'watch_detail_new' => 'Watching for new pages',
    'watch_detail_updates' => 'Watching new pages and updates',
    'watch_detail_comments' => 'Watching new pages, updates & comments',
    'watch_detail_parent_book' => 'Watching via parent book',
    'watch_detail_parent_book_ignore' => 'Ignoring via parent book',
    'watch_detail_parent_chapter' => 'Watching via parent chapter',
    'watch_detail_parent_chapter_ignore' => 'Ignoring via parent chapter',
];
