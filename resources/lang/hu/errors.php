<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nincs jogosultság a kért oldal eléréséhez.',
    'permissionJson' => 'Nincs jogosultság a kért művelet végrehajtásához.',

    // Auth
    'error_user_exists_different_creds' => ':email címmel már létezik felhasználó, de más hitelesítő adatokkal.',
    'email_already_confirmed' => 'Az email cím már meg van erősítve, meg lehet próbálni a bejelentkezést.',
    'email_confirmation_invalid' => 'A megerősítő vezérjel nem érvényes vagy használva volt. Meg kell próbálni újraregisztrálni.',
    'email_confirmation_expired' => 'A megerősítő vezérjel lejárt. Egy új megerősítő email lett elküldve.',
    'email_confirmation_awaiting' => 'A használatban lévő fiók email címét meg kell erősíteni',
    'ldap_fail_anonymous' => 'Nem sikerült az LDAP elérése névtelen csatlakozással',
    'ldap_fail_authed' => 'Az LDAP hozzáférés nem sikerült a megadott DN és jelszó beállításokkal',
    'ldap_extension_not_installed' => 'LDAP PHP kiterjesztés nincs telepítve',
    'ldap_cannot_connect' => 'Nem lehet kapcsolódni az LDAP kiszolgálóhoz, a kezdeti kapcsolatfelvétel nem sikerült',
    'saml_already_logged_in' => 'Már bejelentkezett',
    'saml_user_not_registered' => ':name felhasználó nincs regisztrálva és az automatikus regisztráció le van tiltva',
    'saml_no_email_address' => 'Ehhez a felhasználóhoz nem található email cím a külső hitelesítő rendszer által átadott adatokban',
    'saml_invalid_response_id' => 'A külső hitelesítő rendszerből érkező kérést nem ismerte fel az alkalmazás által indított folyamat. Bejelentkezés után az előző oldalra történő visszalépés okozhatja ezt a hibát.',
    'saml_fail_authed' => 'Bejelentkezés :system használatával sikertelen, a rendszer nem biztosított sikeres hitelesítést',
    'oidc_already_logged_in' => 'Már bejelentkezett',
    'oidc_user_not_registered' => ':name felhasználó nincs regisztrálva és az automatikus regisztráció le van tiltva',
    'oidc_no_email_address' => 'Ehhez a felhasználóhoz nem található email cím a külső hitelesítő rendszer által átadott adatokban',
    'oidc_fail_authed' => 'Bejelentkezés :system használatával sikertelen, a rendszer nem biztosított sikeres hitelesítést',
    'social_no_action_defined' => 'Nincs művelet meghatározva',
    'social_login_bad_response' => "Hiba történt :socialAccount bejelentkezés közben:\n:error",
    'social_account_in_use' => ':socialAccount fiók már használatban van. :socialAccount opción keresztül érdemes megpróbálni a bejelentkezést.',
    'social_account_email_in_use' => ':email email cím már használatban van. Ha már van fiók létrehozva, :egy socialAccount fiókot hozzá lehet csatolni a profil beállításainál.',
    'social_account_existing' => ':socialAccount már hozzá van kapcsolva a fiókhoz.',
    'social_account_already_used_existing' => ':socialAccount fiókot már egy másik felhasználó használja.',
    'social_account_not_used' => ':socialAccount fiók nincs felhasználóhoz kapcsolva. A hozzákapcsolást a profil oldalon lehet elvégezni. ',
    'social_account_register_instructions' => ':socialAccount beállítása használatával is lehet fiókot regisztrálni, ha még nem volt fiók létrehozva.',
    'social_driver_not_found' => 'Közösségi meghajtó nem található',
    'social_driver_not_configured' => ':socialAccount közösségi beállítások nem megfelelőek.',
    'invite_token_expired' => 'Ez a meghívó hivatkozás lejárt. Helyette meg lehet próbálni új jelszót megadni a fiókhoz.',

    // System
    'path_not_writable' => ':filePath elérési út nem tölthető fel. Ellenőrizni kell, hogy az útvonal a kiszolgáló számára írható.',
    'cannot_get_image_from_url' => 'Nem lehet lekérni a képet innen: :url',
    'cannot_create_thumbs' => 'A kiszolgáló nem tud létrehozni bélyegképeket. Ellenőrizni kell, hogy telepítve van-a a GD PHP kiterjesztés.',
    'server_upload_limit' => 'A kiszolgáló nem engedélyez ilyen méretű feltöltéseket. Kisebb fájlmérettel kell próbálkozni.',
    'uploaded'  => 'A kiszolgáló nem engedélyez ilyen méretű feltöltéseket. Kisebb fájlmérettel kell próbálkozni.',
    'image_upload_error' => 'Hiba történt a kép feltöltése közben',
    'image_upload_type_error' => 'A feltöltött kép típusa érvénytelen',
    'file_upload_timeout' => 'A fáj feltöltése időtúllépést okozott.',

    // Attachments
    'attachment_not_found' => 'Csatolmány nem található',

    // Pages
    'page_draft_autosave_fail' => 'Nem sikerült a vázlat mentése. Mentés előtt meg kell róla győződni, hogy van internetkapcsolat',
    'page_custom_home_deletion' => 'Nem lehet oldalt törölni ha kezdőlapnak van beállítva',

    // Entities
    'entity_not_found' => 'Entitás nem található',
    'bookshelf_not_found' => 'Könyvespolc nem található',
    'book_not_found' => 'Könyv nem található',
    'page_not_found' => 'Oldal nem található',
    'chapter_not_found' => 'Fejezet nem található',
    'selected_book_not_found' => 'A kiválasztott könyv nem található',
    'selected_book_chapter_not_found' => 'A kiválasztott könyv vagy fejezet nem található',
    'guests_cannot_save_drafts' => 'Vendégek nem menthetnek el vázlatokat',

    // Users
    'users_cannot_delete_only_admin' => 'Nem lehet törölni az egyetlen adminisztrátort',
    'users_cannot_delete_guest' => 'A vendég felhasználót nem lehet törölni',

    // Roles
    'role_cannot_be_edited' => 'Ezt a szerepkört nem lehet szerkeszteni',
    'role_system_cannot_be_deleted' => 'Ez a szerepkör egy rendszer szerepkör ezért nem törölhető',
    'role_registration_default_cannot_delete' => 'Ezt a szerepkört nem lehet törölni amíg alapértelmezés szerinti regisztrációs szerepkörnek van beállítva',
    'role_cannot_remove_only_admin' => 'Ez a felhasználó az egyetlen, az adminisztrátor szerepkörhöz rendelt felhasználó. Eltávolítása előtt az adminisztrátor szerepkört át kell ruházni egy másik felhasználóra.',

    // Comments
    'comment_list' => 'Hiba történt a megjegyzések lekérése közben.',
    'cannot_add_comment_to_draft' => 'Vázlathoz nem lehet megjegyzéseket fűzni.',
    'comment_add' => 'Hiba történt a megjegyzés hozzáadása / frissítése közben.',
    'comment_delete' => 'Hiba történt a megjegyzés törlése közben.',
    'empty_comment' => 'Üres megjegyzést nem lehet hozzáadni.',

    // Error pages
    '404_page_not_found' => 'Oldal nem található',
    'sorry_page_not_found' => 'Sajnáljuk, a keresett oldal nem található.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'image_not_found' => 'A kép nem található',
    'image_not_found_subtitle' => 'Sajnáljuk, a keresett kép nem található.',
    'image_not_found_details' => 'If you expected this image to exist it might have been deleted.',
    'return_home' => 'Vissza a kezdőlapra',
    'error_occurred' => 'Hiba örtént',
    'app_down' => ':appName jelenleg nem üzemel',
    'back_soon' => 'Hamarosan újra elérhető lesz.',

    // API errors
    'api_no_authorization_found' => 'A kérésben nem található hitelesítési vezérjel',
    'api_bad_authorization_format' => 'A kérésben hitelesítési vezérjel található de a formátuma érvénytelennek tűnik',
    'api_user_token_not_found' => 'A megadott hitelesítési vezérjelhez nem található egyező API vezérjel',
    'api_incorrect_token_secret' => 'The secret provided for the given used API token is incorrect',
    'api_user_no_api_permission' => 'A használt API vezérjel tulajdonosának nincs jogosultsága API hívások végrehajtásához',
    'api_user_token_expired' => 'A használt hitelesítési vezérjel lejárt',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Hiba történt egy teszt email küldésekor:',

];
