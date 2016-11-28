<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Einstellungen',
    'settings_save' => 'Einstellungen speichern',

    'app_settings' => 'Anwendungseinstellungen',
    'app_name' => 'Anwendungsname',
    'app_name_desc' => 'Dieser Name wird im Header und E-Mails angezeigt.',
    'app_name_header' => 'Anwendungsname im Header anzeigen?',
    'app_public_viewing' => '&Ouml;ffentliche Ansicht erlauben?',
    'app_secure_images' => 'Erh&oml;hte Sicherheit f&uuml;r Bilduploads aktivieren?',
    'app_secure_images_desc' => 'Aus Leistungsgr&uuml;nden sind alle Bilder &ouml;ffentlich sichtbar. Diese Option f&uuml;gt zuf&auml;llige, schwer zu eratene, Zeichenketten vor die Bild-URLs hinzu. Stellen sie sicher, dass Verzeichnindexes deaktiviert sind, um einen einfachen Zugrif zu verhindern.',
    'app_editor' => 'Seiteneditor',
    'app_editor_desc' => 'W&auml;hlen sie den Editor aus, der von allen Benutzern genutzt werden soll, um Seiten zu editieren.',
    'app_custom_html' => 'Benutzerdefinierter HTML <head> Inhalt',
    'app_custom_html_desc' => 'Jeder Inhalt, der hier hinzugef&uuml;gt wird, wird am Ende der <head> Sektion jeder Seite eingef&uuml;gt. Diese kann praktisch sein, um CSS Styles anzupassen oder Analytics Code hinzuzuf&uuml;gen.',
    'app_logo' => 'Anwendungslogo',
    'app_logo_desc' => 'Dieses Bild sollte 43px hoch sein. <br>Gr&ouml;&szlig;ere Bilder werden verkleinert.',
    'app_primary_color' => 'Prim&auml;re Anwendungsfarbe',
    'app_primary_color_desc' => 'Dies sollte ein HEX Wert sein. <br>Leer lassen des Feldes setzt auf die Standard-Anwendungsfarbe zur&uuml;ck.',

    'reg_settings' => 'Registrierungseinstellungen',
    'reg_allow' => 'Registrierung erlauben?',
    'reg_default_role' => 'Standard-Benutzerrolle nach Registrierung',
    'reg_confirm_email' => 'Best&auml;tigung per E-Mail erforderlich?',
    'reg_confirm_email_desc' => 'Falls die Einschr&auml;nkung f&uumlr; Domains genutzt wird, ist die Best&auml;tigung per E-Mail zwingend erforderlich und der untenstehende Wert wird ignoriert.',
    'reg_confirm_restrict_domain' => 'Registrierung auf bestimmte Domains einschr&auml;nken',
    'reg_confirm_restrict_domain_desc' => 'F&uuml;gen sie eine, durch Komma getrennte, Liste von E-Mail Domains hinzu, auf die die Registrierung eingeschr&auml;nkt werden soll. Benutzern wird eine E-Mail gesendet, um ihre E-Mail Adresse zu best&auml;tigen, bevor sie diese Anwendung nutzen k&ouml;nnen. <br> Hinweis: Benutzer k&ouml;nnen ihre E-Mail Adresse nach erfolgreicher Registrierung &auml;ndern.',
    'reg_confirm_restrict_domain_placeholder' => 'Keine Einschr&auml;nkung gesetzt',

];
