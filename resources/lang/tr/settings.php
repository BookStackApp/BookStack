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
    'settings_save_success' => 'Ayarlar Kaydedildi',

    // App Settings
    'app_customization' => 'Özelleştirme',
    'app_features_security' => 'Özellikler & Güvenlik',
    'app_name' => 'Uygulama Adı',
    'app_name_desc' => 'Bu isim başlıkta ve sistem tarafında gönderilen tüm mesajlarda gösterilecektir.',
    'app_name_header' => 'İsmi başlıkta göster',
    'app_public_access' => 'Açık Erişim',
    'app_public_access_desc' => 'Bu özelliği aktif etmek giriş yapmamış misafir kullanıcıların sizin BookStack uygulamanıza erişmesini sağlar',
    'app_public_access_desc_guest' => 'Kayıtlı olmayan kullanıcılar için erişim yetkisi "Guest" kullanıcısı üzerinden düzenlenebilir.',
    'app_public_access_toggle' => 'Açık erişime izin ver',
    'app_public_viewing' => 'Herkese açık görüntülenmeye izin verilsin mi?',
    'app_secure_images' => 'Daha Yüksek Güvenlikli Görsel Yüklemeleri',
    'app_secure_images_toggle' => 'Daha yüksek güveblikli görsel yüklemelerine izin ver',
    'app_secure_images_desc' => 'Performans sebepleri nedeniyle bütün görseller halka açık. Bu opsiyon rastgele ve tahmin edilmesi zor dizileri görsel linklerinin önüne ekler. Dizin indexlerinin kapalı olduğundan emin olun.',
    'app_editor' => 'Sayfa Editörü',
    'app_editor_desc' => 'Sayfa düzenlemesi yapılırken hangi editörün kullanılacağını seçin.',
    'app_custom_html' => 'Özel HTML Head İçeriği',
    'app_custom_html_desc' => 'Buraya eklenecek olan içerik <head> taginin en sonuna eklenecektir. Bu stilleri override ederken veya analytics eklerken faydalı bir kullanım şeklidir.',
    'app_custom_html_disabled_notice' => 'Yapılan hatalı değişikliklerin geriye alınabilmesi için bu sayfada özel HTML head içeriği kapalı.',
    'app_logo' => 'Uygulama Logosu',
    'app_logo_desc' => 'Bu görsel 43px yüksekliğinde olmalı. <br>Büyük görseller ölçeklenecektir.',
    'app_primary_color' => 'Uygulamanın Birincil Rengi',
    'app_primary_color_desc' => 'Sets the primary color for the application including the banner, buttons, and links.',
    'app_homepage' => 'Uygulama Anasayfası',
    'app_homepage_desc' => 'Anasayfada görünmesi için bir view seçin. Sayfa izinleri seçili sayfalar için yok sayılacaktır.',
    'app_homepage_select' => 'Sayfa seçiniz',
    'app_disable_comments' => 'Yorumları Engelle',
    'app_disable_comments_toggle' => 'Yorumları engelle',
    'app_disable_comments_desc' => 'Yorumları uygulamadaki bütün sayfalar için engelle. <br> Mevcut yorumlar gösterilmeyecektir.',

    // Color settings
    'content_colors' => 'Content Colors',
    'content_colors_desc' => 'Sets colors for all elements in the page organisation hierarchy. Choosing colors with a similar brightness to the default colors is recommended for readability.',
    'bookshelf_color' => 'Shelf Color',
    'book_color' => 'Book Color',
    'chapter_color' => 'Chapter Color',
    'page_color' => 'Page Color',
    'page_draft_color' => 'Page Draft Color',

    // Registration Settings
    'reg_settings' => 'Kayıt',
    'reg_enable' => 'Kaydolmaya İzin Ver',
    'reg_enable_toggle' => 'Kaydolmaya izin ver',
    'reg_enable_desc' => 'Kayıt olmaya izin verdiğinizde kullanıcılar kendilerini uygulamaya kaydedebilecekler. Kayıt olduktan sonra kendilerine varsayılan kullanıcı rolü atanacaktır.',
    'reg_default_role' => 'Kayıt olduktan sonra varsayılan kullanıcı rolü',
    'reg_enable_ldap_warning' => 'The option above is not used while LDAP authentication is active. User accounts for non-existing members will be auto-created if authentication, against the LDAP system in use, is successful.',
    'reg_email_confirmation' => 'Email Doğrulama',
    'reg_email_confirmation_toggle' => 'E-mail onayı gerektir',
    'reg_confirm_email_desc' => 'Eğer domain kısıtlaması kullanılıyorsa o zaman email doğrulaması gereklidir ve bu seçenek yok sayılacaktır.',
    'reg_confirm_restrict_domain' => 'Domain Kısıtlaması',
    'reg_confirm_restrict_domain_desc' => 'Kısıtlamak istediğiniz email domainlerini vigül ile ayırarak yazınız. Kullanıcılara uygulamaya erişmeden önce adreslerini doğrulamak için bir mail gönderilecektir. <br> Kullanıcılar başarıyla kaydolduktan sonra email adreslerini değiştiremeyeceklerdir.',
    'reg_confirm_restrict_domain_placeholder' => 'Hiçbir kısıtlama tanımlanmamış',

    // Maintenance settings
    'maint' => 'Bakım',
    'maint_image_cleanup' => 'Görsel Temizliği',
    'maint_image_cleanup_desc' => "Sayfaları ve revizyon içeriklerini tarayarak hangi gösel ve çizimlerin kullanımda olduğunu ve hangilerinin gereksiz olduğunu tespit eder. Bunu başlatmadan veritabanı ve görsellerin tam bir yedeğinin alındığından emin olun.",
    'maint_image_cleanup_ignore_revisions' => 'Revizyonlardaki görselleri yoksay',
    'maint_image_cleanup_run' => 'Temizliği Başlat',
    'maint_image_cleanup_warning' => ':count potansiyel kullanılmayan görsel bulundu. Bu görselleri silmek istediğinizden emin misiniz?',
    'maint_image_cleanup_success' => ':count potanisyel kullanılmayan görsel bulundu ve silindi!',
    'maint_image_cleanup_nothing_found' => 'Kullanılmayan görsel bulunamadı ve birşey silinmedi!',
    'maint_send_test_email' => 'Test E-Postası Gönder',
    'maint_send_test_email_desc' => 'Bu profilinizde girdiğiniz e-posta adresine bir test e-postası gönderir.',
    'maint_send_test_email_run' => 'Test E-Postasını gönder',
    'maint_send_test_email_success' => 'E-Posta :address adresine gönderildi',
    'maint_send_test_email_mail_subject' => 'Test e-postası',
    'maint_send_test_email_mail_greeting' => 'E-Posta gönderimi başarılı!',
    'maint_send_test_email_mail_text' => 'Tebrikler! Eğer bu e-posta bildirimini alıyorsanız, e-posta ayarlarınız doğru bir şekilde ayarlanmış demektir.',

    // Role Settings
    'roles' => 'Roller',
    'role_user_roles' => 'Kullanıcı Rolleri',
    'role_create' => 'Yeni Rol Oluştur',
    'role_create_success' => 'Rol Başarıyla Oluşturuldu',
    'role_delete' => 'Rolü Sil',
    'role_delete_confirm' => 'Bu işlem \':roleName\' rolünü silecektir.',
    'role_delete_users_assigned' => 'Bu role atanmış :userCount adet kullanıcı var. Eğer bu kullanıcıların rollerini değiştirmek istiyorsanız aşağıdan yeni bir rol seçin.',
    'role_delete_no_migration' => "Kullanıcıları taşıma",
    'role_delete_sure' => 'Bu rolü silmek istediğinizden emin misiniz?',
    'role_delete_success' => 'Rol başarıyla silindi',
    'role_edit' => 'Rolü Düzenle',
    'role_details' => 'Rol Detayları',
    'role_name' => 'Rol Adı',
    'role_desc' => 'Rolün Kısa Tanımı',
    'role_external_auth_id' => 'Harici Authentication ID\'leri',
    'role_system' => 'Sistem Yetkileri',
    'role_manage_users' => 'Kullanıcıları yönet',
    'role_manage_roles' => 'Rolleri ve rol izinlerini yönet',
    'role_manage_entity_permissions' => 'Bütün kitap, bölüm ve sayfa izinlerini yönet',
    'role_manage_own_entity_permissions' => 'Sahip olunan kitap, bölüm ve sayfaların izinlerini yönet',
    'role_manage_page_templates' => 'Sayfa şablonlarını yönet',
    'role_manage_settings' => 'Uygulama ayarlarını yönet',
    'role_asset' => 'Asset Yetkileri',
    'role_asset_desc' => 'Bu izinleri assetlere sistem içinden varsayılan erişimi kontrol eder. Kitaplar, bölümler ve sayfaların izinleri bu izinleri override eder.',
    'role_asset_admins' => 'Yöneticilere otomatik olarak bütün içeriğe erişim yetkisi verilir fakat bu opsiyonlar UI özelliklerini gösterir veya gizler.',
    'role_all' => 'Hepsi',
    'role_own' => 'Sahip Olunan',
    'role_controlled_by_asset' => 'Yükledikleri asset tarafından kontrol ediliyor',
    'role_save' => 'Rolü Kaydet',
    'role_update_success' => 'Rol başarıyla güncellendi',
    'role_users' => 'Bu roldeki kullanıcılar',
    'role_users_none' => 'Bu role henüz bir kullanıcı atanmadı',

    // Users
    'users' => 'Kullanıcılar',
    'user_profile' => 'Kullanıcı Profili',
    'users_add_new' => 'Yeni Kullanıcı Ekle',
    'users_search' => 'Kullanıcıları Ara',
    'users_details' => 'Kullanıcı Detayları',
    'users_details_desc' => 'Bu kullanıcı için gösterilecek bir isim ve mail adresi belirleyin. Bu e-mail adresi kullanıcı tarafından giriş yaparken kullanılacak.',
    'users_details_desc_no_email' => 'Diğer kullanıcılar tarafından tanınabilmesi için bir isim belirleyin.',
    'users_role' => 'Kullanıcı Rolleri',
    'users_role_desc' => 'Bu kullanıcının hangi rollere atanabileceğini belirleyin. Eğer bir kullanıcıya birden fazla rol atanırsa, kullanıcı bütün rollerin özelliklerini kullanabilir.',
    'users_password' => 'Kullanıcı Parolası',
    'users_password_desc' => 'Kullanıcının giriş yaparken kullanacağı bir parola belirleyin. Parola en az 5 karakter olmalıdır.',
    'users_send_invite_text' => 'Bu kullanıcıya parolasını sıfırlayabilmesi için bir e-posta gönder veya şifresini sen belirle.',
    'users_send_invite_option' => 'Kullanıcıya davet e-postası gönder',
    'users_external_auth_id' => 'Harici Authentication ID\'si',
    'users_external_auth_id_desc' => 'Bu ID kullanıcı LDAP sunucu ile bağlantı kurarken kullanılır.',
    'users_password_warning' => 'Sadece parolanızı değiştirmek istiyorsanız aşağıyı doldurunuz.',
    'users_system_public' => 'Bu kullanıcı sizin uygulamanızı ziyaret eden bütün misafir kullanıcıları temsil eder. Giriş yapmak için kullanılamaz, otomatik olarak atanır.',
    'users_delete' => 'Kullanıcı Sil',
    'users_delete_named' => ':userName kullanıcısını sil ',
    'users_delete_warning' => 'Bu işlem \':userName\' kullanıcısını sistemden tamamen silecektir.',
    'users_delete_confirm' => 'Bu kullanıcıyı tamamen silmek istediğinize emin misiniz?',
    'users_delete_success' => 'Kullanıcılar başarıyla silindi.',
    'users_edit' => 'Kullanıcıyı Güncelle',
    'users_edit_profile' => 'Profili Düzenle',
    'users_edit_success' => 'Kullanıcı başarıyla güncellendi',
    'users_avatar' => 'Kullanıcı Avatarı',
    'users_avatar_desc' => 'Bu kullanıcıyı temsil eden bir görsel seçin. Yaklaşık 256px kare olmalıdır.',
    'users_preferred_language' => 'Tercih Edilen Dil',
    'users_preferred_language_desc' => 'Bu seçenek kullanıcı arayüzünün dilini değiştirecektir. Herhangi bir kullanıcı içeriğini etkilemeyecektir.',
    'users_social_accounts' => 'Sosyal Hesaplar',
    'users_social_accounts_info' => 'Burada diğer hesaplarınızı ekleyerek daha hızlı ve kolay giriş sağlayabilirsiniz. Bir hesabın bağlantısını kesmek daha önce edilnilen erişiminizi kaldırmaz. Profil ayarlarınızdan bağlı sosyal hesabınızın erişimini kaldırınız.',
    'users_social_connect' => 'Hesap Bağla',
    'users_social_disconnect' => 'Hesabın Bağlantısını Kes',
    'users_social_connected' => ':socialAccount hesabı profilinize başarıyla bağlandı.',
    'users_social_disconnected' => ':socialAccount hesabınızın profilinizle ilişiği başarıyla kesildi.',

    //! If editing translations files directly please ignore this in all
    //! languages apart from en. Content will be auto-copied from en.
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
        'ko' => '한국어',
        'ja' => '日本語',
        'pl' => 'Polski',
        'it' => 'Italian',
        'ru' => 'Русский',
        'uk' => 'Українська',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文',
        'hu' => 'Magyar',
        'tr' => 'Türkçe',
    ]
    //!////////////////////////////////
];
