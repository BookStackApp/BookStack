<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'الإعدادات',
    'settings_save' => 'حفظ الإعدادات',
    'settings_save_success' => 'تم حفظ الإعدادات',

    /**
     * App settings
     */

    'app_settings' => 'إعدادات التطبيق',
    'app_name' => 'اسم التطبيق',
    'app_name_desc' => 'سيتم عرض هذا الاسم في الترويسة وفي أي رسالة بريد إلكتروني.',
    'app_name_header' => 'عرض اسم التطبيق في الترويسة؟',
    'app_public_viewing' => 'السماح بالعرض على العامة؟',
    'app_secure_images' => 'تفعيل حماية أكبر لرفع الصور؟',
    'app_secure_images_desc' => 'لتحسين أداء النظام, ستكون جميع الصور متاحة للعامة. هذا الخيار يضيف سلسلة من الحروف والأرقام العشوائية صعبة التخمين إلى رابط الصورة. الرجاء التأكد من تعطيل فهرسة المسارات لمنع الوصول السهل.',
    'app_editor' => 'محرر الصفحة',
    'app_editor_desc' => 'الرجاء اختيار محرر النص الذي سيستخدم من قبل جميع المستخدمين لتحرير الصفحات.',
    'app_custom_html' => 'Custom HTML head content', // جار البحث عن الترجمة الأنسب
    'app_custom_html_desc' => 'Any content added here will be inserted into the bottom of the <head> section of every page. This is handy for overriding styles or adding analytics code.', // جار البحث عن الترجمة الأنسب
    'app_logo' => 'شعار التطبيق',
    'app_logo_desc' => 'يجب أن تكون الصورة بارتفاع 43 بكسل. <br>سيتم تصغير الصور الأكبر من ذلك.',
    'app_primary_color' => 'اللون الأساسي للتطبيق',
    'app_primary_color_desc' => 'يجب أن تكون القيمة من نوع hex. <br>اترك الخانة فارغة للرجوع للون الافتراضي.',
    'app_homepage' => 'الصفحة الرئيسية للتطبيق',
    'app_homepage_desc' => 'الرجاء اختيار صفحة لتصبح الصفحة الرئيسية بدل من الافتراضية. سيتم تجاهل جميع الأذونات الخاصة بالصفحة المختارة.',
    'app_homepage_default' => 'شكل الصفحة الافتراضية المختارة',
    'app_homepage_books' => 'أو من الممكن اختيار صفحة الكتب كصفحة رئيسية. سيتم استبدالها بأي صفحة سابقة تم اختيارها كصفحة رئيسية.',
    'app_disable_comments' => 'تعطيل التعليقات',
    'app_disable_comments_desc' => 'تعطيل التعليقات على جميع الصفحات داخل التطبيق. التعليقات الموجودة من الأصل لن تكون ظاهرة.',

    /**
     * Registration settings
     */

    'reg_settings' => 'إعدادات التسجيل',
    'reg_allow' => 'السماح بالتسجيل؟',
    'reg_default_role' => 'دور المستخدم الأساسي بعد التسجيل',
    'reg_confirm_email' => 'فرض التأكيد عن طريق البريد الإلكتروني؟',
    'reg_confirm_email_desc' => 'إذا تم استخدام قيود للمجال سيصبح التأكيد عن طريق البريد الإلكتروني إلزامي وسيتم تجاهل القيمة أسفله.',
    'reg_confirm_restrict_domain' => 'تقييد التسجيل على مجال محدد',
    'reg_confirm_restrict_domain_desc' => 'Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application. <br> Note that users will be able to change their email addresses after successful registration.', // جار البحث عن الترجمة الأنسب
    'reg_confirm_restrict_domain_placeholder' => 'لم يتم اختيار أي قيود',

    /**
     * Maintenance settings
     */

    'maint' => 'الصيانة',
    'maint_image_cleanup' => 'تنظيف الصور',
    'maint_image_cleanup_desc' => "Scans page & revision content to check which images and drawings are currently in use and which images are redundant. Ensure you create a full database and image backup before running this.", // جار البحث عن الترجمة الأنسب
    'maint_image_cleanup_ignore_revisions' => 'تجاهل الصور في المراجعات',
    'maint_image_cleanup_run' => 'بدء التنظيف',
    'maint_image_cleanup_warning' => 'يوجد عدد :count من الصور المحتمل عدم استخدامها. تأكيد حذف الصور؟',
    'maint_image_cleanup_success' => 'تم إيجاد وحذف عدد :count من الصور المحتمل عدم استخدامها!',
    'maint_image_cleanup_nothing_found' => 'لم يتم حذف أي شيء لعدم وجود أي صور غير مسمتخدمة',

    /**
     * Role settings
     */

    'roles' => 'الأدوار',
    'role_user_roles' => 'أدوار المستخدمين',
    'role_create' => 'إنشاء دور جديد',
    'role_create_success' => 'تم إنشاء الدور بنجاح',
    'role_delete' => 'حذف الدور',
    'role_delete_confirm' => 'سيتم حذف الدور المسمى \':roleName\'.',
    'role_delete_users_assigned' => 'This role has :userCount users assigned to it. If you would like to migrate the users from this role select a new role below.',
    'role_delete_no_migration' => "لا تقم بترجيل المستخدمين",
    'role_delete_sure' => 'تأكيد حذف الدور؟',
    'role_delete_success' => 'تم حذف الدور بنجاح',
    'role_edit' => 'تعديل الدور',
    'role_details' => 'تفاصيل الدور',
    'role_name' => 'اسم الدور', // test tests
    'role_desc' => 'Short Description of Role',
    'role_external_auth_id' => 'External Authentication IDs',
    'role_system' => 'System Permissions',
    'role_manage_users' => 'Manage users',
    'role_manage_roles' => 'Manage roles & role permissions',
    'role_manage_entity_permissions' => 'Manage all book, chapter & page permissions',
    'role_manage_own_entity_permissions' => 'Manage permissions on own book, chapter & pages',
    'role_manage_settings' => 'Manage app settings',
    'role_asset' => 'Asset Permissions',
    'role_asset_desc' => 'These permissions control default access to the assets within the system. Permissions on Books, Chapters and Pages will override these permissions.',
    'role_all' => 'All',
    'role_own' => 'Own',
    'role_controlled_by_asset' => 'Controlled by the asset they are uploaded to',
    'role_save' => 'Save Role',
    'role_update_success' => 'Role successfully updated',
    'role_users' => 'Users in this role',
    'role_users_none' => 'No users are currently assigned to this role',

    /**
     * Users
     */

    'users' => 'Users',
    'user_profile' => 'User Profile',
    'users_add_new' => 'Add New User',
    'users_search' => 'Search Users',
    'users_role' => 'User Roles',
    'users_external_auth_id' => 'External Authentication ID',
    'users_password_warning' => 'Only fill the below if you would like to change your password:',
    'users_system_public' => 'This user represents any guest users that visit your instance. It cannot be used to log in but is assigned automatically.',
    'users_delete' => 'Delete User',
    'users_delete_named' => 'Delete user :userName',
    'users_delete_warning' => 'This will fully delete this user with the name \':userName\' from the system.',
    'users_delete_confirm' => 'Are you sure you want to delete this user?',
    'users_delete_success' => 'Users successfully removed',
    'users_edit' => 'Edit User',
    'users_edit_profile' => 'Edit Profile',
    'users_edit_success' => 'User successfully updated',
    'users_avatar' => 'User Avatar',
    'users_avatar_desc' => 'This image should be approx 256px square.',
    'users_preferred_language' => 'Preferred Language',
    'users_social_accounts' => 'Social Accounts',
    'users_social_accounts_info' => 'Here you can connect your other accounts for quicker and easier login. Disconnecting an account here does not previously authorized access. Revoke access from your profile settings on the connected social account.',
    'users_social_connect' => 'Connect Account',
    'users_social_disconnect' => 'Disconnect Account',
    'users_social_connected' => ':socialAccount account was successfully attached to your profile.',
    'users_social_disconnected' => ':socialAccount account was successfully disconnected from your profile.',

    // Since these labels are already localized this array does not need to be
    // translated in the language-specific files.
    // DELETE BELOW IF COPIED FROM EN (تم الحذف)
];
