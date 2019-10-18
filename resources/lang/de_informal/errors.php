<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Du hast keine Berechtigung, auf diese Seite zuzugreifen.',
    'permissionJson' => 'Du hast keine Berechtigung, die angeforderte Aktion auszuführen.',

    // Auth
    'error_user_exists_different_creds' => '',
    'email_already_confirmed' => 'Die E-Mail-Adresse ist bereits bestätigt. Bitte melde dich an.',
    'email_confirmation_invalid' => 'Der Bestätigungslink ist nicht gültig oder wurde bereits verwendet. Bitte registriere dich erneut.',
    'email_confirmation_expired' => '',
    'ldap_fail_anonymous' => '',
    'ldap_fail_authed' => '',
    'ldap_extension_not_installed' => '',
    'ldap_cannot_connect' => '',
    'social_no_action_defined' => '',
    'social_login_bad_response' => "",
    'social_account_in_use' => 'Dieses :socialAccount-Konto wird bereits verwendet. Bitte melde dich mit dem :socialAccount-Konto an.',
    'social_account_email_in_use' => 'Die E-Mail-Adresse ":email" ist bereits registriert. Wenn Du bereits registriert bist, kannst Du Dein :socialAccount-Konto in Deinen Profil-Einstellungen verknüpfen.',
    'social_account_existing' => '',
    'social_account_already_used_existing' => '',
    'social_account_not_used' => 'Dieses :socialAccount-Konto ist bisher keinem Benutzer zugeordnet. Du kannst das in Deinen Profil-Einstellungen tun.',
    'social_account_register_instructions' => 'Wenn Du bisher kein Social-Media Konto besitzt, kannst Du ein solches Konto mit der :socialAccount Option anlegen.',
    'social_driver_not_found' => '',
    'social_driver_not_configured' => '',
    'invite_token_expired' => '',

    // System
    'path_not_writable' => 'Die Datei kann nicht in den angegebenen Pfad :filePath hochgeladen werden. Stelle sicher, dass dieser Ordner auf dem Server beschreibbar ist.',
    'cannot_get_image_from_url' => '',
    'cannot_create_thumbs' => 'Der Server kann keine Vorschau-Bilder erzeugen. Bitte prüfe, ob die GD PHP-Erweiterung installiert ist.',
    'server_upload_limit' => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuche es mit einer kleineren Datei.',
    'uploaded'  => '',
    'image_upload_error' => '',
    'image_upload_type_error' => '',
    'file_upload_timeout' => '',

    // Attachments
    'attachment_page_mismatch' => '',
    'attachment_not_found' => '',

    // Pages
    'page_draft_autosave_fail' => 'Fehler beim Speichern des Entwurfs. Stelle sicher, dass Du mit dem Internet verbunden bist, bevor Du den Entwurf dieser Seite speicherst.',
    'page_custom_home_deletion' => 'Eine als Startseite gesetzte Seite kann nicht gelöscht werden.',

    // Entities
    'entity_not_found' => '',
    'bookshelf_not_found' => '',
    'book_not_found' => '',
    'page_not_found' => '',
    'chapter_not_found' => '',
    'selected_book_not_found' => '',
    'selected_book_chapter_not_found' => '',
    'guests_cannot_save_drafts' => '',

    // Users
    'users_cannot_delete_only_admin' => 'Du kannst den einzigen Administrator nicht löschen.',
    'users_cannot_delete_guest' => 'Du kannst den Gast-Benutzer nicht löschen',

    // Roles
    'role_cannot_be_edited' => '',
    'role_system_cannot_be_deleted' => '',
    'role_registration_default_cannot_delete' => '',
    'role_cannot_remove_only_admin' => '',

    // Comments
    'comment_list' => '',
    'cannot_add_comment_to_draft' => '',
    'comment_add' => '',
    'comment_delete' => '',
    'empty_comment' => '',

    // Error pages
    '404_page_not_found' => '',
    'sorry_page_not_found' => 'Entschuldigung. Die Seite, die Du angefordert hast, wurde nicht gefunden.',
    'return_home' => '',
    'error_occurred' => '',
    'app_down' => '',
    'back_soon' => '',

];
