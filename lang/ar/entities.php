<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'أنشئت مؤخراً',
    'recently_created_pages' => 'صفحات أنشئت مؤخراً',
    'recently_updated_pages' => 'صفحات حُدثت مؤخراً',
    'recently_created_chapters' => 'فصول أنشئت مؤخراً',
    'recently_created_books' => 'كتب أنشئت مؤخراً',
    'recently_created_shelves' => 'أرفف أنشئت مؤخراً',
    'recently_update' => 'حُدثت مؤخراً',
    'recently_viewed' => 'عُرضت مؤخراً',
    'recent_activity' => 'نشاطات حديثة',
    'create_now' => 'أنشئ الآن',
    'revisions' => 'مراجعات',
    'meta_revision' => 'مراجعة #:revisionCount',
    'meta_created' => 'أنشئ :timeLength',
    'meta_created_name' => 'أنشئ :timeLength بواسطة :user',
    'meta_updated' => 'مُحدث :timeLength',
    'meta_updated_name' => 'مُحدث :timeLength بواسطة :user',
    'meta_owned_name' => 'Owned by :user',
    'meta_reference_page_count' => 'Referenced on :count page|Referenced on :count pages',
    'entity_select' => 'اختيار الكيان',
    'entity_select_lack_permission' => 'You don\'t have the required permissions to select this item',
    'images' => 'صور',
    'my_recent_drafts' => 'مسوداتي الحديثة',
    'my_recently_viewed' => 'ما عرضته مؤخراً',
    'my_most_viewed_favourites' => 'My Most Viewed Favourites',
    'my_favourites' => 'My Favourites',
    'no_pages_viewed' => 'لم تستعرض أي صفحات',
    'no_pages_recently_created' => 'لم تنشأ أي صفحات مؤخراً',
    'no_pages_recently_updated' => 'لم تُحدّث أي صفحات مؤخراً',
    'export' => 'تصدير',
    'export_html' => 'صفحة ويب',
    'export_pdf' => 'ملف PDF',
    'export_text' => 'ملف نص عادي',
    'export_md' => 'Markdown File',

    // Permissions and restrictions
    'permissions' => 'الأذونات',
    'permissions_desc' => 'Set permissions here to override the default permissions provided by user roles.',
    'permissions_book_cascade' => 'Permissions set on books will automatically cascade to child chapters and pages, unless they have their own permissions defined.',
    'permissions_chapter_cascade' => 'Permissions set on chapters will automatically cascade to child pages, unless they have their own permissions defined.',
    'permissions_save' => 'حفظ الأذونات',
    'permissions_owner' => 'Owner',
    'permissions_role_everyone_else' => 'Everyone Else',
    'permissions_role_everyone_else_desc' => 'Set permissions for all roles not specifically overridden.',
    'permissions_role_override' => 'Override permissions for role',
    'permissions_inherit_defaults' => 'Inherit defaults',

    // Search
    'search_results' => 'نتائج البحث',
    'search_total_results_found' => 'عدد النتائج :count|مجموع النتائج :count',
    'search_clear' => 'مسح البحث',
    'search_no_pages' => 'لم يطابق بحثكم أي صفحة',
    'search_for_term' => 'ابحث عن :term',
    'search_more' => 'المزيد من النتائج',
    'search_advanced' => 'بحث مفصل',
    'search_terms' => 'البحث باستخدام المصطلحات',
    'search_content_type' => 'نوع المحتوى',
    'search_exact_matches' => 'نتائج مطابقة تماماً',
    'search_tags' => 'بحث الوسوم',
    'search_options' => 'الخيارات',
    'search_viewed_by_me' => 'استعرضت من قبلي',
    'search_not_viewed_by_me' => 'لم تستعرض من قبلي',
    'search_permissions_set' => 'حزمة الأذونات',
    'search_created_by_me' => 'أنشئت بواسطتي',
    'search_updated_by_me' => 'حُدثت بواسطتي',
    'search_owned_by_me' => 'Owned by me',
    'search_date_options' => 'خيارات التاريخ',
    'search_updated_before' => 'حدثت قبل',
    'search_updated_after' => 'حدثت بعد',
    'search_created_before' => 'أنشئت قبل',
    'search_created_after' => 'أنشئت بعد',
    'search_set_date' => 'تحديد التاريخ',
    'search_update' => 'تحديث البحث',

    // Shelves
    'shelf' => 'رف',
    'shelves' => 'الأرفف',
    'x_shelves' => ':count رف|:count أرفف',
    'shelves_empty' => 'لم ينشأ أي رف',
    'shelves_create' => 'إنشاء رف جديد',
    'shelves_popular' => 'أرفف رائجة',
    'shelves_new' => 'أرفف جديدة',
    'shelves_new_action' => 'رف جديد',
    'shelves_popular_empty' => 'ستظهر هنا الأرفف الأكثر رواجًا.',
    'shelves_new_empty' => 'ستظهر هنا الأرفف التي أنشئت مؤخرًا.',
    'shelves_save' => 'حفظ الرف',
    'shelves_books' => 'كتب على هذا الرف',
    'shelves_add_books' => 'إضافة كتب لهذا الرف',
    'shelves_drag_books' => 'Drag books below to add them to this shelf',
    'shelves_empty_contents' => 'لا توجد كتب مخصصة لهذا الرف',
    'shelves_edit_and_assign' => 'تحرير الرف لإدراج كتب',
    'shelves_edit_named' => 'Edit Shelf :name',
    'shelves_edit' => 'Edit Shelf',
    'shelves_delete' => 'Delete Shelf',
    'shelves_delete_named' => 'Delete Shelf :name',
    'shelves_delete_explain' => "This will delete the shelf with the name ':name'. Contained books will not be deleted.",
    'shelves_delete_confirmation' => 'Are you sure you want to delete this shelf?',
    'shelves_permissions' => 'Shelf Permissions',
    'shelves_permissions_updated' => 'Shelf Permissions Updated',
    'shelves_permissions_active' => 'Shelf Permissions Active',
    'shelves_permissions_cascade_warning' => 'Permissions on shelves do not automatically cascade to contained books. This is because a book can exist on multiple shelves. Permissions can however be copied down to child books using the option found below.',
    'shelves_copy_permissions_to_books' => 'نسخ أذونات الوصول إلى الكتب',
    'shelves_copy_permissions' => 'نسخ الأذونات',
    'shelves_copy_permissions_explain' => 'This will apply the current permission settings of this shelf to all books contained within. Before activating, ensure any changes to the permissions of this shelf have been saved.',
    'shelves_copy_permission_success' => 'Shelf permissions copied to :count books',

    // Books
    'book' => 'كتاب',
    'books' => 'الكتب',
    'x_books' => ':count كتاب|:count كتب',
    'books_empty' => 'لم يتم إنشاء أي كتب',
    'books_popular' => 'كتب رائجة',
    'books_recent' => 'كتب حديثة',
    'books_new' => 'كتب جديدة',
    'books_new_action' => 'كتاب جديد',
    'books_popular_empty' => 'الكتب الأكثر رواجاً ستظهر هنا.',
    'books_new_empty' => 'الكتب المنشأة مؤخراً ستظهر هنا.',
    'books_create' => 'إنشاء كتاب جديد',
    'books_delete' => 'حذف الكتاب',
    'books_delete_named' => 'حذف كتاب :bookName',
    'books_delete_explain' => 'سيتم حذف كتاب \':bookName\'، وأيضا حذف جميع الفصول والصفحات.',
    'books_delete_confirmation' => 'تأكيد حذف الكتاب؟',
    'books_edit' => 'تعديل الكتاب',
    'books_edit_named' => 'تعديل كتاب :bookName',
    'books_form_book_name' => 'اسم الكتاب',
    'books_save' => 'حفظ الكتاب',
    'books_permissions' => 'أذونات الكتاب',
    'books_permissions_updated' => 'تم تحديث أذونات الكتاب',
    'books_empty_contents' => 'لم يتم إنشاء أي صفحات أو فصول لهذا الكتاب.',
    'books_empty_create_page' => 'إنشاء صفحة جديدة',
    'books_empty_sort_current_book' => 'فرز الكتاب الحالي',
    'books_empty_add_chapter' => 'إضافة فصل',
    'books_permissions_active' => 'أذونات الكتاب مفعلة',
    'books_search_this' => 'البحث في هذا الكتاب',
    'books_navigation' => 'تصفح الكتاب',
    'books_sort' => 'فرز محتويات الكتاب',
    'books_sort_desc' => 'Move chapters and pages within a book to reorganise its contents. Other books can be added which allows easy moving of chapters and pages between books.',
    'books_sort_named' => 'فرز كتاب :bookName',
    'books_sort_name' => 'ترتيب حسب الإسم',
    'books_sort_created' => 'ترتيب حسب تاريخ الإنشاء',
    'books_sort_updated' => 'فرز حسب تاريخ التحديث',
    'books_sort_chapters_first' => 'الفصول الأولى',
    'books_sort_chapters_last' => 'الفصول الأخيرة',
    'books_sort_show_other' => 'عرض كتب أخرى',
    'books_sort_save' => 'حفظ الترتيب الجديد',
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
    'chapter' => 'فصل',
    'chapters' => 'فصول',
    'x_chapters' => ':count فصل|:count فصول',
    'chapters_popular' => 'فصول رائجة',
    'chapters_new' => 'فصل جديد',
    'chapters_create' => 'إنشاء فصل جديد',
    'chapters_delete' => 'حذف الفصل',
    'chapters_delete_named' => 'حذف فصل :chapterName',
    'chapters_delete_explain' => 'This will delete the chapter with the name \':chapterName\'. All pages that exist within this chapter will also be deleted.',
    'chapters_delete_confirm' => 'تأكيد حذف الفصل؟',
    'chapters_edit' => 'تعديل الفصل',
    'chapters_edit_named' => 'تعديل فصل :chapterName',
    'chapters_save' => 'حفظ الفصل',
    'chapters_move' => 'نقل الفصل',
    'chapters_move_named' => 'نقل فصل :chapterName',
    'chapter_move_success' => 'تم نقل الفصل إلى :bookName',
    'chapters_copy' => 'Copy Chapter',
    'chapters_copy_success' => 'Chapter successfully copied',
    'chapters_permissions' => 'أذونات الفصل',
    'chapters_empty' => 'لا توجد أي صفحات في هذا الفصل حالياً',
    'chapters_permissions_active' => 'أذونات الفصل مفعلة',
    'chapters_permissions_success' => 'تم تحديث أذونات الفصل',
    'chapters_search_this' => 'البحث في هذا الفصل',
    'chapter_sort_book' => 'Sort Book',

    // Pages
    'page' => 'صفحة',
    'pages' => 'صفحات',
    'x_pages' => ':count صفحة|:count صفحات',
    'pages_popular' => 'صفحات رائجة',
    'pages_new' => 'صفحة جديدة',
    'pages_attachments' => 'مرفقات',
    'pages_navigation' => 'تصفح الصفحة',
    'pages_delete' => 'حذف الصفحة',
    'pages_delete_named' => 'حذف صفحة :pageName',
    'pages_delete_draft_named' => 'حذف مسودة :pageName',
    'pages_delete_draft' => 'حذف المسودة',
    'pages_delete_success' => 'تم حذف الصفحة',
    'pages_delete_draft_success' => 'تم حذف المسودة',
    'pages_delete_confirm' => 'تأكيد حذف الصفحة؟',
    'pages_delete_draft_confirm' => 'تأكيد حذف المسودة؟',
    'pages_editing_named' => ':pageName قيد التعديل',
    'pages_edit_draft_options' => 'خيارات المسودة',
    'pages_edit_save_draft' => 'حفظ المسودة',
    'pages_edit_draft' => 'تعديل مسودة الصفحة',
    'pages_editing_draft' => 'المسودة قيد التعديل',
    'pages_editing_page' => 'الصفحة قيد التعديل',
    'pages_edit_draft_save_at' => 'تم خفظ المسودة في ',
    'pages_edit_delete_draft' => 'حذف المسودة',
    'pages_edit_discard_draft' => 'التخلص من المسودة',
    'pages_edit_switch_to_markdown' => 'Switch to Markdown Editor',
    'pages_edit_switch_to_markdown_clean' => '(Clean Content)',
    'pages_edit_switch_to_markdown_stable' => '(Stable Content)',
    'pages_edit_switch_to_wysiwyg' => 'Switch to WYSIWYG Editor',
    'pages_edit_set_changelog' => 'تثبيت سجل التعديل',
    'pages_edit_enter_changelog_desc' => 'ضع وصف مختصر للتعديلات التي تمت',
    'pages_edit_enter_changelog' => 'أدخل سجل التعديل',
    'pages_editor_switch_title' => 'Switch Editor',
    'pages_editor_switch_are_you_sure' => 'Are you sure you want to change the editor for this page?',
    'pages_editor_switch_consider_following' => 'Consider the following when changing editors:',
    'pages_editor_switch_consideration_a' => 'Once saved, the new editor option will be used by any future editors, including those that may not be able to change editor type themselves.',
    'pages_editor_switch_consideration_b' => 'This can potentially lead to a loss of detail and syntax in certain circumstances.',
    'pages_editor_switch_consideration_c' => 'Tag or changelog changes, made since last save, won\'t persist across this change.',
    'pages_save' => 'حفظ الصفحة',
    'pages_title' => 'عنوان الصفحة',
    'pages_name' => 'اسم الصفحة',
    'pages_md_editor' => 'المحرر',
    'pages_md_preview' => 'معاينة',
    'pages_md_insert_image' => 'إدخال صورة',
    'pages_md_insert_link' => 'إدراج ارتباط الكيان',
    'pages_md_insert_drawing' => 'إدخال رسمة',
    'pages_md_show_preview' => 'Show preview',
    'pages_md_sync_scroll' => 'Sync preview scroll',
    'pages_not_in_chapter' => 'صفحة ليست في فصل',
    'pages_move' => 'نقل الصفحة',
    'pages_move_success' => 'تم نقل الصفحة إلى ":parentName"',
    'pages_copy' => 'نسخ الصفحة',
    'pages_copy_desination' => 'نسخ مكان الوصول',
    'pages_copy_success' => 'تم نسخ الصفحة بنجاح',
    'pages_permissions' => 'أذونات الصفحة',
    'pages_permissions_success' => 'تم تحديث أذونات الصفحة',
    'pages_revision' => 'مراجعة',
    'pages_revisions' => 'مراجعات الصفحة',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'مراجعات صفحة :pageName',
    'pages_revision_named' => 'مراجعة صفحة :pageName',
    'pages_revision_restored_from' => 'Restored from #:id; :summary',
    'pages_revisions_created_by' => 'أنشئ بواسطة',
    'pages_revisions_date' => 'تاريخ المراجعة',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'مراجعة #:id',
    'pages_revisions_numbered_changes' => 'مراجعة #: رقم تعريفي التغييرات',
    'pages_revisions_editor' => 'Editor Type',
    'pages_revisions_changelog' => 'سجل التعديل',
    'pages_revisions_changes' => 'التعديلات',
    'pages_revisions_current' => 'النسخة الحالية',
    'pages_revisions_preview' => 'معاينة',
    'pages_revisions_restore' => 'استرجاع',
    'pages_revisions_none' => 'لا توجد مراجعات لهذه الصفحة',
    'pages_copy_link' => 'نسخ الرابط',
    'pages_edit_content_link' => 'Jump to section in editor',
    'pages_pointer_enter_mode' => 'Enter section select mode',
    'pages_pointer_label' => 'Page Section Options',
    'pages_pointer_permalink' => 'Page Section Permalink',
    'pages_pointer_include_tag' => 'Page Section Include Tag',
    'pages_pointer_toggle_link' => 'Permalink mode, Press to show include tag',
    'pages_pointer_toggle_include' => 'Include tag mode, Press to show permalink',
    'pages_permissions_active' => 'أذونات الصفحة مفعلة',
    'pages_initial_revision' => 'نشر مبدئي',
    'pages_references_update_revision' => 'System auto-update of internal links',
    'pages_initial_name' => 'صفحة جديدة',
    'pages_editing_draft_notification' => 'جارٍ تعديل مسودة لم يتم حفظها من :timeDiff.',
    'pages_draft_edited_notification' => 'تم تحديث هذه الصفحة منذ ذلك الوقت. من الأفضل التخلص من هذه المسودة.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count من المستخدمين بدأوا بتعديل هذه الصفحة',
        'start_b' => ':userName بدأ بتعديل هذه الصفحة',
        'time_a' => 'منذ أن تم تحديث هذه الصفحة',
        'time_b' => 'في آخر :minCount دقيقة/دقائق',
        'message' => 'وقت البدء: احرص على عدم الكتابة فوق تحديثات بعضنا البعض!',
    ],
    'pages_draft_discarded' => 'تم التخلص من المسودة وتحديث المحرر بمحتوى الصفحة الحالي',
    'pages_specific' => 'صفحة محددة',
    'pages_is_template' => 'قالب الصفحة',

    // Editor Sidebar
    'page_tags' => 'وسوم الصفحة',
    'chapter_tags' => 'وسوم الفصل',
    'book_tags' => 'وسوم الكتاب',
    'shelf_tags' => 'علامات الرف',
    'tag' => 'وسم',
    'tags' =>  'وسوم',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'اسم العلامة',
    'tag_value' => 'قيمة الوسم (اختياري)',
    'tags_explain' => "إضافة الوسوم تساعد بترتيب وتقسيم المحتوى. \n من الممكن وضع قيمة لكل وسم لترتيب أفضل وأدق.",
    'tags_add' => 'إضافة وسم آخر',
    'tags_remove' => 'إزالة هذه العلامة',
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
    'attachments' => 'المرفقات',
    'attachments_explain' => 'ارفع بعض الملفات أو أرفق بعض الروابط لعرضها بصفحتك. ستكون الملفات والروابط معروضة في الشريط الجانبي للصفحة.',
    'attachments_explain_instant_save' => 'سيتم حفظ التغييرات هنا آنيا.',
    'attachments_upload' => 'رفع ملف',
    'attachments_link' => 'إرفاق رابط',
    'attachments_upload_drop' => 'Alternatively you can drag and drop a file here to upload it as an attachment.',
    'attachments_set_link' => 'تحديد الرابط',
    'attachments_delete' => 'هل أنت متأكد من أنك تريد حذف هذا المرفق؟',
    'attachments_dropzone' => 'Drop files here to upload',
    'attachments_no_files' => 'لم تُرفع أي ملفات',
    'attachments_explain_link' => 'بالإمكان إرفاق رابط في حال عدم تفضيل رفع ملف. قد يكون الرابط لصفحة أخرى أو لملف في أحد خدمات التخزين السحابي.',
    'attachments_link_name' => 'اسم الرابط',
    'attachment_link' => 'رابط المرفق',
    'attachments_link_url' => 'رابط الملف',
    'attachments_link_url_hint' => 'رابط الموقع أو الملف',
    'attach' => 'إرفاق',
    'attachments_insert_link' => 'إضافة رابط مرفق إلى الصفحة',
    'attachments_edit_file' => 'تعديل الملف',
    'attachments_edit_file_name' => 'اسم الملف',
    'attachments_edit_drop_upload' => 'أسقط الملفات أو اضغط هنا للرفع والاستبدال',
    'attachments_order_updated' => 'تم تحديث ترتيب المرفقات',
    'attachments_updated_success' => 'تم تحديث تفاصيل المرفق',
    'attachments_deleted' => 'تم حذف المرفق',
    'attachments_file_uploaded' => 'تم رفع الملف بنجاح',
    'attachments_file_updated' => 'تم تحديث الملف بنجاح',
    'attachments_link_attached' => 'تم إرفاق الرابط بالصفحة بنجاح',
    'templates' => 'القوالب',
    'templates_set_as_template' => 'هذه الصفحة عبارة عن قالب',
    'templates_explain_set_as_template' => 'يمكنك تعيين هذه الصفحة كقالب بحيث تستخدم محتوياتها عند إنشاء صفحات أخرى. سيتمكن المستخدمون الآخرون من استخدام هذا القالب إذا كان لديهم أذونات عرض لهذه الصفحة.',
    'templates_replace_content' => 'استبدال محتوى الصفحة',
    'templates_append_content' => 'تذييل محتوى الصفحة',
    'templates_prepend_content' => 'بادئة محتوى الصفحة',

    // Profile View
    'profile_user_for_x' => 'المستخدم لـ :time',
    'profile_created_content' => 'المحتوى المنشأ',
    'profile_not_created_pages' => 'لم يتم إنشاء أي صفحات بواسطة :userName',
    'profile_not_created_chapters' => 'لم يتم إنشاء أي فصول بواسطة :userName',
    'profile_not_created_books' => 'لم يتم إنشاء أي كتب بواسطة :userName',
    'profile_not_created_shelves' => 'لم يقم "اسم المستخدم"بإنشاء أي أرفف',

    // Comments
    'comment' => 'تعليق',
    'comments' => 'تعليقات',
    'comment_add' => 'إضافة تعليق',
    'comment_placeholder' => 'ضع تعليقاً هنا',
    'comment_count' => '{0} لا توجد تعليقات|{1} تعليق واحد|{2} تعليقان[3,*] :count تعليقات',
    'comment_save' => 'حفظ التعليق',
    'comment_saving' => 'جار حفظ التعليق...',
    'comment_deleting' => 'جار حذف التعليق...',
    'comment_new' => 'تعليق جديد',
    'comment_created' => 'تم التعليق :createDiff',
    'comment_updated' => 'تم التحديث :updateDiff بواسطة :username',
    'comment_deleted_success' => 'تم حذف التعليق',
    'comment_created_success' => 'تمت إضافة التعليق',
    'comment_updated_success' => 'تم تحديث التعليق',
    'comment_delete_confirm' => 'تأكيد حذف التعليق؟',
    'comment_in_reply_to' => 'رداً على :commentId',

    // Revision
    'revision_delete_confirm' => 'هل أنت متأكد من أنك تريد حذف هذه المراجعة؟',
    'revision_restore_confirm' => 'هل أنت متأكد من أنك تريد استعادة هذه المراجعة؟ سيتم استبدال محتوى الصفحة الحالية.',
    'revision_delete_success' => 'تم حذف المراجعة',
    'revision_cannot_delete_latest' => 'لايمكن حذف آخر مراجعة.',

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
];
