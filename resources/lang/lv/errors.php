<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Jums nav atļauts piekļūt šai lapai.',
    'permissionJson' => 'Jums nav atļauts veikt konkrēto darbību.',

    // Auth
    'error_user_exists_different_creds' => 'Lietotājs ar epastu :email bet ar citiem piekļuves datiem jau eksistē.',
    'email_already_confirmed' => 'Epasts jau ir apstiprināts, mēģini ielogoties.',
    'email_confirmation_invalid' => 'Šis apstiprinājuma žetons nav derīgs vai jau ir izmantots. Lūdzu, mēģiniet reģistrēties vēlreiz.',
    'email_confirmation_expired' => 'Apstiprinājuma žetona derīguma termiņš ir beidzies. Ir nosūtīts jauns apstiprinājuma e-pasts.',
    'email_confirmation_awaiting' => 'Šī konta e-pasta adresei ir nepieciešms apstiprinājums',
    'ldap_fail_anonymous' => 'LDAP piekļuve neveiksmīga izmantojot anonymous bind',
    'ldap_fail_authed' => 'LDAP piekļuve neveiksmīga izmantojot norādīto dn un paroli',
    'ldap_extension_not_installed' => 'LDAP PHP paplašinājums nav instalēts',
    'ldap_cannot_connect' => 'Nav iespējams pieslēgties LDAP serverim, sākotnējais pieslēgums neveiksmīgs',
    'saml_already_logged_in' => 'Jau ielogojies',
    'saml_user_not_registered' => 'Lietotājs :name nav reģistrēts un automātiska reģistrācija ir izslēgta',
    'saml_no_email_address' => 'Ārējās autentifikācijas sistēmas sniegtajos datos nevarēja atrast šī lietotāja e-pasta adresi',
    'saml_invalid_response_id' => 'Ārējās autentifikācijas sistēmas pieprasījums neatpazīst procesu, kuru sākusi šī lietojumprogramma. Pārvietojoties atpakaļ pēc pieteikšanās var rasties šāda problēma.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'Darbības nav definētas',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => 'This :socialAccount account is already in use, Try logging in via the :socialAccount option.',
    'social_account_email_in_use' => 'The email :email is already in use. If you already have an account you can connect your :socialAccount account from your profile settings.',
    'social_account_existing' => 'This :socialAccount is already attached to your profile.',
    'social_account_already_used_existing' => 'This :socialAccount account is already used by another user.',
    'social_account_not_used' => 'This :socialAccount account is not linked to any users. Please attach it in your profile settings. ',
    'social_account_register_instructions' => 'If you do not yet have an account, You can register an account using the :socialAccount option.',
    'social_driver_not_found' => 'Social driver not found',
    'social_driver_not_configured' => 'Jūsu :socialAccount sociālie iestatījumi nav uzstādīti pareizi.',
    'invite_token_expired' => 'Šī uzaicinājuma saite ir novecojusi. Tā vietā jūs varat mēģināt atiestatīt sava konta paroli.',

    // System
    'path_not_writable' => 'Faila ceļā :filePath nav iespējams ielādēt failus. Lūdzu pārliecinieties, ka serverim tur ir rakstīšanas tiesības.',
    'cannot_get_image_from_url' => 'Nevar iegūt bildi no :url',
    'cannot_create_thumbs' => 'Serveris nevar izveidot samazinātus attēlus. Lūdzu pārbaudiet, vai ir uzstādīts PHP GD paplašinājums.',
    'server_upload_limit' => 'Serveris neatļauj šāda izmēra failu ielādi. Lūdzu mēģiniet mazāka izmēra failu.',
    'uploaded'  => 'Serveris neatļauj šāda izmēra failu ielādi. Lūdzu mēģiniet mazāka izmēra failu.',
    'image_upload_error' => 'Radās kļūda augšupielādējot attēlu',
    'image_upload_type_error' => 'Ielādējamā attēla tips nav derīgs',
    'file_upload_timeout' => 'Faila augšupielādē ir iestājies noilgums.',

    // Attachments
    'attachment_not_found' => 'Pielikums nav atrasts',

    // Pages
    'page_draft_autosave_fail' => 'Failed to save draft. Ensure you have internet connection before saving this page',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'Vienība nav atrasta',
    'bookshelf_not_found' => 'Grāmatplaukts nav atrasts',
    'book_not_found' => 'Grāmata nav atrasta',
    'page_not_found' => 'Lapa nav atrasta',
    'chapter_not_found' => 'Nodaļa nav atrasta',
    'selected_book_not_found' => 'Iezīmētā grāmata nav atrasta',
    'selected_book_chapter_not_found' => 'Izvēlētā grāmata vai nodaļa nav atrasta',
    'guests_cannot_save_drafts' => 'Viesi nevar saglabāt melnrakstus',

    // Users
    'users_cannot_delete_only_admin' => 'Jūs nevarat dzēst vienīgo administratoru',
    'users_cannot_delete_guest' => 'Jūs nevarat dzēst lietotāju "viesis"',

    // Roles
    'role_cannot_be_edited' => 'Šo lomu nevar rediģēt',
    'role_system_cannot_be_deleted' => 'Šī ir sistēmas loma un nevar tikt izdzēsta',
    'role_registration_default_cannot_delete' => 'Šī loma nevar tikt izdzēsta, kamēr tā uzstādīta kā noklusētā reģistrācijas loma',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'Radās kļūda ielasot komentārus.',
    'cannot_add_comment_to_draft' => 'Melnrakstam nevar pievienot komentārus.',
    'comment_add' => 'Radās kļūda pievienojot/atjaunojot komentāru.',
    'comment_delete' => 'Radās kļūda dzēšot komentāru.',
    'empty_comment' => 'Nevar pievienot tukšu komentāru.',

    // Error pages
    '404_page_not_found' => 'Lapa nav atrasta',
    'sorry_page_not_found' => 'Sorry, The page you were looking for could not be found.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'return_home' => 'Atgriezties uz sākumu',
    'error_occurred' => 'Radusies kļūda',
    'app_down' => ':appName pagaidām nav pieejams',
    'back_soon' => 'Drīz būs atkal pieejams.',

    // API errors
    'api_no_authorization_found' => 'No authorization token found on the request',
    'api_bad_authorization_format' => 'An authorization token was found on the request but the format appeared incorrect',
    'api_user_token_not_found' => 'No matching API token was found for the provided authorization token',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'The owner of the used API token does not have permission to make API calls',
    'api_user_token_expired' => 'The authorization token used has expired',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Radusies kļūda sūtot testa epastu:',

];
