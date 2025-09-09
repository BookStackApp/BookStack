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
    'auth_pre_register_theme_prevention' => 'لم يتمكن حساب المستخدم من التسجيل للحصول على التفاصيل المقدمة',
    'email_already_confirmed' => 'تم تأكيد البريد الإلكتروني من قبل, الرجاء محاولة تسجيل الدخول.',
    'email_confirmation_invalid' => 'رابط التأكيد غير صحيح أو قد تم استخدامه من قبل, الرجاء محاولة التسجيل من جديد.',
    'email_confirmation_expired' => 'صلاحية رابط التأكيد انتهت, تم إرسال رسالة تأكيد جديدة لعنوان البريد الإلكتروني.',
    'email_confirmation_awaiting' => 'عنوان البريد الإلكتروني للحساب قيد الاستخدام يحتاج إلى تأكيد',
    'ldap_fail_anonymous' => 'فشل الوصول إلى LDAP باستخدام الربط المجهول',
    'ldap_fail_authed' => 'فشل الوصول إلى LDAP باستخدام dn و كلمة السر المعطاة',
    'ldap_extension_not_installed' => 'لم يتم تثبيت إضافة LDAP PHP',
    'ldap_cannot_connect' => 'لا يمكن الاتصال بخادم ldap, فشل الاتصال المبدئي',
    'saml_already_logged_in' => 'تم تسجيل الدخول بالفعل',
    'saml_no_email_address' => 'تعذر العثور على عنوان بريد إلكتروني، لهذا المستخدم، في البيانات المقدمة من نظام المصادقة الخارجي',
    'saml_invalid_response_id' => 'لم يتم التعرف على الطلب من نظام التوثيق الخارجي من خلال عملية تبدأ بهذا التطبيق. العودة بعد تسجيل الدخول يمكن أن يسبب هذه المشكلة.',
    'saml_fail_authed' => 'تسجيل الدخول باستخدام :system فشل، النظام لم يوفر التفويض الناجح',
    'oidc_already_logged_in' => 'تم تسجيل الدخول مسبقاً',
    'oidc_no_email_address' => 'تعذر العثور على عنوان بريد إلكتروني، لهذا المستخدم، في البيانات المقدمة من نظام المصادقة الخارجي',
    'oidc_fail_authed' => 'تسجيل الدخول باستخدام :system فشل، النظام لم يوفر التفويض الناجح',
    'social_no_action_defined' => 'لم يتم تعريف أي إجراء',
    'social_login_bad_response' => "حصل خطأ خلال تسجيل الدخول باستخدام :socialAccount \n:error",
    'social_account_in_use' => 'حساب :socialAccount قيد الاستخدام حالياً, الرجاء محاولة الدخول باستخدام خيار :socialAccount.',
    'social_account_email_in_use' => 'البريد الإلكتروني :email مستخدم. إذا كان لديكم حساب فبإمكانكم ربط حساب :socialAccount من إعدادات ملفكم.',
    'social_account_existing' => 'تم ربط حساب :socialAccount بملفكم من قبل.',
    'social_account_already_used_existing' => 'حساب :socialAccount مستخدَم من قبل مستخدم آخر.',
    'social_account_not_used' => 'حساب :socialAccount غير مرتبط بأي مستخدم. الرجاء ربطه من خلال إعدادات ملفكم. ',
    'social_account_register_instructions' => 'إذا لم يكن لديكم حساب فيمكنكم التجسيل باستخدام خيار :socialAccount.',
    'social_driver_not_found' => 'لم يتم العثور على السوشيال درايفر "Social driver"',
    'social_driver_not_configured' => 'لم يتم تهيئة إعدادات حسابك الاجتماعي بشكل صحيح.',
    'invite_token_expired' => 'انتهت صلاحية رابط هذه الدعوة. يمكنك بدلاً من ذلك محاولة إعادة تعيين كلمة مرور حسابك.',
    'login_user_not_found' => 'لم يتم العثور على مستخدم لهذا الإجراء.',

    // System
    'path_not_writable' => 'لا يمكن الرفع إلى مسار :filePath. الرجاء التأكد من قابلية الكتابة إلى الخادم.',
    'cannot_get_image_from_url' => 'لا يمكن الحصول على الصورة من :url',
    'cannot_create_thumbs' => 'لا يمكن للخادم إنشاء صور مصغرة. الرجاء التأكد من تثبيت إضافة GD PHP.',
    'server_upload_limit' => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',
    'server_post_limit' => 'لا يمكن للخادم تلقي كمية البيانات المتاحة. حاول مرة أخرى باستخدام بيانات أقل أو ملف أصغر.',
    'uploaded'  => 'الخادم لا يسمح برفع ملفات بهذا الحجم. الرجاء محاولة الرفع بحجم أصغر.',

    // Drawing & Images
    'image_upload_error' => 'حدث خطأ خلال رفع الصورة',
    'image_upload_type_error' => 'صيغة الصورة المرفوعة غير صالحة',
    'image_upload_replace_type' => 'يجب أن يكون استبدال ملف الصورة من نفس النوع',
    'image_upload_memory_limit' => 'فشل في التعامل مع تحميل الصورة و/أو إنشاء الصور المصغرة بسبب حدود موارد النظام.',
    'image_thumbnail_memory_limit' => 'فشل في إنشاء تغيرات حجم الصورة بسبب حدود موارد النظام.',
    'image_gallery_thumbnail_memory_limit' => 'فشل في إنشاء الصور المصغرة للمعرض بسبب حدود موارد النظام.',
    'drawing_data_not_found' => 'تعذر تحميل بيانات الرسم. قد لا يكون ملف الرسم موجودا أو قد لا يكون لديك إذن للوصول إليه.',

    // Attachments
    'attachment_not_found' => 'لم يتم العثور على المرفق',
    'attachment_upload_error' => 'حدث خطأ أثناء تحميل الملف المرفق',

    // Pages
    'page_draft_autosave_fail' => 'فشل حفظ المسودة. الرجاء التأكد من وجود اتصال بالإنترنت قبل حفظ الصفحة',
    'page_draft_delete_fail' => 'فشل في حذف مسودة الصفحة وجلب محتوى الصفحة الحالية المحفوظة',
    'page_custom_home_deletion' => 'لا يمكن حذف الصفحة إذا كانت محددة كصفحة رئيسية',

    // Entities
    'entity_not_found' => 'الكيان غير موجود',
    'bookshelf_not_found' => 'رف الكتب غير موجود',
    'book_not_found' => 'لم يتم العثور على الكتاب',
    'page_not_found' => 'لم يتم العثور على الصفحة',
    'chapter_not_found' => 'لم يتم العثور على الفصل',
    'selected_book_not_found' => 'لم يتم العثور على الكتاب المحدد',
    'selected_book_chapter_not_found' => 'لم يتم العثور على الكتاب أو الفصل المحدد',
    'guests_cannot_save_drafts' => 'لا يمكن حفظ المسودات من قبل الضيوف',

    // Users
    'users_cannot_delete_only_admin' => 'لا يمكن حذف المشرف الوحيد',
    'users_cannot_delete_guest' => 'لا يمكن حذف المستخدم الضيف',
    'users_could_not_send_invite' => 'لم يتم إنشاء المستخدم بسبب فشل إرسال بريد الدعوة',

    // Roles
    'role_cannot_be_edited' => 'لا يمكن تعديل هذا الدور',
    'role_system_cannot_be_deleted' => 'هذا الدور خاص بالنظام ولا يمكن حذفه',
    'role_registration_default_cannot_delete' => 'لا يمكن حذف الدور إذا كان مسجل كالدور الأساسي بعد تسجيل الحساب',
    'role_cannot_remove_only_admin' => 'هذا المستخدم هو المستخدم الوحيد المعين لدور المسؤول. قم بتعيين دور المسؤول لمستخدم آخر قبل محاولة إزالته هنا.',

    // Comments
    'comment_list' => 'حصل خطأ خلال جلب التعليقات.',
    'cannot_add_comment_to_draft' => 'لا يمكن إضافة تعليقات على مسودة.',
    'comment_add' => 'حصل خطاً خلال إضافة / تحديث التعليق.',
    'comment_delete' => 'حصل خطأ خلال حذف التعليق.',
    'empty_comment' => 'لايمكن إضافة تعليق فارغ.',

    // Error pages
    '404_page_not_found' => 'لم يتم العثور على الصفحة',
    'sorry_page_not_found' => 'عفواً, لا يمكن العثور على الصفحة التي تبحث عنها.',
    'sorry_page_not_found_permission_warning' => 'إذا كنت تتوقع أن تكون هذه الصفحة موجودة، قد لا يكون لديك تصريح بمشاهدتها.',
    'image_not_found' => 'لم يتم العثور على الصورة',
    'image_not_found_subtitle' => 'عذراً، لم يتم العثور على ملف الصورة الذي كنت تبحث عنه.',
    'image_not_found_details' => 'إذا كنت تتوقع وجود هذه الصورة ربما تم حذفها.',
    'return_home' => 'العودة للصفحة الرئيسية',
    'error_occurred' => 'حدث خطأ',
    'app_down' => ':appName لا يعمل حالياً',
    'back_soon' => 'سيعود للعمل قريباً.',

    // Import
    'import_zip_cant_read' => 'لم أتمكن من قراءة المِلَفّ المضغوط -ZIP-.',
    'import_zip_cant_decode_data' => 'لم نتمكن من العثور على محتوى المِلَفّ المضغوط data.json وفك تشفيره.',
    'import_zip_no_data' => 'لا تتضمن بيانات المِلَفّ المضغوط أي محتوى متوقع للكتاب أو الفصل أو الصفحة.',
    'import_validation_failed' => 'فشل التحقق من صحة استيراد المِلَفّ المضغوط بسبب الأخطاء التالية:',
    'import_zip_failed_notification' => 'فشل استيراد المِلَفّ المضغوط.',
    'import_perms_books' => 'أنت تفتقر إلى الصلاحيات المطلوبة لإنشاء الكتب.',
    'import_perms_chapters' => 'أنت تفتقر إلى الصلاحيات المطلوبة لإنشاء الفصول.',
    'import_perms_pages' => 'أنت تفتقر إلى الصلاحيات المطلوبة لإنشاء الصفحات.',
    'import_perms_images' => 'أنت تفتقر إلى الصلاحيات المطلوبة لإنشاء الصور.',
    'import_perms_attachments' => 'أنت تفتقر إلى الصَّلاحِيَة المطلوب لإنشاء المرفقات.',

    // API errors
    'api_no_authorization_found' => 'لم يتم العثور على رمز ترخيص مميز في الطلب',
    'api_bad_authorization_format' => 'تم العثور على رمز ترخيص مميز في الطلب ولكن يبدو أن التنسيق غير صحيح',
    'api_user_token_not_found' => 'لم يتم العثور على رمز API مطابق لرمز الترخيص المُقدم',
    'api_incorrect_token_secret' => 'الشفرة المُقدمة لرمز API المستخدم المحدد غير صحيحة',
    'api_user_no_api_permission' => 'مالك رمز API المستخدم ليس لديه الصلاحية لإجراء مكالمات API',
    'api_user_token_expired' => 'انتهت صلاحية رمز الترخيص المستخدم',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'حدث خطأ عند إرسال بريد إلكتروني تجريبي:',

    // HTTP errors
    'http_ssr_url_no_match' => 'الرابط لا يتطابق مع الاعدادات المسموح بها لاستضافة SSR',
];
