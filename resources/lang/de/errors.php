<?php

return [

    /**
     * Error text strings.
     */

    // Pages
    'permission' => 'Sie haben keine Berechtigung auf diese Seite zuzugreifen.',
    'permissionJson' => 'Sie haben keine Berechtigung die angeforderte Aktion auszuf&uuml;hren.',

    // Auth
    'error_user_exists_different_creds' => 'Ein Benutzer mit der E-Mail-Adresse :email ist bereits mit anderen Anmeldedaten angelegt.',
    'email_already_confirmed' => 'Die E-Mail-Adresse ist bereits best&auml;tigt. Bitte melden Sie sich an.',
    'email_confirmation_invalid' => 'Der Best&auml;tigungs-Token ist nicht g&uuml;ltig oder wurde bereits verwendet. Bitte registrieren Sie sich erneut.',
    'email_confirmation_expired' => 'Der Best&auml;tigungs-Token ist abgelaufen. Es wurde eine neue Best&auml;tigungs-E-Mail gesendet.',
    'ldap_fail_anonymous' => 'Anonymer LDAP Zugriff ist fehlgeschlafgen',
    'ldap_fail_authed' => 'LDAP Zugriff mit DN & Passwort ist fehlgeschlagen',
    'ldap_extension_not_installed' => 'LDAP PHP Erweiterung ist nicht installiert.',
    'ldap_cannot_connect' => 'Die Verbindung zu LDAP-Server ist fehlgeschlagen. Beim initialen Verbindungsaufbau trat ein Fehler auf.',
    'social_no_action_defined' => 'Es ist keine Aktion definiert',
    'social_account_in_use' => 'Dieses :socialAccount Konto wird bereits verwendet. Bitte melden Sie sich mit dem :socialAccount Konto an.',
    'social_account_email_in_use' => 'Die E-Mail-Adresse :email ist bereits registriert. Wenn Sie bereits registriert sind, k&ouml;nnen Sie Ihr :socialAccount Konto in Ihren Profil-Einstellungen verkn&uuml;pfen.',
    'social_account_existing' => 'Dieses :socialAccount Konto ist bereits mit Ihrem Profil verkn&uuml;pft.',
    'social_account_already_used_existing' => 'Dieses :socialAccount Konto wird bereits durch einen anderen Benutzer verwendet.',
    'social_account_not_used' => 'Dieses :socialAccount Konto ist bisher keinem Benutzer zugeordnet. Bitte verkn&uuml;pfen Sie deses in Ihrem Profil-Einstellungen.',
    'social_account_register_instructions' => 'Wenn Sie bisher keinen Social-Media Konto besitzen k&ouml;nnen Sie ein solches Konto mit der :socialAccount Option anlegen.',
    'social_driver_not_found' => 'Social-Media Konto Treiber nicht gefunden',
    'social_driver_not_configured' => 'Ihr :socialAccount Konto ist nicht korrekt konfiguriert.',

    // System
    'path_not_writable' => 'Die Datei kann nicht in den angegebenen Pfad :filePath hochgeladen werden. Stellen Sie sicher, dass dieser Ordner auf dem Server beschreibbar ist.',
    'cannot_get_image_from_url' => 'Bild konnte nicht von der URL :url geladen werden.',
    'cannot_create_thumbs' => 'Der Server kann keine Vorschau-Bilder erzeugen. Bitte pr&uuml;fen Sie, ob Sie die GD PHP Erweiterung installiert haben.',
    'server_upload_limit' => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigr&ouml;&szlig;e. Bitte versuchen Sie es mit einer kleineren Datei.',
    'image_upload_error' => 'Beim Hochladen des Bildes trat ein Fehler auf.',

    // Attachments
    'attachment_page_mismatch' => 'Die Seite stimmt nach dem Hochladen des Anhangs nicht &uuml;berein.',

    // Pages
    'page_draft_autosave_fail' => 'Fehler beim Speichern des Entwurfs. Stellen Sie sicher, dass Sie mit dem Internet verbunden sind, bevor Sie den Entwurf dieser Seite speichern.',

    // Entities
    'entity_not_found' => 'Eintrag nicht gefunden',
    'book_not_found' => 'Buch nicht gefunden',
    'page_not_found' => 'Seite nicht gefunden',
    'chapter_not_found' => 'Kapitel nicht gefunden',
    'selected_book_not_found' => 'Das gew&auml;hlte Buch wurde nicht gefunden.',
    'selected_book_chapter_not_found' => 'Das gew&auml;hlte Buch oder Kapitel wurde nicht gefunden.',
    'guests_cannot_save_drafts' => 'G&auml;ste k&ouml;nnen keine Entw&uuml;rfe speichern',

    // Users
    'users_cannot_delete_only_admin' => 'Sie k&ouml;nnen den einzigen Administrator nicht l&ouml;schen.',
    'users_cannot_delete_guest' => 'Sie k&ouml;nnen den Gast-Benutzer nicht l&ouml;schen',

    // Roles
    'role_cannot_be_edited' => 'Diese Rolle kann nicht bearbeitet werden.',
    'role_system_cannot_be_deleted' => 'Dies ist eine Systemrolle und kann nicht gel&ouml;scht werden',
    'role_registration_default_cannot_delete' => 'Diese Rolle kann nicht gel&ouml;scht werden solange sie als Standardrolle f&uuml;r neue Registrierungen gesetzt ist',

    // Error pages
    '404_page_not_found' => 'Seite nicht gefunden',
    'sorry_page_not_found' => 'Entschuldigung. Die Seite, die Sie angefordert haben wurde nicht gefunden.',
    'return_home' => 'Zur&uuml;ck zur Startseite',
    'error_occurred' => 'Es ist ein Fehler aufgetreten',
    'app_down' => ':appName befindet sich aktuell im Wartungsmodus.',
    'back_soon' => 'Wir werden so schnell wie m&ouml;glich wieder online sein.',
];
