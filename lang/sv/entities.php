<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nyligen skapat',
    'recently_created_pages' => 'Sidor som skapats nyligen',
    'recently_updated_pages' => 'Sidor som uppdaterats nyligen',
    'recently_created_chapters' => 'Kapitel som skapats nyligen',
    'recently_created_books' => 'Böcker som skapats nyligen',
    'recently_created_shelves' => 'Hyllor som skapats nyligen',
    'recently_update' => 'Nyligen uppdaterat',
    'recently_viewed' => 'Nyligen läst',
    'recent_activity' => 'Aktivitet',
    'create_now' => 'Skapa en nu',
    'revisions' => 'Revisioner',
    'meta_revision' => 'Revisions #:revisionCount',
    'meta_created' => 'Skapad :timeLength',
    'meta_created_name' => 'Skapad :timeLength av :user',
    'meta_updated' => 'Uppdaterad :timeLength',
    'meta_updated_name' => 'Uppdaterad :timeLength av :user',
    'meta_owned_name' => 'Ägs av :user',
    'meta_reference_page_count' => 'Referenced on :count page|Referenced on :count pages',
    'entity_select' => 'Välj enhet',
    'entity_select_lack_permission' => 'Du har inte den behörighet som krävs för att välja det här objektet',
    'images' => 'Bilder',
    'my_recent_drafts' => 'Mina nyaste utkast',
    'my_recently_viewed' => 'Mina senast visade sidor',
    'my_most_viewed_favourites' => 'Mina mest visade favoriter',
    'my_favourites' => 'Mina favoriter',
    'no_pages_viewed' => 'Du har inte visat några sidor',
    'no_pages_recently_created' => 'Inga sidor har skapats nyligen',
    'no_pages_recently_updated' => 'Inga sidor har uppdaterats nyligen',
    'export' => 'Exportera',
    'export_html' => 'Webb-fil',
    'export_pdf' => 'PDF-fil',
    'export_text' => 'Textfil',
    'export_md' => 'Markdown-fil',

    // Permissions and restrictions
    'permissions' => 'Rättigheter',
    'permissions_desc' => 'Sätt rättigheter här för att åsidosätta de standardrättigheter som tillhandahålls av användarroller.',
    'permissions_book_cascade' => 'Rättigheter som sätts på böcker kommer automatiskt att ärvas av underkapitel och sidor, såvida de inte har sina egna rättigheter definierade.',
    'permissions_chapter_cascade' => 'Rättigheter som sätts på kapitel kommer automatiskt att ärvas av underliggande sidor, såvida de inte har sina egna rättigheter definierade.',
    'permissions_save' => 'Spara rättigheter',
    'permissions_owner' => 'Ägare',
    'permissions_role_everyone_else' => 'Alla andra',
    'permissions_role_everyone_else_desc' => 'Ställ in rättigheter för alla roller som inte uttryckligen har åsidosatts.',
    'permissions_role_override' => 'Åsidosätt rättigheter för roll',
    'permissions_inherit_defaults' => 'Inherit defaults',

    // Search
    'search_results' => 'Sökresultat',
    'search_total_results_found' => ':count resultat|:count resultat',
    'search_clear' => 'Rensa sökning',
    'search_no_pages' => 'Inga sidor matchade sökningen',
    'search_for_term' => 'Sök efter :term',
    'search_more' => 'Fler resultat',
    'search_advanced' => 'Avancerad sök',
    'search_terms' => 'Söktermer',
    'search_content_type' => 'Innehållstyp',
    'search_exact_matches' => 'Exakta matchningar',
    'search_tags' => 'Taggar',
    'search_options' => 'Alternativ',
    'search_viewed_by_me' => 'Visade av mig',
    'search_not_viewed_by_me' => 'Ej visade av mig',
    'search_permissions_set' => 'Har anpassade rättigheter',
    'search_created_by_me' => 'Skapade av mig',
    'search_updated_by_me' => 'Uppdaterade av mig',
    'search_owned_by_me' => 'Ägs av mig',
    'search_date_options' => 'Datumalternativ',
    'search_updated_before' => 'Uppdaterade före',
    'search_updated_after' => 'Uppdaterade efter',
    'search_created_before' => 'Skapade före',
    'search_created_after' => 'Skapade efter',
    'search_set_date' => 'Ange datum',
    'search_update' => 'Uppdatera sökning',

    // Shelves
    'shelf' => 'Hylla',
    'shelves' => 'Hyllor',
    'x_shelves' => ':count hylla|:count hyllor',
    'shelves_empty' => 'Du har inte skapat någon hylla',
    'shelves_create' => 'Skapa ny hylla',
    'shelves_popular' => 'Populära hyllor',
    'shelves_new' => 'Nya hyllor',
    'shelves_new_action' => 'Ny hylla',
    'shelves_popular_empty' => 'De populäraste hyllorna kommer hamna här',
    'shelves_new_empty' => 'De senast skapade hyllorna kommer hamna här',
    'shelves_save' => 'Spara hylla',
    'shelves_books' => 'Böcker i denna hylla',
    'shelves_add_books' => 'Lägg till böcker till hyllan',
    'shelves_drag_books' => 'Drag böckerna nedan för att lägga till dem i den här hyllan',
    'shelves_empty_contents' => 'Denna hylla har inga böcker än',
    'shelves_edit_and_assign' => 'Redigera hyllan för att lägga till böcker',
    'shelves_edit_named' => 'Redigera hyllan :name',
    'shelves_edit' => 'Redigera hylla',
    'shelves_delete' => 'Ta bort hylla',
    'shelves_delete_named' => 'Ta bort hyllan :name',
    'shelves_delete_explain' => "Detta kommer att ta bort hyllan med namnet ':name'. Böcker i hyllan kommer inte tas bort.",
    'shelves_delete_confirmation' => 'Är du säker på att du vill ta bort den här hyllan?',
    'shelves_permissions' => 'Rättigheter för hylla',
    'shelves_permissions_updated' => 'Rättigheter för hylla uppdaterades',
    'shelves_permissions_active' => 'Rättigheter för hylla aktiverade',
    'shelves_permissions_cascade_warning' => 'Rättigheter för hyllor ärvs inte automatiskt ner till böckerna i hyllorna. Detta beror på att en bok kan finnas från flera hyllor. Rättigheter kan däremot kopieras ner till en bok i hyllan med hjälp av alternativet nedan.',
    'shelves_copy_permissions_to_books' => 'Kopiera rättigheter till böcker',
    'shelves_copy_permissions' => 'Kopiera rättigheter',
    'shelves_copy_permissions_explain' => 'Detta kommer att tillämpa rättigheterna från den här hyllan på alla böcker den innehåller. Se till att eventuella ändringar sparats innan tillämpningen genomförs.',
    'shelves_copy_permission_success' => 'Rättigheter för hyllan kopierades till :count böcker',

    // Books
    'book' => 'Bok',
    'books' => 'Böcker',
    'x_books' => ':count bok|:count böcker',
    'books_empty' => 'Inga böcker har skapats',
    'books_popular' => 'Populära böcker',
    'books_recent' => 'Nya böcker',
    'books_new' => 'Nya böcker',
    'books_new_action' => 'Ny bok',
    'books_popular_empty' => 'De mest populära böckerna kommer att visas här.',
    'books_new_empty' => 'De senaste böckerna som skapats kommer att visas här.',
    'books_create' => 'Skapa ny bok',
    'books_delete' => 'Ta bort bok',
    'books_delete_named' => 'Ta bort boken :bookName',
    'books_delete_explain' => 'Du håller på att ta bort boken \':bookName\'. Alla sidor och kapitel kommer också att tas bort.',
    'books_delete_confirmation' => 'Är du säker på att du vill ta bort boken?',
    'books_edit' => 'Redigera bok',
    'books_edit_named' => 'Redigera bok :bookName',
    'books_form_book_name' => 'Bokens namn',
    'books_save' => 'Spara bok',
    'books_permissions' => 'Rättigheter för boken',
    'books_permissions_updated' => 'Bokens rättigheter har uppdaterats',
    'books_empty_contents' => 'Det finns inga sidor eller kapitel i den här boken.',
    'books_empty_create_page' => 'Skapa en ny sida',
    'books_empty_sort_current_book' => 'Sortera aktuell bok',
    'books_empty_add_chapter' => 'Lägg till kapitel',
    'books_permissions_active' => 'Anpassade rättigheter är i bruk',
    'books_search_this' => 'Sök i boken',
    'books_navigation' => 'Navigering',
    'books_sort' => 'Sortera bokens innehåll',
    'books_sort_desc' => 'Move chapters and pages within a book to reorganise its contents. Other books can be added which allows easy moving of chapters and pages between books.',
    'books_sort_named' => 'Sortera boken :bookName',
    'books_sort_name' => 'Sortera utifrån namn',
    'books_sort_created' => 'Sortera utifrån skapelse',
    'books_sort_updated' => 'Sortera utifrån uppdatering',
    'books_sort_chapters_first' => 'Kapitel först',
    'books_sort_chapters_last' => 'Kapitel sist',
    'books_sort_show_other' => 'Visa andra böcker',
    'books_sort_save' => 'Spara ordning',
    'books_sort_show_other_desc' => 'Add other books here to include them in the sort operation, and allow easy cross-book reorganisation.',
    'books_sort_move_up' => 'Move Up',
    'books_sort_move_down' => 'Move Down',
    'books_sort_move_prev_book' => 'Move to Previous Book',
    'books_sort_move_next_book' => 'Move to Next Book',
    'books_sort_move_prev_chapter' => 'Move Into Previous Chapter',
    'books_sort_move_next_chapter' => 'Move Into Next Chapter',
    'books_sort_move_book_start' => 'Move to Start of Book',
    'books_sort_move_book_end' => 'Move to End of Book',
    'books_sort_move_before_chapter' => 'Move to Before Chapter',
    'books_sort_move_after_chapter' => 'Move to After Chapter',
    'books_copy' => 'Kopiera bok',
    'books_copy_success' => 'Boken har kopierats',

    // Chapters
    'chapter' => 'Kapitel',
    'chapters' => 'Kapitel',
    'x_chapters' => ':count kapitel|:count kapitel',
    'chapters_popular' => 'Populära kapitel',
    'chapters_new' => 'Nytt kapitel',
    'chapters_create' => 'Skapa nytt kapitel',
    'chapters_delete' => 'Radera kapitel',
    'chapters_delete_named' => 'Radera kapitlet :chapterName',
    'chapters_delete_explain' => 'Detta kommer att ta bort kapitlet med namnet \':chapterName\'. Alla sidor som finns inom detta kapitel kommer också att raderas.',
    'chapters_delete_confirm' => 'Är du säker på att du vill ta bort det här kapitlet?',
    'chapters_edit' => 'Redigera kapitel',
    'chapters_edit_named' => 'Redigera kapitel :chapterName',
    'chapters_save' => 'Spara kapitel',
    'chapters_move' => 'Flytta kapitel',
    'chapters_move_named' => 'Flytta kapitel :chapterName',
    'chapters_copy' => 'Kopiera kapitel',
    'chapters_copy_success' => 'Kapitel har kopierats',
    'chapters_permissions' => 'Rättigheter för kapitel',
    'chapters_empty' => 'Det finns inga sidor i det här kapitlet.',
    'chapters_permissions_active' => 'Anpassade rättigheter är i bruk',
    'chapters_permissions_success' => 'Rättigheterna för kapitlet har uppdaterats',
    'chapters_search_this' => 'Sök i detta kapitel',
    'chapter_sort_book' => 'Sortera bok',

    // Pages
    'page' => 'Sida',
    'pages' => 'Sidor',
    'x_pages' => ':count sida|:count sidor',
    'pages_popular' => 'Populära sidor',
    'pages_new' => 'Ny sida',
    'pages_attachments' => 'Bilagor',
    'pages_navigation' => 'Navigering',
    'pages_delete' => 'Ta bort sida',
    'pages_delete_named' => 'Ta bort sidan :pageName',
    'pages_delete_draft_named' => 'Ta bort utkastet :pageName',
    'pages_delete_draft' => 'Ta bort utkast',
    'pages_delete_success' => 'Sidan har tagits bort',
    'pages_delete_draft_success' => 'Utkastet har tagits bort',
    'pages_delete_confirm' => 'Är du säker på att du vill ta bort den här sidan?',
    'pages_delete_draft_confirm' => 'Är du säker på att du vill ta bort det här utkastet?',
    'pages_editing_named' => 'Redigerar sida :pageName',
    'pages_edit_draft_options' => 'Inställningar för utkast',
    'pages_edit_save_draft' => 'Spara utkast',
    'pages_edit_draft' => 'Redigera utkast',
    'pages_editing_draft' => 'Redigerar utkast',
    'pages_editing_page' => 'Redigerar sida',
    'pages_edit_draft_save_at' => 'Utkastet sparades ',
    'pages_edit_delete_draft' => 'Ta bort utkast',
    'pages_edit_discard_draft' => 'Ta bort utkastet',
    'pages_edit_switch_to_markdown' => 'Växla till Markdown-redigerare',
    'pages_edit_switch_to_markdown_clean' => '(Rent innehåll)',
    'pages_edit_switch_to_markdown_stable' => '(Stabilt innehåll)',
    'pages_edit_switch_to_wysiwyg' => 'Växla till WYSIWYG-redigerare',
    'pages_edit_set_changelog' => 'Beskriv dina ändringar',
    'pages_edit_enter_changelog_desc' => 'Ange en kort beskrivning av de ändringar du har gjort',
    'pages_edit_enter_changelog' => 'Ändringslogg',
    'pages_editor_switch_title' => 'Växla redigerare',
    'pages_editor_switch_are_you_sure' => 'Är du säker på att du vill ändra redigerare för denna sida?',
    'pages_editor_switch_consider_following' => 'Tänk på följande när du byter redigerare:',
    'pages_editor_switch_consideration_a' => 'När du har sparat kommer den nya redigeraren att användas vid alla framtida redigeringar, inklusive de som kanske inte själva kan ändra redigerare.',
    'pages_editor_switch_consideration_b' => 'Detta kan potentiellt leda till förlust av detaljer och syntax under vissa omständigheter.',
    'pages_editor_switch_consideration_c' => 'Osparade ändringar av taggar eller ändringsloggar kommer att gå förlorade.',
    'pages_save' => 'Spara sida',
    'pages_title' => 'Sidtitel',
    'pages_name' => 'Sidans namn',
    'pages_md_editor' => 'Redigerare',
    'pages_md_preview' => 'Förhandsvisa',
    'pages_md_insert_image' => 'Infoga bild',
    'pages_md_insert_link' => 'Infoga länk',
    'pages_md_insert_drawing' => 'Infoga teckning',
    'pages_md_show_preview' => 'Show preview',
    'pages_md_sync_scroll' => 'Sync preview scroll',
    'pages_not_in_chapter' => 'Sidan ligger inte i något kapitel',
    'pages_move' => 'Flytta sida',
    'pages_copy' => 'Kopiera sida',
    'pages_copy_desination' => 'Destination',
    'pages_copy_success' => 'Sidan har kopierats',
    'pages_permissions' => 'Rättigheter för sida',
    'pages_permissions_success' => 'Rättigheterna för sidan har uppdaterats',
    'pages_revision' => 'Revidering',
    'pages_revisions' => 'Sidrevisioner',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'Sidrevisioner för :pageName',
    'pages_revision_named' => 'Sidrevision för :pageName',
    'pages_revision_restored_from' => 'Återställd från #:id; :summary',
    'pages_revisions_created_by' => 'Skapad av',
    'pages_revisions_date' => 'Revisionsdatum',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'Revisions #:id',
    'pages_revisions_numbered_changes' => 'Revision #:id ändringar',
    'pages_revisions_editor' => 'Typ av redigerare',
    'pages_revisions_changelog' => 'Ändringslogg',
    'pages_revisions_changes' => 'Ändringar',
    'pages_revisions_current' => 'Nuvarande version',
    'pages_revisions_preview' => 'Förhandsgranska',
    'pages_revisions_restore' => 'Återställ',
    'pages_revisions_none' => 'Sidan har inga revisioner',
    'pages_copy_link' => 'Kopiera länk',
    'pages_edit_content_link' => 'Jump to section in editor',
    'pages_pointer_enter_mode' => 'Enter section select mode',
    'pages_pointer_label' => 'Page Section Options',
    'pages_pointer_permalink' => 'Page Section Permalink',
    'pages_pointer_include_tag' => 'Page Section Include Tag',
    'pages_pointer_toggle_link' => 'Permalink mode, Press to show include tag',
    'pages_pointer_toggle_include' => 'Include tag mode, Press to show permalink',
    'pages_permissions_active' => 'Anpassade rättigheter är i bruk',
    'pages_initial_revision' => 'Första publicering',
    'pages_references_update_revision' => 'Automatisk uppdatering av interna länkar',
    'pages_initial_name' => 'Ny sida',
    'pages_editing_draft_notification' => 'Du redigerar just nu ett utkast som senast sparades :timeDiff.',
    'pages_draft_edited_notification' => 'Denna sida har uppdaterats sen dess. Vi rekommenderar att du förkastar dina ändringar.',
    'pages_draft_page_changed_since_creation' => 'Denna sida har uppdaterats sedan detta utkast skapades. Det rekommenderas att du slänger detta utkast eller försäkrar att du inte skriver över några sidändringar.',
    'pages_draft_edit_active' => [
        'start_a' => ':count har börjat redigera den här sidan',
        'start_b' => ':userName har börjat redigera den här sidan',
        'time_a' => 'sedan sidan senast uppdaterades',
        'time_b' => 'under de senaste :minCount minuterna',
        'message' => ':start :time. Var försiktiga så att ni inte skriver över varandras ändringar!',
    ],
    'pages_draft_discarded' => 'Utkastet har tagits bort. Redigeringsverktyget har uppdaterats med aktuellt innehåll.',
    'pages_specific' => 'Specifik sida',
    'pages_is_template' => 'Sidmall',

    // Editor Sidebar
    'page_tags' => 'Sidtaggar',
    'chapter_tags' => 'Kapiteltaggar',
    'book_tags' => 'Boktaggar',
    'shelf_tags' => 'Hylltaggar',
    'tag' => 'Tagg',
    'tags' =>  'Taggar',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'Etikettnamn',
    'tag_value' => 'Taggvärde (Frivilligt)',
    'tags_explain' => "Lägg till taggar för att kategorisera ditt innehåll bättre. \n Du kan tilldela ett värde till en tagg för ännu bättre organisering.",
    'tags_add' => 'Lägg till ännu en tagg',
    'tags_remove' => 'Ta bort denna etikett',
    'tags_usages' => 'Totalt antal taggar',
    'tags_assigned_pages' => 'Tilldelad till sidor',
    'tags_assigned_chapters' => 'Tilldelad till kapitel',
    'tags_assigned_books' => 'Tilldelad till böcker',
    'tags_assigned_shelves' => 'Tilldelad till hyllor',
    'tags_x_unique_values' => ':count unika värden',
    'tags_all_values' => 'Alla värden',
    'tags_view_tags' => 'Visa taggar',
    'tags_view_existing_tags' => 'Visa befintliga taggar',
    'tags_list_empty_hint' => 'Taggar kan tilldelas via sideditorns sidofält eller medan du redigerar detaljerna i en bok, kapitel eller hylla.',
    'attachments' => 'Bilagor',
    'attachments_explain' => 'Ladda upp filer eller bifoga länkar till ditt innehåll. Dessa visas i sidokolumnen.',
    'attachments_explain_instant_save' => 'Ändringar här sparas omgående.',
    'attachments_upload' => 'Ladda upp fil',
    'attachments_link' => 'Bifoga länk',
    'attachments_upload_drop' => 'Alternatively you can drag and drop a file here to upload it as an attachment.',
    'attachments_set_link' => 'Ange länk',
    'attachments_delete' => 'Är du säker på att du vill ta bort bilagan?',
    'attachments_dropzone' => 'Drop files here to upload',
    'attachments_no_files' => 'Inga filer har laddats upp',
    'attachments_explain_link' => 'Du kan bifoga en länk om du inte vill ladda upp en fil. Detta kan vara en länk till en annan sida eller till en fil i molnet.',
    'attachments_link_name' => 'Länknamn',
    'attachment_link' => 'Länk till bilaga',
    'attachments_link_url' => 'Länk till fil',
    'attachments_link_url_hint' => 'URL till sida eller fil',
    'attach' => 'Bifoga',
    'attachments_insert_link' => 'Lägg till bilagelänk till sida',
    'attachments_edit_file' => 'Redigera fil',
    'attachments_edit_file_name' => 'Filnamn',
    'attachments_edit_drop_upload' => 'Släpp filer här eller klicka för att ladda upp och skriva över',
    'attachments_order_updated' => 'Ordningen på bilagorna har uppdaterats',
    'attachments_updated_success' => 'Bilagan har uppdaterats',
    'attachments_deleted' => 'Bilagan har tagits bort',
    'attachments_file_uploaded' => 'Filen har laddats upp',
    'attachments_file_updated' => 'Filen har uppdaterats',
    'attachments_link_attached' => 'Länken har bifogats till sidan',
    'templates' => 'Mallar',
    'templates_set_as_template' => 'Sidan är en mall',
    'templates_explain_set_as_template' => 'Du kan använda denna sida som en mall så att dess innehåll kan användas när du skapar andra sidor. Andra användare kommer att kunna använda denna mall om de har visningsrättigheter för den här sidan.',
    'templates_replace_content' => 'Ersätt sidinnehåll',
    'templates_append_content' => 'Lägg till till sidans innehåll',
    'templates_prepend_content' => 'Lägg till före sidans innehåll',

    // Profile View
    'profile_user_for_x' => 'Användare i :time',
    'profile_created_content' => 'Skapat innehåll',
    'profile_not_created_pages' => ':userName har inte skapat några sidor',
    'profile_not_created_chapters' => ':userName har inte skapat några kapitel',
    'profile_not_created_books' => ':userName har inte skapat några böcker',
    'profile_not_created_shelves' => ':userName har inte skapat några hyllor',

    // Comments
    'comment' => 'Kommentar',
    'comments' => 'Kommentarer',
    'comment_add' => 'Lägg till kommentar',
    'comment_placeholder' => 'Lämna en kommentar här',
    'comment_count' => '{0} Inga kommentarer|{1} 1 kommentar|[2,*] :count kommentarer',
    'comment_save' => 'Spara kommentar',
    'comment_new' => 'Ny kommentar',
    'comment_created' => 'kommenterade :createDiff',
    'comment_updated' => 'Uppdaterade :updateDiff av :username',
    'comment_updated_indicator' => 'Updated',
    'comment_deleted_success' => 'Kommentar borttagen',
    'comment_created_success' => 'Kommentaren har sparats',
    'comment_updated_success' => 'Kommentaren har uppdaterats',
    'comment_delete_confirm' => 'Är du säker på att du vill ta bort den här kommentaren?',
    'comment_in_reply_to' => 'Som svar på :commentId',

    // Revision
    'revision_delete_confirm' => 'Är du säker på att du vill radera den här versionen?',
    'revision_restore_confirm' => 'Är du säker på att du vill använda denna revision? Det nuvarande innehållet kommer att ersättas.',
    'revision_cannot_delete_latest' => 'Det går inte att ta bort den senaste versionen.',

    // Copy view
    'copy_consider' => 'Tänk på nedan när du kopierar innehåll.',
    'copy_consider_permissions' => 'Anpassade behörighetsinställningar kommer inte att kopieras.',
    'copy_consider_owner' => 'Du kommer att bli ägare till allt kopierat innehåll.',
    'copy_consider_images' => 'Bildfiler för sidan kommer inte att dupliceras och de ursprungliga bilderna kommer att behålla sin relation till den sida de ursprungligen laddades upp till.',
    'copy_consider_attachments' => 'Sidans bifogade filer kommer inte att kopieras.',
    'copy_consider_access' => 'Ändring av plats, ägare eller behörigheter kan leda till att detta innehåll blir tillgängligt för dem som tidigare inte haft åtkomst.',

    // Conversions
    'convert_to_shelf' => 'Konvertera till hylla',
    'convert_to_shelf_contents_desc' => 'Du kan konvertera denna bok till en ny hylla med samma innehåll. Kapitlen inom denna bok konverteras till nya böcker. Om denna bok innehåller sidor som inte är i ett kapitel så kommer denna bok att döpas om och innehålla dessa sidor. Denna bok blir då en del av den nya hyllan.',
    'convert_to_shelf_permissions_desc' => 'Alla behörigheter som ställs in på denna bok kommer att kopieras till den nya hyllan och till alla nya underböcker som inte har egna behörigheter applicerade. Observera att behörigheter på hyllor inte automatisk ärvs av innehåll inom hyllan, så som med böcker.',
    'convert_book' => 'Konvertera bok',
    'convert_book_confirm' => 'Är du säker på att du vill konvertera boken?',
    'convert_undo_warning' => 'Detta kan inte ångras lika lätt.',
    'convert_to_book' => 'Konvertera till bok',
    'convert_to_book_desc' => 'Du kan konvertera detta kapitel till en ny bok med samma innehåll. Eventuella behörigheter som angetts på detta kapitel kommer att kopieras till den nya boken men ärvda behörigheter från föräldraboken kommer inte att kopieras vilket kan leda till skillnader i åtkomsten.',
    'convert_chapter' => 'Konvertera kapitel',
    'convert_chapter_confirm' => 'Är du säker på att du vill konvertera det här kapitlet?',

    // References
    'references' => 'Referenser',
    'references_none' => 'Det finns inga referenser kopplade till detta objekt.',
    'references_to_desc' => 'Nedan visas alla kända sidor i systemet som länkar till detta objekt.',
];
