<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Inställningar',
    'settings_save' => 'Spara inställningar',
    'settings_save_success' => 'Inställningarna har sparats',

    /**
     * App settings
     */

    'app_settings' => 'Appinställningar',
    'app_name' => 'Applikationsnamn',
    'app_name_desc' => 'Namnet visas i sidhuvdet och i eventuella mail.',
    'app_name_header' => 'Visa applikationsnamn i sidhuvudet?',
    'app_public_viewing' => 'Tillåt publikt innehåll?',
    'app_secure_images' => 'Aktivera högre säkerhet för bilduppladdningar?',
    'app_secure_images_desc' => 'Av prestandaskäl är alla bilder publika. Det här alternativet lägger till en slumpmässig, svårgissad sträng framför alla bild-URL:er. Se till att kataloglistning inte är aktivt för att förhindra åtkomst.',
    'app_editor' => 'Redigeringsverktyg',
    'app_editor_desc' => 'Välj vilket redigeringsverktyg som ska användas av alla användare för att redigera sidor.',
    'app_custom_html' => 'Egen HTML i <head>',
    'app_custom_html_desc' => 'Eventuellt innehåll i det här fältet placeras längst ner i <head>-sektionen på varje sida. Detta är användbart för att skriva över stilmaller eller lägga in spårningskoder.',
    'app_logo' => 'Applikationslogotyp',
    'app_logo_desc' => 'Bilden bör vara minst 43px hög. <br>Större bilder skalas ner.',
    'app_primary_color' => 'Primärfärg',
    'app_primary_color_desc' => 'Detta ska vara en hexadimal färgkod. <br>Lämna tomt för att återställa standardfärgen.',
    'app_homepage' => 'Startsida',
    'app_homepage_desc' => 'Välj en sida att använda som startsida istället för standardvyn. Den valda sidans rättigheter kommer att ignoreras.',
    'app_homepage_default' => 'Vald vy för startsida',
    'app_disable_comments' => 'Inaktivera kommentarer',
    'app_disable_comments_desc' => 'Inaktivera kommentarer på alla sidor i applikationen. Befintliga kommentarer visas inte.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Registreringsinställningar',
    'reg_allow' => 'Tillåt registrering?',
    'reg_default_role' => 'Standardroll efter registrering',
    'reg_confirm_email' => 'Kräv e-postbekräftelse?',
    'reg_confirm_email_desc' => 'Om registrering begränas till vissa domäner kommer e-postbekräftelse alltid att krävas och den här inställningen kommer att ignoreras.',
    'reg_confirm_restrict_domain' => 'Begränsa registrering till viss domän',
    'reg_confirm_restrict_domain_desc' => 'Ange en kommaseparerad lista över e-postdomäner till vilka du vill begränsa registrering. Användare kommer att skickas ett mail för att bekräfta deras e-post innan de får logga in. <br> Notera att användare kommer att kunna ändra sin e-postadress efter lyckad registrering.',
    'reg_confirm_restrict_domain_placeholder' => 'Ingen begränsning satt',

    /**
     * Role settings
     */

    'roles' => 'Roller',
    'role_user_roles' => 'Användarroller',
    'role_create' => 'Skapa ny roll',
    'role_create_success' => 'Rollen har skapats',
    'role_delete' => 'Ta bort roll',
    'role_delete_confirm' => 'Rollen med namn \':roleName\' kommer att tas bort.',
    'role_delete_users_assigned' => 'Det finns :userCount användare som tillhör den här rollen. Om du vill migrera användarna från den här rollen, välj en ny roll nedan.',
    'role_delete_no_migration' => 'Migrera inte användare',
    'role_delete_sure' => 'Är du säker på att du vill ta bort den här rollen?',
    'role_delete_success' => 'Rollen har tagits bort',
    'role_edit' => 'Redigera roll',
    'role_details' => 'Om rollen',
    'role_name' => 'Rollens namn',
    'role_desc' => 'Kort beskrivning av rollen',
    'role_system' => 'Systemrättigheter',
    'role_manage_users' => 'Hanter användare',
    'role_manage_roles' => 'Hantera roller & rättigheter',
    'role_manage_entity_permissions' => 'Hantera rättigheter för alla böcker, kapitel och sidor',
    'role_manage_own_entity_permissions' => 'Hantera rättigheter för egna böcker, kapitel och sidor',
    'role_manage_settings' => 'Hantera appinställningar',
    'role_asset' => 'Tillgång till innehåll',
    'role_asset_desc' => 'Det här är standardinställningarna för allt innehåll i systemet. Eventuella anpassade rättigheter på böcker, kapitel och sidor skriver över dessa inställningar.',
    'role_all' => 'Alla',
    'role_own' => 'Egna',
    'role_controlled_by_asset' => 'Kontrolleras av den sida de laddas upp till',
    'role_save' => 'Spara roll',
    'role_update_success' => 'Rollen har uppdaterats',
    'role_users' => 'Användare med denna roll',
    'role_users_none' => 'Inga användare tillhör den här rollen',

    /**
     * Users
     */

    'users' => 'Användare',
    'user_profile' => 'Användarprofil',
    'users_add_new' => 'Lägg till användare',
    'users_search' => 'Sök användare',
    'users_role' => 'Användarroller',
    'users_external_auth_id' => 'Externt ID för autentisering',
    'users_password_warning' => 'Fyll i nedanstående fält endast om du vill byta lösenord:',
    'users_system_public' => 'Den här användaren representerar eventuella gäster som använder systemet. Den kan inte användas för att logga in utan tilldeles automatiskt.',
    'users_books_view_type' => 'Layout för visning av böcker',
    'users_delete' => 'Ta bort användare',
    'users_delete_named' => 'Ta bort användaren :userName',
    'users_delete_warning' => 'Detta kommer att ta bort användaren \':userName\' från systemet helt och hållet.',
    'users_delete_confirm' => 'Är du säker på att du vill ta bort användaren?',
    'users_delete_success' => 'Användaren har tagits bort',
    'users_edit' => 'Redigera användare',
    'users_edit_profile' => 'Redigera profil',
    'users_edit_success' => 'Användaren har uppdaterats',
    'users_avatar' => 'Avatar',
    'users_avatar_desc' => 'Bilden bör vara kvadratisk och ca 256px stor.',
    'users_preferred_language' => 'Språk',
    'users_social_accounts' => 'Anslutna konton',
    'users_social_accounts_info' => 'Här kan du ansluta dina andra konton för snabbare och smidigare inloggning. Om du kopplar från en tjänst här kommer de behörigheter som tidigare givits inte att tas bort - ta bort behörigheter genom att logga in på ditt konto på tjänsten i fråga.',
    'users_social_connect' => 'Anslut konto',
    'users_social_disconnect' => 'Koppla från konto',
    'users_social_connected' => ':socialAccount har kopplats till ditt konto.',
    'users_social_disconnected' => ':socialAccount har kopplats bort från ditt konto.',

    // Since these labels are already localized this array does not need to be
    // translated in the language-specific files.
    // DELETE BELOW IF COPIED FROM EN
    ///////////////////////////////////
    'language_select' => [
        'en' => 'English',
        'de' => 'Deutsch',
        'es' => 'Español',
        'es_AR' => 'Español Argentina',
        'fr' => 'Français',
        'nl' => 'Nederlands',
        'pt_BR' => 'Português do Brasil',
        'sk' => 'Slovensky',
        'sv' => 'Svenska',
        'ja' => '日本語',
        'pl' => 'Polski',
        'it' => 'Italian',
        'ru' => 'Русский'
    ]
    ///////////////////////////////////
];
