<?php
/**
 * Settings text strings
 * Contains all text strings used in the general settings sections of BookStack
 * including users and roles.
 */
return [

    // Common Messages
    'settings' => 'Ayarlar',
    'settings_save' => 'Ayarları Kaydet',
    'settings_save_success' => 'Ayarlar kaydedildi',

    // App Settings
    'app_customization' => 'Özelleştirme',
    'app_features_security' => 'Özellikler ve Güvenlik',
    'app_name' => 'Uygulama Adı',
    'app_name_desc' => 'Bu ad, başlıkta ve sistem tarafından gönderilen e-postalarda gösterilir.',
    'app_name_header' => 'Adı başlıkta göster',
    'app_public_access' => 'Herkese Açık Erişin',
    'app_public_access_desc' => 'Bu seçeneğin etkinleştirilmesi, oturum açmamış ziyaretçilerin BookStack örneğinizdeki içeriğe erişmesine izin verir.',
    'app_public_access_desc_guest' => 'Genel ziyaretçilere erişim, "Misafir (Guest)" kullanıcısı tarafından kontrol edilebilir.',
    'app_public_access_toggle' => 'Herkese açık erişime izin ver',
    'app_public_viewing' => 'Herkese açık erişime izin verilsin mi?',
    'app_secure_images' => 'Yüksek Güvenlikli Görüntü Yükleme',
    'app_secure_images_toggle' => 'Daha yüksek güvenlikli görüntü yüklemelerini etkinleştir',
    'app_secure_images_desc' => 'Performans nedeniyle tüm görüntüler halka açıktır. Bu seçenek, resim URL\'lerinin önüne rastgele, tahmin edilmesi zor bir dize ekler. Ensure directory indexes are not enabled to prevent easy access.',
    'app_editor' => 'Sayfa Editörü',
    'app_editor_desc' => 'Tüm kullanıcıların hangi editörü kullanacağını belirleyin.',
    'app_custom_html' => 'Özel HTML Başlık İçeriği',
    'app_custom_html_desc' => 'Buraya eklenen herhangi bir içerik, her sayfanın <head> bölümünün altına eklenecektir. Bu, stilleri geçersiz kılmak veya analitik kodları eklemek için kullanışlıdır.',
    'app_custom_html_disabled_notice' => 'Özel HTML başlık içeriği bu ayar sayfasında herhangi bir olumsuzlukta geri almak için devre dışı bırakılmıştır.',
    'app_logo' => 'Uygulama Logosu',
    'app_logo_desc' => 'Bu resim 43px yüksekliğinde olmalı. <br>Büyük resimler küçültülecektir.',
    'app_primary_color' => 'Uygulama Ana Rengi',
    'app_primary_color_desc' => 'Bu bir HEX değer olmalı. <br>Varsayılan renk için boş bırakın.',
    'app_homepage' => 'Uygulama Ana Sayfası',
    'app_homepage_desc' => 'Varsayılan görünüm yerine ana sayfada gösterilecek görünümü seçin. Sayfa izinleri, seçilen sayfalar için dikkate alınmaz.',
    'app_homepage_select' => 'Bir sayfa seç',
    'app_disable_comments' => 'Yorumları Devredışı Bırak',
    'app_disable_comments_toggle' => 'Yorumları devredışı bırak',
    'app_disable_comments_desc' => 'Uygulamadaki tüm sayfalardaki yorumları devre dışı bırakır. <br> Mevcut yorumlar gösterilmez.',

    // Registration Settings
    'reg_settings' => 'Kayıt Olma',
    'reg_enable' => 'Kaydolmaya İzin Ver',
    'reg_enable_toggle' => 'Kaydolmaya izin ver',
    'reg_enable_desc' => 'Kayıt etkinleştirildiğinde, kullanıcı kendilerini bir uygulama kullanıcısı olarak kaydedebilir. Kayıt olduklarında, tek bir varsayılan kullanıcı rolü verilir.',
    'reg_default_role' => 'Kayıttan sonraki varsayılan kullanıcı rolü',
    'reg_email_confirmation' => 'E-Posta Doğrulama',
    'reg_email_confirmation_toggle' => 'E-Posta doğrulama gerektirir',
    'reg_confirm_email_desc' => 'Domain kısıtlaması kullanılıyorsa, e-posta onayı gerekli olacak ve bu seçenek dikkate alınmayacaktır.',
    'reg_confirm_restrict_domain' => 'Domain Kısıtlaması',
    'reg_confirm_restrict_domain_desc' => 'Kaydı sınırlandırmak istediğiniz bir virgülle ayrılmış e-posta domain listesi girin. Kullanıcılara, uygulama ile etkileşime girmesine izin verilmeden önce adreslerini onaylamaları için bir e-posta gönderilecektir. <br> Başarılı bir kayıt işleminden sonra kullanıcıların e-posta adreslerini değiştirebileceklerini unutmayın.',
    'reg_confirm_restrict_domain_placeholder' => 'Kısıtlama belirtilmemiş',

    // Maintenance settings
    'maint' => 'Bakım',
    'maint_image_cleanup' => 'Cleanup Images',
    'maint_image_cleanup_desc' => "Scans page & revision content to check which images and drawings are currently in use and which images are redundant. Ensure you create a full database and image backup before running this.",
    'maint_image_cleanup_ignore_revisions' => 'Ignore images in revisions',
    'maint_image_cleanup_run' => 'Run Cleanup',
    'maint_image_cleanup_warning' => ':count potentially unused images were found. Are you sure you want to delete these images?',
    'maint_image_cleanup_success' => ':count potentially unused images found and deleted!',
    'maint_image_cleanup_nothing_found' => 'No unused images found, Nothing deleted!',

    // Role Settings
    'roles' => 'Roles',
    'role_user_roles' => 'User Roles',
    'role_create' => 'Create New Role',
    'role_create_success' => 'Role successfully created',
    'role_delete' => 'Delete Role',
    'role_delete_confirm' => 'This will delete the role with the name \':roleName\'.',
    'role_delete_users_assigned' => 'This role has :userCount users assigned to it. If you would like to migrate the users from this role select a new role below.',
    'role_delete_no_migration' => "Don't migrate users",
    'role_delete_sure' => 'Are you sure you want to delete this role?',
    'role_delete_success' => 'Role successfully deleted',
    'role_edit' => 'Edit Role',
    'role_details' => 'Role Details',
    'role_name' => 'Role Name',
    'role_desc' => 'Short Description of Role',
    'role_external_auth_id' => 'External Authentication IDs',
    'role_system' => 'System Permissions',
    'role_manage_users' => 'Manage users',
    'role_manage_roles' => 'Manage roles & role permissions',
    'role_manage_entity_permissions' => 'Manage all book, chapter & page permissions',
    'role_manage_own_entity_permissions' => 'Manage permissions on own book, chapter & pages',
    'role_manage_page_templates' => 'Manage page templates',
    'role_manage_settings' => 'Manage app settings',
    'role_asset' => 'Asset Permissions',
    'role_asset_desc' => 'These permissions control default access to the assets within the system. Permissions on Books, Chapters and Pages will override these permissions.',
    'role_asset_admins' => 'Admins are automatically given access to all content but these options may show or hide UI options.',
    'role_all' => 'All',
    'role_own' => 'Own',
    'role_controlled_by_asset' => 'Controlled by the asset they are uploaded to',
    'role_save' => 'Save Role',
    'role_update_success' => 'Role successfully updated',
    'role_users' => 'Users in this role',
    'role_users_none' => 'No users are currently assigned to this role',

    // Users
    'users' => 'Users',
    'user_profile' => 'User Profile',
    'users_add_new' => 'Add New User',
    'users_search' => 'Search Users',
    'users_details' => 'User Details',
    'users_details_desc' => 'Set a display name and an email address for this user. The email address will be used for logging into the application.',
    'users_details_desc_no_email' => 'Set a display name for this user so others can recognise them.',
    'users_role' => 'User Roles',
    'users_role_desc' => 'Select which roles this user will be assigned to. If a user is assigned to multiple roles the permissions from those roles will stack and they will receive all abilities of the assigned roles.',
    'users_password' => 'User Password',
    'users_password_desc' => 'Set a password used to log-in to the application. This must be at least 6 characters long.',
    'users_send_invite_text' => 'You can choose to send this user an invitation email which allows them to set their own password otherwise you can set their password yourself.',
    'users_send_invite_option' => 'Send user invite email',
    'users_external_auth_id' => 'External Authentication ID',
    'users_external_auth_id_desc' => 'This is the ID used to match this user when communicating with your LDAP system.',
    'users_password_warning' => 'Only fill the below if you would like to change your password.',
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
    'users_avatar_desc' => 'Select an image to represent this user. This should be approx 256px square.',
    'users_preferred_language' => 'Preferred Language',
    'users_preferred_language_desc' => 'This option will change the language used for the user-interface of the application. This will not affect any user-created content.',
    'users_social_accounts' => 'Social Accounts',
    'users_social_accounts_info' => 'Here you can connect your other accounts for quicker and easier login. Disconnecting an account here does not revoke previously authorized access. Revoke access from your profile settings on the connected social account.',
    'users_social_connect' => 'Connect Account',
    'users_social_disconnect' => 'Disconnect Account',
    'users_social_connected' => ':socialAccount account was successfully attached to your profile.',
    'users_social_disconnected' => ':socialAccount account was successfully disconnected from your profile.',

    //! Since these labels are already localized this array does not need to be
    //! translated in the language-specific files.
    //! DELETE BELOW IF COPIED FROM EN
    //!////////////////////////////////
    'language_select' => [
        'en' => 'English',
        'ar' => 'العربية',
        'de' => 'Deutsch (Sie)',
        'de_informal' => 'Deutsch (Du)',
        'es' => 'Español',
        'es_AR' => 'Español Argentina',
        'fr' => 'Français',
        'nl' => 'Nederlands',
        'pt_BR' => 'Português do Brasil',
        'sk' => 'Slovensky',
        'cs' => 'Česky',
        'sv' => 'Svenska',
        'kr' => '한국어',
        'ja' => '日本語',
        'pl' => 'Polski',
        'it' => 'Italian',
        'ru' => 'Русский',
        'uk' => 'Українська',
        'zh_CN' => '简体中文',
	'zh_TW' => '繁體中文',
    'hu' => 'Magyar',
    'tr' => 'Türkçe'
    ]
    //!////////////////////////////////
];
