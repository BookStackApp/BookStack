<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Instellingen',
    'settings_save' => 'Instellingen Opslaan',
    'settings_save_success' => 'Instellingen Opgeslagen',

    /**
     * App settings
     */

    'app_settings' => 'App Instellingen',
    'app_name' => 'Applicatienaam',
    'app_name_desc' => 'De applicatienaam wordt in e-mails in in de header weergegeven.',
    'app_name_header' => 'Applicatienaam in de header weergeven?',
    'app_public_viewing' => 'Publieke bewerkingen toestaan?',
    'app_secure_images' => 'Beter beveiligide afbeeldingen gebruiken?',
    'app_secure_images_desc' => 'Omwille van de performance zijn alle afbeeldingen publiek toegankelijk. Zorg ervoor dat je de \'directory index\' niet hebt ingeschakeld.',
    'app_editor' => 'Pagina Bewerken',
    'app_editor_desc' => 'Selecteer welke tekstverwerker je wilt gebruiken.',
    'app_custom_html' => 'Speciale HTML toevoegen',
    'app_custom_html_desc' => 'Alles wat je hier toevoegd wordt in de <head> sectie van elke pagina meengenomen. Dit kun je bijvoorbeeld voor analytics gebruiken.',
    'app_logo' => 'Applicatielogo',
    'app_logo_desc' => 'De afbeelding moet 43px hoog zijn. <br>Grotere afbeeldingen worden geschaald.',
    'app_primary_color' => 'Applicatie hoofdkleur',
    'app_primary_color_desc' => 'Geef een hexadecimale waarde. <br>Als je niks invult wordt de standaardkleur gebruikt.',
    'app_disable_comments' => 'Reacties uitschakelen',
    'app_disable_comments_desc' => 'Schakel opmerkingen uit op alle pagina\'s in de applicatie. Bestaande opmerkingen worden niet getoond.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Registratieinstellingen',
    'reg_allow' => 'Registratie toestaan?',
    'reg_default_role' => 'Standaard rol na registratie',
    'reg_confirm_email' => 'E-mailbevesting vereist?',
    'reg_confirm_email_desc' => 'Als domeinrestricties aan staan dan is altijd e-maibevestiging nodig. Onderstaande instelling wordt dan genegeerd.',
    'reg_confirm_restrict_domain' => 'Beperk registratie tot een maildomein',
    'reg_confirm_restrict_domain_desc' => 'Geen een komma-gescheiden lijst van domeinnamen die gebruikt mogen worden bij registratie. <br> Let op: na registratie kunnen gebruikers hun e-mailadres nog steeds wijzigen.',
    'reg_confirm_restrict_domain_placeholder' => 'Geen beperkingen ingesteld',

    /**
     * Role settings
     */

    'roles' => 'Rollen',
    'role_user_roles' => 'Gebruikrollen',
    'role_create' => 'Nieuwe Rol Maken',
    'role_create_success' => 'Rol succesvol aangemaakt',
    'role_delete' => 'Rol Verwijderen',
    'role_delete_confirm' => 'Dit verwijdert de rol \':roleName\'.',
    'role_delete_users_assigned' => 'Er zijn :userCount gebruikers met deze rol. Selecteer hieronder een nieuwe rol als je deze gebruikers een andere rol wilt geven.',
    'role_delete_no_migration' => "Geen gebruikers migreren",
    'role_delete_sure' => 'Weet je zeker dat je deze rol wilt verwijderen?',
    'role_delete_success' => 'Rol succesvol verwijderd',
    'role_edit' => 'Rol Bewerken',
    'role_details' => 'Rol Details',
    'role_name' => 'Rolnaam',
    'role_desc' => 'Korte beschrijving van de rol',
    'role_system' => 'Systeem Permissies',
    'role_manage_users' => 'Gebruikers beheren',
    'role_manage_roles' => 'Rollen en rechten beheren',
    'role_manage_entity_permissions' => 'Beheer alle boeken-, hoofdstukken- en paginaresitrcties',
    'role_manage_own_entity_permissions' => 'Beheer restricties van je eigen boeken, hoofdstukken en pagina\'s',
    'role_manage_settings' => 'Beheer app instellingen',
    'role_asset' => 'Asset Permissies',
    'role_asset_desc' => 'Deze permissies bepalen de standaardtoegangsrechten. Permissies op boeken, hoofdstukken en pagina\'s overschrijven deze instelling.',
    'role_all' => 'Alles',
    'role_own' => 'Eigen',
    'role_controlled_by_asset' => 'Gecontroleerd door de asset waar deze is geüpload',
    'role_save' => 'Rol Opslaan',
    'role_update_success' => 'Rol succesvol bijgewerkt',
    'role_users' => 'Gebruikers in deze rol',
    'role_users_none' => 'Geen enkele gebruiker heeft deze rol',

    /**
     * Users
     */

    'users' => 'Gebruikers',
    'user_profile' => 'Gebruikersprofiel',
    'users_add_new' => 'Gebruiker toevoegen',
    'users_search' => 'Gebruiker zoeken',
    'users_role' => 'Gebruikersrollen',
    'users_external_auth_id' => 'External Authentication ID',
    'users_password_warning' => 'Vul onderstaande formulier alleen in als je het wachtwoord wilt aanpassen:',
    'users_system_public' => 'De eigenschappen van deze gebruiker worden voor elke gastbezoeker gebruikt. Er kan niet mee ingelogd worden en wordt automatisch toegewezen.',
    'users_books_view_type' => 'Voorkeursuitleg voor het weergeven van boeken',
    'users_delete' => 'Verwijder gebruiker',
    'users_delete_named' => 'Verwijder gebruiker :userName',
    'users_delete_warning' => 'Dit zal de gebruiker \':userName\' volledig uit het systeem verwijderen.',
    'users_delete_confirm' => 'Weet je zeker dat je deze gebruiker wilt verwijderen?',
    'users_delete_success' => 'Gebruiker succesvol verwijderd',
    'users_edit' => 'Bewerk Gebruiker',
    'users_edit_profile' => 'Bewerk Profiel',
    'users_edit_success' => 'Gebruiker succesvol bijgewerkt',
    'users_avatar' => 'Avatar',
    'users_avatar_desc' => 'De afbeelding moet vierkant zijn en ongeveer 256px breed.',
    'users_preferred_language' => 'Voorkeurstaal',
    'users_social_accounts' => 'Social Accounts',
    'users_social_accounts_info' => 'Hier kun je accounts verbinden om makkelijker in te loggen. Via je profiel kun je ook weer rechten intrekken die bij deze social accountsh horen.',
    'users_social_connect' => 'Account Verbinden',
    'users_social_disconnect' => 'Account Ontkoppelen',
    'users_social_connected' => ':socialAccount account is succesvol aan je profiel gekoppeld.',
    'users_social_disconnected' => ':socialAccount account is succesvol ontkoppeld van je profiel.',
];
