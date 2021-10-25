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
    'oidc_already_logged_in' => 'Already logged in',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
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
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

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
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'image_not_found' => 'Image Not Found',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Return to home',
    'error_occurred' => 'An Error Occurred',
    'app_down' => ':appName is down right now',
    'back_soon' => 'It will be back up soon.',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => 'The authorization token used has expired',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Error thrown when sending a test email:',

];
