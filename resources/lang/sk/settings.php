<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Nastavenia',
    'settings_save' => 'Uložiť nastavenia',
    'settings_save_success' => 'Nastavenia uložené',

    /**
     * App settings
     */

    'app_settings' => 'Nastavenia aplikácie',
    'app_name' => 'Názov aplikácia',
    'app_name_desc' => 'Tento názov sa zobrazuje v hlavičke a v emailoch.',
    'app_name_header' => 'Zobraziť názov aplikácie v hlavičke?',
    'app_public_viewing' => 'Povoliť verejné zobrazenie?',
    'app_secure_images' => 'Povoliť nahrávanie súborov so zvýšeným zabezpečením?',
    'app_secure_images_desc' => 'Kvôli výkonu sú všetky obrázky verejné. Táto možnosť pridá pred URL obrázka náhodný, ťažko uhádnuteľný reťazec. Aby ste zabránili jednoduchému prístupu, uistite sa, že indexy priečinkov nie sú povolené.',
    'app_editor' => 'Editor stránky',
    'app_editor_desc' => 'Vyberte editor, ktorý bude používaný všetkými používateľmi na editáciu stránok.',
    'app_custom_html' => 'Vlastný HTML obsah hlavičky',
    'app_custom_html_desc' => 'Všetok text pridaný sem bude vložený naspodok <head> sekcie na každej stránke. Môže sa to zísť pri zmene štýlu alebo pre pridanie analytického kódu.',
    'app_logo' => 'Logo aplikácie',
    'app_logo_desc' => 'Tento obrázok by mal mať 43px na výšku. <br>Veľké obrázky budú preškálované na menší rozmer.',
    'app_primary_color' => 'Primárna farba pre aplikáciu',
    'app_primary_color_desc' => 'Toto by mala byť hodnota v hex tvare. <br>Nechajte prázdne ak chcete použiť prednastavenú farbu.',
    'app_disable_comments' => 'Zakázať komentáre',
    'app_disable_comments_desc' => 'Zakázať komentáre na všetkých stránkach aplikácie. Existujúce komentáre sa nezobrazujú.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Nastavenia registrácie',
    'reg_allow' => 'Povoliť registráciu?',
    'reg_default_role' => 'Prednastavená používateľská rola po registrácii',
    'reg_confirm_email' => 'Vyžadovať overenie emailu?',
    'reg_confirm_email_desc' => 'Ak je použité obmedzenie domény, potom bude vyžadované overenie emailu a hodnota nižšie bude ignorovaná.',
    'reg_confirm_restrict_domain' => 'Obmedziť registráciu na doménu',
    'reg_confirm_restrict_domain_desc' => 'Zadajte zoznam domén, pre ktoré chcete povoliť registráciu oddelených čiarkou. Používatelia dostanú email kvôli overeniu adresy predtým ako im bude dovolené používať aplikáciu. <br> Používatelia si budú môcť po úspešnej registrácii zmeniť svoju emailovú adresu.',
    'reg_confirm_restrict_domain_placeholder' => 'Nie sú nastavené žiadne obmedzenia',

    /**
     * Role settings
     */

    'roles' => 'Roly',
    'role_user_roles' => 'Používateľské roly',
    'role_create' => 'Vytvoriť novú rolu',
    'role_create_success' => 'Rola úspešne vytvorená',
    'role_delete' => 'Zmazať rolu',
    'role_delete_confirm' => 'Toto zmaže rolu menom \':roleName\'.',
    'role_delete_users_assigned' => 'Túto rolu má priradenú :userCount používateľov. Ak chcete premigrovať používateľov z tejto roly, vyberte novú rolu nižšie.',
    'role_delete_no_migration' => "Nemigrovať používateľov",
    'role_delete_sure' => 'Ste si istý, že chcete zmazať túto rolu?',
    'role_delete_success' => 'Rola úspešne zmazaná',
    'role_edit' => 'Upraviť rolu',
    'role_details' => 'Detaily roly',
    'role_name' => 'Názov roly',
    'role_desc' => 'Krátky popis roly',
    'role_system' => 'Systémové oprávnenia',
    'role_manage_users' => 'Spravovať používateľov',
    'role_manage_roles' => 'Spravovať role a oprávnenia rolí',
    'role_manage_entity_permissions' => 'Spravovať všetky oprávnenia kníh, kapitol a stránok',
    'role_manage_own_entity_permissions' => 'Spravovať oprávnenia vlastných kníh, kapitol a stránok',
    'role_manage_settings' => 'Spravovať nastavenia aplikácie',
    'role_asset' => 'Oprávnenia majetku',
    'role_asset_desc' => 'Tieto oprávnenia regulujú prednastavený prístup k zdroju v systéme. Oprávnenia pre knihy, kapitoly a stránky majú vyššiu prioritu.',
    'role_all' => 'Všetko',
    'role_own' => 'Vlastné',
    'role_controlled_by_asset' => 'Regulované zdrojom, do ktorého sú nahrané',
    'role_save' => 'Uložiť rolu',
    'role_update_success' => 'Roly úspešne aktualizované',
    'role_users' => 'Používatelia s touto rolou',
    'role_users_none' => 'Žiadni používatelia nemajú priradenú túto rolu',

    /**
     * Users
     */

    'users' => 'Používatelia',
    'user_profile' => 'Profil používateľa',
    'users_add_new' => 'Pridať nového používateľa',
    'users_search' => 'Hľadať medzi používateľmi',
    'users_role' => 'Používateľské roly',
    'users_external_auth_id' => 'Externé autentifikačné ID',
    'users_password_warning' => 'Pole nižšie vyplňte iba ak chcete zmeniť heslo:',
    'users_system_public' => 'Tento účet reprezentuje každého hosťovského používateľa, ktorý navštívi Vašu inštanciu. Nedá sa pomocou neho prihlásiť a je priradený automaticky.',
    'users_books_view_type' => 'Preferované rozloženie pre prezeranie kníh',
    'users_delete' => 'Zmazať používateľa',
    'users_delete_named' => 'Zmazať používateľa :userName',
    'users_delete_warning' => ' Toto úplne odstráni používateľa menom \':userName\' zo systému.',
    'users_delete_confirm' => 'Ste si istý, že chcete zmazať tohoto používateľa?',
    'users_delete_success' => 'Používateľ úspešne zmazaný',
    'users_edit' => 'Upraviť používateľa',
    'users_edit_profile' => 'Upraviť profil',
    'users_edit_success' => 'Používateľ úspešne upravený',
    'users_avatar' => 'Avatar používateľa',
    'users_avatar_desc' => 'Tento obrázok by mal byť štvorec s rozmerom približne 256px.',
    'users_preferred_language' => 'Preferovaný jazyk',
    'users_social_accounts' => 'Sociálne účty',
    'users_social_accounts_info' => 'Tu si môžete pripojiť iné účty pre rýchlejšie a jednoduchšie prihlásenie. Disconnecting an account here does not previously authorized access. Revoke access from your profile settings on the connected social account.',
    'users_social_connect' => 'Pripojiť účet',
    'users_social_disconnect' => 'Odpojiť účet',
    'users_social_connected' => ':socialAccount účet bol úspešne pripojený k Vášmu profilu.',
    'users_social_disconnected' => ':socialAccount účet bol úspešne odpojený od Vášho profilu.',
];
