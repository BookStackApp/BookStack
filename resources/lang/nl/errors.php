<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Je hebt onvoldoende rechten om deze pagina te zien.',
    'permissionJson' => 'Je hebt onvoldoende rechten voor deze actie.',

    // Auth
    'error_user_exists_different_creds' => 'Een gebruiker met het e-mailadres :email bestaat al.',
    'email_already_confirmed' => 'Het e-mailadres is al bevestigd. Probeer in te loggen.',
    'email_confirmation_invalid' => 'Deze bevestigingstoken is ongeldig, Probeer opnieuw te registreren.',
    'email_confirmation_expired' => 'De bevestigingstoken is verlopen, Een nieuwe bevestigingsmail is verzonden.',
    'ldap_fail_anonymous' => 'LDAP toegang kon geen \'anonymous bind\' uitvoeren',
    'ldap_fail_authed' => 'LDAP toegang was niet mogelijk met de opgegeven dn & wachtwoord',
    'ldap_extension_not_installed' => 'LDAP PHP extension not installed',
    'ldap_cannot_connect' => 'Kon niet met de LDAP server verbinden',
    'saml_already_logged_in' => 'Already logged in',
    'saml_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'saml_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'saml_invalid_response_id' => 'The request from the external authentication system is not recognised by a process started by this application. Navigating back after a login could cause this issue.',
    'saml_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'saml_email_exists' => 'Registration unsuccessful since a user already exists with email address ":email"',
    'social_no_action_defined' => 'Geen actie gedefineerd',
    'social_login_bad_response' => "Error received during :socialAccount login: \n:error",
    'social_account_in_use' => 'Dit :socialAccount account is al in gebruik, Probeer in te loggen met de :socialAccount optie.',
    'social_account_email_in_use' => 'Het e-mailadres :email is al in gebruik. Als je al een account hebt kun je een :socialAccount account verbinden met je profielinstellingen.',
    'social_account_existing' => 'Dit :socialAccount is al gekoppeld aan een profiel.',
    'social_account_already_used_existing' => 'Dit :socialAccount account is ingebruik door een andere gebruiker.',
    'social_account_not_used' => 'Dit :socialAccount account is niet gekopeld aan een gebruiker. Koppel het via je profielinstellingen. ',
    'social_account_register_instructions' => 'Als je nog geen account hebt kun je je registreren met de :socialAccount optie.',
    'social_driver_not_found' => 'Social driver niet gevonden',
    'social_driver_not_configured' => 'Je :socialAccount instellingen zijn correct geconfigureerd.',
    'invite_token_expired' => 'This invitation link has expired. You can instead try to reset your account password.',

    // System
    'path_not_writable' => 'Bestand :filePath kon niet geupload worden. Zorg dat je schrijfrechten op de server hebt.',
    'cannot_get_image_from_url' => 'Kon geen afbeelding genereren van :url',
    'cannot_create_thumbs' => 'De server kon geen thumbnails maken. Controleer of je de GD PHP extensie geïnstalleerd hebt.',
    'server_upload_limit' => 'Het afbeeldingsformaat is te groot. Probeer een kleinere bestandsgrootte.',
    'uploaded'  => 'The server does not allow uploads of this size. Please try a smaller file size.',
    'image_upload_error' => 'Er ging iets fout bij het uploaden van de afbeelding',
    'image_upload_type_error' => 'The image type being uploaded is invalid',
    'file_upload_timeout' => 'Het uploaden van het bestand is verlopen.',

    // Attachments
    'attachment_page_mismatch' => 'Bij het bijwerken van de bijlage bleek de pagina onjuist',
    'attachment_not_found' => 'Attachment not found',

    // Pages
    'page_draft_autosave_fail' => 'Kon het concept niet opslaan. Zorg ervoor dat je een werkende internetverbinding hebt.',
    'page_custom_home_deletion' => 'Cannot delete a page while it is set as a homepage',

    // Entities
    'entity_not_found' => 'Entiteit niet gevonden',
    'bookshelf_not_found' => 'Bookshelf not found',
    'book_not_found' => 'Boek niet gevonden',
    'page_not_found' => 'Pagina niet gevonden',
    'chapter_not_found' => 'Hoofdstuk niet gevonden',
    'selected_book_not_found' => 'Het geselecteerde boek is niet gevonden',
    'selected_book_chapter_not_found' => 'Het geselecteerde boek of hoofdstuk is niet gevonden',
    'guests_cannot_save_drafts' => 'Gasten kunnen geen concepten opslaan',

    // Users
    'users_cannot_delete_only_admin' => 'Je kunt niet het enige admin account verwijderen',
    'users_cannot_delete_guest' => 'Je kunt het gastaccount niet verwijderen',

    // Roles
    'role_cannot_be_edited' => 'Deze rol kan niet bewerkt worden',
    'role_system_cannot_be_deleted' => 'Dit is een systeemrol en kan niet verwijderd worden',
    'role_registration_default_cannot_delete' => 'Deze rol kan niet verwijerd worden zolang dit de standaardrol na registratie is.',
    'role_cannot_remove_only_admin' => 'This user is the only user assigned to the administrator role. Assign the administrator role to another user before attempting to remove it here.',

    // Comments
    'comment_list' => 'Er is een fout opgetreden tijdens het ophalen van de reacties.',
    'cannot_add_comment_to_draft' => 'U kunt geen reacties toevoegen aan een concept.',
    'comment_add' => 'Er is een fout opgetreden tijdens het toevoegen van de reactie.',
    'comment_delete' => 'Er is een fout opgetreden tijdens het verwijderen van de reactie.',
    'empty_comment' => 'Kan geen lege reactie toevoegen.',

    // Error pages
    '404_page_not_found' => 'Pagina Niet Gevonden',
    'sorry_page_not_found' => 'Sorry, de pagina die je zocht is niet beschikbaar.',
    'return_home' => 'Terug naar home',
    'error_occurred' => 'Er Ging Iets Fout',
    'app_down' => ':appName is nu niet beschikbaar',
    'back_soon' => 'Komt snel weer online.',

];
