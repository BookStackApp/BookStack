<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [
    // Pages
    'permission' => 'Du hast keine Berechtigung, auf diese Seite zuzugreifen.',
    'permissionJson' => 'Du hast keine Berechtigung, die angeforderte Aktion auszuführen.',
    // Auth
    'email_already_confirmed' => 'Die E-Mail-Adresse ist bereits bestätigt. Bitte melde dich an.',
    'email_confirmation_invalid' => 'Der Bestätigungslink ist nicht gültig oder wurde bereits verwendet. Bitte registriere dich erneut.',
    'social_account_in_use' => 'Dieses :socialAccount-Konto wird bereits verwendet. Bitte melde dich mit dem :socialAccount-Konto an.',
    'social_account_email_in_use' => 'Die E-Mail-Adresse ":email" ist bereits registriert. Wenn Du bereits registriert bist, kannst Du Dein :socialAccount-Konto in Deinen Profil-Einstellungen verknüpfen.',
    'social_account_not_used' => 'Dieses :socialAccount-Konto ist bisher keinem Benutzer zugeordnet. Du kannst das in Deinen Profil-Einstellungen tun.',
    'social_account_register_instructions' => 'Wenn Du bisher kein Social-Media Konto besitzt, kannst Du ein solches Konto mit der :socialAccount Option anlegen.',
    // System
    'path_not_writable' => 'Die Datei kann nicht in den angegebenen Pfad :filePath hochgeladen werden. Stelle sicher, dass dieser Ordner auf dem Server beschreibbar ist.',
    'cannot_create_thumbs' => 'Der Server kann keine Vorschau-Bilder erzeugen. Bitte prüfe, ob die GD PHP-Erweiterung installiert ist.',
    'server_upload_limit' => 'Der Server verbietet das Hochladen von Dateien mit dieser Dateigröße. Bitte versuche es mit einer kleineren Datei.',
    // Pages
    'page_draft_autosave_fail' => 'Fehler beim Speichern des Entwurfs. Stelle sicher, dass Du mit dem Internet verbunden bist, bevor Du den Entwurf dieser Seite speicherst.',
    'page_custom_home_deletion' => 'Eine als Startseite gesetzte Seite kann nicht gelöscht werden.',
    // Users
    'users_cannot_delete_only_admin' => 'Du kannst den einzigen Administrator nicht löschen.',
    'users_cannot_delete_guest' => 'Du kannst den Gast-Benutzer nicht löschen',
    // Error pages
    'sorry_page_not_found' => 'Entschuldigung. Die Seite, die Du angefordert hast, wurde nicht gefunden.',
];

return array_replace($de_formal, $de_informal);
