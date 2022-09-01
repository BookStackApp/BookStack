<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'شما مجوز مشاهده صفحه درخواست شده را ندارید.',
    'permissionJson' => 'شما مجاز به انجام این عمل نیستید.',

    // Auth
    'error_user_exists_different_creds' => 'کاربری با ایمیل :email از قبل وجود دارد اما دارای اطلاعات متفاوتی می باشد.',
    'email_already_confirmed' => 'ایمیل قبلا تایید شده است، وارد سیستم شوید.',
    'email_confirmation_invalid' => 'این کلمه عبور معتبر نمی باشد و یا قبلا استفاده شده است، لطفا دوباره ثبت نام نمایید.',
    'email_confirmation_expired' => 'کلمه عبور منقضی شده است، یک ایمیل تایید جدید ارسال شد.',
    'email_confirmation_awaiting' => 'آدرس ایمیل حساب مورد استفاده باید تایید شود',
    'ldap_fail_anonymous' => 'دسترسی LDAP با استفاده از صحافی ناشناس انجام نشد',
    'ldap_fail_authed' => 'دسترسی به LDAP با استفاده از جزئیات داده شده و رمز عبور انجام نشد',
    'ldap_extension_not_installed' => 'افزونه PHP LDAP نصب نشده است',
    'ldap_cannot_connect' => 'اتصال به سرور LDAP امکان پذیر نیست، اتصال اولیه برقرار نشد',
    'saml_already_logged_in' => 'قبلا وارد سیستم شده اید',
    'saml_user_not_registered' => 'کاربر :name ثبت نشده است و ثبت نام خودکار غیرفعال است',
    'saml_no_email_address' => 'آدرس داده ای برای این کاربر در داده های ارائه شده توسط سیستم احراز هویت خارجی یافت نشد',
    'saml_invalid_response_id' => 'درخواست از سیستم احراز هویت خارجی توسط فرایندی که توسط این نرم افزار آغاز شده است شناخته نمی شود. بازگشت به سیستم پس از ورود به سیستم می تواند باعث این مسئله شود.',
    'saml_fail_authed' => 'ورود به سیستم :system انجام نشد، سیستم مجوز موفقیت آمیز ارائه نکرد',
    'oidc_already_logged_in' => 'قبلا وارد شده اید',
    'oidc_user_not_registered' => 'کاربر :name ثبت نشده و ثبت نام خودکار غیرفعال است',
    'oidc_no_email_address' => 'آدرس ایمیلی برای این کاربر در داده های ارائه شده توسط سیستم احراز هویت خارجی یافت نشد',
    'oidc_fail_authed' => 'ورود به سیستم با استفاده از :system انجام نشد، سیستم مجوز موفقیت آمیز ارائه نکرد',
    'social_no_action_defined' => 'عملی تعریف نشده است',
    'social_login_bad_response' => "خطای دریافت شده در هنگام ورود به سیستم:\n:error",
    'social_account_in_use' => 'این حساب :socialAccount از قبل در حال استفاده است، سعی کنید از طریق گزینه :socialAccount وارد سیستم شوید.',
    'social_account_email_in_use' => 'ایمیل :email از قبل در حال استفاده است. اگر از قبل حساب کاربری دارید می توانید از تنظیمات نمایه خود :socialAccount خود را وصل کنید.',
    'social_account_existing' => 'این :socialAccount از قبل به نمایه شما پیوست شده است.',
    'social_account_already_used_existing' => 'این حساب :socialAccount قبلا توسط کاربر دیگری استفاده شده است.',
    'social_account_not_used' => 'این حساب :socialAccount به هیچ کاربری پیوند ندارد. لطفا آن را در تنظیمات نمایه خود ضمیمه کنید. ',
    'social_account_register_instructions' => 'اگر هنوز حساب کاربری ندارید ، می توانید با استفاده از گزینه :socialAccount حساب خود را ثبت کنید.',
    'social_driver_not_found' => 'درایور شبکه اجتماعی یافت نشد',
    'social_driver_not_configured' => 'تنظیمات شبکه اجتماعی :socialAccount به درستی پیکربندی نشده است.',
    'invite_token_expired' => 'این پیوند دعوت منقضی شده است. در عوض می توانید سعی کنید رمز عبور حساب خود را بازنشانی کنید.',

    // System
    'path_not_writable' => 'مسیر فایل :filePath را نمی توان در آن آپلود کرد. مطمئن شوید که روی سرور قابل نوشتن است.',
    'cannot_get_image_from_url' => 'نمی توان تصویر را از :url دریافت کرد',
    'cannot_create_thumbs' => 'سرور نمی تواند تصاویر کوچک ایجاد کند. لطفاً بررسی کنید که پسوند GD PHP را نصب کرده اید.',
    'server_upload_limit' => 'سرور اجازه آپلود در این اندازه را نمی دهد. لطفا اندازه فایل کوچکتر را امتحان کنید.',
    'uploaded'  => 'سرور اجازه آپلود در این اندازه را نمی دهد. لطفا اندازه فایل کوچکتر را امتحان کنید.',
    'image_upload_error' => 'هنگام آپلود تصویر خطایی روی داد',
    'image_upload_type_error' => 'نوع تصویر در حال آپلود نامعتبر است',
    'file_upload_timeout' => 'زمان بارگذاری فایل به پایان رسیده است.',

    // Attachments
    'attachment_not_found' => 'پیوست یافت نشد',

    // Pages
    'page_draft_autosave_fail' => 'پیش نویس ذخیره نشد. قبل از ذخیره این صفحه مطمئن شوید که به اینترنت متصل هستید',
    'page_custom_home_deletion' => 'وقتی صفحه ای به عنوان صفحه اصلی تنظیم شده است، نمی توان آن را حذف کرد',

    // Entities
    'entity_not_found' => 'موجودیت یافت نشد',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'کتاب پیدا نشد',
    'page_not_found' => 'صفحه یافت نشد',
    'chapter_not_found' => 'فصل پیدا نشد',
    'selected_book_not_found' => 'کتاب انتخابی یافت نشد',
    'selected_book_chapter_not_found' => 'کتاب یا فصل انتخابی یافت نشد',
    'guests_cannot_save_drafts' => 'مهمانان نمی توانند پیش نویس ها را ذخیره کنند',

    // Users
    'users_cannot_delete_only_admin' => 'شما نمی توانید تنها ادمین را حذف کنید',
    'users_cannot_delete_guest' => 'شما نمی توانید کاربر مهمان را حذف کنید',

    // Roles
    'role_cannot_be_edited' => 'این نقش قابل ویرایش نیست',
    'role_system_cannot_be_deleted' => 'این نقش یک نقش سیستمی است و قابل حذف نیست',
    'role_registration_default_cannot_delete' => 'این نقش در حالی که به عنوان نقش پیش فرض ثبت نام تنظیم شده است قابل حذف نیست',
    'role_cannot_remove_only_admin' => 'این کاربر تنها کاربری است که به نقش مدیر اختصاص داده شده است. قبل از تلاش برای حذف آن در اینجا، نقش مدیر را به کاربر دیگری اختصاص دهید.',

    // Comments
    'comment_list' => 'هنگام واکشی نظرات خطایی روی داد.',
    'cannot_add_comment_to_draft' => 'شما نمی توانید نظراتی را به یک پیش نویس اضافه کنید.',
    'comment_add' => 'هنگام افزودن/به‌روزرسانی نظر خطایی روی داد.',
    'comment_delete' => 'هنگام حذف نظر خطایی روی داد.',
    'empty_comment' => 'نمی توان یک نظر خالی اضافه کرد.',

    // Error pages
    '404_page_not_found' => 'صفحه یافت نشد',
    'sorry_page_not_found' => 'با عرض پوزش، صفحه مورد نظر شما یافت نشد.',
    'sorry_page_not_found_permission_warning' => 'اگر انتظار داشتید این صفحه وجود داشته باشد، ممکن است اجازه مشاهده آن را نداشته باشید.',
    'image_not_found' => 'تصویر پیدا نشد',
    'image_not_found_subtitle' => 'با عرض پوزش، فایل تصویری که به دنبال آن بودید یافت نشد.',
    'image_not_found_details' => 'اگر انتظار داشتید این تصویر وجود داشته باشد، ممکن است حذف شده باشد.',
    'return_home' => 'بازگشت به خانه',
    'error_occurred' => 'خطایی رخ داد',
    'app_down' => ':appName در حال حاضر قطع است',
    'back_soon' => 'به زودی پشتیبان خواهد شد.',

    // API errors
    'api_no_authorization_found' => 'هیچ نشانه مجوزی در درخواست یافت نشد',
    'api_bad_authorization_format' => 'یک نشانه مجوز در این درخواست یافت شد اما قالب نادرست به نظر می‌رسید',
    'api_user_token_not_found' => 'هیچ نشانه API منطبقی برای کد مجوز ارائه شده یافت نشد',
    'api_incorrect_token_secret' => 'راز ارائه شده برای کد API استفاده شده نادرست است',
    'api_user_no_api_permission' => 'مالک نشانه API استفاده شده اجازه برقراری تماس های API را ندارد',
    'api_user_token_expired' => 'رمز مجوز استفاده شده منقضی شده است',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'خطا در هنگام ارسال ایمیل آزمایشی:',

];
