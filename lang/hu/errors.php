<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nincs jogosultság a kért oldal eléréséhez.',
    'permissionJson' => 'Nincs jogosultsága a kért művelet végrehajtásához.',

    // Auth
    'error_user_exists_different_creds' => ':email címmel már létezik felhasználó, de más hitelesítő adatokkal.',
    'auth_pre_register_theme_prevention' => 'A felhasználói fiók nem regisztrálható a megadott adatokkal',
    'email_already_confirmed' => 'Az email cím már meg van erősítve. Próbáljon meg bejelentkezni!',
    'email_confirmation_invalid' => 'Ez a megerősítő kulcs nem érvényes vagy használva már volt. Próbáljon meg újra regisztrálni!',
    'email_confirmation_expired' => 'Ez a megerősítő kulcs már lejárt. Egy új megerősítő email lett küldve.',
    'email_confirmation_awaiting' => 'A használatban lévő fiók email címét meg kell erősíteni',
    'ldap_fail_anonymous' => 'Nem sikerült az LDAP elérése névtelen csatlakozással',
    'ldap_fail_authed' => 'Nem sikerült az LDAP elérése a megadott DN és jelszó adatokkal',
    'ldap_extension_not_installed' => 'Az LDAP PHP kiterjesztés nincsen telepítve',
    'ldap_cannot_connect' => 'Nem lehet kapcsolódni az LDAP szerverhez, a kezdeti kapcsolatfelvétel nem sikerült',
    'saml_already_logged_in' => 'Már be van jelentkezve',
    'saml_no_email_address' => 'Ehhez a felhasználóhoz nem található email cím a külső hitelesítő rendszer által átadott adatokban',
    'saml_invalid_response_id' => 'A külső hitelesítő rendszerből érkező kérést nem ismerte fel egy az alkalmazás által indított folyamat sem. Bejelentkezés után az előző oldalra történő visszalépés okozhatja ezt a hibát.',
    'saml_fail_authed' => 'A bejelentkezés a(z) :system használatával sikertelen volt, a rendszer nem biztosított sikeres hitelesítést',
    'oidc_already_logged_in' => 'Már be van jelentkezve',
    'oidc_no_email_address' => 'Ehhez a felhasználóhoz nem található email cím a külső hitelesítő rendszer által átadott adatokban',
    'oidc_fail_authed' => 'A bejelentkezés a(z) :system használatával sikertelen volt, a rendszer nem biztosított sikeres hitelesítést',
    'social_no_action_defined' => 'Nincs meghatározott művelet',
    'social_login_bad_response' => "Hiba történt a(z) :socialAccount bejelentkezés közben:\n:error",
    'social_account_in_use' => 'Ez a(z) :socialAccount fiók már használatban van. Próbáljon meg bejelentkezni a(z) :socialAccount opción keresztül!',
    'social_account_email_in_use' => 'A(z) :email email cím már használatban van. Ha már van fiókja, hozzá tudja kapcsolni a(z) :socialAccount fiókját a profil beállításainál.',
    'social_account_existing' => 'Ez a(z) :socialAccount már hozzá van kapcsolva a fiókjához.',
    'social_account_already_used_existing' => 'Ezt a(z) :socialAccount fiókot egy másik felhasználó már használja.',
    'social_account_not_used' => 'Ez a(z) :socialAccount fiók nincs egy felhasználóhoz sem kapcsolva. Kapcsolja hozzá a profil beállításoknál. ',
    'social_account_register_instructions' => 'Ha még nincsen fiókja, regisztrálhat egyet a(z) :socialAccount opció használatával.',
    'social_driver_not_found' => 'Nem található közösségi média illesztőprogram',
    'social_driver_not_configured' => 'A(z) :socialAccount fiók összekapcsolás beállításai nem megfelelőek.',
    'invite_token_expired' => 'Ez a meghívó már lejárt. Helyette megpróbálhatja a fiók jelszavát visszaállítani.',
    'login_user_not_found' => 'A művelethez nem található felhasználó.',

    // System
    'path_not_writable' => 'Nem sikerült feltölteni a :filePath elérési útra. Ellenőrizze, hogy az útvonal írható a szerveren!',
    'cannot_get_image_from_url' => 'Nem lehet lekérni a képet a(z) :url címről',
    'cannot_create_thumbs' => 'A szerver nem tud létrehozni bélyegképeket. Ellenőrizze, hogy telepítve van a GD PHP kiterjesztés!',
    'server_upload_limit' => 'A szerver nem engedélyez ekkora méretű feltöltéseket. Kérjük próbálkozzon kisebb fájl mérettel.',
    'server_post_limit' => 'A szerver nem tudja fogadni a megadott adatmennyiséget. Próbálkozzon újra kevesebb adattal vagy egy kisebb fájllal!',
    'uploaded'  => 'A szerver nem engedélyez ekkora méretű feltöltéseket. Kérjük próbálkozzon kisebb fájl mérettel.',

    // Drawing & Images
    'image_upload_error' => 'Hiba történt a kép feltöltése közben',
    'image_upload_type_error' => 'A feltöltött kép típusa érvénytelen',
    'image_upload_replace_type' => 'A cserélt képnek azonos típusúnak kell lennie',
    'image_upload_memory_limit' => 'A rendszererőforrás-korlátok miatt nem sikerült kezelni a képfeltöltést és/vagy az indexképek létrehozását.',
    'image_thumbnail_memory_limit' => 'A rendszererőforrás-korlátok miatt nem sikerült létrehozni a képméret-változatokat.',
    'image_gallery_thumbnail_memory_limit' => 'A rendszererőforrás-korlátok miatt nem sikerült létrehozni a galéria bélyegképét.',
    'drawing_data_not_found' => 'A rajzadatokat nem sikerült betölteni. Előfordulhat, hogy a rajzfájl már nem létezik, vagy nem rendelkezik hozzáférési engedéllyel.',

    // Attachments
    'attachment_not_found' => 'Csatolmány nem található',
    'attachment_upload_error' => 'Hiba történt a melléklet feltöltésekor',

    // Pages
    'page_draft_autosave_fail' => 'Nem sikerült a vázlat mentése. Mentés előtt állítsd helyre az internetkapcsolatot',
    'page_draft_delete_fail' => 'Nem sikerült törölni az oldalvázlatot és lekérni az aktuális oldal mentett tartalmat',
    'page_custom_home_deletion' => 'Nem lehet oldalt törölni ha kezdőlapnak van beállítva',

    // Entities
    'entity_not_found' => 'Entitás nem található',
    'bookshelf_not_found' => 'Polc nem található',
    'book_not_found' => 'Könyv nem található',
    'page_not_found' => 'Oldal nem található',
    'chapter_not_found' => 'Fejezet nem található',
    'selected_book_not_found' => 'A kiválasztott könyv nem található',
    'selected_book_chapter_not_found' => 'A kiválasztott könyv vagy fejezet nem található',
    'guests_cannot_save_drafts' => 'Vendégek nem menthetnek el vázlatokat',

    // Users
    'users_cannot_delete_only_admin' => 'Nem lehet törölni az egyetlen adminisztrátort',
    'users_cannot_delete_guest' => 'A vendég felhasználót nem lehet törölni',
    'users_could_not_send_invite' => 'Nem lehetett létrehozni a felhasználót, mivel a meghívó levelet nem sikerült elküldeni',

    // Roles
    'role_cannot_be_edited' => 'Ezt a szerepkört nem lehet szerkeszteni',
    'role_system_cannot_be_deleted' => 'Ez a szerepkör egy rendszer szerepkör ezért nem törölhető',
    'role_registration_default_cannot_delete' => 'Ezt a szerepkört nem lehet törölni amíg alapértelmezés szerinti regisztrációs szerepkörnek van beállítva',
    'role_cannot_remove_only_admin' => 'Ez a felhasználó az egyetlen, az adminisztrátor szerepkörhöz rendelt felhasználó. Eltávolítása előtt az adminisztrátor szerepkört át kell ruházni egy másik felhasználóra.',

    // Comments
    'comment_list' => 'Hiba történt a hozzászólások lekérése közben.',
    'cannot_add_comment_to_draft' => 'Vázlathoz nem lehet hozzászólni.',
    'comment_add' => 'Hiba történt a hozzászólás hozzáadása/frissítése közben.',
    'comment_delete' => 'Hiba történt a hozzászólás törlése közben.',
    'empty_comment' => 'Üres hozzászólást nem lehet hozzáadni.',

    // Error pages
    '404_page_not_found' => 'Az oldal nem található',
    'sorry_page_not_found' => 'Sajnáljuk, a keresett oldal nem található.',
    'sorry_page_not_found_permission_warning' => 'Ha arra számított, hogy ez az oldal létezik, előfordulhat, hogy nincs jogosultsága a megtekintésére.',
    'image_not_found' => 'A kép nem található',
    'image_not_found_subtitle' => 'Sajnáljuk, a keresett kép nem található.',
    'image_not_found_details' => 'Ha arra számított, hogy ez a kép létezik, akkor előfordulhat, hogy törölték.',
    'return_home' => 'Vissza a kezdőlapra',
    'error_occurred' => 'Hiba történt',
    'app_down' => ':appName jelenleg nem üzemel',
    'back_soon' => 'Hamarosan újra elérhető lesz.',

    // Import
    'import_zip_cant_read' => 'Nem sikerült a ZIP fájlt olvasni.',
    'import_zip_cant_decode_data' => 'Nem sikerült megtalálni és dekódolni a data.json tartalmát a ZIP fájlban.',
    'import_zip_no_data' => 'A ZIP fájlban nincsen könyv, fejezet vagy oldal tartalom.',
    'import_zip_data_too_large' => 'A ZIP fájlban lévő data.json tartalma meghaladja a beállított feltöltési méret limitet.',
    'import_validation_failed' => 'A ZIP fájl importálásának ellenőrzése sikertelen volt a következő hibák miatt:',
    'import_zip_failed_notification' => 'Nem sikerült a ZIP fájlt importálni.',
    'import_perms_books' => 'Nem rendelkezik a könyvek létrehozásához szükséges jogosultságokkal.',
    'import_perms_chapters' => 'Nem rendelkezik a fejezetek létrehozásához szükséges jogosultságokkal.',
    'import_perms_pages' => 'Nem rendelkezik az oldalak létrehozásához szükséges jogosultságokkal.',
    'import_perms_images' => 'Nem rendelkezik a képek létrehozásához szükséges jogosultságokkal.',
    'import_perms_attachments' => 'Nem rendelkezik a csatolmányok létrehozásához szükséges jogosultságokkal.',

    // API errors
    'api_no_authorization_found' => 'A kérésben nem található hitelesítési kulcs',
    'api_bad_authorization_format' => 'A kérésben hitelesítési kulcs található de a formátuma érvénytelennek tűnik',
    'api_user_token_not_found' => 'A megadott hitelesítési kulcshoz nem található egyező API kulcs',
    'api_incorrect_token_secret' => 'Az API kulcshoz megadott jelkulcs helytelen',
    'api_user_no_api_permission' => 'A használt API kulcs tulajdonosának nincs jogosultsága API hívások végrehajtásához',
    'api_user_token_expired' => 'A használt hitelesítési kulcs lejárt',
    'api_cookie_auth_only_get' => 'Kizárólag GET kérések engedélyezettek az API-n keresztül süti alapú authentikáció használatkor',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Hiba történt egy teszt email küldésekor:',

    // HTTP errors
    'http_ssr_url_no_match' => 'Az URL nem egyezik a konfigurált és engedélyezett SSR-állomásokkal',
];
