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
    'ldap_fail_anonymous' => 'LDAP-inloggning misslyckades med anonym bindning',
    'ldap_fail_authed' => 'LDAP-inloggning misslyckades med angivna dn- och lösenordsuppgifter',
    'ldap_extension_not_installed' => 'LDAP PHP-tillägg inte installerat',
    'ldap_cannot_connect' => 'Kan inte ansluta till ldap-servern. Anslutningen misslyckades',
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
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

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
    'attachment_page_mismatch' => 'Fel i sidmatchning vid uppdatering av bilaga',
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
    'return_home' => 'Återvänd till startsidan',
    'error_occurred' => 'Ett fel inträffade',
    'app_down' => ':appName är nere just nu',
    'back_soon' => 'Vi är snart tillbaka.',

];
