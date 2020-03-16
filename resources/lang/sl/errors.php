<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nimate pravic za dostop do želene strani.',
    'permissionJson' => 'Nimate dovoljenja za izvedbo zahtevanega dejanja.',

    // Auth
    'error_user_exists_different_creds' => 'Uporabnik z e-pošto :email že obstaja, vendar z drugačnimi poverilnicami.',
    'email_already_confirmed' => 'E-naslov je že bil potrjen, poskusite se prijaviti.',
    'email_confirmation_invalid' => 'Ta potrditveni žeton ni veljaven ali je že bil uporabljen. Poizkusite znova.',
    'email_confirmation_expired' => 'Potrditveni žeton je pretečen. Nova potrditvena e-pošta je bila poslana.',
    'email_confirmation_awaiting' => 'Potrebno je potrditi e-naslov',
    'ldap_fail_anonymous' => 'Dostop do LDAP ni uspel z anonimno povezavo',
    'ldap_fail_authed' => 'Neuspešen LDAP dostop z danimi podrobnostimi dn & gesla',
    'ldap_extension_not_installed' => 'PHP razširitev za LDAP ni nameščen',
    'ldap_cannot_connect' => 'Ne morem se povezati na LDAP strežnik, neuspešna začetna povezava',
    'saml_already_logged_in' => 'Že prijavljen',
    'saml_user_not_registered' => 'Uporabniško ime :name ni registrirano in avtomatska registracija je onemogočena',
    'saml_no_email_address' => 'Nisem našel e-naslova za tega uporabnika v podatkih iz zunanjega sistema za preverjanje pristnosti',
    'saml_invalid_response_id' => 'Zahteva iz zunanjega sistema za preverjanje pristnosti ni prepoznana s strani procesa zagnanega s strani te aplikacije. Pomik nazaj po prijavi je lahko povzročil te težave.',
    'saml_fail_authed' => 'Prijava z uporabo :system ni uspela, sistem ni zagotovil uspešne avtorizacije',
    'social_no_action_defined' => 'Akcija ni določena',
    'social_login_bad_response' => "Napaka pri :socialAccount prijavi:\n:error",
    'social_account_in_use' => 'Ta :socialAccount je že v uporabi. Poskusite se prijaviti z :socialAccount možnostjo.',
    'social_account_email_in_use' => 'Ta e-naslov :email je že v uporabi. Če že imate račun lahko povežete vaš :socialAccount v vaših nastavitvah profila.',
    'social_account_existing' => 'Ta :socialAccount je že dodan vašemu profilu.',
    'social_account_already_used_existing' => 'Ta :socialAccount je v uporabi s strani drugega uporabnika.',
    'social_account_not_used' => 'Ta :socialAccount ni povezan z nobenim uporabnikom. Prosimo povežite ga v vaših nastavitvah profila. ',
    'social_account_register_instructions' => 'Če še nimate računa, se lahko registrirate z uporabo :socialAccount.',
    'social_driver_not_found' => 'Socialni vtičnik ni najden',
    'social_driver_not_configured' => 'Vaše nastavitve :socialAccount niso pravilo nastavljene.',
    'invite_token_expired' => 'Ta link je pretečen. Namesto tega lahko ponastavite vaše geslo računa.',

    // System
    'path_not_writable' => 'Poti :filePath ni bilo mogoče naložiti. Prepričajte se da je zapisljiva na strežnik.',
    'cannot_get_image_from_url' => 'Ne morem pridobiti slike z :url',
    'cannot_create_thumbs' => 'Strežnik ne more izdelati sličice. Prosimo preverite če imate GD PHP razširitev nameščeno.',
    'server_upload_limit' => 'Strežnik ne dovoli nalaganj take velikosti. Prosimo poskusite manjšo velikost datoteke.',
    'uploaded'  => 'Strežnik ne dovoli nalaganj take velikosti. Prosimo poskusite manjšo velikost datoteke.',
    'image_upload_error' => 'Prišlo je do napake med nalaganjem slike',
    'image_upload_type_error' => 'Napačen tip (format) slike',
    'file_upload_timeout' => 'Čas nalaganjanja datoteke je potekel.',

    // Attachments
    'attachment_page_mismatch' => 'Neskladje strani med posodobitvijo priloge',
    'attachment_not_found' => 'Priloga ni najdena',

    // Pages
    'page_draft_autosave_fail' => 'Osnutka ni bilo mogoče shraniti. Pred shranjevanjem te strani se prepričajte, da imate internetno povezavo',
    'page_custom_home_deletion' => 'Ne morem izbrisati strani dokler je nastavljena kot domača stran',

    // Entities
    'entity_not_found' => 'Entiteta ni najdena',
    'bookshelf_not_found' => 'Knjižna polica ni najdena',
    'book_not_found' => 'Knjiga ni najdena',
    'page_not_found' => 'Stran ni najdena',
    'chapter_not_found' => 'Poglavje ni najdeno',
    'selected_book_not_found' => 'Izbrana knjiga ni najdena',
    'selected_book_chapter_not_found' => 'Izbrana knjiga ali poglavje ni najdeno',
    'guests_cannot_save_drafts' => 'Gosti ne morejo shranjevati osnutkov',

    // Users
    'users_cannot_delete_only_admin' => 'Ne morete odstraniti edinega administratorja',
    'users_cannot_delete_guest' => 'Ne morete odstraniti uporabnika gost',

    // Roles
    'role_cannot_be_edited' => 'Te vloge mi možno urejati',
    'role_system_cannot_be_deleted' => 'Ta vloga je sistemska in je ni možno brisati',
    'role_registration_default_cannot_delete' => 'Te vloge ni možno brisati dokler je nastavljena kot privzeta',
    'role_cannot_remove_only_admin' => 'Ta uporabnik je edini administrator. Dodelite vlogo administratorja drugemu uporabniku preden ga poskusite brisati.',

    // Comments
    'comment_list' => 'Napaka se je pojavila pri pridobivanju komentarjev.',
    'cannot_add_comment_to_draft' => 'Ni mogoče dodajanje komentarjev v osnutek.',
    'comment_add' => 'Napaka se je pojavila pri dodajanju / posodobitev komentarjev.',
    'comment_delete' => 'Napaka se je pojavila pri brisanju komentarja.',
    'empty_comment' => 'Praznega komentarja ne morete objaviti.',

    // Error pages
    '404_page_not_found' => 'Page Not Found',
    'sorry_page_not_found' => 'Sorry, The page you were looking for could not be found.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
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
