<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Einstellungen',
    'settings_save' => 'Einstellungen speichern',
    'settings_save_success' => 'Einstellungen gespeichert',

    /**
     * App settings
     */

    'app_settings' => 'Anwendungseinstellungen',
    'app_name' => 'Anwendungsname',
    'app_name_desc' => 'Dieser Name wird im Header und in E-Mails angezeigt.',
    'app_name_header' => 'Anwendungsname im Header anzeigen?',
    'app_public_viewing' => 'Öffentliche Ansicht erlauben?',
    'app_secure_images' => 'Erhöhte Sicherheit für hochgeladene Bilder aktivieren?',
    'app_secure_images_desc' => 'Aus Leistungsgründen sind alle Bilder öffentlich sichtbar. Diese Option fügt zufällige, schwer zu eratene, Zeichenketten zu Bild-URLs hinzu. Stellen sie sicher, dass Verzeichnisindizes deaktiviert sind, um einen einfachen Zugriff zu verhindern.',
    'app_editor' => 'Seiteneditor',
    'app_editor_desc' => 'Wählen Sie den Editor aus, der von allen Benutzern genutzt werden soll, um Seiten zu editieren.',
    'app_custom_html' => 'Benutzerdefinierter HTML <head> Inhalt',
    'app_custom_html_desc' => 'Jeder Inhalt, der hier hinzugefügt wird, wird am Ende der <head> Sektion jeder Seite eingefügt. Diese kann praktisch sein, um CSS Styles anzupassen oder Analytics-Code hinzuzufügen.',
    'app_logo' => 'Anwendungslogo',
    'app_logo_desc' => "Dieses Bild sollte 43px hoch sein.\nGrößere Bilder werden verkleinert.",
    'app_primary_color' => 'Primäre Anwendungsfarbe',
    'app_primary_color_desc' => "Dies sollte ein HEX Wert sein.\nWenn Sie nicht eingeben, wird die Anwendung auf die Standardfarbe zurückgesetzt.",

    /**
     * Registration settings
     */

    'reg_settings' => 'Registrierungseinstellungen',
    'reg_allow' => 'Registrierung erlauben?',
    'reg_default_role' => 'Standard-Benutzerrolle nach Registrierung',
    'reg_confirm_email' => 'Bestätigung per E-Mail erforderlich?',
    'reg_confirm_email_desc' => 'Falls die Einschränkung für Domains genutzt wird, ist die Bestätigung per E-Mail zwingend erforderlich und der untenstehende Wert wird ignoriert.',
    'reg_confirm_restrict_domain' => 'Registrierung auf bestimmte Domains einschränken',
    'reg_confirm_restrict_domain_desc' => "Fügen sie eine durch Komma getrennte Liste von Domains hinzu, auf die die Registrierung eingeschränkt werden soll. Benutzern wird eine E-Mail gesendet, um ihre E-Mail Adresse zu bestätigen, bevor sie diese Anwendung nutzen können.\nHinweis: Benutzer können ihre E-Mail Adresse nach erfolgreicher Registrierung ändern.",
    'reg_confirm_restrict_domain_placeholder' => 'Keine Einschränkung gesetzt',

    /**
     * Role settings
     */

    'roles' => 'Rollen',
    'role_user_roles' => 'Benutzer-Rollen',
    'role_create' => 'Neue Rolle anlegen',
    'role_create_success' => 'Rolle erfolgreich angelegt',
    'role_delete' => 'Rolle löschen',
    'role_delete_confirm' => 'Sie möchten die Rolle ":roleName" löschen.',
    'role_delete_users_assigned' => 'Diese Rolle ist :userCount Benutzern zugeordnet. Sie können unten eine neue Rolle auswählen, die Sie diesen Benutzern zuordnen möchten.',
    'role_delete_no_migration' => "Den Benutzern keine andere Rolle zuordnen",
    'role_delete_sure' => 'Sind Sie sicher, dass Sie diese Rolle löschen möchten?',
    'role_delete_success' => 'Rolle erfolgreich gelöscht',
    'role_edit' => 'Rolle bearbeiten',
    'role_details' => 'Rollendetails',
    'role_name' => 'Rollenname',
    'role_desc' => 'Kurzbeschreibung der Rolle',
    'role_system' => 'System-Berechtigungen',
    'role_manage_users' => 'Benutzer verwalten',
    'role_manage_roles' => 'Rollen und Rollen-Berechtigungen verwalten',
    'role_manage_entity_permissions' => 'Alle Buch-, Kapitel- und Seiten-Berechtigungen verwalten',
    'role_manage_own_entity_permissions' => 'Nur Berechtigungen eigener Bücher, Kapitel und Seiten verwalten',
    'role_manage_settings' => 'Globaleinstellungen verwalten',
    'role_asset' => 'Berechtigungen',
    'role_asset_desc' => 'Diese Berechtigungen gelten für den Standard-Zugriff innerhalb des Systems. Berechtigungen für Bücher, Kapitel und Seiten überschreiben diese Berechtigungenen.',
    'role_all' => 'Alle',
    'role_own' => 'Eigene',
    'role_controlled_by_asset' => 'Berechtigungen werden vom Uploadziel bestimmt',
    'role_save' => 'Rolle speichern',
    'role_update_success' => 'Rolle erfolgreich gespeichert',
    'role_users' => 'Dieser Rolle zugeordnete Benutzer',
    'role_users_none' => 'Bisher sind dieser Rolle keine Benutzer zugeordnet',

    /**
     * Users
     */

    'users' => 'Benutzer',
    'user_profile' => 'Benutzerprofil',
    'users_add_new' => 'Benutzer hinzufügen',
    'users_search' => 'Benutzer suchen',
    'users_role' => 'Benutzerrollen',
    'users_external_auth_id' => 'Externe Authentifizierungs-ID',
    'users_password_warning' => 'Füllen Sie die folgenden Felder nur aus, wenn Sie Ihr Passwort ändern möchten:',
    'users_system_public' => 'Dieser Benutzer repräsentiert alle unangemeldeten Benutzer, die diese Seite betrachten. Er kann nicht zum Anmelden benutzt werden, sondern wird automatisch zugeordnet.',
    'users_delete' => 'Benutzer löschen',
    'users_delete_named' => 'Benutzer ":userName" löschen',
    'users_delete_warning' => 'Der Benutzer ":userName" wird aus dem System gelöscht.',
    'users_delete_confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?',
    'users_delete_success' => 'Benutzer erfolgreich gelöscht.',
    'users_books_display_type' => 'Bevorzugtes Display-Layout für Bücher',
    'users_edit' => 'Benutzer bearbeiten',
    'users_edit_profile' => 'Profil bearbeiten',
    'users_edit_success' => 'Benutzer erfolgreich aktualisisert',
    'users_avatar' => 'Benutzer-Bild',
    'users_avatar_desc' => 'Das Bild sollte eine Auflösung von 256x256px haben.',
    'users_preferred_language' => 'Bevorzugte Sprache',
    'users_social_accounts' => 'Social-Media Konten',
    'users_social_accounts_info' => 'Hier können Sie andere Social-Media-Konten für eine schnellere und einfachere Anmeldung verknüpfen. Wenn Sie ein Social-Media Konto lösen, bleibt der Zugriff erhalten. Entfernen Sie in diesem Falle die Berechtigung in Ihren Profil-Einstellungen des verknüpften Social-Media-Kontos.',
    'users_social_connect' => 'Social-Media-Konto verknüpfen',
    'users_social_disconnect' => 'Social-Media-Konto lösen',
    'users_social_connected' => ':socialAccount-Konto wurde erfolgreich mit dem Profil verknüpft.',
    'users_social_disconnected' => ':socialAccount-Konto wurde erfolgreich vom Profil gelöst.',
];
