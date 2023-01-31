<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Du hast keine Berechtigung, auf diese Seite zuzugreifen.',
    'permissionJson' => 'Du hast keine Berechtigung, die angeforderte Aktion auszuführen.',

    // Auth
    'error_user_exists_different_creds' => 'Ein Benutzer mit der E-Mail-Adresse :email ist bereits mit anderen Anmeldedaten registriert.',
    'email_already_confirmed' => 'Die E-Mail-Adresse ist bereits bestätigt. Bitte melde dich an.',
    'email_confirmation_invalid' => 'Der Bestätigungslink ist nicht gültig oder wurde bereits verwendet. Bitte registriere dich erneut.',
    'email_confirmation_expired' => 'Der Bestätigungslink ist abgelaufen. Es wurde eine neue Bestätigungs-E-Mail gesendet.',
    'email_confirmation_awaiting' => 'Die E-Mail-Adresse für das verwendete Konto muss bestätigt werden',
    'ldap_fail_anonymous' => 'Anonymer LDAP-Zugriff ist fehlgeschlagen',
    'ldap_fail_authed' => 'LDAP-Zugriff mit DN und Passwort ist fehlgeschlagen',
    'ldap_extension_not_installed' => 'LDAP-PHP-Erweiterung ist nicht installiert',
    'ldap_cannot_connect' => 'Die Verbindung zum LDAP-Server ist fehlgeschlagen. Beim initialen Verbindungsaufbau trat ein Fehler auf',
    'saml_already_logged_in' => 'Du bist bereits angemeldet',
    'saml_user_not_registered' => 'Kein Benutzer mit ID :name registriert und die automatische Registrierung ist deaktiviert',
    'saml_no_email_address' => 'Es konnte keine E-Mail-Adresse für diesen Benutzer in den vom externen Authentifizierungssystem zur Verfügung gestellten Daten gefunden werden',
    'saml_invalid_response_id' => 'Die Anfrage vom externen Authentifizierungssystem wird von einem von dieser Anwendung gestarteten Prozess nicht erkannt. Das Zurückblättern nach einem Login könnte dieses Problem verursachen.',
    'saml_fail_authed' => 'Anmeldung mit :system fehlgeschlagen, System konnte keine erfolgreiche Autorisierung bereitstellen',
    'oidc_already_logged_in' => 'Bereits angemeldet',
    'oidc_user_not_registered' => 'Der Benutzer :name ist nicht registriert und die automatische Registrierung ist deaktiviert',
    'oidc_no_email_address' => 'Es konnte keine E-Mail-Adresse für diesen Benutzer in den vom externen Authentifizierungssystem bereitgestellten Daten gefunden werden',
    'oidc_fail_authed' => 'Anmeldung mit :system fehlgeschlagen, System hat keine erfolgreiche Autorisierung geliefert',
    'social_no_action_defined' => 'Es ist keine Aktion definiert',
    'social_login_bad_response' => "Fehler bei :socialAccount Login: \n:error",
    'social_account_in_use' => 'Dieses :socialAccount-Konto wird bereits verwendet. Bitte melde dich mit dem :socialAccount-Konto an.',
    'social_account_email_in_use' => 'Die E-Mail-Adresse ":email" ist bereits registriert. Wenn du bereits registriert bist, kannst du Dein :socialAccount-Konto in Deinen Profil-Einstellungen verknüpfen.',
    'social_account_existing' => 'Dieses :socialAccount-Konto ist bereits mit deinem Profil verknüpft.',
    'social_account_already_used_existing' => 'Dieses :socialAccount-Konto wird bereits von einem anderen Benutzer verwendet.',
    'social_account_not_used' => 'Dieses :socialAccount-Konto ist bisher keinem Benutzer zugeordnet. Du kannst das in deinen Profil-Einstellungen tun.',
    'social_account_register_instructions' => 'Wenn du bisher kein Social-Media Konto besitzt, kannst du ein solches Konto mit der :socialAccount Option anlegen.',
    'social_driver_not_found' => 'Treiber für Social-Media-Konten nicht gefunden',
    'social_driver_not_configured' => 'Dein :socialAccount-Konto ist nicht korrekt konfiguriert.',
    'invite_token_expired' => 'Dieser Einladungslink ist abgelaufen. Du kannst stattdessen versuchen, dein Passwort zurückzusetzen.',

    // System
    'path_not_writable' => 'Die Datei kann nicht in den angegebenen Pfad :filePath hochgeladen werden. Stelle sicher, dass dieser Ordner auf dem Server beschreibbar ist.',
    'cannot_get_image_from_url' => 'Bild konnte nicht von der URL :url geladen werden.',
    'cannot_create_thumbs' => 'Der Server kann keine Vorschau-Bilder erzeugen. Bitte prüfe, ob die GD PHP-Erweiterung installiert ist.',
    'server_upload_limit' => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuche es mit einer kleineren Datei.',
    'uploaded'  => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuche es mit einer kleineren Datei.',
    'file_upload_timeout' => 'Der Upload der Datei ist abgelaufen.',

    // Drawing & Images
    'image_upload_error' => 'Beim Hochladen des Bildes trat ein Fehler auf.',
    'image_upload_type_error' => 'Der Bildtyp der hochgeladenen Datei ist ungültig.',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Anhang konnte nicht gefunden werden.',

    // Pages
    'page_draft_autosave_fail' => 'Fehler beim Speichern des Entwurfs. Stelle sicher, dass du mit dem Internet verbunden bist, bevor du den Entwurf dieser Seite speicherst.',
    'page_custom_home_deletion' => 'Eine als Startseite gesetzte Seite kann nicht gelöscht werden.',

    // Entities
    'entity_not_found' => 'Eintrag nicht gefunden',
    'bookshelf_not_found' => 'Regal nicht gefunden',
    'book_not_found' => 'Buch nicht gefunden',
    'page_not_found' => 'Seite nicht gefunden',
    'chapter_not_found' => 'Kapitel nicht gefunden',
    'selected_book_not_found' => 'Das gewählte Buch wurde nicht gefunden.',
    'selected_book_chapter_not_found' => 'Das gewählte Buch oder Kapitel wurde nicht gefunden.',
    'guests_cannot_save_drafts' => 'Gäste können keine Entwürfe speichern',

    // Users
    'users_cannot_delete_only_admin' => 'Du kannst den einzigen Administrator nicht löschen.',
    'users_cannot_delete_guest' => 'Du kannst den Gast-Benutzer nicht löschen',

    // Roles
    'role_cannot_be_edited' => 'Diese Rolle kann nicht bearbeitet werden.',
    'role_system_cannot_be_deleted' => 'Dies ist eine Systemrolle und kann nicht gelöscht werden',
    'role_registration_default_cannot_delete' => 'Diese Rolle kann nicht gelöscht werden, solange sie als Standardrolle für neue Registrierungen gesetzt ist',
    'role_cannot_remove_only_admin' => 'Dieser Benutzer ist der einzige Benutzer, welchem die Administratorrolle zugeordnet ist. Ordne die Administratorrolle einem anderen Benutzer zu, bevor du versuchst, sie hier zu entfernen.',

    // Comments
    'comment_list' => 'Beim Abrufen der Kommentare ist ein Fehler aufgetreten.',
    'cannot_add_comment_to_draft' => 'Du kannst keine Kommentare zu einem Entwurf hinzufügen.',
    'comment_add' => 'Beim Hinzufügen des Kommentars ist ein Fehler aufgetreten.',
    'comment_delete' => 'Beim Löschen des Kommentars ist ein Fehler aufgetreten.',
    'empty_comment' => 'Kann keinen leeren Kommentar hinzufügen',

    // Error pages
    '404_page_not_found' => 'Seite nicht gefunden',
    'sorry_page_not_found' => 'Entschuldigung. Die Seite, die du angefordert hast, wurde nicht gefunden.',
    'sorry_page_not_found_permission_warning' => 'Wenn du erwartet hast, dass diese Seite existiert, hast du möglicherweise nicht die Berechtigung, sie anzuzeigen.',
    'image_not_found' => 'Bild nicht gefunden',
    'image_not_found_subtitle' => 'Sorry. Die Bilddatei, nach der du suchst, konnte nicht gefunden werden.',
    'image_not_found_details' => 'Wenn du erwartet hast, dass dieses Bild existiert, wurde es möglicherweise gelöscht.',
    'return_home' => 'Zurück zur Startseite',
    'error_occurred' => 'Es ist ein Fehler aufgetreten',
    'app_down' => ':appName befindet sich aktuell im Wartungsmodus.',
    'back_soon' => 'Wir werden so schnell wie möglich wieder online sein.',

    // API errors
    'api_no_authorization_found' => 'Kein Autorisierungs-Token für die Anfrage gefunden',
    'api_bad_authorization_format' => 'Ein Autorisierungs-Token wurde auf die Anfrage gefunden, aber das Format schien falsch zu sein',
    'api_user_token_not_found' => 'Es wurde kein passender API-Token für den angegebenen Autorisierungs-Token gefunden',
    'api_incorrect_token_secret' => 'Das für den API-Token angegebene geheime Token ist falsch',
    'api_user_no_api_permission' => 'Der Besitzer des verwendeten API-Token hat keine Berechtigung für API-Aufrufe',
    'api_user_token_expired' => 'Das verwendete Autorisierungs-Token ist abgelaufen',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Fehler beim Senden einer Test E-Mail:',

];
