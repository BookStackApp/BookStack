<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Ez duzu baimenik eskatutako baliabidean sartzeko.',
    'permissionJson' => 'Ez duzu baimenik ekintza hau egiteko.',

    // Auth
    'error_user_exists_different_creds' => ':email kontuakin erabiltzaile bat badago, baina kredentzial ezberdinekin.',
    'email_already_confirmed' => 'Email kontua berretsita dago, saiatu saioa hasten.',
    'email_confirmation_invalid' => 'Berrezpen token hau ez da baliozkoa eta iada erabiltzen da, mesedez, saiatu berriz erregistroa burutzen.',
    'email_confirmation_expired' => 'Berrezpen tokena iraungi da, berrezpen email berri bnat bidali da.',
    'email_confirmation_awaiting' => 'Erabiltzen ari den kontuko emaiala berreztea falta da',
    'ldap_fail_anonymous' => 'LDAP sarrerak akatsa eman du lotura anonimoa erabiliz',
    'ldap_fail_authed' => 'LDAP sarrera akatsa eman du dn eta pasahitz hauekin',
    'ldap_extension_not_installed' => 'PHP LDAP extentsioa ez dago instalatuta',
    'ldap_cannot_connect' => 'Ezin izan da ldap zerbitzarira konektatu, hasierako konexioak huts egin du',
    'saml_already_logged_in' => 'Saioa aurretik hasita dago',
    'saml_user_not_registered' => ':name erabiltzailea ez dago erregistratua eta erregistro automatikoa ezgaituta dago',
    'saml_no_email_address' => 'Ezin izan dugu posta helbiderik aurkitu erabiltzaile honentzat, kanpoko autentifikazio zerbitzuak bidalitako datuetan',
    'saml_invalid_response_id' => 'Kanpoko egiazkotasun-sistemaren eskaria ez du onartzen aplikazio honek abiarazitako prozesu batek. Loginean atzera egitea izan daiteke arrazoia.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'oidc_already_logged_in' => 'Dagoeneko saioa hasita',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
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
    'image_upload_error' => 'Errorea gertatu da irudia igotzerakoan',
    'image_upload_type_error' => 'The image type being uploaded is invalid',
    'file_upload_timeout' => 'The file upload has timed out.',

    // Attachments
    'attachment_not_found' => 'Atxikia ez da aurkitu',

    // Pages
    'page_draft_autosave_fail' => 'Failed to save draft. Ensure you have internet connection before saving this page',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'Entity not found',
    'bookshelf_not_found' => 'Shelf not found',
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
    '404_page_not_found' => 'Ez da orrialdea aurkitu',
    'sorry_page_not_found' => 'Sorry, The page you were looking for could not be found.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'image_not_found' => 'Irudia Ez da Aurkitu',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Itzuli hasierara',
    'error_occurred' => 'Akats bat gertatu da',
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
