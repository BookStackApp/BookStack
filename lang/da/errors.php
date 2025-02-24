<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Du har ikke tilladelse til at tilgå den efterspurgte side.',
    'permissionJson' => 'Du har ikke tilladelse til at udføre den valgte handling.',

    // Auth
    'error_user_exists_different_creds' => 'En bruger med email :email eksistere allerede, men med andre legitimationsoplysninger.',
    'auth_pre_register_theme_prevention' => 'Brugerkonto kunne ikke registreres for de angivne detaljer',
    'email_already_confirmed' => 'Email er allerede bekræftet. Prøv at logge ind.',
    'email_confirmation_invalid' => 'Denne bekræftelsestoken er ikke gyldig eller er allerede blevet brugt. Prøv at registrere dig igen.',
    'email_confirmation_expired' => 'Bekræftelsestoken er udløbet. En ny bekræftelsesmail er blevet sendt.',
    'email_confirmation_awaiting' => 'Mail-adressen for din konto i brug er nød til at blive bekræftet',
    'ldap_fail_anonymous' => 'LDAP-adgang fejlede ved brugen af annonym bind',
    'ldap_fail_authed' => 'LDAP adgang fejlede med de givne DN & kodeord oplysninger',
    'ldap_extension_not_installed' => 'LDAP PHP udvidelse er ikke installeret',
    'ldap_cannot_connect' => 'Kan ikke forbinde til ldap server. Indledende forbindelse mislykkedes',
    'saml_already_logged_in' => 'Allerede logget ind',
    'saml_no_email_address' => 'Kunne ikke finde en e-mail-adresse for denne bruger i de data, der leveres af det eksterne godkendelsessystem',
    'saml_invalid_response_id' => 'Anmodningen fra det eksterne godkendelsessystem genkendes ikke af en proces, der er startet af denne applikation. Navigering tilbage efter et login kan forårsage dette problem.',
    'saml_fail_authed' => 'Login ved hjælp af :system failed, systemet har ikke givet tilladelse',
    'oidc_already_logged_in' => 'Allerede logget ind',
    'oidc_no_email_address' => 'Kunne ikke finde en e-mailadresse for denne bruger i de data, der leveres af det eksterne godkendelsessystem',
    'oidc_fail_authed' => 'Login ved hjælp af :system fejlede, systemet har ikke givet tilladelse',
    'social_no_action_defined' => 'Ingen handling er defineret',
    'social_login_bad_response' => "Der opstod en fejl under :socialAccount login:\n:error",
    'social_account_in_use' => 'Denne :socialAccount konto er allerede i brug, prøv at logge ind med :socialAccount loginmetoden.',
    'social_account_email_in_use' => 'Emailen :email er allerede i brug. Hvis du allerede har en konto, kan du forbinde din :socialAccount fra dine profilindstillinger.',
    'social_account_existing' => ':socialAccount er allerede tilknyttet din profil.',
    'social_account_already_used_existing' => 'Denne :socialAccount konto er allerede i brug af en anden bruger.',
    'social_account_not_used' => 'Denne :socialAccount konto er ikke tilknyttet nogle brugere. Tilknyt den i dine profilindstillinger. ',
    'social_account_register_instructions' => 'Hvis du ikke har en konto, kan du registrere en konto gennem :socialAccount loginmetoden.',
    'social_driver_not_found' => 'Socialdriver ikke fundet',
    'social_driver_not_configured' => 'Dine :socialAccount indstillinger er ikke konfigureret korret.',
    'invite_token_expired' => 'Dette invitationslink er udløbet. I stedet kan du prøve at nulstille din kontos kodeord.',
    'login_user_not_found' => 'Der kunne ikke findes en bruger til denne handling.',

    // System
    'path_not_writable' => 'Filsti :filePath kunne ikke uploades til. Sørg for at den kan skrives til af webserveren.',
    'cannot_get_image_from_url' => 'Kan ikke finde billede på :url',
    'cannot_create_thumbs' => 'Serveren kan ikke oprette miniaturer. Kontroller, at GD PHP-udvidelsen er installeret.',
    'server_upload_limit' => 'Serveren tillader ikke uploads af denne størrelse. Prøv en mindre filstørrelse.',
    'server_post_limit' => 'Serveren kan ikke modtage den angivne mængde data. Prøv igen med færre data eller en mindre fil.',
    'uploaded'  => 'Serveren tillader ikke uploads af denne størrelse. Prøv en mindre filstørrelse.',

    // Drawing & Images
    'image_upload_error' => 'Der opstod en fejl ved upload af billedet',
    'image_upload_type_error' => 'Billedtypen, der uploades, er ugyldig',
    'image_upload_replace_type' => 'Udskiftninger af billedfiler skal være af samme type',
    'image_upload_memory_limit' => 'Kunne ikke håndtere billedupload og/eller oprette thumbnails på grund af begrænsninger i systemets ressourcer.',
    'image_thumbnail_memory_limit' => 'Det lykkedes ikke at skabe variationer i billedstørrelsen på grund af begrænsninger i systemets ressourcer.',
    'image_gallery_thumbnail_memory_limit' => 'Det lykkedes ikke at oprette galleriminiaturer på grund af begrænsede systemressourcer.',
    'drawing_data_not_found' => 'Tegningsdata kunne ikke indlæses. Tegningsfilen findes måske ikke længere, eller du har måske ikke tilladelse til at få adgang til den.',

    // Attachments
    'attachment_not_found' => 'Vedhæftning ikke fundet',
    'attachment_upload_error' => 'Der opstod en fejl ved upload af den vedhæftede fil',

    // Pages
    'page_draft_autosave_fail' => 'Kunne ikke gemme kladde. Tjek at du har internetforbindelse før du gemmer siden',
    'page_draft_delete_fail' => 'Kunne ikke slette sideudkast og hente den aktuelle sides gemte indhold',
    'page_custom_home_deletion' => 'Kan ikke slette en side der er sat som forside',

    // Entities
    'entity_not_found' => 'Emne ikke fundet',
    'bookshelf_not_found' => 'Hylde ikke fundet',
    'book_not_found' => 'Bog ikke fundet',
    'page_not_found' => 'Side ikke fundet',
    'chapter_not_found' => 'Kapitel ikke fundet',
    'selected_book_not_found' => 'Den valgte bog kunne ikke findes',
    'selected_book_chapter_not_found' => 'Den valgte bog eller kapitel kunne ikke findes',
    'guests_cannot_save_drafts' => 'Gæster kan ikke gemme kladder',

    // Users
    'users_cannot_delete_only_admin' => 'Du kan ikke slette den eneste admin',
    'users_cannot_delete_guest' => 'Du kan ikke slette gæstebrugeren',
    'users_could_not_send_invite' => 'Kunne ikke oprette en bruger, da invitationsmailen ikke blev sendt',

    // Roles
    'role_cannot_be_edited' => 'Denne rolle kan ikke redigeres',
    'role_system_cannot_be_deleted' => 'Denne rolle er en systemrolle og kan ikke slettes',
    'role_registration_default_cannot_delete' => 'Kan ikke slette rollen mens den er sat som standardrolle for registrerede brugere',
    'role_cannot_remove_only_admin' => 'Denne bruger er den eneste bruger der har administratorrollen. Tilføj en anden bruger til administratorrollen før du forsøger at slette den her.',

    // Comments
    'comment_list' => 'Der opstod en fejl under hentning af kommentarerne.',
    'cannot_add_comment_to_draft' => 'Du kan ikke kommentere på en kladde.',
    'comment_add' => 'Der opstod en fejl under tilføjelse/opdatering af kommentaren.',
    'comment_delete' => 'Der opstod en fejl under sletning af kommentaren.',
    'empty_comment' => 'Kan ikke tilføje en tom kommentar.',

    // Error pages
    '404_page_not_found' => 'Siden blev ikke fundet',
    'sorry_page_not_found' => 'Beklager, siden du leder efter blev ikke fundet.',
    'sorry_page_not_found_permission_warning' => 'Hvis du forventede, at denne side skulle eksistere, har du muligvis ikke tilladelse til at se den.',
    'image_not_found' => 'Billede ikke fundet',
    'image_not_found_subtitle' => 'Beklager, billedet du ledte efter kunne ikke findes.',
    'image_not_found_details' => 'Hvis du forventede, at dette billede skulle eksistere, kan det være blevet slettet.',
    'return_home' => 'Gå tilbage til hjem',
    'error_occurred' => 'Der opstod en fejl',
    'app_down' => ':appName er nede lige nu',
    'back_soon' => 'Den er oppe igen snart.',

    // Import
    'import_zip_cant_read' => 'Kunne ikke læse ZIP-filen.',
    'import_zip_cant_decode_data' => 'Kunne ikke finde og afkode ZIP data.json-indhold.',
    'import_zip_no_data' => 'ZIP-filens data har ikke noget forventet bog-, kapitel- eller sideindhold.',
    'import_validation_failed' => 'Import ZIP kunne ikke valideres med fejl:',
    'import_zip_failed_notification' => 'Kunne ikke importere ZIP-fil.',
    'import_perms_books' => 'Du mangler de nødvendige tilladelser til at oprette bøger.',
    'import_perms_chapters' => 'Du mangler de nødvendige tilladelser til at oprette kapitler.',
    'import_perms_pages' => 'Du mangler de nødvendige tilladelser til at oprette sider.',
    'import_perms_images' => 'Du mangler de nødvendige tilladelser til at oprette billeder.',
    'import_perms_attachments' => 'Du mangler den nødvendige tilladelse til at oprette vedhæftede filer.',

    // API errors
    'api_no_authorization_found' => 'Der blev ikke fundet nogen autorisationstoken på anmodningen',
    'api_bad_authorization_format' => 'En autorisationstoken blev fundet på anmodningen, men formatet var forkert',
    'api_user_token_not_found' => 'Der blev ikke fundet nogen matchende API-token for det angivne autorisationstoken',
    'api_incorrect_token_secret' => 'Hemmeligheden leveret til det givne anvendte API-token er forkert',
    'api_user_no_api_permission' => 'Ejeren af den brugte API token har ikke adgang til at foretage API-kald',
    'api_user_token_expired' => 'Den brugte godkendelsestoken er udløbet',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Følgende fejl opstod under afsendelse af testemail:',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL\'en stemmer ikke overens med de konfigurerede tilladte SSR-værter',
];
