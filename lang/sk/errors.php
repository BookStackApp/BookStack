<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nemáte oprávnenie pre prístup k požadovanej stránke.',
    'permissionJson' => 'Nemáte oprávnenie pre vykonanie požadovaného úkonu.',

    // Auth
    'error_user_exists_different_creds' => 'Používateľ s emailom :email už existuje, ale s inými údajmi.',
    'email_already_confirmed' => 'Email bol už overený, skúste sa prihlásiť.',
    'email_confirmation_invalid' => 'Tento potvrdzujúci token nie je platný alebo už bol použitý, skúste sa prosím registrovať znova.',
    'email_confirmation_expired' => 'Potvrdzujúci token expiroval, bol odoslaný nový potvrdzujúci email.',
    'email_confirmation_awaiting' => 'Potvrďte emailovú adresu pre užívateľský účet',
    'ldap_fail_anonymous' => 'Prístup LDAP zlyhal',
    'ldap_fail_authed' => 'Prístup LDAP zlyhal pomocou zadaných podrobností dn a hesla',
    'ldap_extension_not_installed' => 'Rozšírenie LDAP PHP nie je nainštalované',
    'ldap_cannot_connect' => 'Nedá sa pripojiť k serveru ldap, počiatočné pripojenie zlyhalo',
    'saml_already_logged_in' => 'Používateľ sa už prihlásil',
    'saml_user_not_registered' => 'Používateľ :name nie je zaregistrovaný a automatická registrácia je zakázaná',
    'saml_no_email_address' => 'V údajoch poskytnutých externým overovacím systémom sa nepodarilo nájsť e-mailovú adresu tohto používateľa',
    'saml_invalid_response_id' => 'Požiadavka z externého autentifikačného systému nie je rozpoznaná procesom spusteným touto aplikáciou. Tento problém môže spôsobiť navigácia späť po prihlásení.',
    'saml_fail_authed' => 'Prihlásenie pomocou :system zlyhalo, systém neposkytol úspešnú autorizáciu',
    'oidc_already_logged_in' => 'Používateľ sa už prihlásil',
    'oidc_user_not_registered' => 'Používateľ :name nie je zaregistrovaný a automatická registrácia je zakázaná',
    'oidc_no_email_address' => 'V údajoch poskytnutých externým overovacím systémom sa nepodarilo nájsť e-mailovú adresu tohto používateľa',
    'oidc_fail_authed' => 'Prihlásenie pomocou :system zlyhalo, systém neposkytol úspešnú autorizáciu',
    'social_no_action_defined' => 'Nebola definovaná žiadna akcia',
    'social_login_bad_response' => "Pri prihlásení do účtu :socialAccount došlo k chybe:\n:error",
    'social_account_in_use' => 'Tento :socialAccount účet sa už používa, skúste sa prihlásiť pomocou možnosti :socialAccount.',
    'social_account_email_in_use' => 'Email :email sa už používa. Ak už máte účet, môžete pripojiť svoj :socialAccount účet v nastaveniach profilu.',
    'social_account_existing' => 'Tento :socialAccount účet je už spojený s Vaším profilom.',
    'social_account_already_used_existing' => 'Tento :socialAccount účet už používa iný používateľ.',
    'social_account_not_used' => 'Tento :socialAccount účet nie je spojený so žiadnym používateľom. Pripojte ho prosím v nastaveniach Vášho profilu. ',
    'social_account_register_instructions' => 'Ak zatiaľ nemáte účet, môžete sa registrovať pomocou možnosti :socialAccount.',
    'social_driver_not_found' => 'Ovládač socialnych sietí nebol nájdený',
    'social_driver_not_configured' => 'Nastavenia Vášho :socialAccount účtu nie sú správne.',
    'invite_token_expired' => 'Platnosť tohto odkazu na pozvánku vypršala. Namiesto toho sa môžete pokúsiť obnoviť heslo účtu.',

    // System
    'path_not_writable' => 'Do cesty :filePath sa nedá nahrávať. Uistite sa, že je zapisovateľná serverom.',
    'cannot_get_image_from_url' => 'Nedá sa získať obrázok z :url',
    'cannot_create_thumbs' => 'Server nedokáže vytvoriť náhľady. Skontrolujte prosím, či máte nainštalované GD rozšírenie PHP.',
    'server_upload_limit' => 'Server nedovoľuje nahrávanie súborov s takouto veľkosťou. Skúste prosím menší súbor.',
    'uploaded'  => 'Server nedovoľuje nahrávanie súborov s takouto veľkosťou. Skúste prosím menší súbor.',

    // Drawing & Images
    'image_upload_error' => 'Pri nahrávaní obrázka nastala chyba',
    'image_upload_type_error' => 'Typ nahrávaného obrázka je neplatný',
    'drawing_data_not_found' => 'Údaje výkresu sa nepodarilo načítať. Súbor výkresu už možno neexistuje alebo nemáte povolenie na prístup k nemu.',

    // Attachments
    'attachment_not_found' => 'Príloha nenájdená',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Koncept nemohol byť uložený. Uistite sa, že máte pripojenie k internetu pre uložením tejto stránky',
    'page_custom_home_deletion' => 'Stránku nie je možné odstrániť, kým je nastavená ako domovská stránka',

    // Entities
    'entity_not_found' => 'Entita nenájdená',
    'bookshelf_not_found' => 'Polica nenájdená',
    'book_not_found' => 'Kniha nenájdená',
    'page_not_found' => 'Stránka nenájdená',
    'chapter_not_found' => 'Kapitola nenájdená',
    'selected_book_not_found' => 'Vybraná kniha nebola nájdená',
    'selected_book_chapter_not_found' => 'Vybraná kniha alebo kapitola nebola nájdená',
    'guests_cannot_save_drafts' => 'Hosť nemôže ukladať koncepty',

    // Users
    'users_cannot_delete_only_admin' => 'Nemôžete zmazať posledného správcu',
    'users_cannot_delete_guest' => 'Nemôžete zmazať hosťa',

    // Roles
    'role_cannot_be_edited' => 'Táto rola nemôže byť upravovaná',
    'role_system_cannot_be_deleted' => 'Táto rola je systémová rola a nemôže byť zmazaná',
    'role_registration_default_cannot_delete' => 'Táto rola nemôže byť zmazaná, pretože je nastavená ako prednastavená rola pri registrácii',
    'role_cannot_remove_only_admin' => 'Tento používateľ je jediným používateľom priradeným k role správcu. Priraďte rolu správcu inému používateľovi skôr, ako sa ju pokúsite odstrániť tu.',

    // Comments
    'comment_list' => 'Pri načítaní komentárov sa vyskytla chyba',
    'cannot_add_comment_to_draft' => 'Do konceptu nemôžete pridávať komentáre.',
    'comment_add' => 'Počas pridávania komentára sa vyskytla chyba',
    'comment_delete' => 'Pri odstraňovaní komentára došlo k chybe',
    'empty_comment' => 'Nelze pridať prázdny komentár.',

    // Error pages
    '404_page_not_found' => 'Stránka nenájdená',
    'sorry_page_not_found' => 'Prepáčte, stránka ktorú hľadáte nebola nájdená.',
    'sorry_page_not_found_permission_warning' => 'Ak ste očakávali existenciu tejto stránky, možno nemáte povolenie na jej zobrazenie.',
    'image_not_found' => 'Obrázok nebol nájdený',
    'image_not_found_subtitle' => 'Ľutujeme, obrázok, ktorý ste hľadali, sa nepodarilo nájsť.',
    'image_not_found_details' => 'Ak ste očakávali, že tento obrázok existuje, mohol byť odstránený.',
    'return_home' => 'Vrátiť sa domov',
    'error_occurred' => 'Nastala chyba',
    'app_down' => ':appName je momentálne nedostupná',
    'back_soon' => 'Čoskoro bude opäť dostupná.',

    // API errors
    'api_no_authorization_found' => 'V žiadosti sa nenašiel žiadny autorizačný token',
    'api_bad_authorization_format' => 'V žiadosti sa našiel autorizačný token, ale formát sa zdal nesprávny',
    'api_user_token_not_found' => 'Pre poskytnutý autorizačný token sa nenašiel žiadny zodpovedajúci token rozhrania API',
    'api_incorrect_token_secret' => 'Secret poskytnutý pre daný token API je nesprávny',
    'api_user_no_api_permission' => 'Vlastník použitého tokenu API nemá povolenie na uskutočňovanie volaní rozhrania API',
    'api_user_token_expired' => 'Platnosť použitého autorizačného tokenu vypršala',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Chyba pri odosielaní testovacieho e-mailu:',

];
