<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Nemáte povolení přistupovat na dotazovanou stránku.',
    'permissionJson' => 'Nemáte povolení k provedení požadované akce.',

    // Auth
    'error_user_exists_different_creds' => 'Uživatel s emailem :email již existuje ale s jinými přihlašovacími údaji.',
    'email_already_confirmed' => 'Emailová adresa již byla potvrzena. Zkuste se přihlásit.',
    'email_confirmation_invalid' => 'Tento potvrzovací odkaz již neplatí nebo už byl použit. Zkuste prosím registraci znovu.',
    'email_confirmation_expired' => 'Potvrzovací odkaz už neplatí, email s novým odkazem už byl poslán.',
    'email_confirmation_awaiting' => 'The email address for the account in use needs to be confirmed',
    'ldap_fail_anonymous' => 'Přístup k adresáři LDAP jako anonymní uživatel (anonymous bind) selhal',
    'ldap_fail_authed' => 'Přístup k adresáři LDAP pomocí zadaného jména (dn) a hesla selhal',
    'ldap_extension_not_installed' => 'Není nainstalováno rozšíření LDAP pro PHP',
    'ldap_cannot_connect' => 'Nelze se připojit k adresáři LDAP. Prvotní připojení selhalo.',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
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
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'Nelze zapisovat na cestu k souboru :filePath. Zajistěte aby se dalo nahrávat na server.',
    'cannot_get_image_from_url' => 'Nelze získat obrázek z adresy :url',
    'cannot_create_thumbs' => 'Server nedokáže udělat náhledy. Zkontrolujte, že rozšíření GD pro PHP je nainstalováno.',
    'server_upload_limit' => 'Server nepovoluje nahrávat tak veliké soubory. Zkuste prosím menší soubor.',
    'uploaded'  => 'Server nepovoluje nahrávat tak veliké soubory. Zkuste prosím menší soubor.',
    'image_upload_error' => 'Nastala chyba během nahrávání souboru',
    'image_upload_type_error' => 'Typ nahrávaného obrázku je neplatný.',
    'file_upload_timeout' => 'Nahrávání souboru trvalo příliš dlouho a tak bylo ukončeno.',

    // Attachments
    'attachment_page_mismatch' => 'Došlo ke zmatení stránky během nahrávání přílohy.',
    'attachment_not_found' => 'Příloha nenalezena',

    // Pages
    'page_draft_autosave_fail' => 'Nepovedlo se uložit koncept. Než stránku uložíte, ujistěte se, že jste připojeni k internetu.',
    'page_custom_home_deletion' => 'Nelze smazat tuto stránku, protože je nastavena jako uvítací stránka.',

    // Entities
    'entity_not_found' => 'Prvek nenalezen',
    'bookshelf_not_found' => 'Knihovna nenalezena',
    'book_not_found' => 'Kniha nenalezena',
    'page_not_found' => 'Stránka nenalezena',
    'chapter_not_found' => 'Kapitola nenalezena',
    'selected_book_not_found' => 'Vybraná kniha nebyla nalezena',
    'selected_book_chapter_not_found' => 'Zvolená kniha nebo kapitola nebyla nalezena',
    'guests_cannot_save_drafts' => 'Návštěvníci z řad veřejnosti nemohou ukládat koncepty.',

    // Users
    'users_cannot_delete_only_admin' => 'Nemůžete smazat posledního administrátora',
    'users_cannot_delete_guest' => 'Uživatele host není možno smazat',

    // Roles
    'role_cannot_be_edited' => 'Tuto roli nelze editovat',
    'role_system_cannot_be_deleted' => 'Toto je systémová role a nelze jí smazat.',
    'role_registration_default_cannot_delete' => 'Tuto roli nelze smazat dokud je nastavená jako výchozí role pro registraci nových uživatelů.',
    'role_cannot_remove_only_admin' => 'Tento uživatel má roli administrátora. Přiřaďte roli administrátora někomu jinému než jí odeberete zde.',

    // Comments
    'comment_list' => 'Při dotahování komentářů nastala chyba.',
    'cannot_add_comment_to_draft' => 'Nemůžete přidávat komentáře ke konceptu.',
    'comment_add' => 'Při přidávání / aktualizaci komentáře nastala chyba.',
    'comment_delete' => 'Při mazání komentáře nastala chyba.',
    'empty_comment' => 'Nemůžete přidat prázdný komentář.',

    // Error pages
    '404_page_not_found' => 'Stránka nenalezena',
    'sorry_page_not_found' => 'Omlouváme se, ale stránka, kterou hledáte nebyla nalezena.',
    'sorry_page_not_found_permission_warning' => 'If you expected this page to exist, you might not have permission to view it.',
    'return_home' => 'Návrat domů',
    'error_occurred' => 'Nastala chyba',
    'app_down' => ':appName je momentálně vypnutá',
    'back_soon' => 'Brzy naběhne.',

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
