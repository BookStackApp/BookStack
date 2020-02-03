<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'لم يؤذن لك بالدخول للصفحة المطلوبة.',
    'permissionJson' => 'لم يؤذن لك بعمل الإجراء المطلوب.',

    // Auth
    'error_user_exists_different_creds' => 'يوجد مستخدم ببيانات مختلفة مسجل بالنظام للبريد الإلكتروني :email.',
    'email_already_confirmed' => 'تم تأكيد البريد الإلكتروني من قبل, الرجاء محاولة تسجيل الدخول.',
    'email_confirmation_invalid' => 'رابط التأكيد غير صحيح أو قد تم استخدامه من قبل, الرجاء محاولة التسجيل من جديد.',
    'email_confirmation_expired' => 'صلاحية رابط التأكيد انتهت, تم إرسال رسالة تأكيد جديدة لعنوان البريد الإلكتروني.',
    'email_confirmation_awaiting' => 'The email address for the account in use needs to be confirmed',
    'ldap_fail_anonymous' => 'فشل الوصول إلى LDAP باستخدام الربط المجهول',
    'ldap_fail_authed' => 'فشل الوصول إلى LDAP باستخدام dn و password المعطاة',
    'ldap_extension_not_installed' => 'لم يتم تثبيت إضافة LDAP PHP',
    'ldap_cannot_connect' => 'لا يمكن الاتصال بخادم ldap, فشل الاتصال المبدئي',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'لم يتم تعريف أي إجراء',
    'social_login_bad_response' => "حصل خطأ خلال تسجيل الدخول باستخدام :socialAccount \n:error",
    'social_account_in_use' => 'حساب :socialAccount قيد الاستخدام حالياً, الرجاء محاولة الدخول باستخدام خيار :socialAccount.',
    'social_account_email_in_use' => 'البريد الإلكتروني :email مستخدم. إذا كان لديكم حساب فبإمكانكم ربط حساب :socialAccount من إعدادات ملفكم.',
    'social_account_existing' => 'تم ربط حساب :socialAccount بملفكم من قبل.',
    'social_account_already_used_existing' => 'حساب :socialAccount مستخدَم من قبل مستخدم آخر.',
    'social_account_not_used' => 'حساب :socialAccount غير مرتبط بأي مستخدم. الرجاء ربطه من خلال إعدادات ملفكم. ',
    'social_account_register_instructions' => 'إذا لم يكن لديكم حساب فيمكنكم التجسيل باستخدام خيار :socialAccount.',
    'social_driver_not_found' => 'Social driver not found',
    'social_driver_not_configured' => 'Your :socialAccount social settings are not configured correctly.',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'لا يمكن الرفع إلى مسار :filePath. الرجاء التأكد من قابلية الكتابة إلى الخادم.',
    'cannot_get_image_from_url' => 'لا يمكن الحصول على الصورة من :url',
    'cannot_create_thumbs' => 'لا يمكن للخادم إنشاء صور مصغرة. الرجاء التأكد من تثبيت إضافة GD PHP.',
    'server_upload_limit' => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',
    'uploaded'  => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',
    'image_upload_error' => 'حدث خطأ خلال رفع الصورة',
    'image_upload_type_error' => 'صيغة الصورة المرفوعة غير صالحة',
    'file_upload_timeout' => 'انتهت عملية تحميل الملف.',

    // Attachments
    'attachment_page_mismatch' => 'Page mismatch during attachment update',
    'attachment_not_found' => 'لم يتم العثور على المرفق',

    // Pages
    'page_draft_autosave_fail' => 'فشل حفظ المسودة. الرجاء التأكد من وجود اتصال بالإنترنت قبل حفظ الصفحة',
    'page_custom_home_deletion' => 'لا يمكن حذف الصفحة إذا كانت محددة كصفحة رئيسية',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Bookshelf not found',
    'book_not_found' => 'لم يتم العثور على الكتاب',
    'page_not_found' => 'لم يتم العثور على الصفحة',
    'chapter_not_found' => 'لم يتم العثور على الفصل',
    'selected_book_not_found' => 'لم يتم العثور على الكتاب المحدد',
    'selected_book_chapter_not_found' => 'لم يتم العثور على الكتاب أو الفصل المحدد',
    'guests_cannot_save_drafts' => 'لا يمكن حفظ المسودات من قبل الضيوف',

    // Users
    'users_cannot_delete_only_admin' => 'لا يمكن حذف المشرف الوحيد',
    'users_cannot_delete_guest' => 'لا يمكن حذف المستخدم الضيف',

    // Roles
    'role_cannot_be_edited' => 'لا يمكن تعديل هذا الدور',
    'role_system_cannot_be_deleted' => 'هذا الدور خاص بالنظام ولا يمكن حذفه',
    'role_registration_default_cannot_delete' => 'لا يمكن حذف الدور إذا كان مسجل كالدور الأساسي بعد تسجيل الحساب',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'حصل خطأ خلال جلب التعليقات.',
    'cannot_add_comment_to_draft' => 'لا يمكن إضافة تعليقات على مسودة.',
    'comment_add' => 'حصل خطاً خلال إضافة / تحديث التعليق.',
    'comment_delete' => 'حصل خطأ خلال حذف التعليق.',
    'empty_comment' => 'لايمكن إضافة تعليق فارغ.',

    // Error pages
    '404_page_not_found' => 'لم يتم العثور على الصفحة',
    'sorry_page_not_found' => 'عفواً, لا يمكن العثور على الصفحة التي تبحث عنها.',
    'return_home' => 'العودة للصفحة الرئيسية',
    'error_occurred' => 'حدث خطأ',
    'app_down' => ':appName لا يعمل حالياً',
    'back_soon' => 'سيعود للعمل قريباً.',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => 'The authorization token used has expired',

];
