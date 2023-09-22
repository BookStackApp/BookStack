<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Jūs neturite leidimo atidaryti šio puslapio.',
    'permissionJson' => 'Jūs neturite leidimo atlikti prašomo veiksmo.',

    // Auth
    'error_user_exists_different_creds' => 'Naudotojo elektroninis paštas :email jau egzistuoja, bet su kitokiais įgaliojimais.',
    'email_already_confirmed' => 'Elektroninis paštas jau buvo patvirtintas, pabandykite prisijungti.',
    'email_confirmation_invalid' => 'Šis patvirtinimo prieigos raktas negalioja arba jau buvo panaudotas, prašome bandykite vėl registruotis.',
    'email_confirmation_expired' => 'Šis patvirtinimo prieigos raktas baigė galioti, naujas patvirtinimo laiškas jau išsiųstas elektroniniu paštu.',
    'email_confirmation_awaiting' => 'Elektroninio pašto adresą paskyrai reikia patvirtinti',
    'ldap_fail_anonymous' => 'Nepavyko pasiekti LDAP naudojant anoniminį susiejimą',
    'ldap_fail_authed' => 'Nepavyko pasiekti LDAP naudojant išsamią dn ir slaptažodžio informaciją',
    'ldap_extension_not_installed' => 'LDAP PHP išplėtimas neįdiegtas',
    'ldap_cannot_connect' => 'Negalima prisijungti prie LDAP serverio, nepavyko prisijungti',
    'saml_already_logged_in' => 'Jau prisijungta',
    'saml_user_not_registered' => 'Naudotojas :name neužregistruotas ir automatinė registracija yra išjungta',
    'saml_no_email_address' => 'Nerandamas šio naudotojo elektroninio pašto adresas išorinės autentifikavimo sistemos pateiktuose duomenyse',
    'saml_invalid_response_id' => 'Prašymas iš išorinės autentifikavimo sistemos nėra atpažintas proceso, kurį pradėjo ši programa. Naršymas po prisijungimo gali sukelti šią problemą.',
    'saml_fail_authed' => 'Prisijungimas, naudojant :system nepavyko, sistema nepateikė sėkmingo leidimo.',
    'oidc_already_logged_in' => 'Already logged in',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'Neapibrėžtas joks veiksmas',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => 'Ši :socialAccount paskyra jau yra naudojama, pabandykite prisijungti per :socialAccount pasirinkimą.',
    'social_account_email_in_use' => 'Elektroninis paštas :email jau yra naudojamas. Jei jūs jau turite paskyrą, galite prijungti savo :socialAccount paskyrą iš savo profilio nustatymų.',
    'social_account_existing' => 'Šis :socialAccount jau yra pridėtas prie jūsų profilio.',
    'social_account_already_used_existing' => 'Ši :socialAccount paskyra jau yra naudojama kito naudotojo.',
    'social_account_not_used' => 'Ši :socialAccount paskyra nėra susieta su jokiais naudotojais. Prašome, pridėkite ją į savo profilio nustatymus.',
    'social_account_register_instructions' => 'Jei dar neturite paskyros, galite užregistruoti paskyrą, naudojant :socialAccount pasirinkimą.',
    'social_driver_not_found' => 'Socialinis diskas nerastas',
    'social_driver_not_configured' => 'Jūsų :socialAccount socaliniai nustatymai sukonfigūruoti neteisingai.',
    'invite_token_expired' => 'Ši kvietimo nuoroda baigė galioti. Vietoj to, jūs galite bandyti iš naujo nustatyti savo paskyros slaptažodį.',

    // System
    'path_not_writable' => 'Į failo kelią :filePath negalima įkelti. Įsitikinkite, kad jis yra įrašomas į serverį.',
    'cannot_get_image_from_url' => 'Negalima gauti vaizdo iš :url',
    'cannot_create_thumbs' => 'Serveris negali sukurti miniatiūros. Prašome patikrinkite, ar turite įdiegtą GD PHP plėtinį.',
    'server_upload_limit' => 'Serveris neleidžia įkelti tokio dydžio failų. Prašome bandykite mažesnį failo dydį.',
    'uploaded'  => 'Serveris neleidžia įkelti tokio dydžio failų. Prašome bandykite mažesnį failo dydį.',

    // Drawing & Images
    'image_upload_error' => 'Įvyko klaida įkeliant vaizdą',
    'image_upload_type_error' => 'Vaizdo tipas, kurį norima įkelti, yra neteisingas',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Priedas nerastas',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Juodraščio išsaugoti nepavyko. Įsitikinkite, jog turite interneto ryšį prieš išsaugant šį paslapį.',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'Negalima ištrinti šio puslapio, kol jis yra nustatytas kaip pagrindinis puslapis',

    // Entities
    'entity_not_found' => 'Subjektas nerastas',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Knyga nerasta',
    'page_not_found' => 'Puslapis nerastas',
    'chapter_not_found' => 'Skyrius nerastas',
    'selected_book_not_found' => 'Pasirinkta knyga nerasta',
    'selected_book_chapter_not_found' => 'Pasirinkta knyga ar skyrius buvo nerasti',
    'guests_cannot_save_drafts' => 'Svečiai negali išsaugoti juodraščių',

    // Users
    'users_cannot_delete_only_admin' => 'Negalite ištrinti vienintelio administratoriaus',
    'users_cannot_delete_guest' => 'Negalite ištrinti svečio naudotojo',

    // Roles
    'role_cannot_be_edited' => 'Šio vaidmens negalima redaguoti',
    'role_system_cannot_be_deleted' => 'Šis vaidmuo yra sistemos vaidmuo ir jo negalima ištrinti',
    'role_registration_default_cannot_delete' => 'Šis vaidmuo negali būti ištrintas, kai yra nustatytas kaip numatytasis registracijos vaidmuo',
    'role_cannot_remove_only_admin' => 'Šis naudotojas yra vienintelis naudotojas, kuriam yra paskirtas administratoriaus vaidmuo. Paskirkite administratoriaus vaidmenį kitam naudotojui prieš bandant jį pašalinti.',

    // Comments
    'comment_list' => 'Gaunant komentarus įvyko klaida.',
    'cannot_add_comment_to_draft' => 'Negalite pridėti komentaro juodraštyje',
    'comment_add' => 'Klaido įvyko pridedant/atnaujinant komantarą.',
    'comment_delete' => 'Trinant komentarą įvyko klaida.',
    'empty_comment' => 'Negalite pridėti tuščio komentaro.',

    // Error pages
    '404_page_not_found' => 'Puslapis nerastas',
    'sorry_page_not_found' => 'Atleiskite, puslapis, kurio ieškote, nerastas.',
    'sorry_page_not_found_permission_warning' => 'Jei tikėjotės, kad šis puslapis egzistuoja, galbūt neturite leidimo jo peržiūrėti.',
    'image_not_found' => 'Image Not Found',
    'image_not_found_subtitle' => 'Sorry, The image file you were looking for could not be found.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Grįžti į namus',
    'error_occurred' => 'Įvyko klaida',
    'app_down' => ':appName dabar yra apačioje',
    'back_soon' => 'Tai sugrįž greitai',

    // API errors
    'api_no_authorization_found' => 'Užklausoje nerastas įgaliojimo prieigos raktas',
    'api_bad_authorization_format' => 'Užklausoje rastas prieigos raktas, tačiau formatas yra neteisingas',
    'api_user_token_not_found' => 'Pateiktam prieigos raktui nebuvo rastas atitinkamas API prieigos raktas',
    'api_incorrect_token_secret' => 'Pateiktas panaudoto API žetono slėpinys yra neteisingas',
    'api_user_no_api_permission' => 'API prieigos rakto savininkas neturi leidimo daryti API skambučius',
    'api_user_token_expired' => 'Prieigos rakto naudojimas baigė galioti',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Siunčiant bandymo email: įvyko klaida',

    // HTTP errors
    'http_ssr_url_no_match' => 'The URL does not match the configured allowed SSR hosts',
];
