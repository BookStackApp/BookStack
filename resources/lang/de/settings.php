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
    'app_public_viewing' => '&Ouml;ffentliche Ansicht erlauben?',
    'app_secure_images' => 'Erh&ouml;hte Sicherheit f&uuml;r Bilduploads aktivieren?',
    'app_secure_images_desc' => 'Aus Leistungsgr&uuml;nden sind alle Bilder &ouml;ffentlich sichtbar. Diese Option f&uuml;gt zuf&auml;llige, schwer zu eratene, Zeichenketten vor die Bild-URLs hinzu. Stellen sie sicher, dass Verzeichnindexes deaktiviert sind, um einen einfachen Zugriff zu verhindern.',
    'app_editor' => 'Seiteneditor',
    'app_editor_desc' => 'W&auml;hlen sie den Editor aus, der von allen Benutzern genutzt werden soll, um Seiten zu editieren.',
    'app_custom_html' => 'Benutzerdefinierter HTML <head> Inhalt',
    'app_custom_html_desc' => 'Jeder Inhalt, der hier hinzugef&uuml;gt wird, wird am Ende der <head> Sektion jeder Seite eingef&uuml;gt. Diese kann praktisch sein, um CSS Styles anzupassen oder Analytics Code hinzuzuf&uuml;gen.',
    'app_logo' => 'Anwendungslogo',
    'app_logo_desc' => 'Dieses Bild sollte 43px hoch sein. <br>Gr&ouml;&szlig;ere Bilder werden verkleinert.',
    'app_primary_color' => 'Prim&auml;re Anwendungsfarbe',
    'app_primary_color_desc' => 'Dies sollte ein HEX Wert sein. <br>Wenn Sie nicht eingeben, wird die Anwendung auf die Standardfarbe zur&uuml;ckgesetzt.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Registrierungseinstellungen',
    'reg_allow' => 'Registrierung erlauben?',
    'reg_default_role' => 'Standard-Benutzerrolle nach Registrierung',
    'reg_confirm_email' => 'Best&auml;tigung per E-Mail erforderlich?',
    'reg_confirm_email_desc' => 'Falls die Einschr&auml;nkung f&uuml;r Domains genutzt wird, ist die Best&auml;tigung per E-Mail zwingend erforderlich und der untenstehende Wert wird ignoriert.',
    'reg_confirm_restrict_domain' => 'Registrierung auf bestimmte Domains einschr&auml;nken',
    'reg_confirm_restrict_domain_desc' => 'F&uuml;gen sie eine, durch Komma getrennte, Liste von E-Mail Domains hinzu, auf die die Registrierung eingeschr&auml;nkt werden soll. Benutzern wird eine E-Mail gesendet, um ihre E-Mail Adresse zu best&auml;tigen, bevor sie diese Anwendung nutzen k&ouml;nnen. <br> Hinweis: Benutzer k&ouml;nnen ihre E-Mail Adresse nach erfolgreicher Registrierung &auml;ndern.',
    'reg_confirm_restrict_domain_placeholder' => 'Keine Einschr&auml;nkung gesetzt',

    /**
     * Role settings
     */

    'roles' => 'Rollen',
    'role_user_roles' => 'Benutzer-Rollen',
    'role_create' => 'Neue Rolle anlegen',
    'role_create_success' => 'Rolle erfolgreich angelegt',
    'role_delete' => 'Rolle l&ouml;schen',
    'role_delete_confirm' => 'Sie m&ouml;chten die Rolle \':roleName\' l&ouml;schen.',
    'role_delete_users_assigned' => 'Diese Rolle ist :userCount Benutzern zugeordnet. Sie k&ouml;nnen unten eine neue Rolle ausw&auml;hlen, die Sie diesen Benutzern zuordnen m&ouml;chten.',
    'role_delete_no_migration' => "Den Benutzern keine andere Rolle zuordnen",
    'role_delete_sure' => 'Sind Sie sicher, dass Sie diese Rolle l&ouml;schen m&ouml;chten?',
    'role_delete_success' => 'Rolle erfolgreich gel&ouml;scht',
    'role_edit' => 'Rolle bearbeiten',
    'role_details' => 'Rollen-Details',
    'role_name' => 'Rollenname',
    'role_desc' => 'Kurzbeschreibung der Rolle',
    'role_system' => 'System-Berechtigungen',
    'role_manage_users' => 'Benutzer verwalten',
    'role_manage_roles' => 'Rollen & Rollen-Berechtigungen verwalten',
    'role_manage_entity_permissions' => 'Alle Buch-, Kapitel und Seiten-Berechtigungen verwalten',
    'role_manage_own_entity_permissions' => 'Nur Berechtigungen eigener B&uuml;cher, Kapitel und Seiten verwalten',
    'role_manage_settings' => 'Globaleinstellungen verwalrten',
    'role_asset' => 'Berechtigungen',
    'role_asset_desc' => 'Diese Berechtigungen gelten f&uuml;r den Standard-Zugriff innerhalb des Systems. Berechtigungen f&uuml;r B&uuml;cher, Kapitel und Seiten &uuml;berschreiben diese Berechtigungenen.',
    'role_all' => 'Alle',
    'role_own' => 'Eigene',
    'role_controlled_by_asset' => 'Controlled by the asset they are uploaded to',
    'role_save' => 'Rolle speichern',
    'role_update_success' => 'Rolle erfolgreich gespeichert',
    'role_users' => 'Dieser Rolle zugeordnete Benutzer',
    'role_users_none' => 'Bisher sind dieser Rolle keiner Benutzer zugeordnet,',

    /**
     * Users
     */

    'users' => 'Benutzer',
    'user_profile' => 'Benutzerprofil',
    'users_add_new' => 'Benutzer hinzuf&uuml;gen',
    'users_search' => 'Benutzer suchen',
    'users_role' => 'Benutzerrollen',
    'users_external_auth_id' => 'Externe Authentifizierungs-ID',
    'users_password_warning' => 'F&uuml;llen Sie die folgenden Felder nur aus, wenn Sie Ihr Passwort &auml;ndern m&ouml;chten:',
    'users_system_public' => 'Dieser Benutzer repr&auml;sentiert alle Gast-Benutzer, die diese Seite betrachten. Er kann nicht zum Anmelden benutzt werden, sondern wird automatisch zugeordnet.',
    'users_books_display_type' => 'Bevorzugtes Display-Layout für Bücher',
    'users_delete' => 'Benutzer l&ouml;schen',
    'users_delete_named' => 'Benutzer :userName l&ouml;schen',
    'users_delete_warning' => 'Sie m&ouml;chten den Benutzer \':userName\' g&auml;nzlich aus dem System l&ouml;schen.',
    'users_delete_confirm' => 'Sind Sie sicher, dass Sie diesen Benutzer l&ouml;schen m&ouml;chten?',
    'users_delete_success' => 'Benutzer erfolgreich gel&ouml;scht.',
    'users_edit' => 'Benutzer bearbeiten',
    'users_edit_profile' => 'Profil bearbeiten',
    'users_edit_success' => 'Benutzer erfolgreich aktualisisert',
    'users_avatar' => 'Benutzer-Bild',
    'users_avatar_desc' => 'Dieses Bild sollte einen Durchmesser von ca. 256px haben.',
    'users_preferred_language' => 'Bevorzugte Sprache',
    'users_social_accounts' => 'Social-Media Konten',
    'users_social_accounts_info' => 'Hier k&ouml;nnen Sie andere Social-Media Konten f&uuml;r eine schnellere und einfachere Anmeldung verkn&uuml;pfen. Wenn Sie ein Social-Media Konto hier l&ouml;sen, bleibt der Zugriff erhalteb. Entfernen Sie in diesem Falle die Berechtigung in Ihren Profil-Einstellungen des verkn&uuml;pften Social-Media Kontos.',
    'users_social_connect' => 'Social-Media Konto verkn&uuml;pfen',
    'users_social_disconnect' => 'Social-Media Kontoverkn&uuml;pfung l&ouml;sen',
    'users_social_connected' => ':socialAccount Konto wurde erfolgreich mit dem Profil verkn&uuml;pft.',
    'users_social_disconnected' => ':socialAccount Konto wurde erfolgreich vom Profil gel&ouml;st.',
];
