<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Du har inte tillgång till den här sidan.',
    'permissionJson' => 'Du har inte rätt att utföra den här åtgärden.',

    // Auth
    'error_user_exists_different_creds' => 'En användare med adressen :email finns redan.',
    'email_already_confirmed' => 'E-posten har redan bekräftats, prova att logga in.',
    'email_confirmation_invalid' => 'Denna bekräftelsekod är inte giltig eller har redan använts. Vänligen prova att registera dig på nytt',
    'email_confirmation_expired' => 'Denna bekräftelsekod har gått ut. Vi har skickat dig en ny.',
    'email_confirmation_awaiting' => 'E-postadressen för det konto som används måste bekräftas',
    'ldap_fail_anonymous' => 'LDAP-inloggning misslyckades med anonym bindning',
    'ldap_fail_authed' => 'LDAP-inloggning misslyckades med angivna dn- och lösenordsuppgifter',
    'ldap_extension_not_installed' => 'LDAP PHP-tillägg inte installerat',
    'ldap_cannot_connect' => 'Kan inte ansluta till ldap-servern. Anslutningen misslyckades',
    'saml_already_logged_in' => 'Redan inloggad',
    'saml_user_not_registered' => 'Användarnamnet är inte registrerat och automatisk registrering är inaktiverad',
    'saml_no_email_address' => 'Kunde inte hitta en e-postadress för den här användaren i data som tillhandahålls av det externa autentiseringssystemet',
    'saml_invalid_response_id' => 'En begäran från det externa autentiseringssystemet känns inte igen av en process som startats av denna applikation. Att navigera bakåt efter en inloggning kan orsaka detta problem.',
    'saml_fail_authed' => 'Inloggning med :system misslyckades, systemet godkände inte auktoriseringen',
    'oidc_already_logged_in' => 'Already logged in',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'Ingen åtgärd definierad',
    'social_login_bad_response' => "Ett fel inträffade vid inloggning genom :socialAccount: \n:error",
    'social_account_in_use' => 'Detta konto från :socialAccount används redan. Testa att logga in med :socialAccount istället.',
    'social_account_email_in_use' => 'E-posten :email används redan. Om du redan har ett konto kan du ansluta ditt konto från :socialAccount via dina profilinställningar.',
    'social_account_existing' => 'Detta konto från :socialAccount är redan länkat till din profil.',
    'social_account_already_used_existing' => 'Detta konto från :socialAccount används redan av en annan användare.',
    'social_account_not_used' => 'Detta konto från :socialAccount är inte länkat till någon användare. Vänligen anslut via dina profilinställningar. ',
    'social_account_register_instructions' => 'Om du inte har något konto ännu kan du registerar dig genom att välja :socialAccount.',
    'social_driver_not_found' => 'Drivrutinen för den här tjänsten hittades inte',
    'social_driver_not_configured' => 'Dina inställningar för :socialAccount är inte korrekta.',
    'invite_token_expired' => 'Denna inbjudningslänk har löpt ut. Du kan istället försöka återställa ditt kontos lösenord.',

    // System
    'path_not_writable' => 'Kunde inte ladda upp till sökvägen :filePath. Kontrollera att webbservern har skrivåtkomst.',
    'cannot_get_image_from_url' => 'Kan inte hämta bild från :url',
    'cannot_create_thumbs' => 'Servern kan inte skapa miniatyrer. Kontrollera att du har PHPs GD-tillägg aktiverat.',
    'server_upload_limit' => 'Servern tillåter inte så här stora filer. Prova en mindre fil.',
    'uploaded'  => 'Servern tillåter inte så här stora filer. Prova en mindre fil.',
    'image_upload_error' => 'Ett fel inträffade vid uppladdningen',
    'image_upload_type_error' => 'Filtypen du försöker ladda upp är ogiltig',
    'file_upload_timeout' => 'Filuppladdningen har tagits ut.',

    // Attachments
    'attachment_not_found' => 'Bilagan hittades ej',

    // Pages
    'page_draft_autosave_fail' => 'Kunde inte spara utkastet. Kontrollera att du är ansluten till internet.',
    'page_custom_home_deletion' => 'Det går inte att ta bort sidan medan den används som startsida',

    // Entities
    'entity_not_found' => 'Innehållet hittades inte',
    'bookshelf_not_found' => 'Hyllan hittades inte',
    'book_not_found' => 'Boken hittades inte',
    'page_not_found' => 'Sidan hittades inte',
    'chapter_not_found' => 'Kapitlet hittades inte',
    'selected_book_not_found' => 'Den valda boken hittades inte',
    'selected_book_chapter_not_found' => 'Den valda boken eller kapitlet hittades inte',
    'guests_cannot_save_drafts' => 'Gäster kan inte spara utkast',

    // Users
    'users_cannot_delete_only_admin' => 'Du kan inte ta bort den enda admin-användaren',
    'users_cannot_delete_guest' => 'Du kan inte ta bort gästanvändaren',

    // Roles
    'role_cannot_be_edited' => 'Den här rollen kan inte redigeras',
    'role_system_cannot_be_deleted' => 'Det här är en systemroll och kan därför inte tas bort',
    'role_registration_default_cannot_delete' => 'Det går inte att ta bort rollen medan den används som standardroll.',
    'role_cannot_remove_only_admin' => 'Detta är den enda användaren med administratörsroll. Gör någon annan användare till administratör innan du tar bort denna.',

    // Comments
    'comment_list' => 'Ett fel inträffade då kommentarer skulle hämtas.',
    'cannot_add_comment_to_draft' => 'Du kan inte kommentera ett utkast.',
    'comment_add' => 'Ett fel inträffade då kommentaren skulle sparas.',
    'comment_delete' => 'Ett fel inträffade då kommentaren skulle tas bort.',
    'empty_comment' => 'Kan inte lägga till en tom kommentar.',

    // Error pages
    '404_page_not_found' => 'Sidan hittades inte',
    'sorry_page_not_found' => 'Tyvärr gick det inte att hitta sidan du söker.',
    'sorry_page_not_found_permission_warning' => 'Om du förväntade dig att denna sida skulle existera, kanske du inte har behörighet att se den.',
    'image_not_found' => 'Bilden hittades inte',
    'image_not_found_subtitle' => 'Tyvärr gick det inte att hitta bilden du letade efter.',
    'image_not_found_details' => 'Om du förväntade dig att den här bilden skulle finnas kan den ha tagits bort.',
    'return_home' => 'Återvänd till startsidan',
    'error_occurred' => 'Ett fel inträffade',
    'app_down' => ':appName är nere just nu',
    'back_soon' => 'Vi är snart tillbaka.',

    // API errors
    'api_no_authorization_found' => 'Ingen auktoriseringstoken hittades på denna begäran',
    'api_bad_authorization_format' => 'En auktoriseringstoken hittades på denna begäran men formatet verkade felaktigt',
    'api_user_token_not_found' => 'Ingen matchande API-token hittades för den angivna auktoriseringstoken',
    'api_incorrect_token_secret' => 'Hemligheten för den angivna API-token är felaktig',
    'api_user_no_api_permission' => 'Ägaren av den använda API-token har inte behörighet att göra API-anrop',
    'api_user_token_expired' => 'Den använda auktoriseringstoken har löpt ut',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Ett fel uppstod när ett test mail skulle skickas:',

];
