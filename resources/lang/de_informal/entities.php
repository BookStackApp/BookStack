<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [
    /**
     * Shared
     */
    'no_pages_viewed' => 'Du hast bisher keine Seiten angesehen.',
    'no_pages_recently_created' => 'Du hast bisher keine Seiten angelegt.',
    'no_pages_recently_updated' => 'Du hast bisher keine Seiten aktualisiert.',
    /**
     * Permissions and restrictions
     */

    /**
     * Search
     */

    /**
     * Books
     */
    'books_delete_confirmation' => 'Bist Du sicher, dass Du dieses Buch löschen möchtest?',
    /**
     * Chapters
     */
    'chapters_delete_confirm' => 'Bist Du sicher, dass Du dieses Kapitel löschen möchtest?',
    /**
     * Pages
     */
    'pages_delete_confirm' => 'Bist Du sicher, dass Du diese Seite löschen möchtest?',
    'pages_delete_draft_confirm' => 'Bist Du sicher, dass Du diesen Seitenentwurf löschen möchtest?',
    'pages_edit_enter_changelog_desc' => 'Bitte gib eine kurze Zusammenfassung Deiner Änderungen ein',
    'pages_editing_draft_notification' => 'Du bearbeitest momenten einen Entwurf, der zuletzt :timeDiff gespeichert wurde.',
    'pages_draft_edit_active' => [
        'start_a' => ':count Benutzer bearbeiten derzeit diese Seite.',
        'start_b' => ':userName bearbeitet jetzt diese Seite.',
        'time_a' => 'seit die Seiten zuletzt aktualisiert wurden.',
        'time_b' => 'in den letzten :minCount Minuten',
        'message' => ':start :time. Achte darauf, keine Änderungen von anderen Benutzern zu überschreiben!',
    ],
    /**
     * Editor sidebar
     */
    'tags_explain' => "Füge Schlagwörter hinzu, um ihren Inhalt zu kategorisieren.\nDu kannst einen erklärenden Inhalt hinzufügen, um eine genauere Unterteilung vorzunehmen.",
    'attachments_explain' => 'Du kannst auf Deiner Seite Dateien hochladen oder Links hinzufügen. Diese werden in der Seitenleiste angezeigt.',
    'attachments_delete_confirm' => 'Klicke erneut auf löschen, um diesen Anhang zu entfernen.',
    'attachments_dropzone' => 'Ziehe Dateien hierher oder klicke hier, um eine Datei auszuwählen',
    'attachments_explain_link' => 'Wenn Du keine Datei hochladen möchtest, kannst Du stattdessen einen Link hinzufügen. Dieser Link kann auf eine andere Seite oder eine Datei im Internet verweisen.',
    'attachments_edit_drop_upload' => 'Ziehe Dateien hierher, um diese hochzuladen und zu überschreiben',
    /**
     * Profile View
     */
    
    /**
     * Comments
     */
    'comment_placeholder' => 'Gib hier Deine Kommentare ein (Markdown unterstützt)',
    'comment_delete_confirm' => 'Möchtst Du diesen Kommentar wirklich löschen?',

    /**
     * Revision
     */
    'revision_delete_confirm' => 'Bist Du sicher, dass Du diese Revision löschen möchtest?',
];

return array_replace($de_formal, $de_informal);
