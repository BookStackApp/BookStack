<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Sinulla ei ole pääsyoikeutta pyydettyyn sivuun.',
    'permissionJson' => 'Sinulla ei ole oikeutta suorittaa pyydettyä toimintoa.',

    // Auth
    'error_user_exists_different_creds' => 'Sähköpostiosoite :email on jo käytössä toisessa käyttäjätunnuksessa.',
    'auth_pre_register_theme_prevention' => 'Käyttäjätiliä ei voitu rekisteröidä annetuille tiedoille',
    'email_already_confirmed' => 'Sähköposti on jo vahvistettu, yritä kirjautua sisään.',
    'email_confirmation_invalid' => 'Tämä vahvistuslinkki ei ole voimassa tai sitä on jo käytetty, yritä rekisteröityä uudelleen.',
    'email_confirmation_expired' => 'Vahvistuslinkki on vanhentunut, uusi vahvistussähköposti on lähetetty.',
    'email_confirmation_awaiting' => 'Tämän tilin sähköpostiosoite pitää vahvistaa',
    'ldap_fail_anonymous' => 'Anonyymi LDAP-todennus epäonnistui',
    'ldap_fail_authed' => 'LDAP-todennus epäonnistui annetulla nimellä ja salasanalla',
    'ldap_extension_not_installed' => 'PHP:n LDAP-laajennusta ei ole asennettu',
    'ldap_cannot_connect' => 'Yhteyttä LDAP-palvelimeen ei voida muodostaa, alustava yhteys epäonnistui',
    'saml_already_logged_in' => 'Olet jo kirjautunut sisään',
    'saml_no_email_address' => 'Tämän käyttäjän sähköpostiosoitetta ei löytynyt ulkoisesta todennuspalvelusta',
    'saml_invalid_response_id' => 'Tämän sovelluksen käynnistämä prosessi ei tunnista ulkoisen todennusjärjestelmän pyyntöä.
