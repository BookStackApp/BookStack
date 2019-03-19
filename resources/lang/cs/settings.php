<?php
/**
 * Settings text strings
 * Contains all text strings used in the general settings sections of BookStack
 * including users and roles.
 */
return [

    // Common Messages
    'settings' => 'Nastavení',
    'settings_save' => 'Uložit nastavení',
    'settings_save_success' => 'Nastavení bylo uloženo',

    // App Settings
    'app_settings' => 'Nastavení aplikace',
    'app_name' => 'Název aplikace',
    'app_name_desc' => 'Název se bude zobrazovat v záhlaví této aplikace a v odesílaných emailech.',
    'app_name_header' => 'Zobrazovát název aplikace v záhlaví?',
    'app_public_viewing' => 'Povolit prohlížení veřejností?',
    'app_secure_images' => 'Nahrávat obrázky neveřejně a zabezpečeně?',
    'app_secure_images_desc' => 'Z výkonnostních důvodů jsou všechny obrázky veřejné. Tato volba přidá do adresy obrázku náhodné číslo, aby nikdno neodhadnul adresu obrázku. Zajistěte ať adresáře nikomu nezobrazují seznam soubroů.',
    'app_editor' => 'Editor stránek',
    'app_editor_desc' => 'Zvolte který editor budou užívat všichni uživatelé k úpravě stránek.',
    'app_custom_html' => 'Vlastní HTML kód pro sekci hlavičky (<head>).',
    'app_custom_html_desc' => 'Cokoliv sem napíšete bude přidáno na konec sekce <head> v každém místě této aplikace. To se hodí pro přidávání nebo změnu CSS stylů nebo přidání kódu pro analýzu používání (např.: google analytics.).',
    'app_logo' => 'Logo aplikace',
    'app_logo_desc' => 'Obrázek by měl mít 43 pixelů na výšku. <br>Větší obrázky zmenšíme na tuto velikost.',
    'app_primary_color' => 'Hlavní barva aplikace',
    'app_primary_color_desc' => 'Zápis by měl být hexa (#aabbcc). <br>Pro základní barvu nechte pole prázdné.',
    'app_homepage' => 'Úvodní stránka aplikace',
    'app_homepage_desc' => 'Zvolte pohled který se objeví jako úvodní stránka po přihlášení. Pokud zvolíte stránku, její specifická oprávnění budou ignorována (výjimka z výjimky 😜).',
    'app_homepage_select' => 'Zvolte stránku',
    'app_disable_comments' => 'Zakázání komentářů',
    'app_disable_comments_desc' => 'Zakáže komentáře napříč všemi stránkami. Existující komentáře se přestanou zobrazovat.',

    // Registration Settings
    'reg_settings' => 'Nastavení registrace',
    'reg_allow' => 'Povolit registrace?',
    'reg_default_role' => 'Role přiřazená po registraci',
    'reg_confirm_email' => 'Vyžadovat oveření emailové adresy?',
    'reg_confirm_email_desc' => 'Pokud zapnete omezení emailové domény, tak bude ověřování emailové adresy vyžadováno vždy.',
    'reg_confirm_restrict_domain' => 'Omezit registraci podle domény',
    'reg_confirm_restrict_domain_desc' => 'Zadejte emailové domény, kterým bude povolena registrace uživatelů. Oddělujete čárkou. Uživatelům bude odeslán email s odkazem pro potvrzení vlastnictví emailové adresy. Bez potvrzení nebudou moci aplikaci používat. <br> Pozn.: Uživatelé si mohou emailovou adresu změnit po úspěšné registraci.',
    'reg_confirm_restrict_domain_placeholder' => 'Žádná omezení nebyla nastvena',

    // Maintenance settings
    'maint' => 'Údržba',
    'maint_image_cleanup' => 'Pročistění obrázků',
    'maint_image_cleanup_desc' => "Prohledá stránky a jejich revize, aby zjistil, které obrázky a kresby jsou momentálně používány a které jsou zbytečné. Zajistěte plnou zálohu databáze a obrázků než se do toho pustíte.",
    'maint_image_cleanup_ignore_revisions' => 'Ignorovat obrázky v revizích',
    'maint_image_cleanup_run' => 'Spustit pročištění',
    'maint_image_cleanup_warning' => 'Nalezeno :count potenciálně nepoužitých obrázků. Jste si jistí, že je chcete smazat?',
    	
    'maint_image_cleanup_success' => 'Potenciálně nepoužité obrázky byly smazány. Celkem :count.',
    'maint_image_cleanup_nothing_found' => 'Žádné potenciálně nepoužité obrázky nebyly nalezeny. Nic nebylo smazáno.',

    // Role Settings
    'roles' => 'Role',
    'role_user_roles' => 'Uživatelské role',
    'role_create' => 'Vytvořit novou roli',
    'role_create_success' => 'Role byla úspěšně vytvořena',
    'role_delete' => 'Smazat roli',
    'role_delete_confirm' => 'Role \':roleName\' bude smazána.',
    'role_delete_users_assigned' => 'Role je přiřazena :userCount uživatelům. Pokud jim chcete náhradou přidělit jinou roli, zvolte jednu z následujících.',
    'role_delete_no_migration' => "Nepřiřazovat uživatelům náhradní roli",
    'role_delete_sure' => 'Opravdu chcete tuto roli smazat?',
    'role_delete_success' => 'Role byla úspěšně smazána',
    'role_edit' => 'Upravit roli',
    'role_details' => 'Detaily role',
    'role_name' => 'Název role',
    'role_desc' => 'Stručný popis role',
    'role_external_auth_id' => 'Přihlašovací identifikátory třetích stran',
    'role_system' => 'Systémová oprávnění',
    'role_manage_users' => 'Správa úživatelů',
    'role_manage_roles' => 'Správa rolí a jejich práv',
    'role_manage_entity_permissions' => 'Správa práv všech knih, kapitol a stránek',
    'role_manage_own_entity_permissions' => 'Správa práv vlastních knih, kapitol a stránek',
    'role_manage_settings' => 'Správa nastavení aplikace',
    'role_asset' => 'Práva děl',
    'role_asset_desc' => 'Tato práva řídí přístup k dílům v rámci systému. Specifická práva na knihách, kapitolách a stránkách překryjí tato nastavení.',
    'role_asset_admins' => 'Administrátoři automaticky dostávají přístup k veškerému obsahu, ale tyto volby mohou ukázat nebo skrýt volby v uživatelském rozhranní.',
    'role_all' => 'Vše',
    'role_own' => 'Vlastní',
    'role_controlled_by_asset' => 'Řídí se dílem do kterého jsou nahrávány',
    'role_save' => 'Uloži roli',
    'role_update_success' => 'Role úspěšně aktualizována',
    'role_users' => 'Uživatelé mající tuto roli',
    'role_users_none' => 'Žádný uřivatel nemá tuto roli.',

    // Users
    'users' => 'Uživatelé',
    'user_profile' => 'Profil uživatele',
    'users_add_new' => 'Přidat nového uživatele',
    'users_search' => 'Vyhledávání uživatelů',
    'users_role' => 'Uživatelské role',
    'users_external_auth_id' => 'Přihlašovací identifikátory třetích stran',
    'users_password_warning' => 'Vyplňujte pouze v případě, že chcete heslo změnit:',
    'users_system_public' => 'Symbolizuje libovolného veřejnéhoh návštevníka, který vaštívil vaší aplikaci. Nelze ho použít k přihlášení ale je přiřazen automaticky veřejnosti.',
    'users_delete' => 'Smazat uživatele',
    'users_delete_named' => 'Smazat uživatele :userName',
    'users_delete_warning' => 'Uživatel \':userName\' bude úplně smazán ze systému.',
    'users_delete_confirm' => 'Opravdu chete tohoto uživatele smazat?',
    'users_delete_success' => 'Uživatel byl úspěšně smazán',
    'users_edit' => 'Upravit uživatele',
    'users_edit_profile' => 'Upravit profil',
    'users_edit_success' => 'Uživatel byl úspěšně aktualizován',
    'users_avatar' => 'Uživatelský obrázek',
    'users_avatar_desc' => 'Obrázek by měl být čtverec 256 pixelů široký. Bude oříznut do kruhu.',
    'users_preferred_language' => 'Upřednostňovaný jazyk',
    'users_social_accounts' => 'Přidružené účty ze sociálnních sítí',
    'users_social_accounts_info' => 'Zde můžete přidat vaše účty ze sociálních sítí pro pohodlnější přihlašování. Zrušení přidružení zde neznamená, že tato aplikace pozbyde práva číst detaily z vašeho účtu. Zakázat této aplikaci přístup k detailům vašeho účtu musíte přímo ve vašem profilu na dané sociální síti.',    		
    
    'users_social_connect' => 'Přidružit účet',
    'users_social_disconnect' => 'Zrušit přidružení',
    'users_social_connected' => 'Účet :socialAccount byl úspěšně přidružen k vašemu profilu.',
    'users_social_disconnected' => 'Přidružení účetu :socialAccount k vašemu profilu bylo úspěšně zrušeno.'
];
