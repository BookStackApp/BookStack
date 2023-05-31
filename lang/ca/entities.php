<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Creat fa poc',
    'recently_created_pages' => 'Pàgines creades fa poc',
    'recently_updated_pages' => 'Pàgines actualitzades fa poc',
    'recently_created_chapters' => 'Capítols creats fa poc',
    'recently_created_books' => 'Llibres creats fa poc',
    'recently_created_shelves' => 'Prestatges creats fa poc',
    'recently_update' => 'Actualitzat fa poc',
    'recently_viewed' => 'Vist fa poc',
    'recent_activity' => 'Activitat recent',
    'create_now' => 'Crea\'n ara',
    'revisions' => 'Revisions',
    'meta_revision' => 'Revisió núm. :revisionCount',
    'meta_created' => 'Creat :timeLength',
    'meta_created_name' => 'Creat :timeLength per :user',
    'meta_updated' => 'Actualitzat :timeLength',
    'meta_updated_name' => 'Actualitzat :timeLength per :user',
    'meta_owned_name' => 'Propietat de :user',
    'meta_reference_page_count' => 'Referenced on :count page|Referenced on :count pages',
    'entity_select' => 'Selecciona una entitat',
    'entity_select_lack_permission' => 'You don\'t have the required permissions to select this item',
    'images' => 'Imatges',
    'my_recent_drafts' => 'Els vostres esborranys recents',
    'my_recently_viewed' => 'Les vostres visualitzacions recents',
    'my_most_viewed_favourites' => 'My Most Viewed Favourites',
    'my_favourites' => 'My Favourites',
    'no_pages_viewed' => 'No heu vist cap pàgina',
    'no_pages_recently_created' => 'No s\'ha creat cap pàgina fa poc',
    'no_pages_recently_updated' => 'No s\'ha actualitzat cap pàgina fa poc',
    'export' => 'Exporta',
    'export_html' => 'Fitxer web independent',
    'export_pdf' => 'Fitxer PDF',
    'export_text' => 'Fitxer de text sense format',
    'export_md' => 'Markdown File',

    // Permissions and restrictions
    'permissions' => 'Permisos',
    'permissions_desc' => 'Set permissions here to override the default permissions provided by user roles.',
    'permissions_book_cascade' => 'Permissions set on books will automatically cascade to child chapters and pages, unless they have their own permissions defined.',
    'permissions_chapter_cascade' => 'Permissions set on chapters will automatically cascade to child pages, unless they have their own permissions defined.',
    'permissions_save' => 'Desa els permisos',
    'permissions_owner' => 'Propietari',
    'permissions_role_everyone_else' => 'Everyone Else',
    'permissions_role_everyone_else_desc' => 'Set permissions for all roles not specifically overridden.',
    'permissions_role_override' => 'Override permissions for role',
    'permissions_inherit_defaults' => 'Inherit defaults',

    // Search
    'search_results' => 'Resultats de la cerca',
    'search_total_results_found' => 'S\'ha trobat :count resultat en total|S\'han trobat :count resultats en total',
    'search_clear' => 'Esborra la cerca',
    'search_no_pages' => 'La cerca no coincideix amb cap pàgina',
    'search_for_term' => 'Cerca :term',
    'search_more' => 'Més resultats',
    'search_advanced' => 'Cerca avançada',
    'search_terms' => 'Termes de la cerca',
    'search_content_type' => 'Tipus de contingut',
    'search_exact_matches' => 'Coincidències exactes',
    'search_tags' => 'Cerca d\'etiquetes',
    'search_options' => 'Opcions',
    'search_viewed_by_me' => 'Visualitzat per mi',
    'search_not_viewed_by_me' => 'No visualitzat per mi',
    'search_permissions_set' => 'Amb permisos definits',
    'search_created_by_me' => 'Creat per mi',
    'search_updated_by_me' => 'Actualitzat per mi',
    'search_owned_by_me' => 'Owned by me',
    'search_date_options' => 'Opcions de dates',
    'search_updated_before' => 'Actualitzat abans de',
    'search_updated_after' => 'Actualitzat després de',
    'search_created_before' => 'Creat abans de',
    'search_created_after' => 'Creat després de',
    'search_set_date' => 'Defineix una data',
    'search_update' => 'Actualitza la cerca',

    // Shelves
    'shelf' => 'Prestatge',
    'shelves' => 'Prestatges',
    'x_shelves' => ':count prestatge|:count prestatges',
    'shelves_empty' => 'No hi ha cap prestatge creat',
    'shelves_create' => 'Crea un prestatge nou',
    'shelves_popular' => 'Prestatges populars',
    'shelves_new' => 'Prestatges nous',
    'shelves_new_action' => 'Prestatge nou',
    'shelves_popular_empty' => 'Aquí apareixeran els prestatges més populars.',
    'shelves_new_empty' => 'Aquí apareixeran els prestatges creats fa poc.',
    'shelves_save' => 'Desa el prestatge',
    'shelves_books' => 'Llibres en aquest prestatge',
    'shelves_add_books' => 'Afegeix llibres a aquest prestatge',
    'shelves_drag_books' => 'Drag books below to add them to this shelf',
    'shelves_empty_contents' => 'Aquest prestatge no té cap llibre assignat',
    'shelves_edit_and_assign' => 'Editeu el prestatge per a assignar-hi llibres',
    'shelves_edit_named' => 'Edit Shelf :name',
    'shelves_edit' => 'Edit Shelf',
    'shelves_delete' => 'Delete Shelf',
    'shelves_delete_named' => 'Delete Shelf :name',
    'shelves_delete_explain' => "This will delete the shelf with the name ':name'. Contained books will not be deleted.",
    'shelves_delete_confirmation' => 'Are you sure you want to delete this shelf?',
    'shelves_permissions' => 'Shelf Permissions',
    'shelves_permissions_updated' => 'Shelf Permissions Updated',
    'shelves_permissions_active' => 'Shelf Permissions Active',
    'shelves_permissions_cascade_warning' => 'Permissions on shelves do not automatically cascade to contained books. This is because a book can exist on multiple shelves. Permissions can however be copied down to child books using the option found below.',
    'shelves_copy_permissions_to_books' => 'Copia els permisos als llibres',
    'shelves_copy_permissions' => 'Copia els permisos',
    'shelves_copy_permissions_explain' => 'This will apply the current permission settings of this shelf to all books contained within. Before activating, ensure any changes to the permissions of this shelf have been saved.',
    'shelves_copy_permission_success' => 'Shelf permissions copied to :count books',

    // Books
    'book' => 'Llibre',
    'books' => 'Llibres',
    'x_books' => ':count llibre|:count llibres',
    'books_empty' => 'No hi ha cap llibre creat',
    'books_popular' => 'Llibres populars',
    'books_recent' => 'Llibres recents',
    'books_new' => 'Llibres nous',
    'books_new_action' => 'Llibre nou',
    'books_popular_empty' => 'Aquí apareixeran els llibres més populars.',
    'books_new_empty' => 'Aquí apareixeran els llibres creats fa poc.',
    'books_create' => 'Crea un llibre nou',
    'books_delete' => 'Suprimeix el llibre',
    'books_delete_named' => 'Suprimeix el llibre :bookName',
    'books_delete_explain' => 'Se suprimirà el llibre amb el nom \':bookName\'. Se\'n suprimiran les pàgines i els capítols.',
    'books_delete_confirmation' => 'Segur que voleu suprimir aquest llibre?',
    'books_edit' => 'Edita el llibre',
    'books_edit_named' => 'Edita el llibre :bookName',
    'books_form_book_name' => 'Nom del llibre',
    'books_save' => 'Desa el llibre',
    'books_permissions' => 'Permisos del llibre',
    'books_permissions_updated' => 'S\'han actualitzat els permisos del llibre',
    'books_empty_contents' => 'No hi ha cap pàgina ni cap capítol creat en aquest llibre.',
    'books_empty_create_page' => 'Crea una pàgina nova',
    'books_empty_sort_current_book' => 'Ordena el llibre actual',
    'books_empty_add_chapter' => 'Afegeix un capítol',
    'books_permissions_active' => 'S\'han activat els permisos del llibre',
    'books_search_this' => 'Cerca en aquest llibre',
    'books_navigation' => 'Navegació pel llibre',
    'books_sort' => 'Ordena el contingut del llibre',
    'books_sort_desc' => 'Move chapters and pages within a book to reorganise its contents. Other books can be added which allows easy moving of chapters and pages between books.',
    'books_sort_named' => 'Ordena el llibre :bookName',
    'books_sort_name' => 'Ordena per nom',
    'books_sort_created' => 'Ordena per data de creació',
    'books_sort_updated' => 'Ordena per data d\'actualització',
    'books_sort_chapters_first' => 'Els capítols al principi',
    'books_sort_chapters_last' => 'Els capítols al final',
    'books_sort_show_other' => 'Mostra altres llibres',
    'books_sort_save' => 'Desa l\'ordre nou',
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
    'books_copy' => 'Copy Book',
    'books_copy_success' => 'Book successfully copied',

    // Chapters
    'chapter' => 'Capítol',
    'chapters' => 'Capítols',
    'x_chapters' => ':count capítol|:count capítols',
    'chapters_popular' => 'Capítols populars',
    'chapters_new' => 'Capítol nou',
    'chapters_create' => 'Crea un capítol nou',
    'chapters_delete' => 'Suprimeix el capítol',
    'chapters_delete_named' => 'Suprimeix el capítol :chapterName',
    'chapters_delete_explain' => 'Se suprimirà el capítol amb el nom \':chapterName\'. Totes les pàgines que contingui també se suprimiran.',
    'chapters_delete_confirm' => 'Segur que voleu suprimir aquest capítol?',
    'chapters_edit' => 'Edita el capítol',
    'chapters_edit_named' => 'Edita el capítol :chapterName',
    'chapters_save' => 'Desa el capítol',
    'chapters_move' => 'Mou el capítol',
    'chapters_move_named' => 'Mou el capítol :chapterName',
    'chapter_move_success' => 'S\'ha mogut el capítol a :bookName',
    'chapters_copy' => 'Copy Chapter',
    'chapters_copy_success' => 'Chapter successfully copied',
    'chapters_permissions' => 'Permisos del capítol',
    'chapters_empty' => 'De moment, aquest capítol no conté cap pàgina.',
    'chapters_permissions_active' => 'S\'han activat els permisos del capítol',
    'chapters_permissions_success' => 'S\'han actualitzat els permisos del capítol',
    'chapters_search_this' => 'Cerca en aquest capítol',
    'chapter_sort_book' => 'Sort Book',

    // Pages
    'page' => 'Pàgina',
    'pages' => 'Pàgines',
    'x_pages' => ':count pàgina|:count pàgines',
    'pages_popular' => 'Pàgines populars',
    'pages_new' => 'Pàgina nova',
    'pages_attachments' => 'Adjuncions',
    'pages_navigation' => 'Navegació per la pàgina',
    'pages_delete' => 'Suprimeix la pàgina',
    'pages_delete_named' => 'Suprimeix la pàgina :pageName',
    'pages_delete_draft_named' => 'Suprimeix l\'esborrany de pàgina :pageName',
    'pages_delete_draft' => 'Suprimeix l\'esborrany de pàgina',
    'pages_delete_success' => 'S\'ha suprimit la pàgina',
    'pages_delete_draft_success' => 'S\'ha suprimit l\'esborrany de pàgina',
    'pages_delete_confirm' => 'Segur que voleu suprimir aquesta pàgina?',
    'pages_delete_draft_confirm' => 'Segur que voleu suprimir aquest esborrany de pàgina?',
    'pages_editing_named' => 'Esteu editant :pageName',
    'pages_edit_draft_options' => 'Opcions d\'esborrany',
    'pages_edit_save_draft' => 'Desa l\'esborrany',
    'pages_edit_draft' => 'Edita l\'esborrany de pàgina',
    'pages_editing_draft' => 'Esteu editant l\'esborrany',
    'pages_editing_page' => 'Esteu editant la pàgina',
    'pages_edit_draft_save_at' => 'Esborrany desat ',
    'pages_edit_delete_draft' => 'Suprimeix l\'esborrany',
    'pages_edit_discard_draft' => 'Descarta l\'esborrany',
    'pages_edit_switch_to_markdown' => 'Switch to Markdown Editor',
    'pages_edit_switch_to_markdown_clean' => '(Clean Content)',
    'pages_edit_switch_to_markdown_stable' => '(Stable Content)',
    'pages_edit_switch_to_wysiwyg' => 'Switch to WYSIWYG Editor',
    'pages_edit_set_changelog' => 'Defineix el registre de canvis',
    'pages_edit_enter_changelog_desc' => 'Introduïu una breu descripció dels canvis que heu fet',
    'pages_edit_enter_changelog' => 'Introduïu un registre de canvis',
    'pages_editor_switch_title' => 'Switch Editor',
    'pages_editor_switch_are_you_sure' => 'Are you sure you want to change the editor for this page?',
    'pages_editor_switch_consider_following' => 'Consider the following when changing editors:',
    'pages_editor_switch_consideration_a' => 'Once saved, the new editor option will be used by any future editors, including those that may not be able to change editor type themselves.',
    'pages_editor_switch_consideration_b' => 'This can potentially lead to a loss of detail and syntax in certain circumstances.',
    'pages_editor_switch_consideration_c' => 'Tag or changelog changes, made since last save, won\'t persist across this change.',
    'pages_save' => 'Desa la pàgina',
    'pages_title' => 'Títol de la pàgina',
    'pages_name' => 'Nom de la pàgina',
    'pages_md_editor' => 'Editor',
    'pages_md_preview' => 'Previsualització',
    'pages_md_insert_image' => 'Insereix una imatge',
    'pages_md_insert_link' => 'Insereix un enllaç a una entitat',
    'pages_md_insert_drawing' => 'Insereix un diagrama',
    'pages_md_show_preview' => 'Show preview',
    'pages_md_sync_scroll' => 'Sync preview scroll',
    'pages_not_in_chapter' => 'La pàgina no pertany a cap capítol',
    'pages_move' => 'Mou la pàgina',
    'pages_move_success' => 'S\'ha mogut la pàgina a ":parentName"',
    'pages_copy' => 'Copia la pàgina',
    'pages_copy_desination' => 'Destinació de la còpia',
    'pages_copy_success' => 'Pàgina copiada correctament',
    'pages_permissions' => 'Permisos de la pàgina',
    'pages_permissions_success' => 'S\'han actualitzat els permisos de la pàgina',
    'pages_revision' => 'Revisió',
    'pages_revisions' => 'Revisions de la pàgina',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'Revisions de la pàgina :pageName',
    'pages_revision_named' => 'Revisió de la pàgina :pageName',
    'pages_revision_restored_from' => 'Restaurada de núm. :id; :summary',
    'pages_revisions_created_by' => 'Creada per',
    'pages_revisions_date' => 'Data de la revisió',
    'pages_revisions_number' => 'Núm. ',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'Revisió núm. :id',
    'pages_revisions_numbered_changes' => 'Canvis de la revisió núm. :id',
    'pages_revisions_editor' => 'Editor Type',
    'pages_revisions_changelog' => 'Registre de canvis',
    'pages_revisions_changes' => 'Canvis',
    'pages_revisions_current' => 'Versió actual',
    'pages_revisions_preview' => 'Previsualitza',
    'pages_revisions_restore' => 'Restaura',
    'pages_revisions_none' => 'Aquesta pàgina no té cap revisió',
    'pages_copy_link' => 'Copia l\'enllaç',
    'pages_edit_content_link' => 'Jump to section in editor',
    'pages_pointer_enter_mode' => 'Enter section select mode',
    'pages_pointer_label' => 'Page Section Options',
    'pages_pointer_permalink' => 'Page Section Permalink',
    'pages_pointer_include_tag' => 'Page Section Include Tag',
    'pages_pointer_toggle_link' => 'Permalink mode, Press to show include tag',
    'pages_pointer_toggle_include' => 'Include tag mode, Press to show permalink',
    'pages_permissions_active' => 'S\'han activat els permisos de la pàgina',
    'pages_initial_revision' => 'Publicació inicial',
    'pages_references_update_revision' => 'System auto-update of internal links',
    'pages_initial_name' => 'Pàgina nova',
    'pages_editing_draft_notification' => 'Esteu editant un esborrany que es va desar per darrer cop :timeDiff.',
    'pages_draft_edited_notification' => 'Aquesta pàgina s\'ha actualitzat d\'ençà d\'aleshores. Us recomanem que descarteu aquest esborrany.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count usuaris han començat a editar aquesta pàgina',
        'start_b' => ':userName ha començat a editar aquesta pàgina',
        'time_a' => 'd\'ençà que la pàgina es va actualitzar per darrer cop',
        'time_b' => 'en els darrers :minCount minuts',
        'message' => ':start :time. Aneu amb compte de no trepitjar-vos les actualitzacions entre vosaltres!',
    ],
    'pages_draft_discarded' => 'S\'ha descartat l\'esborrany, l\'editor s\'ha actualitzat amb el contingut actual de la pàgina',
    'pages_specific' => 'Una pàgina específica',
    'pages_is_template' => 'Plantilla de pàgina',

    // Editor Sidebar
    'page_tags' => 'Etiquetes de la pàgina',
    'chapter_tags' => 'Etiquetes del capítol',
    'book_tags' => 'Etiquetes del llibre',
    'shelf_tags' => 'Etiquetes del prestatge',
    'tag' => 'Etiqueta',
    'tags' =>  'Etiquetes',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'Nom de l\'etiqueta',
    'tag_value' => 'Valor de l\'etiqueta (opcional)',
    'tags_explain' => "Afegiu etiquetes per a categoritzar millor el contingut. \n Podeu assignar un valor a cada etiqueta per a una organització més detallada.",
    'tags_add' => 'Afegeix una altra etiqueta',
    'tags_remove' => 'Elimina aquesta etiqueta',
    'tags_usages' => 'Total tag usages',
    'tags_assigned_pages' => 'Assigned to Pages',
    'tags_assigned_chapters' => 'Assigned to Chapters',
    'tags_assigned_books' => 'Assigned to Books',
    'tags_assigned_shelves' => 'Assigned to Shelves',
    'tags_x_unique_values' => ':count unique values',
    'tags_all_values' => 'All values',
    'tags_view_tags' => 'View Tags',
    'tags_view_existing_tags' => 'View existing tags',
    'tags_list_empty_hint' => 'Tags can be assigned via the page editor sidebar or while editing the details of a book, chapter or shelf.',
    'attachments' => 'Adjuncions',
    'attachments_explain' => 'Pugeu fitxers o adjunteu enllaços per a mostrar-los a la pàgina. Són visibles a la barra lateral de la pàgina.',
    'attachments_explain_instant_save' => 'Els canvis fets aquí es desen instantàniament.',
    'attachments_upload' => 'Puja un fitxer',
    'attachments_link' => 'Adjunta un enllaç',
    'attachments_upload_drop' => 'Alternatively you can drag and drop a file here to upload it as an attachment.',
    'attachments_set_link' => 'Defineix l\'enllaç',
    'attachments_delete' => 'Seguir que voleu suprimir aquesta adjunció?',
    'attachments_dropzone' => 'Drop files here to upload',
    'attachments_no_files' => 'No s\'ha pujat cap fitxer',
    'attachments_explain_link' => 'Podeu adjuntar un enllaç si preferiu no pujar un fitxer. Pot ser un enllaç a una altra pàgina o un enllaç a un fitxer al núvol.',
    'attachments_link_name' => 'Nom de l\'enllaç',
    'attachment_link' => 'Enllaç de l\'adjunció',
    'attachments_link_url' => 'Enllaç al fitxer',
    'attachments_link_url_hint' => 'URL del lloc o fitxer',
    'attach' => 'Adjunta',
    'attachments_insert_link' => 'Afegeix un enllaç de l\'adjunció a la pàgina',
    'attachments_edit_file' => 'Edita el fitxer',
    'attachments_edit_file_name' => 'Nom del fitxer',
    'attachments_edit_drop_upload' => 'Arrossegueu fitxers o feu clic aquí per a pujar-los i sobreescriure\'ls',
    'attachments_order_updated' => 'S\'ha actualitzat l\'ordre de les adjuncions',
    'attachments_updated_success' => 'S\'han actualitzat els detalls de les adjuncions',
    'attachments_deleted' => 'S\'ha suprimit l\'adjunció',
    'attachments_file_uploaded' => 'Fitxer pujat correctament',
    'attachments_file_updated' => 'Fitxer actualitzat correctament',
    'attachments_link_attached' => 'Enllaç adjuntat a la pàgina correctament',
    'templates' => 'Plantilles',
    'templates_set_as_template' => 'La pàgina és una plantilla',
    'templates_explain_set_as_template' => 'Podeu definir aquesta pàgina com a plantilla perquè el seu contingut es pugui fer servir en crear altres pàgines. Els altres usuaris podran fer servir la plantilla si tenen permís per a veure aquesta pàgina.',
    'templates_replace_content' => 'Substitueix el contingut de la pàgina',
    'templates_append_content' => 'Afegeix al final del contingut de la pàgina',
    'templates_prepend_content' => 'Afegeix al principi del contingut de la pàgina',

    // Profile View
    'profile_user_for_x' => 'Usuari fa :time',
    'profile_created_content' => 'Contingut creat',
    'profile_not_created_pages' => ':userName no ha creat cap pàgina',
    'profile_not_created_chapters' => ':userName no ha creat cap capítol',
    'profile_not_created_books' => ':userName no ha creat cap llibre',
    'profile_not_created_shelves' => ':userName no ha creat cap prestatge',

    // Comments
    'comment' => 'Comentari',
    'comments' => 'Comentaris',
    'comment_add' => 'Afegeix un comentari',
    'comment_placeholder' => 'Deixeu un comentari aquí',
    'comment_count' => '{0} Sense comentaris|{1} 1 comentari|[2,*] :count comentaris',
    'comment_save' => 'Desa el comentari',
    'comment_saving' => 'S\'està desant el comentari...',
    'comment_deleting' => 'S\'està suprimint el comentari...',
    'comment_new' => 'Comentari nou',
    'comment_created' => 'ha comentat :createDiff',
    'comment_updated' => 'Actualitzat :updateDiff per :username',
    'comment_deleted_success' => 'Comentari suprimit',
    'comment_created_success' => 'Comentari afegit',
    'comment_updated_success' => 'Comentari actualitzat',
    'comment_delete_confirm' => 'Segur que voleu suprimir aquest comentari?',
    'comment_in_reply_to' => 'En resposta a :commentId',

    // Revision
    'revision_delete_confirm' => 'Segur que voleu suprimir aquesta revisió?',
    'revision_restore_confirm' => 'Segur que voleu restaurar aquesta revisió? Se substituirà el contingut de la pàgina actual.',
    'revision_delete_success' => 'S\'ha suprimit la revisió',
    'revision_cannot_delete_latest' => 'No es pot suprimir la darrera revisió.',

    // Copy view
    'copy_consider' => 'Please consider the below when copying content.',
    'copy_consider_permissions' => 'Custom permission settings will not be copied.',
    'copy_consider_owner' => 'You will become the owner of all copied content.',
    'copy_consider_images' => 'Page image files will not be duplicated & the original images will retain their relation to the page they were originally uploaded to.',
    'copy_consider_attachments' => 'Page attachments will not be copied.',
    'copy_consider_access' => 'A change of location, owner or permissions may result in this content being accessible to those previously without access.',

    // Conversions
    'convert_to_shelf' => 'Convert to Shelf',
    'convert_to_shelf_contents_desc' => 'You can convert this book to a new shelf with the same contents. Chapters contained within this book will be converted to new books. If this book contains any pages, that are not in a chapter, this book will be renamed and contain such pages, and this book will become part of the new shelf.',
    'convert_to_shelf_permissions_desc' => 'Any permissions set on this book will be copied to the new shelf and to all new child books that don\'t have their own permissions enforced. Note that permissions on shelves do not auto-cascade to content within, as they do for books.',
    'convert_book' => 'Convert Book',
    'convert_book_confirm' => 'Are you sure you want to convert this book?',
    'convert_undo_warning' => 'This cannot be as easily undone.',
    'convert_to_book' => 'Convert to Book',
    'convert_to_book_desc' => 'You can convert this chapter to a new book with the same contents. Any permissions set on this chapter will be copied to the new book but any inherited permissions, from the parent book, will not be copied which could lead to a change of access control.',
    'convert_chapter' => 'Convert Chapter',
    'convert_chapter_confirm' => 'Are you sure you want to convert this chapter?',

    // References
    'references' => 'References',
    'references_none' => 'There are no tracked references to this item.',
    'references_to_desc' => 'Shown below are all the known pages in the system that link to this item.',
];
