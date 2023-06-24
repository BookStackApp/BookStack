<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Sul puudub õigus selle lehe vaatamiseks.',
    'permissionJson' => 'Sul puudub õigus selle tegevuse teostamiseks.',

    // Auth
    'error_user_exists_different_creds' => 'See e-posti aadress on juba seotud teise kasutajaga.',
    'email_already_confirmed' => 'E-posti aadress on juba kinnitatud. Proovi sisse logida.',
    'email_confirmation_invalid' => 'Kinnituslink ei ole kehtiv või on seda juba kasutatud. Proovi uuesti registreeruda.',
    'email_confirmation_expired' => 'Kinnituslink on aegunud. Sulle saadeti aadressi kinnitamiseks uus e-kiri.',
    'email_confirmation_awaiting' => 'Selle kasutajakonto e-posti aadress vajab kinnitamist',
    'ldap_fail_anonymous' => 'LDAP anonüümne ligipääs ebaõnnestus',
    'ldap_fail_authed' => 'LDAP ligipääs antud nime ja parooliga ebaõnnestus',
    'ldap_extension_not_installed' => 'PHP LDAP laiendus ei ole paigaldatud',
    'ldap_cannot_connect' => 'Ühendus LDAP serveriga ebaõnnestus',
    'saml_already_logged_in' => 'Juba sisse logitud',
    'saml_user_not_registered' => 'Kasutaja :name ei ole registreeritud ning automaatne registreerimine on keelatud',
    'saml_no_email_address' => 'Selle kasutaja e-posti aadressi ei õnnestunud välisest autentimissüsteemist leida',
    'saml_invalid_response_id' => 'Välisest autentimissüsteemist tulnud päringut ei algatatud sellest rakendusest. Seda viga võib põhjustada pärast sisselogimist tagasi liikumine.',
    'saml_fail_authed' => 'Sisenemine :system kaudu ebaõnnestus, süsteem ei andnud volitust',
    'oidc_already_logged_in' => 'Juba sisse logitud',
    'oidc_user_not_registered' => 'Kasutaja :name ei ole registreeritud ning automaatne registreerimine on keelatud',
    'oidc_no_email_address' => 'Selle kasutaja e-posti aadressi ei õnnestunud välisest autentimissüsteemist leida',
    'oidc_fail_authed' => 'Sisenemine :system kaudu ebaõnnestus, süsteem ei andnud volitust',
    'social_no_action_defined' => 'Tegevus on defineerimata',
    'social_login_bad_response' => ":socialAccount kaudu sisselogimisel tekkis viga: \n:error",
    'social_account_in_use' => 'See :socialAccount konto on juba kasutusel, proovi :socialAccount kaudu sisse logida.',
    'social_account_email_in_use' => 'E-posti aadress :email on juba kasutusel. Kui sul on juba kasutajakonto, saad oma :socialAccount konto siduda profiili seadetes.',
    'social_account_existing' => 'See :socialAccount konto on juba seotud su profiiliga.',
    'social_account_already_used_existing' => 'See :socialAccount konto on juba seotud teise kasutajaga.',
    'social_account_not_used' => 'See :socialAccount konto ei ole seotud ühegi kasutajaga. Seosta see oma profiili seadetes. ',
    'social_account_register_instructions' => 'Kui sul pole veel kasutajakontot, saad selle registreerida :socialAccount kaudu.',
    'social_driver_not_found' => 'Sotsiaalmeedia kontode draiverit ei leitud',
    'social_driver_not_configured' => 'Sinu :socialAccount konto seaded ei ole korrektsed.',
    'invite_token_expired' => 'Link on aegunud. Võid selle asemel proovida oma konto parooli lähtestada.',

    // System
    'path_not_writable' => 'Faili asukohaga :filePath ei õnnestunud üles laadida. Veendu, et serveril on kirjutusõigused.',
    'cannot_get_image_from_url' => 'Ei suutnud laadida pilti aadressilt :url',
    'cannot_create_thumbs' => 'Server ei saa piltide eelvaateid tekitada. Veendu, et PHP GD laiendus on paigaldatud.',
    'server_upload_limit' => 'Server ei luba nii suurte failide üleslaadimist. Proovi väiksema failiga.',
    'uploaded'  => 'Server ei luba nii suurte failide üleslaadimist. Proovi väiksema failiga.',

    // Drawing & Images
    'image_upload_error' => 'Pildi üleslaadimisel tekkis viga',
    'image_upload_type_error' => 'Pildifaili tüüp ei ole korrektne',
    'image_upload_replace_type' => 'Pildifaili asendused peavad olema sama tüüpi',
    'drawing_data_not_found' => 'Joonise andmeid ei õnnestunud laadida. Joonist ei pruugi enam eksisteerida, või sul puuduvad õigused selle vaatamiseks.',

    // Attachments
    'attachment_not_found' => 'Manust ei leitud',
    'attachment_upload_error' => 'Manuse faili üleslaadimisel tekkis viga',

    // Pages
    'page_draft_autosave_fail' => 'Mustandi salvestamine ebaõnnestus. Kontrolli oma internetiühendust',
    'page_draft_delete_fail' => 'Mustandi kustutamine ja lehe salvestatud seisu laadimine ebaõnnestus',
    'page_custom_home_deletion' => 'Ei saa kustutada lehte, mis on määratud avaleheks',

    // Entities
    'entity_not_found' => 'Objekti ei leitud',
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
    'back_soon' => 'See on varsti tagasi.',

    // API errors
    'api_no_authorization_found' => 'Päringust ei leitud volitustunnust',
    'api_bad_authorization_format' => 'Päringust leiti volitustunnus, aga see ei olnud korrektses formaadis',
    'api_user_token_not_found' => 'Volitustunnusele vastavat API tunnust ei leitud',
    'api_incorrect_token_secret' => 'API tunnusele lisatud salajane võti ei ole korrektne',
    'api_user_no_api_permission' => 'Selle API tunnuse omanikul ei ole õigust API päringuid teha',
    'api_user_token_expired' => 'Volitustunnus on aegunud',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Test e-kirja saatmisel tekkis viga:',

];
