<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Sie haben keine Zugriffsberechtigung auf die angeforderte Seite.',
    'permissionJson' => 'Sie haben keine Berechtigung die angeforderte Aktion auszuführen.',

    // Auth
    'error_user_exists_different_creds' => 'Ein Benutzer mit der E-Mail-Adresse :email ist bereits mit anderen Anmeldedaten registriert.',
    'email_already_confirmed' => 'Die E-Mail-Adresse ist bereits bestätigt. Bitte melden Sie sich an.',
    'email_confirmation_invalid' => 'Der Bestätigungslink ist nicht gültig oder wurde bereits verwendet. Bitte registrieren Sie sich erneut.',
    'email_confirmation_expired' => 'Der Bestätigungslink ist abgelaufen. Es wurde eine neue Bestätigungs-E-Mail gesendet.',
    'email_confirmation_awaiting' => 'Die E-Mail-Adresse für das verwendete Konto muss bestätigt werden',
    'ldap_fail_anonymous' => 'Anonymer LDAP-Zugriff ist fehlgeschlagen',
    'ldap_fail_authed' => 'LDAP-Zugriff mit DN und Passwort ist fehlgeschlagen',
    'ldap_extension_not_installed' => 'LDAP-PHP-Erweiterung ist nicht installiert.',
    'ldap_cannot_connect' => 'Die Verbindung zum LDAP-Server ist fehlgeschlagen. Beim initialen Verbindungsaufbau trat ein Fehler auf.',
    'saml_already_logged_in' => 'Sie sind bereits angemeldet',
    'saml_user_not_registered' => 'Es ist kein Benutzer mit ID :name registriert und die automatische Registrierung ist deaktiviert',
    'saml_no_email_address' => 'Es konnte keine E-Mail-Adresse für diesen Benutzer in den vom externen Authentifizierungssystem zur Verfügung gestellten Daten gefunden werden',
    'saml_invalid_response_id' => 'Die Anfrage vom externen Authentifizierungssystem wird von einem von dieser Anwendung gestarteten Prozess nicht erkannt. Das Zurückgehen nach einer Anmeldung könnte dieses Problem verursachen.',
    'saml_fail_authed' => 'Anmeldung mit :system fehlgeschlagen, System konnte keine erfolgreiche Autorisierung bereitstellen',
    'oidc_already_logged_in' => 'Bereits angemeldet',
    'oidc_user_not_registered' => 'Der Benutzer :name ist nicht registriert und die automatische Registrierung ist deaktiviert',
    'oidc_no_email_address' => 'Es konnte keine E-Mail-Adresse für diesen Benutzer in den vom externen Authentifizierungssystem zur Verfügung gestellten Daten gefunden werden',
    'oidc_fail_authed' => 'Anmeldung mit :system fehlgeschlagen, System konnte keine erfolgreiche Autorisierung bereitstellen',
    'social_no_action_defined' => 'Es ist keine Aktion definiert',
    'social_login_bad_response' => "Fehler bei der :socialAccount-Anmeldung: \n:error",
    'social_account_in_use' => 'Dieses :socialAccount-Konto wird bereits verwendet. Bitte melden Sie sich mit dem :socialAccount-Konto an.',
    'social_account_email_in_use' => 'Die E-Mail-Adresse ":email" ist bereits registriert. Wenn Sie bereits registriert sind, können Sie Ihr :socialAccount-Konto in Ihren Profil-Einstellungen verknüpfen.',
    'social_account_existing' => 'Dieses :socialAccount-Konto ist bereits mit Ihrem Profil verknüpft.',
    'social_account_already_used_existing' => 'Dieses :socialAccount-Konto wird bereits von einem anderen Benutzer verwendet.',
    'social_account_not_used' => 'Dieses :socialAccount-Konto ist bisher keinem Benutzer zugeordnet. Sie können es in Ihren Profil-Einstellungen zuordnen. ',
    'social_account_register_instructions' => 'Wenn Sie bisher keinen Social-Media Konto besitzen, können Sie ein solches Konto mit der :socialAccount Option anlegen.',
    'social_driver_not_found' => 'Treiber für Social-Media-Konten nicht gefunden',
    'social_driver_not_configured' => 'Ihr :socialAccount-Konto ist nicht korrekt konfiguriert.',
    'invite_token_expired' => 'Dieser Einladungslink ist abgelaufen. Sie können stattdessen versuchen Ihr Passwort zurückzusetzen.',

    // System
    'path_not_writable' => 'Die Datei kann nicht in den angegebenen Pfad :filePath hochgeladen werden. Stellen Sie sicher, dass dieser Ordner auf dem Server beschreibbar ist.',
    'cannot_get_image_from_url' => 'Bild konnte nicht von der URL :url geladen werden.',
    'cannot_create_thumbs' => 'Der Server kann keine Vorschau-Bilder erzeugen. Bitte prüfen Sie, ob die GD PHP-Erweiterung installiert ist.',
    'server_upload_limit' => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuchen Sie es mit einer kleineren Datei.',
    'uploaded'  => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuchen Sie es mit einer kleineren Datei.',
    'image_upload_error' => 'Beim Hochladen des Bildes trat ein Fehler auf.',
    'image_upload_type_error' => 'Der Bildtyp der hochgeladenen Datei ist ungültig.',
    'file_upload_timeout' => 'Der Datei-Upload hat das Zeitlimit überschritten.',

    // Attachments
    'attachment_not_found' => 'Anhang konnte nicht gefunden werden.',

    // Pages
    'page_draft_autosave_fail' => 'Fehler beim Speichern des Entwurfs. Stellen Sie sicher, dass Sie mit dem Internet verbunden sind, bevor Sie den Entwurf dieser Seite speichern.',
    'page_custom_home_deletion' => 'Eine als Startseite gesetzte Seite kann nicht gelöscht werden',

    // Entities
    'entity_not_found' => 'Eintrag nicht gefunden',
    'bookshelf_not_found' => 'Regal nicht gefunden',
    'book_not_found' => 'Buch nicht gefunden',
    'page_not_found' => 'Seite nicht gefunden',
    'chapter_not_found' => 'Kapitel nicht gefunden',
    'selected_book_not_found' => 'Das gewählte Buch wurde nicht gefunden',
    'selected_book_chapter_not_found' => 'Das gewählte Buch oder Kapitel wurde nicht gefunden.',
    'guests_cannot_save_drafts' => 'Gäste können keine Entwürfe speichern',

    // Users
    'users_cannot_delete_only_admin' => 'Sie können den einzigen Administrator nicht löschen',
    'users_cannot_delete_guest' => 'Sie können den Gast-Benutzer nicht löschen',

    // Roles
    'role_cannot_be_edited' => 'Diese Rolle kann nicht bearbeitet werden',
    'role_system_cannot_be_deleted' => 'Dies ist eine Systemrolle und kann nicht gelöscht werden',
    'role_registration_default_cannot_delete' => 'Diese Rolle kann nicht gelöscht werden, solange sie als Standardrolle für neue Registrierungen gesetzt ist',
    'role_cannot_remove_only_admin' => 'Dieser Benutzer ist der einzige Benutzer, welchem die Administratorrolle zugeordnet ist. Ordnen Sie die Administratorrolle einem anderen Benutzer zu bevor Sie versuchen sie hier zu entfernen.',

    // Comments
    'comment_list' => 'Beim Abrufen der Kommentare ist ein Fehler aufgetreten.',
    'cannot_add_comment_to_draft' => 'Sie können keine Kommentare zu einem Entwurf hinzufügen.',
    'comment_add' => 'Beim Hinzufügen des Kommentars ist ein Fehler aufgetreten.',
    'comment_delete' => 'Beim Löschen des Kommentars ist ein Fehler aufgetreten.',
    'empty_comment' => 'Kann keinen leeren Kommentar hinzufügen.',

    // Error pages
    '404_page_not_found' => 'Seite nicht gefunden',
    'sorry_page_not_found' => 'Entschuldigung. Die angeforderte Seite wurde nicht gefunden.',
    'sorry_page_not_found_permission_warning' => 'Wenn Sie erwartet haben, dass diese Seite existiert, haben Sie möglicherweise nicht die Berechtigung, sie anzuzeigen.',
    'image_not_found' => 'Bild nicht gefunden',
    'image_not_found_subtitle' => 'Entschuldigung. Das angeforderte Bild wurde nicht gefunden.',
    'image_not_found_details' => 'Wenn Sie erwartet haben, dass dieses Bild existiert, könnte es gelöscht worden sein.',
    'return_home' => 'Zurück zur Startseite',
    'error_occurred' => 'Es ist ein Fehler aufgetreten',
    'app_down' => ':appName befindet sich aktuell im Wartungsmodus',
    'back_soon' => 'Wir werden so schnell wie möglich wieder online sein.',

    // API errors
    'api_no_authorization_found' => 'Kein Autorisierungstoken für die Anfrage gefunden',
    'api_bad_authorization_format' => 'Ein Autorisierungstoken wurde auf die Anfrage gefunden, aber das Format schien falsch zu sein',
    'api_user_token_not_found' => 'Es wurde kein passender API-Token für den angegebenen Autorisierungstoken gefunden',
    'api_incorrect_token_secret' => 'Das Kennwort für das angegebene API-Token ist falsch',
    'api_user_no_api_permission' => 'Der Besitzer des verwendeten API-Tokens hat keine Berechtigung für API-Aufrufe',
    'api_user_token_expired' => 'Das verwendete Autorisierungstoken ist abgelaufen',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Fehler beim Versenden einer Test E-Mail:',

];
