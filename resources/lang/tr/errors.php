<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'İstenilen sayfaya erişim izniniz yok.',
    'permissionJson' => 'İstenen işlemi gerçekleştirme izniniz yok.',

    // Auth
    'error_user_exists_different_creds' => 'E-postaya sahip bir kullanıcı zaten var, ancak :email farklı kimlik bilgilerine sahip.',
    'email_already_confirmed' => 'E-posta zaten onaylandı, Giriş yapmayı deneyin.',
    'email_confirmation_invalid' => 'Bu onaylama kodu geçerli değil veya daha önce kullanılmış. Lütfen tekrar kayıt olmayı deneyin.',
    'email_confirmation_expired' => 'Onay kodunun süresi doldu, Yeni bir onay e-postası gönderildi.',
    'ldap_fail_anonymous' => 'LDAP erişimi anonim bağlantı kullanarak başarısız oldu',
    'ldap_fail_authed' => 'LDAP erişimi, verilen dn ve şifre bilgileri kullanılarak başarısız oldu',
    'ldap_extension_not_installed' => 'LDAP PHP uzantısı yüklü değil',
    'ldap_cannot_connect' => 'LDAP sunucusuna bağlanılamıyor, İlk bağlantı başarısız',
    'social_no_action_defined' => 'Eylem tanımlanmadı',
    'social_login_bad_response' => ":socialAccount hesabına bağlanırken bir hata oluştu: \n:error",
    'social_account_in_use' => ':socialAccount hesabı zaten kullanımda, Lütfen :socialAccount seçeneği ile bağlanmayı deneyin.',
    'social_account_email_in_use' => ':email e-postası zten kullanımda. Eğer zaten hesabınız var ise :socialAccount ile profil ayarlarından bağlanabilirsiniz.',
    'social_account_existing' => 'Bu :socialAccount hesabı zaten profilinizle ilişkilendirilmiş.',
    'social_account_already_used_existing' => 'Bu :socialAccount hesabı başka bir kullanıcı tarafından zaten kullanılıyor.',
    'social_account_not_used' => 'Bu :socialAccount hesabı herhangi bir kullanıcı ile bağdaştırılamadı. Lütfen profil ayarlarında ekleyiniz. ',
    'social_account_register_instructions' => 'Henüz bir hesabınız yoksa, :socialAccount seçeneğini kullanarak bir hesap açabilirsiniz.',
    'social_driver_not_found' => 'Social driver not found',
    'social_driver_not_configured' => 'Sizin :socialAccount sosyal ayarlarınız düzgün yapılmamış.',
    'invite_token_expired' => 'Bu davet bağlantısının süresi doldu. Bunun yerine hesap şifrenizi sıfırlamayı deneyebilirsiniz.',

    // System
    'path_not_writable' => 'File path :filePath could not be uploaded to. Ensure it is writable to the server.',
    'cannot_get_image_from_url' => 'Cannot get image from :url',
    'cannot_create_thumbs' => 'The server cannot create thumbnails. Please check you have the GD PHP extension installed.',
    'server_upload_limit' => 'Sunucu bu boyutta yüklemelere izin vermiyor. Lütfen daha küçük bir dosya boyutu deneyin.',
    'uploaded'  => 'Sunucu bu boyutta yüklemelere izin vermiyor. Lütfen daha küçük bir dosya boyutu deneyin.',
    'image_upload_error' => 'Resim yüklenirken bir hata oluştu',
    'image_upload_type_error' => 'Yüklenmekte olan resim türü geçersiz',
    'file_upload_timeout' => 'Dosya yükleme işlemi zaman aşımına uğradı.',

    // Attachments
    'attachment_page_mismatch' => 'Ek güncelleme sırasında sayfa uyuşmazlığı',
    'attachment_not_found' => 'Ek bulunamadı',

    // Pages
    'page_draft_autosave_fail' => 'Taslak kaydedilemedi. Bu sayfayı kaydetmeden önce internet bağlantınızın olduğundan emin olun.',
    'page_custom_home_deletion' => 'Ana sayfa olarak ayarlanmış bir sayfayı silinemez',

    // Entities
    'entity_not_found' => 'Öğe bulunamadı',
    'bookshelf_not_found' => 'Kitaplık bulunamadı',
    'book_not_found' => 'Kitap bulunamadı',
    'page_not_found' => 'Sayfa bulunamadı',
    'chapter_not_found' => 'Bölüm bulunamadı',
    'selected_book_not_found' => 'Seçilen kitap bulunamadı',
    'selected_book_chapter_not_found' => 'Seçilen kitap ya da bölüm bulunamadı',
    'guests_cannot_save_drafts' => 'Misafirler taslak kaydedemezler',

    // Users
    'users_cannot_delete_only_admin' => 'Sadece yöneticiler silebilir',
    'users_cannot_delete_guest' => 'Misafir kullanıcıyı silmeye yetkiniz yok',

    // Roles
    'role_cannot_be_edited' => 'Bu rol düzenlemedi',
    'role_system_cannot_be_deleted' => 'Bu bir sistem rolüdür ve silinemez',
    'role_registration_default_cannot_delete' => 'Bu rol, varsayılan kayıt rolü olarak kaldıkça silinemez',
    'role_cannot_remove_only_admin' => 'Bu kullanıcı yönetici rolüne atanan tek kullanıcıdır. Burada kaldırmayı denemeden önce yönetici rolünü başka bir kullanıcıya atayın.',

    // Comments
    'comment_list' => 'Yorumlar yüklenirken bir hata oluştu.',
    'cannot_add_comment_to_draft' => 'Bir taslağa yorum ekleyemezsiniz.',
    'comment_add' => 'Yorum eklerken/güncellerken bşr hata oluştu.',
    'comment_delete' => 'Yorumu silerken bir hata oluştu.',
    'empty_comment' => 'Boş yorum eklenemez.',

    // Error pages
    '404_page_not_found' => 'Sayfa Bulunamadı',
    'sorry_page_not_found' => 'Üzgünüm, aradığınız sayfa bulunamadı.',
    'return_home' => 'Ana sayfaya dön',
    'error_occurred' => 'Bir Hata Oluştu',
    'app_down' => ':appName şimdi çöktü',
    'back_soon' => 'Yakında geri dönecektir.',

];
