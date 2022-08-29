<?php
/**
 * Settings text strings
 * Contains all text strings used in the general settings sections of BookStack
 * including users and roles.
 */
return [

    // Common Messages
    'settings' => 'Instellingen',
    'settings_save' => 'Instellingen opslaan',
    'settings_save_success' => 'Instellingen Opgeslagen',
    'system_version' => 'Systeem versie',
    'categories' => 'Categorieën',

    // App Settings
    'app_customization' => 'Aanpassingen',
    'app_features_security' => 'Functies en beveiliging',
    'app_name' => 'Applicatienaam',
    'app_name_desc' => 'Deze naam wordt getoond in de header en in alle door het systeem verstuurde e-mails.',
    'app_name_header' => 'Toon naam in header',
    'app_public_access' => 'Openbare toegang',
    'app_public_access_desc' => 'Door deze optie in te schakelen kunnen bezoekers, die niet ingelogd zijn, toegang krijgen tot de inhoud van uw BookStack omgeving.',
    'app_public_access_desc_guest' => 'De toegang voor publieke bezoekers kan worden ingesteld via de "Guest" gebruiker.',
    'app_public_access_toggle' => 'Openbare toegang toestaan',
    'app_public_viewing' => 'Publieke bezichtigingen toestaan?',
    'app_secure_images' => 'Uploaden van afbeeldingen met hogere beveiliging',
    'app_secure_images_toggle' => 'Activeer uploaden van afbeeldingen met hogere beveiliging',
    'app_secure_images_desc' => 'Om prestatieredenen zijn alle afbeeldingen openbaar. Deze optie voegt een willekeurige en moeilijk te raden tekst toe aan de URL\'s van de afbeeldingen. Zorg ervoor dat "directory indexes" niet ingeschakeld zijn om eenvoudige toegang te voorkomen.',
    'app_default_editor' => 'Standaard Pagina Bewerker',
    'app_default_editor_desc' => 'Selecteer welke bewerker standaard zal worden gebruikt bij het bewerken van nieuwe pagina\'s. Dit kan worden overschreven op paginaniveau als de rechten dat toestaan.',
    'app_custom_html' => 'HTML aan <head> toevoegen',
    'app_custom_html_desc' => 'Alle hieronder toegevoegde data wordt aan het einde van de <head> sectie van elke pagina toegevoegd. Gebruik dit om stijlen te overschrijven of analytische code toe te voegen.',
    'app_custom_html_disabled_notice' => 'Bovenstaande wordt niet toegevoegd aan deze pagina om ervoor te zorgen dat je foutieve code steeds ongedaan kan maken.',
    'app_logo' => 'Applicatielogo',
    'app_logo_desc' => 'De afbeelding moet 43px hoog zijn. <br>Grotere afbeeldingen worden geschaald.',
    'app_primary_color' => 'Applicatie hoofdkleur',
    'app_primary_color_desc' => 'Stelt de primaire kleur in voor de applicatie, inclusief de banner, knoppen en hyperlinks.',
    'app_homepage' => 'Applicatie Startpagina',
    'app_homepage_desc' => 'Selecteer een weergave om weer te geven op de startpagina in plaats van de standaard weergave. Paginamachtigingen worden genegeerd voor geselecteerde pagina\'s.',
    'app_homepage_select' => 'Selecteer een pagina',
    'app_footer_links' => 'Voettekst hyperlinks',
    'app_footer_links_desc' => 'Voeg hyperlinks toe aan de voettekst van de applicatie. Deze zullen onderaan de meeste pagina\'s getoond worden, ook aan pagina\'s die geen login vereisen. U kunt een label van "trans::<key>" gebruiken om systeem-gedefinieerde vertalingen te gebruiken. Bijvoorbeeld: Het gebruik van "trans::common.privacy_policy" zal de vertaalde tekst "Privacy Policy" opleveren en "trans::common.terms_of_service" zal de vertaalde tekst "Gebruiksvoorwaarden" opleveren.',
    'app_footer_links_label' => 'Link label',
    'app_footer_links_url' => 'Link URL',
    'app_footer_links_add' => 'Voettekst link toevoegen',
    'app_disable_comments' => 'Reacties uitschakelen',
    'app_disable_comments_toggle' => 'Reacties uitschakelen',
    'app_disable_comments_desc' => 'Schakel reacties uit op alle pagina\'s in de applicatie. <br> Bestaande reacties worden niet getoond.',

    // Color settings
    'content_colors' => 'Kleuren van inhoud',
    'content_colors_desc' => 'Stelt de kleuren in voor alle elementen van de hiërarchische pagina-indeling. Voor de leesbaarheid wordt aanbevolen kleuren te kiezen met een vergelijkbare helderheid als de standaardkleuren.',
    'bookshelf_color' => 'Kleur van de Boekenplank',
    'book_color' => 'Kleur van het Boek',
    'chapter_color' => 'Kleur van het Hoofdstuk',
    'page_color' => 'Pagina kleur',
    'page_draft_color' => 'Concept pagina kleur',

    // Registration Settings
    'reg_settings' => 'Registratie',
    'reg_enable' => 'Registratie inschakelen',
    'reg_enable_toggle' => 'Registratie inschakelen',
    'reg_enable_desc' => 'Wanneer registratie is ingeschakeld, kunnen gebruikers zichzelf aanmelden als applicatiegebruiker. Bij registratie krijgen ze een enkele, standaard gebruikersrol.',
    'reg_default_role' => 'Standaard rol na registratie',
    'reg_enable_external_warning' => 'De optie hierboven wordt niet gebruikt terwijl externe LDAP- of SAML authenticatie actief is. Gebruikersaccounts voor niet-bestaande leden zullen automatisch worden aangemaakt wanneer authenticatie tegen het gebruikte externe systeem succesvol is.',
    'reg_email_confirmation' => 'E-mail bevestiging',
    'reg_email_confirmation_toggle' => 'E-mailbevestiging vereisen',
    'reg_confirm_email_desc' => 'Als domeinrestricties aan staan dan is e-maibevestiging altijd nodig. Onderstaande instelling wordt dan genegeerd.',
    'reg_confirm_restrict_domain' => 'Beperk registratie tot een domein',
    'reg_confirm_restrict_domain_desc' => 'Geef een door komma-gescheiden lijst van domeinnamen op die gebruikt mogen worden bij registratie. Gebruikers dienen de ontvangen e-mail te bevestigen voordat ze toegang krijgen tot de applicatie. <br>Let op: Gebruikers kunnen na registratie hun e-mailadres nog steeds wijzigen.',
    'reg_confirm_restrict_domain_placeholder' => 'Geen beperkingen ingesteld',

    // Maintenance settings
    'maint' => 'Onderhoud',
    'maint_image_cleanup' => 'Afbeeldingen opschonen',
    'maint_image_cleanup_desc' => 'Scant pagina- en revisie inhoud om te controleren welke afbeeldingen en tekeningen momenteel worden gebruikt en welke afbeeldingen overbodig zijn. Zorg ervoor dat je een volledige database- en afbeelding back-up maakt voordat je dit uitvoert.',
    'maint_delete_images_only_in_revisions' => 'Ook afbeeldingen verwijderen die alleen in oude pagina revisies bestaan',
    'maint_image_cleanup_run' => 'Opschonen uitvoeren',
    'maint_image_cleanup_warning' => ':count potentieel ongebruikte afbeeldingen gevonden. Weet u zeker dat u deze afbeeldingen wilt verwijderen?',
    'maint_image_cleanup_success' => ':count potentieel ongebruikte afbeeldingen gevonden en verwijderd!',
    'maint_image_cleanup_nothing_found' => 'Geen ongebruikte afbeeldingen gevonden, niets verwijderd!',
    'maint_send_test_email' => 'Stuur een test e-mail',
    'maint_send_test_email_desc' => 'Dit verstuurt een test e-mail naar het e-mailadres dat je in je profiel hebt opgegeven.',
    'maint_send_test_email_run' => 'Verzend test e-mail',
    'maint_send_test_email_success' => 'E-mail verzonden naar :address',
    'maint_send_test_email_mail_subject' => 'Test E-mail',
    'maint_send_test_email_mail_greeting' => 'E-mailbezorging lijkt te werken!',
    'maint_send_test_email_mail_text' => 'Gefeliciteerd! Nu je deze e-mailmelding hebt ontvangen, lijken je e-mailinstellingen correct te zijn geconfigureerd.',
    'maint_recycle_bin_desc' => 'Verwijderde boekenplanken, boeken, hoofdstukken en pagina\'s worden naar de prullenbak gestuurd waar ze hersteld of definitief verwijderd kunnen worden. Oudere items in de prullenbak kunnen automatisch worden verwijderd, afhankelijk van de systeemconfiguratie.',
    'maint_recycle_bin_open' => 'Prullenbak openen',
    'maint_regen_references' => 'Regenerate References',
    'maint_regen_references_desc' => 'This action will rebuild the cross-item reference index within the database. This is usually handled automatically but this action can be useful to index old content or content added via unofficial methods.',
    'maint_regen_references_success' => 'Reference index has been regenerated!',
    'maint_timeout_command_note' => 'Note: This action can take time to run, which can lead to timeout issues in some web environments. As an alternative, this action be performed using a terminal command.',

    // Recycle Bin
    'recycle_bin' => 'Prullenbak',
    'recycle_bin_desc' => 'Hier kunt u items herstellen die zijn verwijderd of ervoor kiezen om ze permanent uit het systeem te verwijderen. Deze lijst is ongefilterd, in tegenstelling tot vergelijkbare activiteitenlijsten in het systeem waar machtigingenfilters worden toegepast.',
    'recycle_bin_deleted_item' => 'Verwijderde Item',
    'recycle_bin_deleted_parent' => 'Bovenliggende',
    'recycle_bin_deleted_by' => 'Verwijderd door',
    'recycle_bin_deleted_at' => 'Verwijderd op',
    'recycle_bin_permanently_delete' => 'Permanent verwijderen',
    'recycle_bin_restore' => 'Herstellen',
    'recycle_bin_contents_empty' => 'De prullenbak is momenteel leeg',
    'recycle_bin_empty' => 'Prullenbak legen',
    'recycle_bin_empty_confirm' => 'Dit zal permanent alle items in de prullenbak vernietigen, inclusief de inhoud die in elk item zit. Weet u zeker dat u de prullenbak wilt legen?',
    'recycle_bin_destroy_confirm' => 'Deze actie zal dit item permanent verwijderen, samen met alle onderliggende elementen hieronder vanuit het systeem en u kunt deze inhoud niet herstellen. Weet u zeker dat u dit item permanent wilt verwijderen?',
    'recycle_bin_destroy_list' => 'Te vernietigen items',
    'recycle_bin_restore_list' => 'Items te herstellen',
    'recycle_bin_restore_confirm' => 'Deze actie herstelt het verwijderde item, inclusief alle onderliggende elementen, op hun oorspronkelijke locatie. Als de oorspronkelijke locatie sindsdien is verwijderd en zich nu in de prullenbak bevindt, zal ook het bovenliggende item moeten worden hersteld.',
    'recycle_bin_restore_deleted_parent' => 'De bovenliggende map van dit item is ook verwijderd. Dit zal verwijderd blijven tot het bovenliggende ook hersteld is.',
    'recycle_bin_restore_parent' => 'Herstel bovenliggende',
    'recycle_bin_destroy_notification' => ':count items uit de prullenbak verwijderd.',
    'recycle_bin_restore_notification' => ':count items uit de prullenbak hersteld.',

    // Audit Log
    'audit' => 'Controlelogboek',
    'audit_desc' => 'Dit controle logboek toont een lijst van activiteiten die in het systeem zijn bijgehouden. Deze lijst is ongefilterd in tegenstelling tot soortgelijke activiteitenlijsten in het systeem waar machtigingfilters worden toegepast.',
    'audit_event_filter' => 'Gebeurtenis filter',
    'audit_event_filter_no_filter' => 'Geen filter',
    'audit_deleted_item' => 'Verwijderd Item',
    'audit_deleted_item_name' => 'Naam: :name',
    'audit_table_user' => 'Gebruiker',
    'audit_table_event' => 'Gebeurtenis',
    'audit_table_related' => 'Gerelateerd Item of Detail',
    'audit_table_ip' => 'IP-adres',
    'audit_table_date' => 'Activiteit datum',
    'audit_date_from' => 'Datum bereik vanaf',
    'audit_date_to' => 'Datum bereik tot',

    // Role Settings
    'roles' => 'Rollen',
    'role_user_roles' => 'Gebruikersrollen',
    'role_create' => 'Nieuwe Rol Maken',
    'role_create_success' => 'Rol succesvol aangemaakt',
    'role_delete' => 'Rol Verwijderen',
    'role_delete_confirm' => 'Dit verwijdert de rol met naam: \':roleName\'.',
    'role_delete_users_assigned' => 'Er zijn :userCount gebruikers met deze rol. Selecteer hieronder een nieuwe rol als je deze gebruikers een andere rol wilt geven.',
    'role_delete_no_migration' => "Geen gebruikers migreren",
    'role_delete_sure' => 'Weet je zeker dat je deze rol wilt verwijderen?',
    'role_delete_success' => 'Rol succesvol verwijderd',
    'role_edit' => 'Rol Bewerken',
    'role_details' => 'Rol Details',
    'role_name' => 'Rolnaam',
    'role_desc' => 'Korte beschrijving van de rol',
    'role_mfa_enforced' => 'Meervoudige verificatie verreist',
    'role_external_auth_id' => 'Externe authenticatie ID\'s',
    'role_system' => 'Systeem Machtigingen',
    'role_manage_users' => 'Gebruikers beheren',
    'role_manage_roles' => 'Beheer rollen & rolmachtigingen',
    'role_manage_entity_permissions' => 'Beheer alle machtigingen voor boeken, hoofdstukken en pagina\'s',
    'role_manage_own_entity_permissions' => 'Beheer machtigingen van je eigen boek, hoofdstuk & pagina\'s',
    'role_manage_page_templates' => 'Paginasjablonen beheren',
    'role_access_api' => 'Ga naar systeem API',
    'role_manage_settings' => 'Beheer app instellingen',
    'role_export_content' => 'Exporteer inhoud',
    'role_editor_change' => 'Wijzig pagina bewerker',
    'role_asset' => 'Asset Machtigingen',
    'roles_system_warning' => 'Wees ervan bewust dat toegang tot een van de bovengenoemde drie machtigingen een gebruiker in staat kan stellen zijn eigen machtigingen of de machtigingen van anderen in het systeem kan wijzigen. Wijs alleen rollen toe met deze machtigingen aan vertrouwde gebruikers.',
    'role_asset_desc' => 'Deze machtigingen bepalen de standaard toegang tot de assets binnen het systeem. Machtigingen op boeken, hoofdstukken en pagina\'s overschrijven deze instelling.',
    'role_asset_admins' => 'Beheerders krijgen automatisch toegang tot alle inhoud, maar deze opties kunnen gebruikersinterface opties tonen of verbergen.',
    'role_all' => 'Alles',
    'role_own' => 'Eigen',
    'role_controlled_by_asset' => 'Gecontroleerd door de asset waar deze is geüpload',
    'role_save' => 'Rol Opslaan',
    'role_update_success' => 'Rol succesvol bijgewerkt',
    'role_users' => 'Gebruikers in deze rol',
    'role_users_none' => 'Geen enkele gebruiker heeft deze rol',

    // Users
    'users' => 'Gebruikers',
    'user_profile' => 'Gebruikersprofiel',
    'users_add_new' => 'Gebruiker toevoegen',
    'users_search' => 'Gebruiker zoeken',
    'users_latest_activity' => 'Laatste activiteit',
    'users_details' => 'Gebruiker details',
    'users_details_desc' => 'Stel een weergavenaam en e-mailadres in voor deze gebruiker. Het e-mailadres zal worden gebruikt om in te loggen.',
    'users_details_desc_no_email' => 'Stel een weergavenaam in voor deze gebruiker zodat anderen deze kunnen herkennen.',
    'users_role' => 'Gebruikersrollen',
    'users_role_desc' => 'Selecteer aan welke rollen deze gebruiker zal worden toegewezen. Als een gebruiker aan meerdere rollen wordt toegewezen, worden de machtigingen van die rollen samengevoegd en krijgt hij alle mogelijkheden van de toegewezen rollen.',
    'users_password' => 'Wachtwoord gebruiker',
    'users_password_desc' => 'Stel een wachtwoord in om op de applicatie in te loggen. Dit moet minstens 8 tekens lang zijn.',
    'users_send_invite_text' => 'U kunt ervoor kiezen om deze gebruiker een uitnodigingsmail te sturen waarmee hij zijn eigen wachtwoord kan instellen, anders kunt u zelf zijn wachtwoord instellen.',
    'users_send_invite_option' => 'Stuur gebruiker uitnodigings e-mail',
    'users_external_auth_id' => 'Externe authenticatie ID',
    'users_external_auth_id_desc' => 'Dit is het ID dat gebruikt wordt om deze gebruiker te vergelijken met uw externe verificatiesysteem.',
    'users_password_warning' => 'Vul onderstaande formulier alleen in als je het wachtwoord wilt aanpassen.',
    'users_system_public' => 'Deze gebruiker vertegenwoordigt alle gastgebruikers die uw applicatie bezoeken. Hij kan niet worden gebruikt om in te loggen, maar wordt automatisch toegewezen.',
    'users_delete' => 'Verwijder gebruiker',
    'users_delete_named' => 'Verwijder gebruiker :userName',
    'users_delete_warning' => 'Dit zal de gebruiker \':userName\' volledig uit het systeem verwijderen.',
    'users_delete_confirm' => 'Weet je zeker dat je deze gebruiker wilt verwijderen?',
    'users_migrate_ownership' => 'Draag eigendom over',
    'users_migrate_ownership_desc' => 'Selecteer een gebruiker hier als u wilt dat een andere gebruiker de eigenaar wordt van alle items die momenteel eigendom zijn van deze gebruiker.',
    'users_none_selected' => 'Geen gebruiker geselecteerd',
    'users_edit' => 'Bewerk Gebruiker',
    'users_edit_profile' => 'Bewerk Profiel',
    'users_avatar' => 'Avatar',
    'users_avatar_desc' => 'Selecteer een afbeelding om deze gebruiker voor te stellen. Deze moet ongeveer 256px breed en vierkant zijn.',
    'users_preferred_language' => 'Voorkeurstaal',
    'users_preferred_language_desc' => 'Deze optie wijzigt de taal die gebruikt wordt voor de gebruikersinterface. Dit heeft geen invloed op door gebruiker gemaakte inhoud.',
    'users_social_accounts' => 'Sociale media accounts',
    'users_social_accounts_info' => 'Hier kunt u uw andere accounts koppelen om sneller en eenvoudiger in te loggen. Als u hier een account loskoppelt, wordt de eerder gemachtigde toegang niet ingetrokken. U kunt de toegang intrekken via uw profielinstellingen op het gekoppelde socialemedia-account zelf.',
    'users_social_connect' => 'Account Verbinden',
    'users_social_disconnect' => 'Account Ontkoppelen',
    'users_social_connected' => ':socialAccount account is succesvol aan je profiel gekoppeld.',
    'users_social_disconnected' => ':socialAccount account is succesvol ontkoppeld van je profiel.',
    'users_api_tokens' => 'API Tokens',
    'users_api_tokens_none' => 'Er zijn geen API-tokens gemaakt voor deze gebruiker',
    'users_api_tokens_create' => 'Token aanmaken',
    'users_api_tokens_expires' => 'Verloopt',
    'users_api_tokens_docs' => 'API Documentatie',
    'users_mfa' => 'Meervoudige Verificatie',
    'users_mfa_desc' => 'Stel meervoudige verificatie in als extra beveiligingslaag voor uw gebruikersaccount.',
    'users_mfa_x_methods' => ':count methode geconfigureerd|:count methoden geconfigureerd',
    'users_mfa_configure' => 'Configureer methoden',

    // API Tokens
    'user_api_token_create' => 'API-token aanmaken',
    'user_api_token_name' => 'Naam',
    'user_api_token_name_desc' => 'Geef je token een leesbare naam als een toekomstige herinnering aan het beoogde doel.',
    'user_api_token_expiry' => 'Vervaldatum',
    'user_api_token_expiry_desc' => 'Stel een datum in waarop deze token verloopt. Na deze datum zullen aanvragen die met deze token zijn ingediend niet langer werken. Als dit veld leeg blijft, wordt een vervaldatum van 100 jaar in de toekomst ingesteld.',
    'user_api_token_create_secret_message' => 'Onmiddellijk na het aanmaken van dit token zal een "Token ID" en "Token Geheim" worden gegenereerd en weergegeven. Het geheim zal slechts één keer getoond worden. Kopieer de waarde dus eerst op een veilige plaats voordat u doorgaat.',
    'user_api_token_create_success' => 'API token succesvol aangemaakt',
    'user_api_token_update_success' => 'API token succesvol bijgewerkt',
    'user_api_token' => 'API Token',
    'user_api_token_id' => 'Token ID',
    'user_api_token_id_desc' => 'Dit is een niet-wijzigbare, door het systeem gegenereerde identificatiecode voor dit token, die in API-verzoeken moet worden verstrekt.',
    'user_api_token_secret' => 'Geheime token sleutel',
    'user_api_token_secret_desc' => 'Dit is een door het systeem gegenereerd geheim voor dit token dat in API verzoeken zal moeten worden verstrekt. Dit zal slechts één keer worden weergegeven, dus kopieer deze waarde naar een veilige plaats.',
    'user_api_token_created' => 'Token :timeAgo geleden aangemaakt',
    'user_api_token_updated' => 'Token :timeAgo geleden bijgewerkt',
    'user_api_token_delete' => 'Token Verwijderen',
    'user_api_token_delete_warning' => 'Dit zal de API-token met de naam \':tokenName\' volledig uit het systeem verwijderen.',
    'user_api_token_delete_confirm' => 'Weet u zeker dat u deze API-token wilt verwijderen?',
    'user_api_token_delete_success' => 'API-token succesvol verwijderd',

    // Webhooks
    'webhooks' => 'Webhooks',
    'webhooks_create' => 'Nieuwe Webhook Maken',
    'webhooks_none_created' => 'Er zijn nog geen webhooks aangemaakt.',
    'webhooks_edit' => 'Bewerk Webhook',
    'webhooks_save' => 'Webhook opslaan',
    'webhooks_details' => 'Webhook Details',
    'webhooks_details_desc' => 'Geef een gebruiksvriendelijke naam en een POST eindpunt op als locatie waar de webhook gegevens naartoe gestuurd zullen worden.',
    'webhooks_events' => 'Webhook gebeurtenissen',
    'webhooks_events_desc' => 'Selecteer alle gebeurtenissen die deze webhook dient te activeren.',
    'webhooks_events_warning' => 'Houd er rekening mee dat deze gebeurtenissen zullen worden geactiveerd voor alle geselecteerde gebeurtenissen, zelfs als aangepaste machtigingen zijn toegepast. Zorg ervoor dat het gebruik van deze webhook geen vertrouwelijke inhoud blootlegt.',
    'webhooks_events_all' => 'Alle systeemgebeurtenissen',
    'webhooks_name' => 'Webhook Naam',
    'webhooks_timeout' => 'Webhook Verzoek Time-out (Seconden)',
    'webhooks_endpoint' => 'Webhook Eindpunt',
    'webhooks_active' => 'Webhook Actief',
    'webhook_events_table_header' => 'Gebeurtenissen',
    'webhooks_delete' => 'Verwijder Webhook',
    'webhooks_delete_warning' => 'Dit zal de webhook met naam \':webhookName\' volledig verwijderen van het systeem.',
    'webhooks_delete_confirm' => 'Weet u zeker dat u deze webhook wil verwijderen?',
    'webhooks_format_example' => 'Voorbeeld Webhook Formaat',
    'webhooks_format_example_desc' => 'Webhook gegevens worden verzonden als een POST verzoek naar het geconfigureerde eindpunt als JSON volgens het onderstaande formaat. De "related_item" en "url" eigenschappen zijn optioneel en hangen af van het type gebeurtenis die geactiveerd wordt.',
    'webhooks_status' => 'Webhook Status',
    'webhooks_last_called' => 'Laatst Opgeroepen:',
    'webhooks_last_errored' => 'Laatst Gefaald:',
    'webhooks_last_error_message' => 'Laatste Foutmelding:',


    //! If editing translations files directly please ignore this in all
    //! languages apart from en. Content will be auto-copied from en.
    //!////////////////////////////////
    'language_select' => [
        'en' => 'Engels',
        'ar' => 'العربية (Arabisch)',
        'bg' => 'Bǎlgarski (Bulgaars)',
        'bs' => 'Bosanski (Bosnisch)',
        'ca' => 'Català (Catalaans)',
        'cs' => 'Česky (Tsjechisch)',
        'da' => 'Dansk (Deens)',
        'de' => 'Deutsch (Duits)',
        'de_informal' => 'Deutsch (Du) (Informeel Duits)',
        'es' => 'Español (Spaans)',
        'es_AR' => 'Español Argentina (Argentijns Spaans)',
        'et' => 'Eesti keel (Estisch)',
        'eu' => 'Euskara',
        'fa' => 'فارسی',
        'fr' => 'Français (Frans)',
        'he' => 'עברית (Hebreeuws)',
        'hr' => 'Hrvatski (Kroatisch)',
        'hu' => 'Magyar (Hongaars)',
        'id' => 'Bahasa Indonesia (Indonesisch)',
        'it' => 'Italiano (Italiaans)',
        'ja' => '日本語 (Japans)',
        'ko' => '한국어 (Koreaans)',
        'lt' => 'Lietuvių Kalba (Litouws)',
        'lv' => 'Latviešu Valoda (Lets)',
        'nl' => 'Nederlands',
        'nb' => 'Norsk (Bokmål) (Noors)',
        'pl' => 'Polski (Pools)',
        'pt' => 'Português (Portugees)',
        'pt_BR' => 'Português do Brasil (Braziliaans-Portugees)',
        'ru' => 'Русский (Russisch)',
        'sk' => 'Slovensky (Slowaaks)',
        'sl' => 'Slovenščina (Sloveens)',
        'sv' => 'Svenska (Zweeds)',
        'tr' => 'Türkçe (Turks)',
        'uk' => 'Українська (Oekraïens)',
        'vi' => 'Tiếng Việt (Vietnamees)',
        'zh_CN' => '简体中文 (Chinees)',
        'zh_TW' => '繁體中文 (Traditioneel Chinees)',
    ],
    //!////////////////////////////////
];
