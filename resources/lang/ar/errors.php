<?php

return [

    /**
     * Error text strings.
     */

    // Permissions
    'permission' => 'لم يؤذن لك بالدخول للصفحة المطلوبة.',
    'permissionJson' => 'لم يؤذن لك بعمل الإجراء المطلوب.',

    // Auth
    'error_user_exists_different_creds' => 'يوجد مستخدم ببيانات مختلفة مسجل بالنظام للبريد الإلكتروني :email.',
    'email_already_confirmed' => 'تم تأكيد البريد الإلكتروني من قبل, الرجاء محاولة تسجيل الدخول.',
    'email_confirmation_invalid' => 'رابط التأكيد غير صحيح أو قد تم استخدامه من قبل, الرجاء محاولة التسجيل من جديد.',
    'email_confirmation_expired' => 'صلاحية رابط التأكيد انتهت, تم إرسال رسالة تأكيد جديدة لعنوان البريد الإلكتروني.',
    'ldap_fail_anonymous' => 'LDAP access failed using anonymous bind', // جار البحث عن الترجمة الأنسب
    'ldap_fail_authed' => 'LDAP access failed using given dn & password details', // جار البحث عن الترجمة الأنسب
    'ldap_extension_not_installed' => 'لم يتم تثبيت إضافة LDAP PHP',
    'ldap_cannot_connect' => 'لا يمكن الاتصال بخادم ldap, فشل الاتصال المبدئي',
    'social_no_action_defined' => 'لم يتم تعريف أي إجراء',
    'social_login_bad_response' => "حصل خطأ خلال تسجيل الدخول باستخدام :socialAccount \n:error",
    'social_account_in_use' => 'This :socialAccount account is already in use, Try logging in via the :socialAccount option.', // جار البحث عن الترجمة الأنسب
    'social_account_email_in_use' => 'The email :email is already in use. If you already have an account you can connect your :socialAccount account from your profile settings.', // جار البحث عن الترجمة الأنسب
    'social_account_existing' => 'This :socialAccount is already attached to your profile.', // جار البحث عن الترجمة الأنسب
    'social_account_already_used_existing' => 'This :socialAccount account is already used by another user.', // جار البحث عن الترجمة الأنسب
    'social_account_not_used' => 'This :socialAccount account is not linked to any users. Please attach it in your profile settings. ', // جار البحث عن الترجمة الأنسب
    'social_account_register_instructions' => 'إذا لم يكن لديك حساب فيمكنك التجسيل باستخدام خيار :socialAccount.',
    'social_driver_not_found' => 'Social driver not found', // جار البحث عن الترجمة الأنسب
    'social_driver_not_configured' => 'Your :socialAccount social settings are not configured correctly.', // جار البحث عن الترجمة الأنسب

    // System
    'path_not_writable' => 'لا يمكن الرفع إلى مسار :filePath. الرجاء التأكد من قابلية الكتابة إلى الخادم.',
    'cannot_get_image_from_url' => 'لا يمكن الحصول على الصورة من :url',
    'cannot_create_thumbs' => 'لا يمكن للخادم إنشاء صور مصغرة. الرجاء التأكد من تثبيت إضافة GD PHP.',
    'server_upload_limit' => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',
    'uploaded'  => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',
    'image_upload_error' => 'حدث خطأ خلال رفع الصورة',
    'image_upload_type_error' => 'صيغة الصورة المرفوعة غير صالحة',

    // Attachments
    'attachment_page_mismatch' => 'Page mismatch during attachment update', // جار البحث عن الترجمة الأنسب
    'attachment_not_found' => 'لم يتم العثور على المرفق',

    // Pages
    'page_draft_autosave_fail' => 'فشل حفظ المسودة. الرجاء التأكد من وجود اتصال بالإنترنت قبل حفظ الصفحة',
    'page_custom_home_deletion' => 'لا يمكن حذف الصفحة إذا كانت محددة كصفحة رئيسية',

    // Entities
    'entity_not_found' => 'Entity not found', // جار البحث عن الترجمة الأنسب
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
];
