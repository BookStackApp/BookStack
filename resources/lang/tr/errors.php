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
    'server_upload_limit' => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'uploaded'  => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'image_upload_error' => 'An error occurred uploading the image',
    'image_upload_type_error' => 'The image type being uploaded is invalid',
    'file_upload_timeout' => 'The file upload has timed out.',

    // Attachments
    'attachment_page_mismatch' => 'Page mismatch during attachment update',
    'attachment_not_found' => 'Attachment not found',

    // Pages
    'page_draft_autosave_fail' => 'Failed to save draft. Ensure you have internet connection before saving this page',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Bookshelf not found',
    'book_not_found' => 'Book not found',
    'page_not_found' => 'Page not found',
    'chapter_not_found' => 'Chapter not found',
    'selected_book_not_found' => 'The selected book was not found',
    'selected_book_chapter_not_found' => 'The selected Book or Chapter was not found',
    'guests_cannot_save_drafts' => 'Guests cannot save drafts',

    // Users
    'users_cannot_delete_only_admin' => 'You cannot delete the only admin',
    'users_cannot_delete_guest' => 'You cannot delete the guest user',

    // Roles
    'role_cannot_be_edited' => 'This role cannot be edited',
    'role_system_cannot_be_deleted' => 'This role is a system role and cannot be deleted',
    'role_registration_default_cannot_delete' => 'This role cannot be deleted while set as the default registration role',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'An error occurred while fetching the comments.',
    'cannot_add_comment_to_draft' => 'You cannot add comments to a draft.',
    'comment_add' => 'An error occurred while adding / updating the comment.',
    'comment_delete' => 'An error occurred while deleting the comment.',
    'empty_comment' => 'Cannot add an empty comment.',

    // Error pages
    '404_page_not_found' => 'Page Not Found',
    'sorry_page_not_found' => 'Sorry, The page you were looking for could not be found.',
    'return_home' => 'Return to home',
    'error_occurred' => 'An Error Occurred',
    'app_down' => ':appName is down right now',
    'back_soon' => 'It will be back up soon.',

];
