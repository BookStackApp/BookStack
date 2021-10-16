<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Sul puudub õigus selle lehe vaatamiseks.',
    'permissionJson' => 'Sul puudub õigus selle tegevuse teostamiseks.',

    // Auth
    'error_user_exists_different_creds' => 'A user with the email :email already exists but with different credentials.',
    'email_already_confirmed' => 'E-posti aadress on juba kinnitatud. Proovi sisse logida.',
    'email_confirmation_invalid' => 'This confirmation token is not valid or has already been used, Please try registering again.',
    'email_confirmation_expired' => 'The confirmation token has expired, A new confirmation email has been sent.',
    'email_confirmation_awaiting' => 'The email address for the account in use needs to be confirmed',
    'ldap_fail_anonymous' => 'LDAP access failed using anonymous bind',
    'ldap_fail_authed' => 'LDAP access failed using given dn & password details',
    'ldap_extension_not_installed' => 'LDAP PHP extension not installed',
    'ldap_cannot_connect' => 'Cannot connect to ldap server, Initial connection failed',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'Kasutaja :name ei ole registreeritud ning automaatne registreerimine on keelatud',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'No action defined',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => 'This :socialAccount account is already in use, Try logging in via the :socialAccount option.',
    'social_account_email_in_use' => 'The email :email is already in use. If you already have an account you can connect your :socialAccount account from your profile settings.',
    'social_account_existing' => 'This :socialAccount is already attached to your profile.',
    'social_account_already_used_existing' => 'This :socialAccount account is already used by another user.',
    'social_account_not_used' => 'This :socialAccount account is not linked to any users. Please attach it in your profile settings. ',
    'social_account_register_instructions' => 'If you do not yet have an account, You can register an account using the :socialAccount option.',
    'social_driver_not_found' => 'Social driver not found',
    'social_driver_not_configured' => 'Your :socialAccount social settings are not configured correctly.',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'File path :filePath could not be uploaded to. Ensure it is writable to the server.',
    'cannot_get_image_from_url' => 'Cannot get image from :url',
    'cannot_create_thumbs' => 'The server cannot create thumbnails. Please check you have the GD PHP extension installed.',
    'server_upload_limit' => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'uploaded'  => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'image_upload_error' => 'Pildi üleslaadimisel tekkis viga',
    'image_upload_type_error' => 'Pildifaili tüüp ei ole korrektne',
    'file_upload_timeout' => 'The file upload has timed out.',

    // Attachments
    'attachment_not_found' => 'Manust ei leitud',

    // Pages
    'page_draft_autosave_fail' => 'Mustandi salvestamine ebaõnnestus. Kontrolli oma internetiühendust',
    'page_custom_home_deletion' => 'Ei saa kustutada lehte, mis on määratud avaleheks',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Riiulit ei leitud',
    'book_not_found' => 'Raamatut ei leitud',
    'page_not_found' => 'Lehte ei leitud',
    'chapter_not_found' => 'Peatükki ei leitud',
    'selected_book_not_found' => 'Valitud raamatut ei leitud',
    'selected_book_chapter_not_found' => 'Valitud raamatut või peatükki ei leitud',
    'guests_cannot_save_drafts' => 'Külalised ei saa mustandeid salvestada',

    // Users
    'users_cannot_delete_only_admin' => 'Ainsat administraatorit ei saa kustutada',
    'users_cannot_delete_guest' => 'Külaliskasutajat ei saa kustutada',

    // Roles
    'role_cannot_be_edited' => 'Seda rolli ei saa muuta',
    'role_system_cannot_be_deleted' => 'See roll on süsteemne ja seda ei saa kustutada',
    'role_registration_default_cannot_delete' => 'Seda rolli ei saa kustutada, kuna see on seatud uute kasutajate vaikimisi rolliks',
    'role_cannot_remove_only_admin' => 'See kasutaja on ainus, kellel on administraatori roll. Enne kustutamist lisa administraatori roll mõnele teisele kasutajale.',

    // Comments
    'comment_list' => 'Kommentaaride pärimisel tekkis viga.',
    'cannot_add_comment_to_draft' => 'Mustandile ei saa kommentaare lisada.',
    'comment_add' => 'Kommentaari lisamisel / muutmisel tekkis viga.',
    'comment_delete' => 'Kommentaari kustutamisel tekkis viga.',
    'empty_comment' => 'Tühja kommentaari ei saa lisada.',

    // Error pages
    '404_page_not_found' => 'Lehekülge ei leitud',
    'sorry_page_not_found' => 'Vabandust, soovitud lehekülge ei leitud.',
    'sorry_page_not_found_permission_warning' => 'Kui see lehekülg peaks kindlalt olemas olema, ei pruugi sul olla õigust selle vaatamiseks.',
    'image_not_found' => 'Pildifaili ei leitud',
    'image_not_found_subtitle' => 'Vabandust, soovitud pildifaili ei leitud.',
    'image_not_found_details' => 'Kui sa eeldasid, et see pildifail on olemas, võib see olla kustutatud.',
    'return_home' => 'Tagasi avalehele',
    'error_occurred' => 'Tekkis viga',
    'app_down' => ':appName on hetkel maas',
    'back_soon' => 'It will be back up soon.',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => 'The authorization token used has expired',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Test e-kirja saatmisel tekkis viga:',

];
