<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nylig oppretta',
    'recently_created_pages' => 'Nyleg oppretta sider',
    'recently_updated_pages' => 'Nyleg oppdaterte sider',
    'recently_created_chapters' => 'Nyleg oppretta kapitler',
    'recently_created_books' => 'Nyleg oppretta bøker',
    'recently_created_shelves' => 'Nyleg oppretta bokhyller',
    'recently_update' => 'Nyleg oppdatert',
    'recently_viewed' => 'Nyleg vist',
    'recent_activity' => 'Nyleg aktivitet',
    'create_now' => 'Opprett ein no',
    'revisions' => 'Revisjonar',
    'meta_revision' => 'Revisjon #:revisionCount',
    'meta_created' => 'Oppretta :timeLength',
    'meta_created_name' => 'Oppretta :timeLength av :user',
    'meta_updated' => 'Oppdatert :timeLength',
    'meta_updated_name' => 'Oppdatert :timeLength av :user',
    'meta_owned_name' => 'Eigd av :user',
    'meta_reference_page_count' => 'Sitert på :count side|Sitert på :count sider',
    'entity_select' => 'Velg entitet',
    'entity_select_lack_permission' => 'Du har ikkje tilgang til å velge dette elementet',
    'images' => 'Bilete',
    'my_recent_drafts' => 'Mine nylege utkast',
    'my_recently_viewed' => 'Mine nylege visingar',
    'my_most_viewed_favourites' => 'Mine mest sette favorittar',
    'my_favourites' => 'Mine favorittar',
    'no_pages_viewed' => 'Du har ikkje sett på nokre sider',
    'no_pages_recently_created' => 'Ingen sider har nylig blitt oppretta',
    'no_pages_recently_updated' => 'Ingen sider har nylig blitt oppdatert',
    'export' => 'Eksporter',
    'export_html' => 'Nettside med alt',
    'export_pdf' => 'PDF-fil',
    'export_text' => 'Tekstfil',
    'export_md' => 'Markdownfil',

    // Permissions and restrictions
    'permissions' => 'Tilgongar',
    'permissions_desc' => 'Endringar gjort her vil overstyra standardrettar gitt via brukarroller.',
    'permissions_book_cascade' => 'Rettar sett på bøker vil automatisk arvast ned til sidenivå. Du kan overstyra arv ved å definera eigne rettar på kapittel eller sider.',
    'permissions_chapter_cascade' => 'Rettar sett på kapittel vil automatisk arvast ned til sider. Du kan overstyra arv ved å definera rettar på enkeltsider.',
    'permissions_save' => 'Lagre løyver',
    'permissions_owner' => 'Eigar',
    'permissions_role_everyone_else' => 'Alle andre',
    'permissions_role_everyone_else_desc' => 'Angi rettar for alle roller som ikkje blir overstyrt (arva rettar).',
    'permissions_role_override' => 'Overstyr rettar for rolle',
    'permissions_inherit_defaults' => 'Arv standardrettar',

    // Search
    'search_results' => 'Søkeresultat',
    'search_total_results_found' => ':count resultat funne|:count totalt',
    'search_clear' => 'Nullstill søk',
    'search_no_pages' => 'Ingen sider passar med søket',
    'search_for_term' => 'Søk etter :term',
    'search_more' => 'Fleire resultat',
    'search_advanced' => 'Avansert søk',
    'search_terms' => 'Søkeord',
    'search_content_type' => 'Innhaldstype',
    'search_exact_matches' => 'Eksakte ord',
    'search_tags' => 'Søk på merker',
    'search_options' => 'Alternativ',
    'search_viewed_by_me' => 'Sett av meg',
    'search_not_viewed_by_me' => 'Ikkje sett av meg',
    'search_permissions_set' => 'Tilgongar er sett',
    'search_created_by_me' => 'Oppretta av meg',
    'search_updated_by_me' => 'Oppdatert av meg',
    'search_owned_by_me' => 'Eigd av meg',
    'search_date_options' => 'Datoalternativ',
    'search_updated_before' => 'Oppdatert før',
    'search_updated_after' => 'Oppdatert etter',
    'search_created_before' => 'Oppretta før',
    'search_created_after' => 'Oppretta etter',
    'search_set_date' => 'Angi dato',
    'search_update' => 'Oppdater søk',

    // Shelves
    'shelf' => 'Hylle',
    'shelves' => 'Hyller',
    'x_shelves' => ':count hylle|:count hyller',
    'shelves_empty' => 'Ingen bokhyller er oppretta',
    'shelves_create' => 'Opprett ny bokhylle',
    'shelves_popular' => 'Populære bokhyller',
    'shelves_new' => 'Nye bokhyller',
    'shelves_new_action' => 'Ny bokhylle',
    'shelves_popular_empty' => 'Dei mest populære bokhyllene blir vist her.',
    'shelves_new_empty' => 'Nylig opprettede bokhyller vises her.',
    'shelves_save' => 'Lagre hylle',
    'shelves_books' => 'Bøker på denne hylla',
    'shelves_add_books' => 'Legg til bøker på denne hyllen',
    'shelves_drag_books' => 'Dra og slepp bøker nedanfor for å legge dei til i denne hylla',
    'shelves_empty_contents' => 'Ingen bøker er stabla i denne hylla',
    'shelves_edit_and_assign' => 'Endre hylla for å legge til bøker',
    'shelves_edit_named' => 'Rediger :name (hylle)',
    'shelves_edit' => 'Rediger hylle',
    'shelves_delete' => 'Fjern hylle',
    'shelves_delete_named' => 'Fjern :name (hylle)',
    'shelves_delete_explain' => "Dette vil fjerne hylla «:name». Bøkene på hylla vil ikkje bli sletta frå systemet.",
    'shelves_delete_confirmation' => 'Er du sikker på at du vil fjerne denne hylla?',
    'shelves_permissions' => 'Hylletilgangar',
    'shelves_permissions_updated' => 'Oppdaterte hylletilgangar',
    'shelves_permissions_active' => 'Aktiverte hylletilgangar',
    'shelves_permissions_cascade_warning' => 'Tilgangar på ei hylle vert ikkje automatisk arva av bøker på hylla. Dette er fordi ei bok kan finnast på fleire hyller samstundes. Tilgangar kan likevel verte kopiert til bøker på hylla ved å bruke alternativa under.',
    'shelves_permissions_create' => 'Bokhylle-tilgangar vert brukt for kopiering av løyver til under-bøker ved hjelp av handlinga nedanfor. Dei kontrollerer ikkje rettane til å lage bøker.',
    'shelves_copy_permissions_to_books' => 'Kopier tilgangar til bøkene på hylla',
    'shelves_copy_permissions' => 'Kopier tilgangar',
    'shelves_copy_permissions_explain' => 'Dette vil kopiere tilgangar på denne hylla til alle bøkene som er plassert på den. Før du starter kopieringen bør du sjekke at tilgangane på hylla er lagra.',
    'shelves_copy_permission_success' => 'Rettighetene ble kopiert til :count bøker',

    // Books
    'book' => 'Bok',
    'books' => 'Bøker',
    'x_books' => ':count bok|:count bøker',
    'books_empty' => 'Ingen bøker er skrevet',
    'books_popular' => 'Populære bøker',
    'books_recent' => 'Nylige bøker',
    'books_new' => 'Nye bøker',
    'books_new_action' => 'Ny bok',
    'books_popular_empty' => 'De mest populære bøkene',
    'books_new_empty' => 'Siste utgivelser vises her.',
    'books_create' => 'Skriv ny bok',
    'books_delete' => 'Brenn bok',
    'books_delete_named' => 'Brenn boken :bookName',
    'books_delete_explain' => 'Dette vil brenne boken «:bookName». Alle sider i boken vil fordufte for godt.',
    'books_delete_confirmation' => 'Er du sikker på at du vil brenne boken?',
    'books_edit' => 'Endre bok',
    'books_edit_named' => 'Endre boken :bookName',
    'books_form_book_name' => 'Boktittel',
    'books_save' => 'Lagre bok',
    'books_permissions' => 'Boktilganger',
    'books_permissions_updated' => 'Boktilganger oppdatert',
    'books_empty_contents' => 'Ingen sider eller kapitler finst i denne boka.',
    'books_empty_create_page' => 'Skriv ei ny side',
    'books_empty_sort_current_book' => 'Sorter innhaldet i boka',
    'books_empty_add_chapter' => 'Start på nytt kapittel',
    'books_permissions_active' => 'Boktilganger er aktive',
    'books_search_this' => 'Søk i boka',
    'books_navigation' => 'Boknavigasjon',
    'books_sort' => 'Sorter bokinnhald',
    'books_sort_desc' => 'Flytt kapitler og sider i ei bok for å omorganisere dei. Andre bøker kan bli lagt til slik at det er enklere å flytte fram og tilbake mellom dei.',
    'books_sort_named' => 'Omorganisér :bookName (bok)',
    'books_sort_name' => 'Sorter på namn',
    'books_sort_created' => 'Sorter på oppretta dato',
    'books_sort_updated' => 'Sorter på oppdatert dato',
    'books_sort_chapters_first' => 'Kapitler først',
    'books_sort_chapters_last' => 'Kapitler sist',
    'books_sort_show_other' => 'Vis andre bøker',
    'books_sort_save' => 'Lagre sortering',
    'books_sort_show_other_desc' => 'Legg til andre bøker her for å inkludere dei i omorganiseringa og gjer det enklare å flytte på tvers av dei.',
    'books_sort_move_up' => 'Flytt opp',
    'books_sort_move_down' => 'Flytt ned',
    'books_sort_move_prev_book' => 'Flytt til førre bok',
    'books_sort_move_next_book' => 'Flytt til neste bok',
    'books_sort_move_prev_chapter' => 'Flytt inn i førre kapittel',
    'books_sort_move_next_chapter' => 'Flytt inn i neste kapittel',
    'books_sort_move_book_start' => 'Flytt til starten av boka',
    'books_sort_move_book_end' => 'Flytt til slutten av boka',
    'books_sort_move_before_chapter' => 'Flytt før kapittel',
    'books_sort_move_after_chapter' => 'Flytt etter kapittel',
    'books_copy' => 'Kopiér bok',
    'books_copy_success' => 'Boka vart kopiert',

    // Chapters
    'chapter' => 'Kapittel',
    'chapters' => 'Kapitler',
    'x_chapters' => ':count kapittel|:count kapitler',
    'chapters_popular' => 'Populære kapitler',
    'chapters_new' => 'Nytt kapittel',
    'chapters_create' => 'Skriv nytt kapittel',
    'chapters_delete' => 'Riv ut kapittel',
    'chapters_delete_named' => 'Slett :chapterName (kapittel)',
    'chapters_delete_explain' => 'Dette vil slette «:chapterName» (kapittel). Alle sider i kapittelet vil også slettes.',
    'chapters_delete_confirm' => 'Er du sikker på at du vil slette dette kapittelet?',
    'chapters_edit' => 'Redigér kapittel',
    'chapters_edit_named' => 'Redigér :chapterName (kapittel)',
    'chapters_save' => 'Lagre kapittel',
    'chapters_move' => 'Flytt kapittel',
    'chapters_move_named' => 'Flytt :chapterName (kapittel)',
    'chapters_copy' => 'Kopiér kapittel',
    'chapters_copy_success' => 'Kapitelet vart kopiert',
    'chapters_permissions' => 'Kapitteltilgongar',
    'chapters_empty' => 'Det finnes ingen sider i dette kapittelet.',
    'chapters_permissions_active' => 'Kapitteltilganger er aktivert',
    'chapters_permissions_success' => 'Kapitteltilgager er oppdatert',
    'chapters_search_this' => 'Søk i dette kapittelet',
    'chapter_sort_book' => 'Omorganisér bok',

    // Pages
    'page' => 'Side',
    'pages' => 'Sider',
    'x_pages' => ':count side|:count sider',
    'pages_popular' => 'Populære sider',
    'pages_new' => 'Ny side',
    'pages_attachments' => 'Vedlegg',
    'pages_navigation' => 'Sidenavigasjon',
    'pages_delete' => 'Slett side',
    'pages_delete_named' => 'Slett :pageName (side)',
    'pages_delete_draft_named' => 'Slett utkastet :pageName (side)',
    'pages_delete_draft' => 'Slett utkastet',
    'pages_delete_success' => 'Siden er slettet',
    'pages_delete_draft_success' => 'Sideutkastet ble slettet',
    'pages_delete_confirm' => 'Er du sikker på at du vil slette siden?',
    'pages_delete_draft_confirm' => 'Er du sikker på at du vil slette utkastet?',
    'pages_editing_named' => 'Redigerer :pageName (side)',
    'pages_edit_draft_options' => 'Utkastsalternativer',
    'pages_edit_save_draft' => 'Lagre utkast',
    'pages_edit_draft' => 'Redigér utkast',
    'pages_editing_draft' => 'Redigerer utkast',
    'pages_editing_page' => 'Redigerer side',
    'pages_edit_draft_save_at' => 'Sist lagret ',
    'pages_edit_delete_draft' => 'Slett utkast',
    'pages_edit_delete_draft_confirm' => 'Er du sikker på at du vil slette utkastendringer i utkastet? Alle dine endringer, siden siste lagring vil gå tapt, og editoren vil bli oppdatert med den siste siden uten utkast til lagring.',
    'pages_edit_discard_draft' => 'Tilbakestill endring',
    'pages_edit_switch_to_markdown' => 'Bytt til Markdown tekstredigering',
    'pages_edit_switch_to_markdown_clean' => '(Renset innhold)',
    'pages_edit_switch_to_markdown_stable' => '(Urørt innhold)',
    'pages_edit_switch_to_wysiwyg' => 'Bytt til WYSIWYG tekstredigering',
    'pages_edit_set_changelog' => 'Angi endringslogg',
    'pages_edit_enter_changelog_desc' => 'Gi ei kort skildring av endringane dine',
    'pages_edit_enter_changelog' => 'Sjå endringslogg',
    'pages_editor_switch_title' => 'Bytt tekstredigeringsprogram',
    'pages_editor_switch_are_you_sure' => 'Er du sikker på at du vil bytte tekstredigeringsprogram for denne sida?',
    'pages_editor_switch_consider_following' => 'Hugs dette når du byttar tekstredigeringsprogram:',
    'pages_editor_switch_consideration_a' => 'Når du bytter, vil den nye tekstredigeraren bli valgt for alle framtidige redaktørar. Dette inkluderer alle redaktørar som ikkje kan endre type sjølv.',
    'pages_editor_switch_consideration_b' => 'I visse tilfeller kan det føre til tap av detaljar og syntaks.',
    'pages_editor_switch_consideration_c' => 'Etikett- eller redigeringslogg-endringar loggført sidan siste lagring vil ikkje føres vidare etter endringa.',
    'pages_save' => 'Lagre side',
    'pages_title' => 'Sidetittel',
    'pages_name' => 'Sidenamn',
    'pages_md_editor' => 'Tekstbehandlar',
    'pages_md_preview' => 'Førehandsvising',
    'pages_md_insert_image' => 'Sett in bilete',
    'pages_md_insert_link' => 'Sett inn lenke',
    'pages_md_insert_drawing' => 'Sett inn tegning',
    'pages_md_show_preview' => 'Førhandsvisning',
    'pages_md_sync_scroll' => 'Synkroniser førehandsvisingsrulle',
    'pages_drawing_unsaved' => 'Ulagra teikning funne',
    'pages_drawing_unsaved_confirm' => 'Ulagret tegningsdata ble funnet fra en tidligere mislykket lagring. Vil du gjenopprette og fortsette å redigere denne ulagrede tegningen?',
    'pages_not_in_chapter' => 'Sida tilhøyrer ingen kapittel',
    'pages_move' => 'Flytt sida',
    'pages_copy' => 'Kopier side',
    'pages_copy_desination' => 'Destinasjon',
    'pages_copy_success' => 'Sida vart flytta',
    'pages_permissions' => 'Sidetilgongar',
    'pages_permissions_success' => 'Sidetilgongar vart endra',
    'pages_revision' => 'Revisjon',
    'pages_revisions' => 'Revisjonar for sida',
    'pages_revisions_desc' => 'Oppført nedanfor er alle tidlegare revisjonar av denne sida. Du kan sjå tilbake igjen, samanlikna og retta opp igjen tidlegare sideversjonar viss du tillèt det. Den heile historikken til sida kan kanskje ikkje speglast fullstendig her, avhengig av systemkonfigurasjonen, kan gamle revisjonar bli sletta automatisk.',
    'pages_revisions_named' => 'Revisjonar for :pageName',
    'pages_revision_named' => 'Revisjonar for :pageName',
    'pages_revision_restored_from' => 'Gjenoppretta fra #:id; :summary',
    'pages_revisions_created_by' => 'Skrive av',
    'pages_revisions_date' => 'Revideringsdato',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revisjonsnummer',
    'pages_revisions_numbered' => 'Revisjon #:id',
    'pages_revisions_numbered_changes' => 'Endringar på revisjon #:id',
    'pages_revisions_editor' => 'Tekstredigeringstype',
    'pages_revisions_changelog' => 'Endringslogg',
    'pages_revisions_changes' => 'Endringar',
    'pages_revisions_current' => 'Siste versjon',
    'pages_revisions_preview' => 'Forhåndsvisning',
    'pages_revisions_restore' => 'Gjenopprett',
    'pages_revisions_none' => 'Denne siden har ingen revisjoner',
    'pages_copy_link' => 'Kopier lenke',
    'pages_edit_content_link' => 'Hopp til seksjonen i tekstbehandleren',
    'pages_pointer_enter_mode' => 'Gå til seksjonen velg modus',
    'pages_pointer_label' => 'Sidens seksjon alternativer',
    'pages_pointer_permalink' => 'Sideseksjons permalenke',
    'pages_pointer_include_tag' => 'Sideseksjonen inkluderer Tag',
    'pages_pointer_toggle_link' => 'Permalenke modus, trykk for å vise inkluderer tag',
    'pages_pointer_toggle_include' => 'Inkluder tag-modus, trykk for å vise permalenke',
    'pages_permissions_active' => 'Sidetilganger er aktive',
    'pages_initial_revision' => 'Første publisering',
    'pages_references_update_revision' => 'Automatisk oppdatering av interne lenker',
    'pages_initial_name' => 'Ny side',
    'pages_editing_draft_notification' => 'Du skriver på et utkast som sist ble lagret :timeDiff.',
    'pages_draft_edited_notification' => 'Siden har blitt endret siden du startet. Det anbefales at du forkaster dine endringer.',
    'pages_draft_page_changed_since_creation' => 'Denne siden er blitt oppdatert etter at dette utkastet ble opprettet. Det anbefales at du forkaster dette utkastet, eller er ekstra forsiktig slik at du ikke overskriver noen sideendringer.',
    'pages_draft_edit_active' => [
        'start_a' => ':count forfattere har begynt å endre denne siden.',
        'start_b' => ':userName skriver på siden for øyeblikket',
        'time_a' => 'siden sist siden ble oppdatert',
        'time_b' => 'i løpet av de siste :minCount minuttene',
        'message' => ':start :time. Prøv å ikke overskriv hverandres endringer!',
    ],
    'pages_draft_discarded' => 'Utkastet er forkastet! Redigeringsprogrammet er oppdatert med gjeldende sideinnhold',
    'pages_draft_deleted' => 'Utkast sletta! Redigeringsprogrammet er oppdatert med gjeldande sideinnhald',
    'pages_specific' => 'Bestemt side',
    'pages_is_template' => 'Sidemal',

    // Editor Sidebar
    'toggle_sidebar' => 'Vis/gøym sidepanelet',
    'page_tags' => 'Sidemerker',
    'chapter_tags' => 'Kapittelmerker',
    'book_tags' => 'Bokmerker',
    'shelf_tags' => 'Hyllemerker',
    'tag' => 'Merke',
    'tags' =>  'Merker',
    'tags_index_desc' => 'Merker kan brukes på innhold i systemet for å anvende en kategorisering på en fleksibel måte. Etiketter kan ha både en nøkkel og verdi, med valgfri. Når det er brukt, kan innhold sjekkes ved hjelp av taggnavn og verdi.',
    'tag_name' =>  'Merketittel',
    'tag_value' => 'Merkeverdi (Valgfritt)',
    'tags_explain' => "Legg til merker for å kategorisere innholdet ditt. \n Du kan legge til merkeverdier for å beskrive dem ytterligere.",
    'tags_add' => 'Legg til flere merker',
    'tags_remove' => 'Fjern merke',
    'tags_usages' => 'Totalt emneordbruk',
    'tags_assigned_pages' => 'Tilordnet sider',
    'tags_assigned_chapters' => 'Tildelt til kapitler',
    'tags_assigned_books' => 'Tilordnet til bøker',
    'tags_assigned_shelves' => 'Tilordnet hyller',
    'tags_x_unique_values' => ':count unike verdier',
    'tags_all_values' => 'Alle verdier',
    'tags_view_tags' => 'Vis etiketter',
    'tags_view_existing_tags' => 'Vis eksisterende etiketter',
    'tags_list_empty_hint' => 'Etiketter kan tilordnes via sidepanelet, eller mens du redigerer detaljene for en hylle, bok eller kapittel.',
    'attachments' => 'Vedlegg',
    'attachments_explain' => 'Last opp vedlegg eller legg til lenker for å berike innholdet. Disse vil vises i sidestolpen på siden.',
    'attachments_explain_instant_save' => 'Endringer her blir lagret med en gang.',
    'attachments_upload' => 'Last opp vedlegg',
    'attachments_link' => 'Fest lenke',
    'attachments_upload_drop' => 'Alternativt kan du dra og slippe en fil her for å laste den opp som et vedlegg.',
    'attachments_set_link' => 'Angi lenke',
    'attachments_delete' => 'Er du sikker på at du vil fjerne vedlegget?',
    'attachments_dropzone' => 'Slipp filer her for å laste opp',
    'attachments_no_files' => 'Ingen vedlegg er lastet opp',
    'attachments_explain_link' => 'Du kan feste lenker til denne. Det kan være henvisning til andre sider, bøker etc. eller lenker fra nettet.',
    'attachments_link_name' => 'Lenkenavn',
    'attachment_link' => 'Vedleggslenke',
    'attachments_link_url' => 'Lenke til vedlegg',
    'attachments_link_url_hint' => 'Adresse til lenke eller vedlegg',
    'attach' => 'Fest',
    'attachments_insert_link' => 'Fest vedleggslenke',
    'attachments_edit_file' => 'Endre vedlegg',
    'attachments_edit_file_name' => 'Vedleggsnavn',
    'attachments_edit_drop_upload' => 'Dra og slipp eller trykk her for å oppdatere eller overskrive',
    'attachments_order_updated' => 'Vedleggssortering endret',
    'attachments_updated_success' => 'Vedleggsdetaljer endret',
    'attachments_deleted' => 'Vedlegg fjernet',
    'attachments_file_uploaded' => 'Vedlegg ble lastet opp',
    'attachments_file_updated' => 'Vedlegget ble oppdatert',
    'attachments_link_attached' => 'Lenken ble festet til siden',
    'templates' => 'Maler',
    'templates_set_as_template' => 'Siden er en mal',
    'templates_explain_set_as_template' => 'Du kan angi denne siden som en mal slik at innholdet kan brukes når du oppretter andre sider. Andre brukere vil kunne bruke denne malen hvis de har visningstillatelser for denne siden.',
    'templates_replace_content' => 'Bytt sideinnhold',
    'templates_append_content' => 'Legg til neders på siden',
    'templates_prepend_content' => 'Legg til øverst på siden',

    // Profile View
    'profile_user_for_x' => 'Medlem i :time',
    'profile_created_content' => 'Har skrevet',
    'profile_not_created_pages' => ':userName har ikke forfattet noen sider',
    'profile_not_created_chapters' => ':userName har ikke opprettet noen kapitler',
    'profile_not_created_books' => ':userName har ikke laget noen bøker',
    'profile_not_created_shelves' => ':userName har ikke hengt opp noen hyller',

    // Comments
    'comment' => 'Kommentar',
    'comments' => 'Kommentarer',
    'comment_add' => 'Skriv kommentar',
    'comment_placeholder' => 'Skriv en kommentar her',
    'comment_count' => '{0} Ingen kommentarer|{1} 1 kommentar|[2,*] :count kommentarer',
    'comment_save' => 'Publiser kommentar',
    'comment_new' => 'Ny kommentar',
    'comment_created' => 'kommenterte :createDiff',
    'comment_updated' => 'Oppdatert :updateDiff av :username',
    'comment_updated_indicator' => 'Oppdatert',
    'comment_deleted_success' => 'Kommentar fjernet',
    'comment_created_success' => 'Kommentar skrevet',
    'comment_updated_success' => 'Kommentar endret',
    'comment_delete_confirm' => 'Er du sikker på at du vil fjerne kommentaren?',
    'comment_in_reply_to' => 'Som svar til :commentId',
    'comment_editor_explain' => 'Her er kommentarene som er på denne siden. Kommentarer kan legges til og administreres når du ser på den lagrede siden.',

    // Revision
    'revision_delete_confirm' => 'Vil du slette revisjonen?',
    'revision_restore_confirm' => 'Vil du gjenopprette revisjonen? Innholdet på siden vil bli overskrevet med denne revisjonen.',
    'revision_cannot_delete_latest' => 'CKan ikke slette siste revisjon.',

    // Copy view
    'copy_consider' => 'Vennligst vurder nedenfor når du kopierer innholdet.',
    'copy_consider_permissions' => 'Egendefinerte tilgangsinnstillinger vil ikke bli kopiert.',
    'copy_consider_owner' => 'Du vil bli eier av alt kopiert innhold.',
    'copy_consider_images' => 'Sidebildefiler vil ikke bli duplisert og de opprinnelige bildene beholder relasjonen til siden de opprinnelig ble lastet opp til.',
    'copy_consider_attachments' => 'Sidevedlegg vil ikke bli kopiert.',
    'copy_consider_access' => 'Endring av sted, eier eller rettigheter kan føre til at innholdet er tilgjengelig for dem som tidligere har vært uten adgang.',

    // Conversions
    'convert_to_shelf' => 'Konverter til bokhylle',
    'convert_to_shelf_contents_desc' => 'Du kan konvertere denne boken til en ny hylle med samme innhold. Kapitteler i denne boken vil bli konvertert til nye bøker. Hvis boken inneholder noen sider, som ikke er i et kapitler, boka blir omdøpt og med slike sider, og boka blir en del av den nye bokhyllen.',
    'convert_to_shelf_permissions_desc' => 'Eventuelle tillatelser som er satt på denne boka, vil bli kopiert til ny hylle og til alle nye under-bøker som ikke har egne tillatelser satt. Vær oppmerksom på at tillatelser på hyllene ikke skjuler automatisk innhold innenfor, da de gjør for bøker.',
    'convert_book' => 'Konverter bok',
    'convert_book_confirm' => 'Er du sikker på at du vil konvertere denne boken?',
    'convert_undo_warning' => 'Dette kan ikke bli så lett å angre.',
    'convert_to_book' => 'Konverter til bok',
    'convert_to_book_desc' => 'Du kan konvertere kapittelet til en ny bok med samme innhold. Alle tillatelser som er angitt i dette kapittelet vil bli kopiert til den nye boken, men alle arvede tillatelser, fra overordnet bok vil ikke kopieres noe som kan føre til en endring av tilgangskontroll.',
    'convert_chapter' => 'Konverter kapittel',
    'convert_chapter_confirm' => 'Er du sikker på at du vil konvertere dette kapittelet?',

    // References
    'references' => 'Referanser',
    'references_none' => 'Det er ingen sporede referanser til dette elementet.',
    'references_to_desc' => 'Nedenfor vises alle de kjente sidene i systemet som lenker til denne oppføringen.',

    // Watch Options
    'watch' => 'Overvåk',
    'watch_title_default' => 'Standardinnstillinger',
    'watch_desc_default' => 'Bytt til dine standardinnstilleringer for varsling.',
    'watch_title_ignore' => 'Ignorer',
    'watch_desc_ignore' => 'Ignorer alle varslinger, inkludert de fra preferanser for brukernivå.',
    'watch_title_new' => 'Nye sider',
    'watch_desc_new' => 'Varsle når en ny side er opprettet innenfor dette elementet.',
    'watch_title_updates' => 'Alle sideoppdateringer',
    'watch_desc_updates' => 'Varsle på alle nye sider og endringer av siden.',
    'watch_desc_updates_page' => 'Varsle ved alle sideendringer.',
    'watch_title_comments' => 'Alle sideoppdateringer og kommentarer',
    'watch_desc_comments' => 'Varsle om alle nye sider, endringer på side og nye kommentarer.',
    'watch_desc_comments_page' => 'Varsle ved sideendringer og nye kommentarer.',
    'watch_change_default' => 'Endre standard varslingsinnstillinger',
    'watch_detail_ignore' => 'Ignorerer varsler',
    'watch_detail_new' => 'Varsling for nye sider',
    'watch_detail_updates' => 'Varsling for nye sider og oppdateringer',
    'watch_detail_comments' => 'Varsling for nye sider, oppdateringer og kommentarer',
    'watch_detail_parent_book' => 'Overvåker via overordnet bok',
    'watch_detail_parent_book_ignore' => 'Ignorerer via overordnet bok',
    'watch_detail_parent_chapter' => 'Overvåker via overordnet kapittel',
    'watch_detail_parent_chapter_ignore' => 'Ignorerer via overordnet kapittel',
];
