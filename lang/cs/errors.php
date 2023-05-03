<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nemáte povolení přistupovat na požadovanou stránku.',
    'permissionJson' => 'Nemáte povolení k provedení požadované akce.',

    // Auth
    'error_user_exists_different_creds' => 'Uživatel s emailem :email již existuje ale s jinými přihlašovacími údaji.',
    'email_already_confirmed' => 'Emailová adresa již byla potvrzena. Zkuste se přihlásit.',
    'email_confirmation_invalid' => 'Tento potvrzovací odkaz již neplatí nebo už byl použit. Zkuste prosím registraci znovu.',
    'email_confirmation_expired' => 'Tento potvrzovací odkaz již neplatí, byl Vám odeslán nový potvrzovací e-mail.',
    'email_confirmation_awaiting' => 'E-mailová adresa pro používaný účet musí být potvrzena',
    'ldap_fail_anonymous' => 'Přístup k adresáři LDAP jako anonymní uživatel (anonymous bind) selhal',
    'ldap_fail_authed' => 'Přístup k adresáři LDAP pomocí zadaného jména (dn) a hesla selhal',
    'ldap_extension_not_installed' => 'Není nainstalováno rozšíření LDAP pro PHP',
    'ldap_cannot_connect' => 'Nelze se připojit k adresáři LDAP. Prvotní připojení selhalo.',
    'saml_already_logged_in' => 'Již jste přihlášeni',
    'saml_user_not_registered' => 'Uživatel :name není registrován a automatická registrace je zakázána',
    'saml_no_email_address' => 'Nelze najít e-mailovou adresu pro tohoto uživatele v datech poskytnutých externím přihlašovacím systémem',
    'saml_invalid_response_id' => 'Požadavek z externího ověřovacího systému nebyl rozpoznám procesem, který tato aplikace spustila. Tento problém může způsobit stisknutí tlačítka Zpět po přihlášení.',
    'saml_fail_authed' => 'Přihlášení pomocí :system selhalo, systém neposkytl úspěšnou autorizaci',
    'oidc_already_logged_in' => 'Již jste přihlášeni',
    'oidc_user_not_registered' => 'Uživatel :name není registrován a automatická registrace je zakázána',
    'oidc_no_email_address' => 'Nelze najít e-mailovou adresu pro tohoto uživatele v datech poskytnutých externím přihlašovacím systémem',
    'oidc_fail_authed' => 'Přihlášení pomocí :system selhalo, systém neposkytl úspěšnou autorizaci',
    'social_no_action_defined' => 'Nebyla zvolena žádá akce',
    'social_login_bad_response' => "Nastala chyba během přihlašování přes :socialAccount \n:error",
    'social_account_in_use' => 'Tento účet na :socialAccount se již používá. Pokuste se s ním přihlásit volbou Přihlásit přes :socialAccount.',
    'social_account_email_in_use' => 'Emailová adresa :email se již používá. Pokud máte již máte náš účet, můžete si jej propojit se svým účtem na :socialAccount v nastavení vašeho profilu.',
    'social_account_existing' => 'Tento účet na :socialAccount je již propojen s vaším profilem zde.',
    'social_account_already_used_existing' => 'Tento účet na :socialAccount je již používán jiným uživatelem.',
    'social_account_not_used' => 'Tento účet na :socialAccount není spřažen s žádným uživatelem. Prosím přiřaďtě si jej v nastavení svého profilu.',
    'social_account_register_instructions' => 'Pokud ještě nemáte náš účet, můžete se zaregistrovat pomocí vašeho účtu na :socialAccount.',
    'social_driver_not_found' => 'Doplněk pro tohoto správce identity nebyl nalezen.',
    'social_driver_not_configured' => 'Nastavení vašeho účtu na :socialAccount není správné. :socialAccount musí mít vaše svolení pro naší aplikaci vás přihlásit.',
    'invite_token_expired' => 'Odkaz v pozvánce již bohužel vypršel. Namísto toho ale můžete zkusit resetovat heslo do Vašeho účtu.',

    // System
    'path_not_writable' => 'Nelze zapisovat na cestu k souboru :filePath. Zajistěte aby se dalo nahrávat na server.',
    'cannot_get_image_from_url' => 'Nelze získat obrázek z adresy :url',
    'cannot_create_thumbs' => 'Server nedokáže udělat náhledy. Zkontrolujte, že rozšíření GD pro PHP je nainstalováno.',
    'server_upload_limit' => 'Server nepovoluje nahrávat tak veliké soubory. Zkuste prosím menší soubor.',
    'uploaded'  => 'Server nepovoluje nahrávat tak veliké soubory. Zkuste prosím menší soubor.',

    // Drawing & Images
    'image_upload_error' => 'Nastala chyba během nahrávání souboru',
    'image_upload_type_error' => 'Typ nahrávaného obrázku je neplatný.',
    'drawing_data_not_found' => 'Data výkresu nelze načíst. Výkresový soubor již nemusí existovat nebo nemusí mít oprávnění k němu přistupovat.',

    // Attachments
    'attachment_not_found' => 'Příloha nenalezena',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Nepovedlo se uložit koncept. Než stránku uložíte, ujistěte se, že jste připojeni k internetu.',
    'page_custom_home_deletion' => 'Nelze odstranit tuto stránku, protože je nastavena jako uvítací stránka',

    // Entities
    'entity_not_found' => 'Prvek nenalezen',
    'bookshelf_not_found' => 'Knihovna nenalezena',
    'book_not_found' => 'Kniha nenalezena',
    'page_not_found' => 'Stránka nenalezena',
    'chapter_not_found' => 'Kapitola nenalezena',
    'selected_book_not_found' => 'Vybraná kniha nebyla nalezena',
    'selected_book_chapter_not_found' => 'Zvolená kniha nebo kapitola nebyla nalezena',
    'guests_cannot_save_drafts' => 'Nepřihlášení návštěvníci nemohou ukládat koncepty',

    // Users
    'users_cannot_delete_only_admin' => 'Nemůžete odstranit posledního administrátora',
    'users_cannot_delete_guest' => 'Uživatele Host není možno odstranit',

    // Roles
    'role_cannot_be_edited' => 'Tuto roli nelze editovat',
    'role_system_cannot_be_deleted' => 'Toto je systémová role a nelze jí odstranit',
    'role_registration_default_cannot_delete' => 'Tuto roli nelze odstranit dokud je nastavená jako výchozí role pro registraci nových uživatelů',
    'role_cannot_remove_only_admin' => 'Tento uživatel má roli administrátora. Přiřaďte roli administrátora někomu jinému než jí odeberete zde.',

    // Comments
    'comment_list' => 'Při načítání komentářů nastala chyba.',
    'cannot_add_comment_to_draft' => 'Nemůžete přidávat komentáře ke konceptu.',
    'comment_add' => 'Při přidávání / aktualizaci komentáře nastala chyba.',
    'comment_delete' => 'Při odstraňování komentáře nastala chyba.',
    'empty_comment' => 'Nemůžete přidat prázdný komentář.',

    // Error pages
    '404_page_not_found' => 'Stránka nenalezena',
    'sorry_page_not_found' => 'Omlouváme se, ale stránka, kterou hledáte, nebyla nalezena.',
    'sorry_page_not_found_permission_warning' => 'Pokud očekáváte, že by stránka měla existovat, možná jen nemáte oprávnění pro její zobrazení.',
    'image_not_found' => 'Obrázek nenalezen',
    'image_not_found_subtitle' => 'Omlouváme se, ale obrázek, který hledáte, nebyl nalezen.',
    'image_not_found_details' => 'Pokud očekáváte, že by obrázel měl existovat, tak byl zřejmě již odstraněn.',
    'return_home' => 'Návrat domů',
    'error_occurred' => 'Nastala chyba',
    'app_down' => ':appName je momentálně vypnutá',
    'back_soon' => 'Brzy bude opět v provozu.',

    // API errors
    'api_no_authorization_found' => 'V požadavku nebyl nalezen žádný autorizační token',
    'api_bad_authorization_format' => 'V požadavku byl nalezen autorizační token, ale jeho formát se zdá být chybný',
    'api_user_token_not_found' => 'Pro zadaný autorizační token nebyl nalezen žádný odpovídající API token',
    'api_incorrect_token_secret' => 'Poskytnutý Token Secret neodpovídá použitému API tokenu',
    'api_user_no_api_permission' => 'Vlastník použitého API tokenu nemá oprávnění provádět API volání',
    'api_user_token_expired' => 'Platnost autorizačního tokenu vypršela',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Při posílání testovacího e-mailu nastala chyba:',

];