Sovellus ei tunnista ulkoisen todennuspalvelun pyyntöä. Ongelman voi aiheuttaa siirtyminen selaimessa takaisin edelliseen näkymään kirjautumisen jälkeen.',
    'saml_fail_authed' => 'Sisäänkirjautuminen :system käyttäen epäonnistui, järjestelmä ei antanut valtuutusta',
    'oidc_already_logged_in' => 'Olet jo kirjautunut sisään',
    'oidc_no_email_address' => 'Ulkoisen todennuspalvelun antamista tiedoista ei löytynyt tämän käyttäjän sähköpostiosoitetta',
    'oidc_fail_authed' => 'Sisäänkirjautuminen :system käyttäen epäonnistui, järjestelmä ei antanut valtuutusta',
    'social_no_action_defined' => 'Ei määriteltyä toimenpidettä',
    'social_login_bad_response' => "Virhe :socialAccount-kirjautumisen aikana: \n:error",
    'social_account_in_use' => 'Tämä :socialAccount-tili on jo käytössä, yritä kirjautua sisään :socialAccount-vaihtoehdon kautta.',
    'social_account_email_in_use' => 'Sähköposti :email on jo käytössä. Jos sinulla on jo sivustolla käyttäjätili, voit yhdistää :socialAccount-tilisi profiiliasetuksista.',
    'social_account_existing' => 'Tämä :socialAccount-tili on jo liitetty profiiliisi.',
    'social_account_already_used_existing' => 'Tämä :socialAccount-tili on jo toisen käyttäjän käytössä.',
    'social_account_not_used' => 'Tätä :socialAccount-tiliä ei ole liitetty mihinkään käyttäjään. Voit liittää sen profiiliasetuksistasi. ',
    'social_account_register_instructions' => 'Jos sinulla ei vielä ole käyttäjätiliä, voit rekisteröidä tilin käyttämällä :socialAccount-vaihtoehtoa.',
    'social_driver_not_found' => 'Sosiaalisen median tilin ajuria ei löytynyt',
    'social_driver_not_configured' => ':socialAccount-tilin asetuksia ei ole määritetty oikein.',
    'invite_token_expired' => 'Tämä kutsulinkki on vanhentunut. Voit sen sijaan yrittää palauttaa tilisi salasanan.',
    'login_user_not_found' => 'Käyttäjää tälle toiminnolle ei löytynyt.',

    // System
    'path_not_writable' => 'Tiedostopolkuun :filePath ei voitu ladata tiedostoa. Tarkista polun kirjoitusoikeudet.',
    'cannot_get_image_from_url' => 'Kuvan hakeminen osoitteesta :url ei onnistu',
    'cannot_create_thumbs' => 'Palvelin ei voi luoda pikkukuvia. Tarkista, että PHP:n GD-laajennus on asennettu.',
    'server_upload_limit' => 'Palvelin ei salli näin suuria tiedostoja. Kokeile pienempää tiedostokokoa.',
    'server_post_limit' => 'Palvelin ei pysty vastaanottamaan annettua tietomäärää. Yritä uudelleen pienemmällä tiedostolla.',
    'uploaded'  => 'Palvelin ei salli näin suuria tiedostoja. Kokeile pienempää tiedostokokoa.',

    // Drawing & Images
    'image_upload_error' => 'Kuvan lataamisessa tapahtui virhe',
    'image_upload_type_error' => 'Ladattavan kuvan tyyppi on virheellinen',
    'image_upload_replace_type' => 'Korvaavan kuvatiedoston tulee olla samaa tyyppiä kuin alkuperäinen kuva',
    'image_upload_memory_limit' => 'Kuvan lataaminen ja/tai pikkukuvien luominen epäonnistui järjestelmän resurssirajoitusten vuoksi.',
    'image_thumbnail_memory_limit' => 'Kuvan kokovaihtoehtojen luominen epäonnistui järjestelmän resurssirajoitusten vuoksi.',
    'image_gallery_thumbnail_memory_limit' => 'Gallerian pikkukuvien luominen epäonnistui järjestelmän resurssirajoitusten vuoksi.',
    'drawing_data_not_found' => 'Piirustuksen tietoja ei voitu ladata. Piirustustiedostoa ei ehkä ole enää olemassa tai sinulla ei ole oikeutta käyttää sitä.',

    // Attachments
    'attachment_not_found' => 'Liitettä ei löytynyt',
    'attachment_upload_error' => 'Liitteen lataamisessa tapahtui virhe',

    // Pages
    'page_draft_autosave_fail' => 'Luonnoksen tallentaminen epäonnistui. Varmista, että sinulla on toimiva internetyhteys ennen sivun tallentamista',
    'page_draft_delete_fail' => 'Luonnoksen poistaminen ja sivun tallennetun sisällön noutaminen epäonnistui',
    'page_custom_home_deletion' => 'Sivua ei voi poistaa, koska se on asetettu etusivuksi',

    // Entities
    'entity_not_found' => 'Kohdetta ei löydy',
    'bookshelf_not_found' => 'Hyllyä ei löytynyt',
    'book_not_found' => 'Kirjaa ei löytynyt',
    'page_not_found' => 'Sivua ei löytynyt',
    'chapter_not_found' => 'Lukua ei löytynyt',
    'selected_book_not_found' => 'Valittua kirjaa ei löytynyt',
    'selected_book_chapter_not_found' => 'Valittua kirjaa tai lukua ei löytynyt',
    'guests_cannot_save_drafts' => 'Vieraat eivät voi tallentaa luonnoksia',

    // Users
    'users_cannot_delete_only_admin' => 'Ainoaa ylläpitäjää ei voi poistaa',
    'users_cannot_delete_guest' => 'Vieraskäyttäjää ei voi poistaa',
    'users_could_not_send_invite' => 'Käyttäjää ei voitu luoda kutsun lähettämisen jälkeen',

    // Roles
    'role_cannot_be_edited' => 'Tätä roolia ei voi muokata',
    'role_system_cannot_be_deleted' => 'Tämä rooli on järjestelmärooli, eikä sitä voi poistaa',
    'role_registration_default_cannot_delete' => 'Tätä roolia ei voi poistaa, kun se on asetettu oletusrooliksi uusille rekisteröityville käyttäjille',
    'role_cannot_remove_only_admin' => 'Tämä käyttäjä on ainoa käyttäjä, jolle on määritetty ylläpitäjän rooli. Määritä ylläpitäjän rooli toiselle käyttäjälle, ennen kuin yrität poistaa tämän käyttäjän.',

    // Comments
    'comment_list' => 'Kommenttien noutamisessa tapahtui virhe.',
    'cannot_add_comment_to_draft' => 'Luonnokseen ei voi lisätä kommentteja.',
    'comment_add' => 'Kommentin lisäämisessä tai päivittämisessä tapahtui virhe.',
    'comment_delete' => 'Kommentin poistamisessa tapahtui virhe.',
    'empty_comment' => 'Tyhjää kommenttia ei voi lisätä.',

    // Error pages
    '404_page_not_found' => 'Sivua ei löydy',
    'sorry_page_not_found' => 'Valitettavasti etsimääsi sivua ei löytynyt.',
    'sorry_page_not_found_permission_warning' => 'Jos oletit, että tämä sivu on olemassa, sinulla ei ehkä ole lupaa tarkastella sitä.',
    'image_not_found' => 'Kuvaa ei löytynyt',
    'image_not_found_subtitle' => 'Valitettavasti etsimääsi kuvatiedostoa ei löytynyt.',
    'image_not_found_details' => 'Jos oletit, että tämä kuva on olemassa, se on ehkä poistettu.',
    'return_home' => 'Palaa etusivulle',
    'error_occurred' => 'Tapahtui virhe',
    'app_down' => ':appName on kaatunut',
    'back_soon' => 'Se palautetaan pian.',

    // Import
    'import_zip_cant_read' => 'ZIP-tiedostoa ei voitu lukea.',
    'import_zip_cant_decode_data' => 'ZIP-tiedoston data.json sisältöä ei löydy eikä sitä voitu purkaa.',
    'import_zip_no_data' => 'ZIP-tiedostoilla ei ole odotettua kirjaa, lukua tai sivun sisältöä.',
    'import_validation_failed' => 'Tuonti ZIP epäonnistui virheiden kanssa:',
    'import_zip_failed_notification' => 'ZIP-tiedoston tuominen epäonnistui.',
    'import_perms_books' => 'Sinulla ei ole tarvittavia oikeuksia luoda kirjoja.',
    'import_perms_chapters' => 'Sinulla ei ole tarvittavia oikeuksia luoda kappaleita.',
    'import_perms_pages' => 'Sinulla ei ole tarvittavia oikeuksia luoda sivuja.',
    'import_perms_images' => 'Sinulla ei ole tarvittavia oikeuksia luoda kuvia.',
    'import_perms_attachments' => 'Sinulla ei ole tarvittavaa lupaa luoda liitteitä.',

    // API errors
    'api_no_authorization_found' => 'Pyynnöstä ei löytynyt valtuutuskoodia',
    'api_bad_authorization_format' => 'Pyynnöstä löytyi valtuutuskoodi, mutta sen muoto oli virheellinen',
    'api_user_token_not_found' => 'Annetulle valtuutuskoodille ei löytynyt vastaavaa API-tunnistetta',
    'api_incorrect_token_secret' => 'API-tunnisteelle annettu salainen avain on virheellinen',
    'api_user_no_api_permission' => 'Käytetyn API-tunnisteen omistajalla ei ole oikeutta tehdä API-kutsuja',
    'api_user_token_expired' => 'Käytetty valtuutuskoodi on vanhentunut',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Virhe testisähköpostia lähetettäessä:',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL-osoite ei vastaa määritettyjä sallittuja SSR-isäntiä',
];
