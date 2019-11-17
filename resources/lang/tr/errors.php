<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Bu sayfaya erişme yetkiniz yok.',
    'permissionJson' => 'Bu işlemi yapmak için yetkiniz yo.',

    // Auth
    'error_user_exists_different_creds' => ':email adresi farklı kullanıcı bilgileri ile zaten kullanımda.',
    'email_already_confirmed' => 'E-mail halihazırda onaylanmış, giriş yapmayı dene.',
    'email_confirmation_invalid' => 'Bu doğrulama tokenı daha önce kullanılmış veya geçerli değil, lütfen tekrar kayıt olmayı deneyin.',
    'email_confirmation_expired' => 'Doğrulama token\'ının süresi geçmiş, yeni bir mail gönderildi.',
    'ldap_fail_anonymous' => 'Anonim LDAP girişi başarısız oldu',
    'ldap_fail_authed' => 'Verdiğiniz bilgiler ile LDAP girişi başarısız oldu.',
    'ldap_extension_not_installed' => 'LDAP PHP eklentisi yüklenmedi',
    'ldap_cannot_connect' => 'LDAP sunucusuna bağlanılamadı, ilk bağlantı başarısız oldu',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'saml_email_exists' => 'Registration unsuccessful since a user already exists with email address ":email"',
    'social_no_action_defined' => 'Bir aksiyon tanımlanmadı',
    'social_login_bad_response' => ":socialAccount girişi sırasında hata oluştu: \n:error",
    'social_account_in_use' => 'Bu :socialAccount zaten kullanımda, :socialAccount hesabıyla giriş yapmayı deneyin.',
    'social_account_email_in_use' => ':email adresi zaten kullanımda. Eğer zaten bir hesabınız varsa :socialAccount hesabınızı profil ayarları kısmından bağlayabilirsiniz.',
    'social_account_existing' => 'Bu :socialAccount zaten profilinize eklenmiş.',
    'social_account_already_used_existing' => 'Bu :socialAccount başka bir kullanıcı tarafından kullanılıyor.',
    'social_account_not_used' => 'Bu :socialAccount hesabı hiç bir kullanıcıya bağlı değil. Lütfen profil ayarlarına gidiniz ve bağlayınız. ',
    'social_account_register_instructions' => 'Hala bir hesabınız yoksa :socialAccount ile kayıt olabilirsiniz.',
    'social_driver_not_found' => 'Social driver bulunamadı',
    'social_driver_not_configured' => ':socialAccount ayarlarınız doğru bir şekilde ayarlanmadı.',
    'invite_token_expired' => 'Davetiye linkinin süresi doldu. Bunun yerine parolanızı sıfırlamayı deneyebilirsiniz.',

    // System
    'path_not_writable' => ':filePath dosya yolu yüklenemedi. Sunucuya yazılabilir olduğundan emin olun.',
    'cannot_get_image_from_url' => ':url\'den görsel alınamadı',
    'cannot_create_thumbs' => 'Sunucu küçük resimleri oluşturamadı. Lütfen GD PHP eklentisinin yüklü olduğundan emin olun.',
    'server_upload_limit' => 'Sunucu bu boyutta dosya yüklemenize izin vermiyor. Lütfen daha küçük boyutta dosya yüklemeyi deneyiniz.',
    'uploaded'  => 'Sunucu bu boyutta dosya yüklemenize izin vermiyor. Lütfen daha küçük boyutta dosya yüklemeyi deneyiniz.',
    'image_upload_error' => 'Görsel yüklenirken bir hata oluştu',
    'image_upload_type_error' => 'Yüklemeye çalıştığınız dosya türü geçerli değildir',
    'file_upload_timeout' => 'Dosya yüklemesi zaman aşımına uğradı',

    // Attachments
    'attachment_page_mismatch' => 'Ek güncellemesi sırasında sayfa uyuşmazlığı yaşandı',
    'attachment_not_found' => 'Ek bulunamadı',

    // Pages
    'page_draft_autosave_fail' => 'Taslak kaydetme başarısız. Sayfanızı kaydetmeden önce internet bağlantınız olduğundan emin olun',
    'page_custom_home_deletion' => 'Bu sayfa anasayfa olarak ayarlandığı için silinemez',

    // Entities
    'entity_not_found' => 'Eleman bulunamadı',
    'bookshelf_not_found' => 'Kitaplık bulunamadı',
    'book_not_found' => 'Kitap bulunamadı',
    'page_not_found' => 'Sayfa bulunamadı',
    'chapter_not_found' => 'Bölüm bulunamadı',
    'selected_book_not_found' => 'Seçilen kitap bulunamadı',
    'selected_book_chapter_not_found' => 'Seçilen kitap veya bölüm bulunamadı',
    'guests_cannot_save_drafts' => 'Misafirler taslak kaydedemezler',

    // Users
    'users_cannot_delete_only_admin' => 'Tek olan yöneticiyi silemezsiniz',
    'users_cannot_delete_guest' => 'Misafir kullanıyıcıyı silemezsiniz',

    // Roles
    'role_cannot_be_edited' => 'Bu rol düzenlenemez',
    'role_system_cannot_be_deleted' => 'Bu bir yönetici rolüdür ve silinemez',
    'role_registration_default_cannot_delete' => 'Bu rol varsayılan yönetici rolü olarak atandığı için silinemez ',
    'role_cannot_remove_only_admin' => 'Bu kullanıcı yönetici rolü olan tek kullanıcı olduğu için silinemez. Bu kullanıcıyı silmek için önce başka bir kullanıcıya yönetici rolü atayın.',

    // Comments
    'comment_list' => 'Yorumlar yüklenirken bir hata oluştu.',
    'cannot_add_comment_to_draft' => 'Taslaklara yorum ekleyemezsiniz.',
    'comment_add' => 'Yorum eklerken/güncellerken bir hata olıuştu.',
    'comment_delete' => 'Yorum silinirken bir hata oluştu.',
    'empty_comment' => 'Boş bir yorum eklenemez.',

    // Error pages
    '404_page_not_found' => 'Sayfa Bulunamadı',
    'sorry_page_not_found' => 'Üzgünüz, aradığınız sayfa bulunamıyor.',
    'return_home' => 'Anasayfaya dön',
    'error_occurred' => 'Bir Hata Oluştu',
    'app_down' => ':appName şu anda inaktif',
    'back_soon' => 'En kısa zamanda aktif hale gelecek.',

];
