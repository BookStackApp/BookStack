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
    'ldap_fail_anonymous' => '',
    'ldap_fail_authed' => '',
    'ldap_extension_not_installed' => '',
    'ldap_cannot_connect' => '',
    'social_no_action_defined' => 'Nebola definovaná žiadna akcia',
    'social_login_bad_response' => "",
    'social_account_in_use' => 'Tento :socialAccount účet sa už používa, skúste sa prihlásiť pomocou možnosti :socialAccount.',
    'social_account_email_in_use' => 'Email :email sa už používa. Ak už máte účet, môžete pripojiť svoj :socialAccount účet v nastaveniach profilu.',
    'social_account_existing' => 'Tento :socialAccount účet je už spojený s Vaším profilom.',
    'social_account_already_used_existing' => 'Tento :socialAccount účet už používa iný používateľ.',
    'social_account_not_used' => 'Tento :socialAccount účet nie je spojený so žiadnym používateľom. Pripojte ho prosím v nastaveniach Vášho profilu. ',
    'social_account_register_instructions' => 'Ak zatiaľ nemáte účet, môžete sa registrovať pomocou možnosti :socialAccount.',
    'social_driver_not_found' => 'Ovládač socialnych sietí nebol nájdený',
    'social_driver_not_configured' => 'Nastavenia Vášho :socialAccount účtu nie sú správne.',
    'invite_token_expired' => '',

    // System
    'path_not_writable' => 'Do cesty :filePath sa nedá nahrávať. Uistite sa, že je zapisovateľná serverom.',
    'cannot_get_image_from_url' => 'Nedá sa získať obrázok z :url',
    'cannot_create_thumbs' => 'Server nedokáže vytvoriť náhľady. Skontrolujte prosím, či máte nainštalované GD rozšírenie PHP.',
    'server_upload_limit' => 'Server nedovoľuje nahrávanie súborov s takouto veľkosťou. Skúste prosím menší súbor.',
    'uploaded'  => '',
    'image_upload_error' => 'Pri nahrávaní obrázka nastala chyba',
    'image_upload_type_error' => '',
    'file_upload_timeout' => 'Nahrávanie súboru vypršalo.',

    // Attachments
    'attachment_page_mismatch' => '',
    'attachment_not_found' => '',

    // Pages
    'page_draft_autosave_fail' => 'Koncept nemohol byť uložený. Uistite sa, že máte pripojenie k internetu pre uložením tejto stránky',
    'page_custom_home_deletion' => '',

    // Entities
    'entity_not_found' => 'Entita nenájdená',
    'bookshelf_not_found' => '',
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
    'role_cannot_remove_only_admin' => '',

    // Comments
    'comment_list' => 'Pri načítaní komentárov sa vyskytla chyba',
    'cannot_add_comment_to_draft' => 'Do konceptu nemôžete pridávať komentáre.',
    'comment_add' => 'Počas pridávania komentára sa vyskytla chyba',
    'comment_delete' => 'Pri odstraňovaní komentára došlo k chybe',
    'empty_comment' => 'Nelze pridať prázdny komentár.',

    // Error pages
    '404_page_not_found' => 'Stránka nenájdená',
    'sorry_page_not_found' => 'Prepáčte, stránka ktorú hľadáte nebola nájdená.',
    'return_home' => 'Vrátiť sa domov',
    'error_occurred' => 'Nastala chyba',
    'app_down' => ':appName je momentálne nedostupná',
    'back_soon' => 'Čoskoro bude opäť dostupná.',

];
