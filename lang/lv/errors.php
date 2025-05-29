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
    'auth_pre_register_theme_prevention' => 'Lietotāja kontu nevar reģistrēt ar norādītajām detaļām',
    'email_already_confirmed' => 'Epasts jau ir apstiprināts, mēģini ielogoties.',
    'email_confirmation_invalid' => 'Šis apstiprinājuma žetons nav derīgs vai jau ir izmantots. Lūdzu, mēģiniet reģistrēties vēlreiz.',
    'email_confirmation_expired' => 'Apstiprinājuma žetona derīguma termiņš ir beidzies. Ir nosūtīts jauns apstiprinājuma e-pasts.',
    'email_confirmation_awaiting' => 'Šī konta e-pasta adresei ir nepieciešms apstiprinājums',
    'ldap_fail_anonymous' => 'LDAP piekļuve neveiksmīga izmantojot anonymous bind',
    'ldap_fail_authed' => 'LDAP piekļuve neveiksmīga izmantojot norādīto dn un paroli',
    'ldap_extension_not_installed' => 'LDAP PHP paplašinājums nav instalēts',
    'ldap_cannot_connect' => 'Nav iespējams pieslēgties LDAP serverim, sākotnējais pieslēgums neveiksmīgs',
    'saml_already_logged_in' => 'Jau ielogojies',
    'saml_no_email_address' => 'Ārējās autentifikācijas sistēmas sniegtajos datos nevarēja atrast šī lietotāja e-pasta adresi',
    'saml_invalid_response_id' => 'Ārējās autentifikācijas sistēmas pieprasījums neatpazīst procesu, kuru sākusi šī lietojumprogramma. Pārvietojoties atpakaļ pēc pieteikšanās var rasties šāda problēma.',
    'saml_fail_authed' => 'Piekļuve ar :system neizdevās, sistēma nepieļāva veiksmīgu autorizāciju',
    'oidc_already_logged_in' => 'Jau esat ielogojies',
    'oidc_no_email_address' => 'Ārējās autentifikācijas sistēmas sniegtajos datos nevarēja atrast šī lietotāja e-pasta adresi',
    'oidc_fail_authed' => 'Piekļuve ar :system neizdevās, sistēma nepieļāva veiksmīgu autorizāciju',
    'social_no_action_defined' => 'Darbības nav definētas',
    'social_login_bad_response' => "Saņemta kļūda izmantojot :socialAccount piekļuvi:\n:error",
    'social_account_in_use' => 'Šis :socialAccount konts jau tiek izmantots, mēģiniet ieiet ar :socialAccount piekļuves iespēju.',
    'social_account_email_in_use' => 'Šis epasts :email jau tiek izmantots. Ja jums jau ir konts, jūs varat pieslēgt savu :socialAccount kontu savos profila uzstādījumos.',
    'social_account_existing' => 'Šis :socialAccount konts jau ir piesaistīts jūsu profilam.',
    'social_account_already_used_existing' => 'Šo :socialAccount konts jau ir piesaistīts citam lietotājam.',
    'social_account_not_used' => 'Šis :socialAccount konts nav piesaistīts nevienam lietotājām. Lūdzu pievienojiet to savos profila uzstādījumos. ',
    'social_account_register_instructions' => 'Ja jums vēl nav savs konts, jūs varat reģistrēt kontu izmantojot :socialAccount piekļuvi.',
    'social_driver_not_found' => 'Sociālā tīkla savienojums nav atrasts',
    'social_driver_not_configured' => 'Jūsu :socialAccount sociālie iestatījumi nav uzstādīti pareizi.',
    'invite_token_expired' => 'Šī uzaicinājuma saite ir novecojusi. Tā vietā jūs varat mēģināt atiestatīt sava konta paroli.',
    'login_user_not_found' => 'Šai darbībai netika atrasts lietotājs.',

    // System
    'path_not_writable' => 'Faila ceļā :filePath nav iespējams ielādēt failus. Lūdzu pārliecinieties, ka serverim tur ir rakstīšanas tiesības.',
    'cannot_get_image_from_url' => 'Nevar iegūt bildi no :url',
    'cannot_create_thumbs' => 'Serveris nevar izveidot samazinātus attēlus. Lūdzu pārbaudiet, vai ir uzstādīts PHP GD paplašinājums.',
    'server_upload_limit' => 'Serveris neatļauj šāda izmēra failu ielādi. Lūdzu mēģiniet mazāka izmēra failu.',
    'server_post_limit' => 'Serveris nevar apstrādāt šāda izmēra datus. Lūdzu mēģiniet vēlreiz ar mazāku datu apjomu vai mazāku failu.',
    'uploaded'  => 'Serveris neatļauj šāda izmēra failu ielādi. Lūdzu mēģiniet mazāka izmēra failu.',

    // Drawing & Images
    'image_upload_error' => 'Radās kļūda augšupielādējot attēlu',
    'image_upload_type_error' => 'Ielādējamā attēla tips nav derīgs',
    'image_upload_replace_type' => 'Aizvietojot attēlu tipiem ir jābūt vienādiem',
    'image_upload_memory_limit' => 'Neizdevās apstrādāt attēla ielādi vai izveidot attēlu variantus sistēmas resursu ierobežojumu dēļ.',
    'image_thumbnail_memory_limit' => 'Neizdevās izveidot attēla dažādu izmēru variantus sistēmas resursu ierobežojumu dēļ.',
    'image_gallery_thumbnail_memory_limit' => 'Neizdevās izveidot galerijas sīktēlus sistēmas resursu ierobežojumu dēļ.',
    'drawing_data_not_found' => 'Attēla datus nevarēja ielādēt. Attēla fails, iespējams, vairs neeksistē, vai arī jums varētu nebūt piekļuves tiesības tam.',

    // Attachments
    'attachment_not_found' => 'Pielikums nav atrasts',
    'attachment_upload_error' => 'Radās kļūda augšupielādējot pievienoto failu',

    // Pages
    'page_draft_autosave_fail' => 'Neizdevās saglabāt uzmetumu. Pārliecinieties, ka jūsu interneta pieslēgums ir aktīvs pirms saglabājiet šo lapu',
    'page_draft_delete_fail' => 'Neizdevās izdzēst lapas melnrakstu un iegūt pašreizējās lapas saglabāto saturu',
    'page_custom_home_deletion' => 'Nav iespējams izdzēst lapu kamēr tā ir uzstādīta kā sākumlapa',

    // Entities
    'entity_not_found' => 'Vienība nav atrasta',
    'bookshelf_not_found' => 'Plaukts nav atrasts',
    'book_not_found' => 'Grāmata nav atrasta',
    'page_not_found' => 'Lapa nav atrasta',
    'chapter_not_found' => 'Nodaļa nav atrasta',
    'selected_book_not_found' => 'Iezīmētā grāmata nav atrasta',
    'selected_book_chapter_not_found' => 'Izvēlētā grāmata vai nodaļa nav atrasta',
    'guests_cannot_save_drafts' => 'Viesi nevar saglabāt melnrakstus',

    // Users
    'users_cannot_delete_only_admin' => 'Jūs nevarat dzēst vienīgo administratoru',
    'users_cannot_delete_guest' => 'Jūs nevarat dzēst lietotāju "viesis"',
    'users_could_not_send_invite' => 'Neizdevās izveidot lietotāju, jo neizdevās nosūtīt ielūguma epastu',

    // Roles
    'role_cannot_be_edited' => 'Šo lomu nevar rediģēt',
    'role_system_cannot_be_deleted' => 'Šī ir sistēmas loma un nevar tikt izdzēsta',
    'role_registration_default_cannot_delete' => 'Šī loma nevar tikt izdzēsta, kamēr tā uzstādīta kā noklusētā reģistrācijas loma',
    'role_cannot_remove_only_admin' => 'Šis ir vienīgais lietotājs, kam norādīta administratora loma. Pievienojiet administratora lomu citam lietotājam pirms mēģiniet to izslēgt šeit.',

    // Comments
    'comment_list' => 'Radās kļūda ielasot komentārus.',
    'cannot_add_comment_to_draft' => 'Melnrakstam nevar pievienot komentārus.',
    'comment_add' => 'Radās kļūda pievienojot/atjaunojot komentāru.',
    'comment_delete' => 'Radās kļūda dzēšot komentāru.',
    'empty_comment' => 'Nevar pievienot tukšu komentāru.',

    // Error pages
    '404_page_not_found' => 'Lapa nav atrasta',
    'sorry_page_not_found' => 'Atvainojiet, meklētā lapa nav atrasta.',
    'sorry_page_not_found_permission_warning' => 'Ja šai lapai būtu bijis te jābūt, jums var nebūt pietiekamas piekļuves tiesības, lai to apskatītu.',
    'image_not_found' => 'Attēls nav atrasts',
    'image_not_found_subtitle' => 'Atvainojiet, meklētais attēla fails nav atrasts.',
    'image_not_found_details' => 'Ja attēlam būtu jābūt pieejamam, iespējams, tas ir ticis izdzēsts.',
    'return_home' => 'Atgriezties uz sākumu',
    'error_occurred' => 'Radusies kļūda',
    'app_down' => ':appName pagaidām nav pieejams',
    'back_soon' => 'Drīz būs atkal pieejams.',

    // Import
    'import_zip_cant_read' => 'Nevarēja nolasīt ZIP failu.',
    'import_zip_cant_decode_data' => 'Nevarēja atrast un nolasīt data.json saturu ZIP failā.',
    'import_zip_no_data' => 'ZIP faila datos nav atrasts grāmatu, nodaļu vai lapu saturs.',
    'import_validation_failed' => 'ZIP faila imports ir neveiksmīgs ar šādām kļūdām:',
    'import_zip_failed_notification' => 'ZIP faila imports ir neveiksmīgs.',
    'import_perms_books' => 'Jums nav nepieciešamo tiesību izveidot grāmatas.',
    'import_perms_chapters' => 'Jums nav nepieciešamo tiesību izveidot nodaļas.',
    'import_perms_pages' => 'Jums nav nepieciešamo tiesību izveidot lapas.',
    'import_perms_images' => 'Jums nav nepieciešamo tiesību izviedot attēlus.',
    'import_perms_attachments' => 'Jums nav nepieciešamo tiesību izveidot pielikumus.',

    // API errors
    'api_no_authorization_found' => 'Pieprasījumā nav atrasts autorizācijas žetons',
    'api_bad_authorization_format' => 'Pieprasījumā atrasts autorizācijas žetons, taču tā formāts nav pareizs',
    'api_user_token_not_found' => 'Nav atrasts norādītajam autorizācijas žetonam atbilstošs API žetons',
    'api_incorrect_token_secret' => 'Norādītā slepenā atslēga izmantotajam API žetonam nav pareiza',
    'api_user_no_api_permission' => 'Izmantotā API žetona īpašniekam nav tiesības veikt API izsaukumus',
    'api_user_token_expired' => 'Autorizācijas žetona derīguma termiņš ir izbeidzies',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Radusies kļūda sūtot testa epastu:',

    // HTTP errors
    'http_ssr_url_no_match' => 'Adrese (URL) nesakrīt ar atļautajām SSR adresēm',
];
