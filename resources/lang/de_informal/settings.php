<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [
    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */
    /**
     * App settings
     */
    'app_editor_desc' => 'Wähle den Editor aus, der von allen Benutzern genutzt werden soll, um Seiten zu editieren.',
    'app_primary_color_desc' => "Dies sollte ein HEX Wert sein.\nWenn Du nichts eingibst, wird die Anwendung auf die Standardfarbe zurückgesetzt.",
    'app_homepage_desc' => 'Wähle eine Seite als Startseite aus, die statt der Standardansicht angezeigt werden soll. Seitenberechtigungen werden für die ausgewählten Seiten ignoriert.',
    'app_homepage_books' => 'Oder wähle die Buch-Übersicht als Startseite. Das wird die Seiten-Auswahl überschreiben.',
    /**
     * Maintenance settings
     */
    'maint_image_cleanup_desc' => 'Überprüft Seiten- und Versionsinhalte auf ungenutzte und mehrfach vorhandene Bilder. Erstelle vor dem Start ein Backup Deiner Datenbank und Bilder.',
    'maint_image_cleanup_warning' => ':count eventuell unbenutze Bilder wurden gefunden. Möchtest Du diese Bilder löschen?',
    /**
     * Role settings
     */
    'role_delete_confirm' => 'Du möchtest die Rolle ":roleName" löschen.',
    'role_delete_users_assigned' => 'Diese Rolle ist :userCount Benutzern zugeordnet. Du kannst unten eine neue Rolle auswählen, die Du diesen Benutzern zuordnen möchtest.',
    'role_delete_sure' => 'Bist Du sicher, dass Du diese Rolle löschen möchtest?',
    /**
     * Users
     */
    'users_password_warning' => 'Fülle die folgenden Felder nur aus, wenn Du Dein Passwort ändern möchtest:',
    'users_delete_confirm' => 'Bist Du sicher, dass Du diesen Benutzer löschen möchtest?',
    'users_social_accounts_info' => 'Hier kannst Du andere Social-Media-Konten für eine schnellere und einfachere Anmeldung verknüpfen. Wenn Du ein Social-Media Konto löschst, bleibt der Zugriff erhalten. Entferne in diesem Falle die Berechtigung in Deinen Profil-Einstellungen des verknüpften Social-Media-Kontos.',
];

return array_replace($de_formal, $de_informal);
