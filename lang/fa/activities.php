<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'تاریخ ایجاد',
    'page_create_notification'    => 'صفحه با موفقیت ایجاد شد',
    'page_update'                 => 'به روزرسانی صفحه',
    'page_update_notification'    => 'صفحه با موفقیت به روزرسانی شد',
    'page_delete'                 => 'حذف صفحه',
    'page_delete_notification'    => 'صفحه با موفقیت حذف شد',
    'page_restore'                => 'بازیابی صفحه',
    'page_restore_notification'   => 'صفحه با موفقیت بازیابی شد',
    'page_move'                   => 'انتقال صفحه',
    'page_move_notification'      => 'صفحه با موفقیت جابه‌جا شد',

    // Chapters
    'chapter_create'              => 'ایجاد فصل',
    'chapter_create_notification' => 'فصل با موفقیت ایجاد شد',
    'chapter_update'              => 'به روزرسانی فصل',
    'chapter_update_notification' => 'فصل با موفقیت به روزرسانی شد',
    'chapter_delete'              => 'حذف فصل',
    'chapter_delete_notification' => 'فصل با موفقیت حذف شد',
    'chapter_move'                => 'انتقال فصل',
    'chapter_move_notification' => 'فصل با موفقیت جابه‌جا شد',

    // Books
    'book_create'                 => 'ایجاد کتاب',
    'book_create_notification'    => 'کتاب با موفقیت ایجاد شد',
    'book_create_from_chapter'              => 'تبدیل فصل به کتاب',
    'book_create_from_chapter_notification' => 'کتاب با موفقیت به یک قفسه تبدیل شد',
    'book_update'                 => 'به روزرسانی کتاب',
    'book_update_notification'    => 'کتاب با موفقیت به روزرسانی شد',
    'book_delete'                 => 'حذف کتاب',
    'book_delete_notification'    => 'کتاب با موفقیت حذف شد',
    'book_sort'                   => 'مرتب سازی کتاب',
    'book_sort_notification'      => 'کتاب با موفقیت مرتب سازی شد',

    // Bookshelves
    'bookshelf_create'            => 'ایجاد قفسه',
    'bookshelf_create_notification'    => 'قفسه کتاب با موفقیت ایجاد شد',
    'bookshelf_create_from_book'    => 'تبدیل کتاب به قفسه',
    'bookshelf_create_from_book_notification'    => 'کتاب با موفقیت به یک قفسه تبدیل شد',
    'bookshelf_update'                 => 'به روزرسانی قفسه',
    'bookshelf_update_notification'    => 'قفسه با موفقیت به روزرسانی شد',
    'bookshelf_delete'                 => 'قفسه حذف شده',
    'bookshelf_delete_notification'    => 'قفسه کتاب با موفقیت حذف شد',

    // Revisions
    'revision_restore' => 'نسخه بازیابی شده',
    'revision_delete' => 'نسخه حذف شده',
    'revision_delete_notification' => 'نسخه مورد نظر با موفقیت حذف شد',

    // Favourites
    'favourite_add_notification' => '":name" به علاقه مندی های شما اضافه شد',
    'favourite_remove_notification' => '":name" از علاقه مندی های شما حذف شد',

    // Watching
    'watch_update_level_notification' => 'تنظیمات نظارت با موفقیت بروز شد',

    // Auth
    'auth_login' => 'وارد شده',
    'auth_register' => 'ثبت نام شده بعنوان کاربر جدید',
    'auth_password_reset_request' => 'بازیابی درخواست شده رمز عبور کاربر',
    'auth_password_reset_update' => 'بازیابی رمز عبور کاربر',
    'mfa_setup_method' => 'متد MFA پیکربندی شده',
    'mfa_setup_method_notification' => 'روش چند فاکتوری با موفقیت پیکربندی شد',
    'mfa_remove_method' => 'روش MFA حذف شده',
    'mfa_remove_method_notification' => 'روش چند فاکتوری با موفقیت حذف شد',

    // Settings
    'settings_update' => 'تنظیمات بروز شده',
    'settings_update_notification' => 'تنظیمات با موفقیت به روز شد',
    'maintenance_action_run' => 'فعالیت نگهداری اجرا شده',

    // Webhooks
    'webhook_create' => 'ایجاد وب هوک',
    'webhook_create_notification' => 'وب هوک با موفقیت ایجاد شد',
    'webhook_update' => 'به روزرسانی وب هوک',
    'webhook_update_notification' => 'وب هوک با موفقیت بروزرسانی شد',
    'webhook_delete' => 'حذف وب هوک',
    'webhook_delete_notification' => 'وب هوک با موفقیت حذف شد',

    // Users
    'user_create' => 'کاربر ایجاد شده',
    'user_create_notification' => 'کاربر با موفقیت به ایجاد شد',
    'user_update' => 'کاربر بروز شده',
    'user_update_notification' => 'کاربر با موفقیت به روز شد',
    'user_delete' => 'کاربر حذف شده',
    'user_delete_notification' => 'کاربر با موفقیت حذف شد',

    // API Tokens
    'api_token_create' => 'توکن api ایجاد شده',
    'api_token_create_notification' => 'توکن api با موفقیت ایجاد شد',
    'api_token_update' => 'توکن api بروز شده',
    'api_token_update_notification' => 'توکن API با موفقیت بروزرسانی شد',
    'api_token_delete' => 'توکن api حذف شده',
    'api_token_delete_notification' => 'توکن API با موفقیت حذف شد',

    // Roles
    'role_create' => 'نقش ایجاد شده',
    'role_create_notification' => 'نقش با موفقیت ایجاد شد',
    'role_update' => 'نقش بروز شده',
    'role_update_notification' => 'نقش با موفقیت به روز شد',
    'role_delete' => 'نقش حذف شده',
    'role_delete_notification' => 'نقش با موفقیت حذف شد',

    // Recycle Bin
    'recycle_bin_empty' => 'سطل زباله خالی',
    'recycle_bin_restore' => 'از سطل بازیافت، بازآوری شده است',
    'recycle_bin_destroy' => 'از سطل بازیافت حذف شده است',

    // Comments
    'commented_on'                => 'ثبت دیدگاه',
    'comment_create'              => 'نظر اضافه شده',
    'comment_update'              => 'نظر به روز شده',
    'comment_delete'              => 'نظر حذف شده',

    // Other
    'permissions_update'          => 'به روزرسانی مجوزها',
];
