<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'اخیرا ایجاد شده',
    'recently_created_pages' => 'صفحات اخیرا ایجاد شده',
    'recently_updated_pages' => 'صفحاتی که اخیرا روزآمد شده‌اند',
    'recently_created_chapters' => 'فصل های اخیرا ایجاد شده',
    'recently_created_books' => 'کتاب های اخیرا ایجاد شده',
    'recently_created_shelves' => 'قفسه کتاب های اخیرا ایجاد شده',
    'recently_update' => 'اخیرا به روز شده',
    'recently_viewed' => 'اخیرا مشاهده شده',
    'recent_activity' => 'فعالیت های اخیر',
    'create_now' => 'اکنون یکی ایجاد کنید',
    'revisions' => 'بازبینی‌ها',
    'meta_revision' => 'بازبینی #:revisionCount',
    'meta_created' => 'ایجاد شده :timeLength',
    'meta_created_name' => 'ایجاد شده :timeLength توسط :user',
    'meta_updated' => 'به روزرسانی شده :timeLength',
    'meta_updated_name' => 'به روزرسانی شده :timeLength توسط :user',
    'meta_owned_name' => 'توسط :user ایجاد شده‌است',
    'meta_reference_page_count' => 'در 1 صفحه به آن ارجاع داده شده|در :count صفحه به آن ارجاع داده شده',
    'entity_select' => 'انتخاب موجودیت',
    'entity_select_lack_permission' => 'شما مجوزهای لازم برای انتخاب این مورد را ندارید',
    'images' => 'عکس‌ها',
    'my_recent_drafts' => 'پیش نویس های اخیر من',
    'my_recently_viewed' => 'بازدیدهای اخیر من',
    'my_most_viewed_favourites' => 'محبوب ترین موارد مورد علاقه من',
    'my_favourites' => 'مورد علاقه من',
    'no_pages_viewed' => 'شما هیچ صفحه ای را مشاهده نکرده اید',
    'no_pages_recently_created' => 'اخیرا هیچ صفحه ای ایجاد نشده است',
    'no_pages_recently_updated' => 'اخیرا هیچ صفحه ای به روزرسانی نشده است',
    'export' => 'خروجی',
    'export_html' => 'فایل وب موجود است',
    'export_pdf' => 'فایل PDF',
    'export_text' => 'پرونده متنی ساده',
    'export_md' => 'راهنما مارک‌دون',

    // Permissions and restrictions
    'permissions' => 'مجوزها',
    'permissions_desc' => 'مجوزها را در اینجا تنظیم کنید تا مجوزهای پیش فرض تنظیم شده برای نقش های کاربر را لغو کنید.',
    'permissions_book_cascade' => 'مجوزهای تنظیم‌شده روی کتاب‌ها به‌طور خودکار به فصل‌ها و صفحات داخل آن اختصاص داده می‌شوند، مگر اینکه مجوزهای اختصاصی برای آن‌ها (فصل‌ها و صفحات) تعریف شده باشد.',
    'permissions_chapter_cascade' => 'مجوزهای تنظیم‌شده روی فصل‌ها به‌طور خودکار به صفحات داخل آن اختصاص داده می‌شوند، مگر اینکه مجوزهای اختصاصی برای آن‌ها (صفحات) تعریف شده باشد.',
    'permissions_save' => 'ذخيره مجوزها',
    'permissions_owner' => 'مالک',
    'permissions_role_everyone_else' => 'سایر کاربران',
    'permissions_role_everyone_else_desc' => 'مجوزها را برای نقش‌هایی تنظیم کنید که به طور خاص لغو نشده‌اند.',
    'permissions_role_override' => 'لغو مجوز برای نقش',
    'permissions_inherit_defaults' => 'ارث بردن از مجوزهای پیش‌فرض',

    // Search
    'search_results' => 'نتایج جستجو',
    'search_total_results_found' => 'نتیجه یافت شد :count | نتایج یافت شده :count',
    'search_clear' => 'پاک کردن جستجو',
    'search_no_pages' => 'هیچ صفحه ای با این جستجو مطابقت ندارد',
    'search_for_term' => 'جستجو برای :term',
    'search_more' => 'نتایج بیشتر',
    'search_advanced' => 'جستجوی پیشرفته',
    'search_terms' => 'عبارات جستجو',
    'search_content_type' => 'نوع محتوا',
    'search_exact_matches' => 'مطابقت کامل',
    'search_tags' => 'جستجوها را برچسب بزنید',
    'search_options' => 'گزینه ها',
    'search_viewed_by_me' => 'بازدید شده به وسیله من',
    'search_not_viewed_by_me' => 'توسط من مشاهده نشده است',
    'search_permissions_set' => 'مجوزها تنظیم شده است',
    'search_created_by_me' => 'ایجاد شده توسط من',
    'search_updated_by_me' => 'به روز شده توسط من',
    'search_owned_by_me' => 'متعلق به من است',
    'search_date_options' => 'گزینه های تاریخ',
    'search_updated_before' => 'قبلا به روز شده',
    'search_updated_after' => 'پس از به روز رسانی',
    'search_created_before' => 'ایجاد شده قبل از',
    'search_created_after' => 'ایجاد شده پس از',
    'search_set_date' => 'تنظیم تاریخ',
    'search_update' => 'جستجو را به روز کنید',

    // Shelves
    'shelf' => 'قفسه',
    'shelves' => 'قفسه ها',
    'x_shelves' => ':count قفسه|:count قفسه‌ها',
    'shelves_empty' => 'هیچ قفسه ای ایجاد نشده است',
    'shelves_create' => 'ایجاد قفسه جدید',
    'shelves_popular' => 'قفسه های محبوب',
    'shelves_new' => 'قفسه های جدید',
    'shelves_new_action' => 'قفسه جدید',
    'shelves_popular_empty' => 'محبوب ترین قفسه ها در اینجا ظاهر می شوند.',
    'shelves_new_empty' => 'جدیدترین قفسه های ایجاد شده در اینجا ظاهر می شوند.',
    'shelves_save' => 'ذخیره قفسه',
    'shelves_books' => 'کتاب های موجود در این قفسه',
    'shelves_add_books' => 'کتاب ها را به این قفسه اضافه کنید',
    'shelves_drag_books' => 'کتاب‌ها را به اینجا بکشید تا به این قفسه اضافه شوند',
    'shelves_empty_contents' => 'این قفسه هیچ کتابی به آن اختصاص داده نشده است',
    'shelves_edit_and_assign' => 'برای اختصاص کتاب‌ها، قفسه را ویرایش کنید',
    'shelves_edit_named' => 'ویرایش قفسه :name',
    'shelves_edit' => 'ویرایش قفسه',
    'shelves_delete' => 'حذف قفسه',
    'shelves_delete_named' => 'حذف قفسه :name',
    'shelves_delete_explain' => "با این کار قفسه کتاب با نام ':name' حذف می‌شود. کتاب های موجود حذف نمی‌شوند.",
    'shelves_delete_confirmation' => 'آیا مطمئن هستید که می‌خواهید این قفسه را حذف کنید؟',
    'shelves_permissions' => 'مجوزهای قفسه',
    'shelves_permissions_updated' => 'مجوزهای کانال بروزرسانی شد',
    'shelves_permissions_active' => 'مجوزهای قفسه فعال است',
    'shelves_permissions_cascade_warning' => 'مجوزهای موجود در قفسه‌ها به طور خودکار به کتاب‌های حاوی اطلاق نمی‌شوند. دلیل آن این است که یک کتاب می تواند در چندین قفسه وجود داشته باشد. با این حال، مجوزها را می‌توان با استفاده از گزینه پایین همین صفحه در کتاب‌های فرزند کپی کرد.',
    'shelves_copy_permissions_to_books' => 'کپی مجوزها در کتابها',
    'shelves_copy_permissions' => 'کپی مجوزها',
    'shelves_copy_permissions_explain' => 'با این کار تنظیمات مجوز فعلی این قفسه برای همه کتاب‌های موجود در آن اعمال می‌شود. قبل از فعال کردن، مطمئن شوید که هر گونه تغییر در مجوزهای این قفسه، ذخیره شده است.',
    'shelves_copy_permission_success' => 'مجوزهای قفسه در :count کتاب کپی شد',

    // Books
    'book' => 'کتاب',
    'books' => 'کتابها',
    'x_books' => ':count کتاب|:count کتاب',
    'books_empty' => 'هیچ کتابی ایجاد نشده است',
    'books_popular' => 'کتاب های محبوب',
    'books_recent' => 'کتاب های اخیر',
    'books_new' => 'کتاب های جدید',
    'books_new_action' => 'کتاب جدید',
    'books_popular_empty' => 'محبوب ترین کتاب ها در اینجا ظاهر می شوند.',
    'books_new_empty' => 'جدیدترین کتاب‌های ایجاد شده در اینجا ظاهر می‌شوند.',
    'books_create' => 'ایجاد کتاب جدید',
    'books_delete' => 'حذف کتاب',
    'books_delete_named' => 'حذف کتاب:bookName',
    'books_delete_explain' => 'با این کار کتابی با نام \':bookName\' حذف می شود. تمام صفحات و فصل ها حذف خواهند شد.',
    'books_delete_confirmation' => 'آیا مطمئن هستید که می خواهید این کتاب را حذف کنید؟',
    'books_edit' => 'ویرایش کتاب',
    'books_edit_named' => 'ویرایش کتاب:bookName',
    'books_form_book_name' => 'نام کتاب',
    'books_save' => 'ذخیره کتاب',
    'books_permissions' => 'مجوزهای کتاب',
    'books_permissions_updated' => 'مجوزهای کتاب به روز شد',
    'books_empty_contents' => 'هیچ صفحه یا فصلی برای این کتاب ایجاد نشده است.',
    'books_empty_create_page' => 'یک صفحه جدید ایجاد کنید',
    'books_empty_sort_current_book' => 'کتاب فعلی را مرتب کنید',
    'books_empty_add_chapter' => 'یک فصل اضافه کنید',
    'books_permissions_active' => 'مجوزهای کتاب فعال است',
    'books_search_this' => 'این کتاب را جستجو کنید',
    'books_navigation' => 'ناوبری کتاب',
    'books_sort' => 'مرتب سازی مطالب کتاب',
    'books_sort_named' => 'مرتب‌سازی کتاب:bookName',
    'books_sort_name' => 'مرتب سازی بر اساس نام',
    'books_sort_created' => 'مرتب سازی بر اساس تاریخ ایجاد',
    'books_sort_updated' => 'مرتب سازی بر اساس تاریخ به روز رسانی',
    'books_sort_chapters_first' => 'فصل اول',
    'books_sort_chapters_last' => 'فصل آخر',
    'books_sort_show_other' => 'نمایش کتاب های دیگر',
    'books_sort_save' => 'ذخیره سفارش جدید',
    'books_copy' => 'کپی کتاب',
    'books_copy_success' => 'کتاب با موفقیت کپی شد',

    // Chapters
    'chapter' => 'فصل',
    'chapters' => 'فصل',
    'x_chapters' => ':count فصل|:count فصل',
    'chapters_popular' => 'فصل های محبوب',
    'chapters_new' => 'فصل جدید',
    'chapters_create' => 'ایجاد فصل جدید',
    'chapters_delete' => 'حذف فصل',
    'chapters_delete_named' => 'حذف فصل :chapterName',
    'chapters_delete_explain' => 'با این کار فصلی با نام \':chapterName\' حذف می شود. تمامی صفحاتی که در این فصل وجود دارند نیز حذف خواهند شد.',
    'chapters_delete_confirm' => 'آیا مطمئن هستید که می خواهید این فصل را حذف کنید؟',
    'chapters_edit' => 'ویرایش فصل',
    'chapters_edit_named' => 'ویرایش فصل :chapterName',
    'chapters_save' => 'ذخیره فصل',
    'chapters_move' => 'انتقال فصل',
    'chapters_move_named' => 'انتقال فصل :chapterName',
    'chapter_move_success' => 'فصل به :bookName منتقل شد',
    'chapters_copy' => 'کپی فصل',
    'chapters_copy_success' => 'فصل با موفقیت کپی شد',
    'chapters_permissions' => 'مجوزهای فصل',
    'chapters_empty' => 'در حال حاضر هیچ صفحه ای در این فصل وجود ندارد.',
    'chapters_permissions_active' => 'مجوزهای فصل فعال است',
    'chapters_permissions_success' => 'مجوزهای فصل به روز شد',
    'chapters_search_this' => 'این فصل را جستجو کنید',
    'chapter_sort_book' => 'مرتب‌سازی کتاب',

    // Pages
    'page' => 'صفحه',
    'pages' => 'صفحات',
    'x_pages' => ':count صفحه|:count صفحه',
    'pages_popular' => 'صفحات محبوب',
    'pages_new' => 'صفحه جدید',
    'pages_attachments' => 'پیوست‌ها',
    'pages_navigation' => 'پیمایش صفحه',
    'pages_delete' => 'حذف صفحه',
    'pages_delete_named' => 'حذف صفحه:pageName',
    'pages_delete_draft_named' => 'حذف پیش نویس صفحه:pageName',
    'pages_delete_draft' => 'حذف صفحه پیش نویس',
    'pages_delete_success' => 'صفحه حذف شد',
    'pages_delete_draft_success' => 'صفحه پیش نویس حذف شد',
    'pages_delete_confirm' => 'آیا مطمئن هستید که می خواهید این صفحه را حذف کنید؟',
    'pages_delete_draft_confirm' => 'آیا مطمئن هستید که می خواهید این صفحه پیش نویس را حذف کنید؟',
    'pages_editing_named' => 'ویرایش صفحه :pageName',
    'pages_edit_draft_options' => 'گزینه های پیش نویس',
    'pages_edit_save_draft' => 'ذخیره پیش نویس',
    'pages_edit_draft' => 'ویرایش پیش نویس صفحه',
    'pages_editing_draft' => 'در حال ویرایش پیش نویس',
    'pages_editing_page' => 'در حال ویرایش صفحه',
    'pages_edit_draft_save_at' => 'پیش نویس ذخیره شده در',
    'pages_edit_delete_draft' => 'حذف پیش نویس',
    'pages_edit_discard_draft' => 'دور انداختن پیش نویس',
    'pages_edit_switch_to_markdown' => 'به ویرایشگر Markdown بروید',
    'pages_edit_switch_to_markdown_clean' => '(مطالب تمیز)',
    'pages_edit_switch_to_markdown_stable' => '(محتوای پایدار)',
    'pages_edit_switch_to_wysiwyg' => 'به ویرایشگر WYSIWYG بروید',
    'pages_edit_set_changelog' => 'تنظیم تغییرات',
    'pages_edit_enter_changelog_desc' => 'توضیح مختصری از تغییراتی که ایجاد کرده اید وارد کنید',
    'pages_edit_enter_changelog' => 'وارد کردن تغییرات',
    'pages_editor_switch_title' => 'ویرایشگر را تغییر دهید',
    'pages_editor_switch_are_you_sure' => 'آیا مطمئن هستید که می خواهید ویرایشگر این صفحه را تغییر دهید؟',
    'pages_editor_switch_consider_following' => 'هنگام تغییر ویرایشگر موارد زیر را در نظر بگیرید:',
    'pages_editor_switch_consideration_a' => 'پس از ذخیره، گزینه ویرایشگر جدید توسط هر ویرایشگر آینده، از جمله ویرایشگرانی که ممکن است خودشان نتوانند نوع ویرایشگر را تغییر دهند، استفاده خواهد شد.',
    'pages_editor_switch_consideration_b' => 'این به طور بالقوه می تواند منجر به از دست دادن جزئیات و نحو در شرایط خاص شود.',
    'pages_editor_switch_consideration_c' => 'تغییرات برچسب‌ها یا تغییرات ثبت شده از آخرین ذخیره‌سازی انجام شده، در این تغییر باقی نمی‌مانند.',
    'pages_save' => 'ذخیره صفحه',
    'pages_title' => 'عنوان صفحه',
    'pages_name' => 'نام صفحه',
    'pages_md_editor' => 'ویرایشگر',
    'pages_md_preview' => 'پيش نمايش',
    'pages_md_insert_image' => 'درج تصویر',
    'pages_md_insert_link' => 'پیوند نهاد را درج کنید',
    'pages_md_insert_drawing' => 'درج طرح',
    'pages_md_show_preview' => 'Show preview',
    'pages_md_sync_scroll' => 'Sync preview scroll',
    'pages_not_in_chapter' => 'صفحه در یک فصل نیست',
    'pages_move' => 'انتقال صفحه',
    'pages_move_success' => 'صفحه به ":parentName" منتقل شد',
    'pages_copy' => 'کپی صفحه',
    'pages_copy_desination' => 'مقصد را کپی کنید',
    'pages_copy_success' => 'صفحه با موفقیت کپی شد',
    'pages_permissions' => 'مجوزهای صفحه',
    'pages_permissions_success' => 'مجوزهای صفحه به روز شد',
    'pages_revision' => 'تجدید نظر',
    'pages_revisions' => 'ویرایش های صفحه',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'بازبینی صفحه برای :pageName',
    'pages_revision_named' => 'ویرایش صفحه برای :pageName',
    'pages_revision_restored_from' => 'بازیابی شده از #:id; :summary',
    'pages_revisions_created_by' => 'ایجاد شده توسط',
    'pages_revisions_date' => 'تاریخ تجدید نظر',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'تجدید نظر #:id',
    'pages_revisions_numbered_changes' => 'بازبینی #:id تغییرات',
    'pages_revisions_editor' => 'نوع ویرایشگر',
    'pages_revisions_changelog' => 'لیست تغییرات',
    'pages_revisions_changes' => 'تغییرات',
    'pages_revisions_current' => 'نسخه‌ی جاری',
    'pages_revisions_preview' => 'پيش نمايش',
    'pages_revisions_restore' => 'بازگرداندن',
    'pages_revisions_none' => 'این صفحه هیچ ویرایشی ندارد',
    'pages_copy_link' => 'کپی لینک',
    'pages_edit_content_link' => 'ویرایش محتوا',
    'pages_permissions_active' => 'مجوزهای صفحه فعال است',
    'pages_initial_revision' => 'انتشار اولیه',
    'pages_references_update_revision' => 'به‌روز‌رسانی خودکار لینک‌های داخلی سیستم',
    'pages_initial_name' => 'برگهٔ تازه',
    'pages_editing_draft_notification' => 'شما در حال ویرایش پیش نویسی هستید که آخرین بار در :timeDiff ذخیره شده است.',
    'pages_draft_edited_notification' => 'این صفحه از همان زمان به روز شده است. توصیه می شود از این پیش نویس صرف نظر کنید.',
    'pages_draft_page_changed_since_creation' => 'این صفحه از زمان ایجاد این پیش نویس به روز شده است. توصیه می‌شود که این پیش‌نویس را کنار بگذارید یا مراقب باشید که تغییرات صفحه را بازنویسی نکنید.',
    'pages_draft_edit_active' => [
        'start_a' => ':count کاربران شروع به ویرایش این صفحه کرده اند',
        'start_b' => ':userName ویرایش این صفحه را شروع کرده است',
        'time_a' => 'از آخرین به روز رسانی صفحه',
        'time_b' => 'در آخرین دقیقه :minCount',
        'message' => ':start :time. مراقب باشید به روز رسانی های یکدیگر را بازنویسی نکنید!',
    ],
    'pages_draft_discarded' => 'پیش نویس حذف شد، ویرایشگر با محتوای صفحه فعلی به روز شده است',
    'pages_specific' => 'صفحه خاص',
    'pages_is_template' => 'الگوی صفحه',

    // Editor Sidebar
    'page_tags' => 'برچسب‌های صفحه',
    'chapter_tags' => 'برچسب های فصل',
    'book_tags' => 'برچسب های کتاب',
    'shelf_tags' => 'برچسب های قفسه',
    'tag' => 'برچسب',
    'tags' =>  'برچسب ها',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'نام برچسب',
    'tag_value' => 'مقدار برچسب (اختیاری)',
    'tags_explain' => "برای دسته بندی بهتر مطالب خود چند برچسب اضافه کنید.\nمی توانید برای سازماندهی عمیق‌تر، یک مقدار به یک برچسب اختصاص دهید.",
    'tags_add' => 'یک برچسب دیگر اضافه کنید',
    'tags_remove' => 'این برچسب را حذف کنید',
    'tags_usages' => 'مجموع استفاده از برچسب',
    'tags_assigned_pages' => 'به صفحات اختصاص داده شده است',
    'tags_assigned_chapters' => 'اختصاص به فصل',
    'tags_assigned_books' => 'به کتاب ها اختصاص داده شده است',
    'tags_assigned_shelves' => 'به قفسه ها اختصاص داده شده است',
    'tags_x_unique_values' => ':count مقادیر منحصر به فرد',
    'tags_all_values' => 'همه ارزش ها',
    'tags_view_tags' => 'مشاهده برچسب ها',
    'tags_view_existing_tags' => 'مشاهده برچسب‌های موجود',
    'tags_list_empty_hint' => 'برچسب ها را می توان از طریق نوار کناری ویرایشگر صفحه یا هنگام ویرایش جزئیات یک کتاب، فصل یا قفسه اختصاص داد.',
    'attachments' => 'پیوست ها',
    'attachments_explain' => 'چند فایل را آپلود کنید یا چند پیوند را برای نمایش در صفحه خود ضمیمه کنید. اینها در نوار کناری صفحه قابل مشاهده هستند.',
    'attachments_explain_instant_save' => 'تغییرات در اینجا فورا ذخیره می شوند.',
    'attachments_items' => 'موارد پیوست شده',
    'attachments_upload' => 'آپلود فایل',
    'attachments_link' => 'پیوند را ضمیمه کنید',
    'attachments_set_link' => 'پیوند را تنظیم کنید',
    'attachments_delete' => 'آیا مطمئن هستید که می خواهید این پیوست را حذف کنید؟',
    'attachments_dropzone' => 'فایل ها را رها کنید یا برای پیوست کردن یک فایل اینجا را کلیک کنید',
    'attachments_no_files' => 'هیچ فایلی آپلود نشده است',
    'attachments_explain_link' => 'اگر ترجیح می دهید فایلی را آپلود نکنید، می توانید پیوندی را پیوست کنید. این می تواند پیوندی به صفحه دیگر یا پیوندی به فایلی در فضای ابری باشد.',
    'attachments_link_name' => 'نام پیوند',
    'attachment_link' => 'لینک پیوست',
    'attachments_link_url' => 'پیوند به فایل',
    'attachments_link_url_hint' => 'آدرس سایت یا فایل',
    'attach' => 'ضمیمه کنید',
    'attachments_insert_link' => 'پیوند پیوست را به صفحه اضافه کنید',
    'attachments_edit_file' => 'ویرایش فایل',
    'attachments_edit_file_name' => 'نام فایل',
    'attachments_edit_drop_upload' => 'فایل ها را رها کنید یا برای آپلود و بازنویسی اینجا کلیک کنید',
    'attachments_order_updated' => 'سفارش پیوست به روز شد',
    'attachments_updated_success' => 'جزئیات پیوست به روز شد',
    'attachments_deleted' => 'پیوست حذف شد',
    'attachments_file_uploaded' => 'فایل با موفقیت آپلود شد',
    'attachments_file_updated' => 'فایل با موفقیت به روز شد',
    'attachments_link_attached' => 'پیوند با موفقیت به صفحه پیوست شد',
    'templates' => 'قالب ها',
    'templates_set_as_template' => 'صفحه یک الگو است',
    'templates_explain_set_as_template' => 'می توانید این صفحه را به عنوان یک الگو تنظیم کنید تا از محتویات آن هنگام ایجاد صفحات دیگر استفاده شود. سایر کاربران در صورت داشتن مجوز مشاهده برای این صفحه می توانند از این الگو استفاده کنند.',
    'templates_replace_content' => 'محتوای صفحه را جایگزین کنید',
    'templates_append_content' => 'به محتوای صفحه اضافه کنید',
    'templates_prepend_content' => 'به محتوای صفحه اضافه کنید',

    // Profile View
    'profile_user_for_x' => 'کاربر برای :time',
    'profile_created_content' => 'محتوا ایجاد کرد',
    'profile_not_created_pages' => ':userName هیچ صفحه ای ایجاد نکرده است',
    'profile_not_created_chapters' => ':userName هیچ فصلی ایجاد نکرده است',
    'profile_not_created_books' => ':userName هیچ کتابی ایجاد نکرده است',
    'profile_not_created_shelves' => ':userName هیچ قفسه ای ایجاد نکرده است',

    // Comments
    'comment' => 'اظهار نظر',
    'comments' => 'نظرات',
    'comment_add' => 'افزودن توضیح',
    'comment_placeholder' => 'اینجا نظر بدهید',
    'comment_count' => '{0} بدون نظر|{1} 1 نظر|[2,*] :count نظرات',
    'comment_save' => 'ذخیره نظر',
    'comment_saving' => 'در حال ذخیره نظر...',
    'comment_deleting' => 'در حال حذف نظر...',
    'comment_new' => 'نظر جدید',
    'comment_created' => ':createDiff نظر داد',
    'comment_updated' => 'به روز رسانی :updateDiff توسط :username',
    'comment_deleted_success' => 'نظر حذف شد',
    'comment_created_success' => 'نظر اضافه شد',
    'comment_updated_success' => 'نظر به روز شد',
    'comment_delete_confirm' => 'آیا مطمئن هستید که می خواهید این نظر را حذف کنید؟',
    'comment_in_reply_to' => 'در پاسخ به :commentId',

    // Revision
    'revision_delete_confirm' => 'آیا مطمئن هستید که می خواهید این ویرایش را حذف کنید؟',
    'revision_restore_confirm' => 'آیا مطمئن هستید که می خواهید این ویرایش را بازیابی کنید؟ محتوای صفحه فعلی جایگزین خواهد شد.',
    'revision_delete_success' => 'ویرایش حذف شد',
    'revision_cannot_delete_latest' => 'نمی توان آخرین نسخه را حذف کرد.',

    // Copy view
    'copy_consider' => 'لطفاً هنگام کپی کردن مطالب به موارد زیر توجه کنید.',
    'copy_consider_permissions' => 'تنظیمات مجوز سفارشی کپی نخواهد شد.',
    'copy_consider_owner' => 'شما مالک تمام محتوای کپی شده خواهید شد.',
    'copy_consider_images' => 'فایل های تصویر صفحه تکراری نخواهند شد و تصاویر اصلی ارتباط خود را با صفحه ای که در ابتدا در آن آپلود شده اند حفظ می کنند.',
    'copy_consider_attachments' => 'پیوست های صفحه کپی نمی شود.',
    'copy_consider_access' => 'تغییر مکان، مالک یا مجوزها ممکن است منجر به دسترسی به این محتوا برای افرادی شود که قبلاً به آنها دسترسی نداشتند.',

    // Conversions
    'convert_to_shelf' => 'تبدیل به قفسه',
    'convert_to_shelf_contents_desc' => 'شما می توانید این کتاب را به یک قفسه جدید با همان مطالب تبدیل کنید. فصل های موجود در این کتاب به کتاب های جدید تبدیل می شوند. اگر این کتاب حاوی صفحاتی باشد که در یک فصل نیستند، این کتاب تغییر نام داده و حاوی چنین صفحاتی است و این کتاب بخشی از قفسه جدید خواهد شد.',
    'convert_to_shelf_permissions_desc' => 'هر گونه مجوز تنظیم شده در این کتاب در قفسه جدید و همه کتاب‌های فرزند جدید که مجوزهای خود را ندارند کپی می‌شود. توجه داشته باشید که مجوزهای موجود در قفسه‌ها مانند کتاب‌ها به طور خودکار به محتوای درون آن ها شامل نمی شود.',
    'convert_book' => 'تبدیل کتاب',
    'convert_book_confirm' => 'آیا از تبدیل این کتاب مطمئن هستید؟',
    'convert_undo_warning' => 'برگشت دادن این فرایند به آسانی نخواهد بود.',
    'convert_to_book' => 'تبدیل به کتاب',
    'convert_to_book_desc' => 'می توانید این فصل را به یک کتاب جدید با همین مطالب تبدیل کنید. هر مجوزی که در این فصل تنظیم شده است در کتاب جدید کپی می شود، اما هر گونه مجوز ارثی، از کتاب والد، کپی نمی شود که می تواند منجر به تغییر کنترل دسترسی شود.',
    'convert_chapter' => 'تبدیل فصل',
    'convert_chapter_confirm' => 'آیا از تبدیل این فصل مطمئن هستید؟',

    // References
    'references' => 'مراجع',
    'references_none' => 'There are no tracked references to this item.',
    'references_to_desc' => 'در زیر تمام صفحات شناخته شده در سیستم که به این مورد پیوند دارند، نشان داده شده است.',
];
