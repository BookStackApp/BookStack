<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nyligt oprettet',
    'recently_created_pages' => 'Nyligt oprettede sider',
    'recently_updated_pages' => 'Nyligt opdaterede sider',
    'recently_created_chapters' => 'Nyligt oprettede kapitler',
    'recently_created_books' => 'Nyligt oprettede bøger',
    'recently_created_shelves' => 'Nyligt oprettede hylder',
    'recently_update' => 'Opdateret for nyligt',
    'recently_viewed' => 'Senest viste',
    'recent_activity' => 'Seneste aktivitet',
    'create_now' => 'Opret en nu',
    'revisions' => 'Revisioner',
    'meta_revision' => 'Revision #:revisionCount',
    'meta_created' => 'Oprettet :timeLength',
    'meta_created_name' => 'Oprettet :timeLength af :user',
    'meta_updated' => 'Opdateret :timeLength',
    'meta_updated_name' => 'Opdateret :timeLength af :user',
    'entity_select' => 'Entity Select',
    'images' => 'Billeder',
    'my_recent_drafts' => 'Mine seneste kladder',
    'my_recently_viewed' => 'Mine senest viste',
    'no_pages_viewed' => 'Du har ikke besøgt nogle sider',
    'no_pages_recently_created' => 'Ingen sider er blevet oprettet for nyligt',
    'no_pages_recently_updated' => 'Ingen sider er blevet opdateret for nyligt',
    'export' => 'Exporter',
    'export_html' => 'Indeholdt webfil',
    'export_pdf' => 'PDF-fil',
    'export_text' => 'Almindelig tekstfil',

    // Permissions and restrictions
    'permissions' => 'Rettigheder',
    'permissions_intro' => 'Når de er aktiveret, vil disse tilladelser have prioritet over alle indstillede rolletilladelser.',
    'permissions_enable' => 'Aktivér tilpassede tilladelser',
    'permissions_save' => 'Gem tilladelser',

    // Search
    'search_results' => 'Søgeresultater',
    'search_total_results_found' => ':count resultat fundet|:count resultater fundet',
    'search_clear' => 'Ryd søgning',
    'search_no_pages' => 'Ingen sider matchede søgning',
    'search_for_term' => 'Søgning for :term',
    'search_more' => 'Flere resultater',
    'search_filters' => 'Søgefiltre',
    'search_content_type' => 'Indholdstype',
    'search_exact_matches' => 'Nøjagtige matches',
    'search_tags' => 'Tag Searches',
    'search_options' => 'Indstillinger',
    'search_viewed_by_me' => 'Set af mig',
    'search_not_viewed_by_me' => 'Ikke set af mig',
    'search_permissions_set' => 'Rettigheders sæt',
    'search_created_by_me' => 'Oprettet af mig',
    'search_updated_by_me' => 'Opdateret af mig',
    'search_date_options' => 'Date Options',
    'search_updated_before' => 'Opdateret før',
    'search_updated_after' => 'Updated after',
    'search_created_before' => 'Oprettet før',
    'search_created_after' => 'Oprettet efter',
    'search_set_date' => 'Set Date',
    'search_update' => 'Update Search',

    // Shelves
    'shelf' => 'Hylde',
    'shelves' => 'Hylder',
    'x_shelves' => ':count Shelf|:count Shelves',
    'shelves_long' => 'Boghylder',
    'shelves_empty' => 'Ingen hylder er blevet oprettet',
    'shelves_create' => 'Opret ny hylde',
    'shelves_popular' => 'Populære hylder',
    'shelves_new' => 'Nye hylder',
    'shelves_new_action' => 'Ny hylde',
    'shelves_popular_empty' => 'De mest populære hylder vil blive vist her.',
    'shelves_new_empty' => 'De nyeste hylder vil blive vist her.',
    'shelves_save' => 'Gem hylde',
    'shelves_books' => 'Bøger på denne hylde',
    'shelves_add_books' => 'Tilføj bøger til denne hylde',
    'shelves_drag_books' => 'Træk bog her for at tilføje dem til denne hylde',
    'shelves_empty_contents' => 'Denne hylde har ingen bøger tilknyttet til den',
    'shelves_edit_and_assign' => 'Rediger hylder for at tilføje bøger',
    'shelves_edit_named' => 'Rediger hylde :name',
    'shelves_edit' => 'Rediger hylde',
    'shelves_delete' => 'Delete Bookshelf',
    'shelves_delete_named' => 'Delete Bookshelf :name',
    'shelves_delete_explain' => "This will delete the bookshelf with the name ':name'. Contained books will not be deleted.",
    'shelves_delete_confirmation' => 'Are you sure you want to delete this bookshelf?',
    'shelves_permissions' => 'Bookshelf Permissions',
    'shelves_permissions_updated' => 'Bookshelf Permissions Updated',
    'shelves_permissions_active' => 'Bookshelf Permissions Active',
    'shelves_copy_permissions_to_books' => 'Copy Permissions to Books',
    'shelves_copy_permissions' => 'Copy Permissions',
    'shelves_copy_permissions_explain' => 'This will apply the current permission settings of this bookshelf to all books contained within. Before activating, ensure any changes to the permissions of this bookshelf have been saved.',
    'shelves_copy_permission_success' => 'Bookshelf permissions copied to :count books',

    // Books
    'book' => 'Bog',
    'books' => 'Bøger',
    'x_books' => ':count Book|:count Books',
    'books_empty' => 'No books have been created',
    'books_popular' => 'Popular Books',
    'books_recent' => 'Recent Books',
    'books_new' => 'Nye bøger',
    'books_new_action' => 'Ny bog',
    'books_popular_empty' => 'The most popular books will appear here.',
    'books_new_empty' => 'The most recently created books will appear here.',
    'books_create' => 'Lav en ny bog',
    'books_delete' => 'Slet bog',
    'books_delete_named' => 'Delete Book :bookName',
    'books_delete_explain' => 'This will delete the book with the name \':bookName\'. All pages and chapters will be removed.',
    'books_delete_confirmation' => 'Are you sure you want to delete this book?',
    'books_edit' => 'Rediger bog',
    'books_edit_named' => 'Edit Book :bookName',
    'books_form_book_name' => 'Book Name',
    'books_save' => 'Gem bog',
    'books_permissions' => 'Book Permissions',
    'books_permissions_updated' => 'Book Permissions Updated',
    'books_empty_contents' => 'No pages or chapters have been created for this book.',
    'books_empty_create_page' => 'Opret en ny side',
    'books_empty_sort_current_book' => 'Sort the current book',
    'books_empty_add_chapter' => 'Tilføj et kapitel',
    'books_permissions_active' => 'Book Permissions Active',
    'books_search_this' => 'Search this book',
    'books_navigation' => 'Book Navigation',
    'books_sort' => 'Sort Book Contents',
    'books_sort_named' => 'Sort Book :bookName',
    'books_sort_name' => 'Sortér efter navn',
    'books_sort_created' => 'Sort by Created Date',
    'books_sort_updated' => 'Sort by Updated Date',
    'books_sort_chapters_first' => 'Chapters First',
    'books_sort_chapters_last' => 'Chapters Last',
    'books_sort_show_other' => 'Show Other Books',
    'books_sort_save' => 'Save New Order',

    // Chapters
    'chapter' => 'Chapter',
    'chapters' => 'Chapters',
    'x_chapters' => ':count Chapter|:count Chapters',
    'chapters_popular' => 'Popular Chapters',
    'chapters_new' => 'New Chapter',
    'chapters_create' => 'Create New Chapter',
    'chapters_delete' => 'Slet kapitel',
    'chapters_delete_named' => 'Slet kapitel :chapterName',
    'chapters_delete_explain' => 'This will delete the chapter with the name \':chapterName\'. All pages will be removed and added directly to the parent book.',
    'chapters_delete_confirm' => 'Er du sikker på du vil slette dette kapitel?',
    'chapters_edit' => 'Rediger kapitel',
    'chapters_edit_named' => 'Rediger kapitel :chapterName',
    'chapters_save' => 'Gem kapitel',
    'chapters_move' => 'Flyt kapitel',
    'chapters_move_named' => 'Flyt kapitel :chapterName',
    'chapter_move_success' => 'Chapter moved to :bookName',
    'chapters_permissions' => 'Chapter Permissions',
    'chapters_empty' => 'No pages are currently in this chapter.',
    'chapters_permissions_active' => 'Chapter Permissions Active',
    'chapters_permissions_success' => 'Chapter Permissions Updated',
    'chapters_search_this' => 'Search this chapter',

    // Pages
    'page' => 'Side',
    'pages' => 'Sider',
    'x_pages' => ':count Side|:count Sider',
    'pages_popular' => 'Popular Pages',
    'pages_new' => 'Ny side',
    'pages_attachments' => 'Attachments',
    'pages_navigation' => 'Page Navigation',
    'pages_delete' => 'Slet side',
    'pages_delete_named' => 'Slet side :pageName',
    'pages_delete_draft_named' => 'Delete Draft Page :pageName',
    'pages_delete_draft' => 'Delete Draft Page',
    'pages_delete_success' => 'Side slettet',
    'pages_delete_draft_success' => 'Draft page deleted',
    'pages_delete_confirm' => 'Er du sikker på, du vil slette denne side?',
    'pages_delete_draft_confirm' => 'Are you sure you want to delete this draft page?',
    'pages_editing_named' => 'Editing Page :pageName',
    'pages_edit_draft_options' => 'Draft Options',
    'pages_edit_save_draft' => 'Save Draft',
    'pages_edit_draft' => 'Edit Page Draft',
    'pages_editing_draft' => 'Editing Draft',
    'pages_editing_page' => 'Editing Page',
    'pages_edit_draft_save_at' => 'Draft saved at ',
    'pages_edit_delete_draft' => 'Delete Draft',
    'pages_edit_discard_draft' => 'Discard Draft',
    'pages_edit_set_changelog' => 'Set Changelog',
    'pages_edit_enter_changelog_desc' => 'Enter a brief description of the changes you\'ve made',
    'pages_edit_enter_changelog' => 'Enter Changelog',
    'pages_save' => 'Gem siden',
    'pages_title' => 'Overskrift',
    'pages_name' => 'Sidenavn',
    'pages_md_editor' => 'Editor',
    'pages_md_preview' => 'Forhåndsvisning',
    'pages_md_insert_image' => 'Indsæt billede',
    'pages_md_insert_link' => 'Insert Entity Link',
    'pages_md_insert_drawing' => 'Indsæt tegning',
    'pages_not_in_chapter' => 'Page is not in a chapter',
    'pages_move' => 'Flyt side',
    'pages_move_success' => 'Flyt side til ":parentName"',
    'pages_copy' => 'Kopier side',
    'pages_copy_desination' => 'Copy Destination',
    'pages_copy_success' => 'Page successfully copied',
    'pages_permissions' => 'Page Permissions',
    'pages_permissions_success' => 'Page permissions updated',
    'pages_revision' => 'Revision',
    'pages_revisions' => 'Page Revisions',
    'pages_revisions_named' => 'Page Revisions for :pageName',
    'pages_revision_named' => 'Page Revision for :pageName',
    'pages_revisions_created_by' => 'Oprettet af',
    'pages_revisions_date' => 'Revision Date',
    'pages_revisions_number' => '#',
    'pages_revisions_numbered' => 'Revision #:id',
    'pages_revisions_numbered_changes' => 'Revision #:id Changes',
    'pages_revisions_changelog' => 'Changelog',
    'pages_revisions_changes' => 'Ændringer',
    'pages_revisions_current' => 'Nuværende version',
    'pages_revisions_preview' => 'Forhåndsvisning',
    'pages_revisions_restore' => 'Gendan',
    'pages_revisions_none' => 'This page has no revisions',
    'pages_copy_link' => 'Kopier link',
    'pages_edit_content_link' => 'Redigér indhold',
    'pages_permissions_active' => 'Page Permissions Active',
    'pages_initial_revision' => 'Initial publish',
    'pages_initial_name' => 'Ny side',
    'pages_editing_draft_notification' => 'You are currently editing a draft that was last saved :timeDiff.',
    'pages_draft_edited_notification' => 'This page has been updated by since that time. It is recommended that you discard this draft.',
    'pages_draft_edit_active' => [
        'start_a' => ':count users have started editing this page',
        'start_b' => ':userName has started editing this page',
        'time_a' => 'since the page was last updated',
        'time_b' => 'in the last :minCount minutes',
        'message' => ':start :time. Take care not to overwrite each other\'s updates!',
    ],
    'pages_draft_discarded' => 'Draft discarded, The editor has been updated with the current page content',
    'pages_specific' => 'Specifik side',
    'pages_is_template' => 'Sideskabelon',

    // Editor Sidebar
    'page_tags' => 'Page Tags',
    'chapter_tags' => 'Chapter Tags',
    'book_tags' => 'Book Tags',
    'shelf_tags' => 'Shelf Tags',
    'tag' => 'Tag',
    'tags' =>  'Tags',
    'tag_name' =>  'Tag Name',
    'tag_value' => 'Tag Value (Optional)',
    'tags_explain' => "Add some tags to better categorise your content. \n You can assign a value to a tag for more in-depth organisation.",
    'tags_add' => 'Add another tag',
    'tags_remove' => 'Remove this tag',
    'attachments' => 'Attachments',
    'attachments_explain' => 'Upload some files or attach some links to display on your page. These are visible in the page sidebar.',
    'attachments_explain_instant_save' => 'Changes here are saved instantly.',
    'attachments_items' => 'Attached Items',
    'attachments_upload' => 'Upload fil',
    'attachments_link' => 'Attach Link',
    'attachments_set_link' => 'Set Link',
    'attachments_delete_confirm' => 'Click delete again to confirm you want to delete this attachment.',
    'attachments_dropzone' => 'Slip filer eller klik her for at vedhæfte en fil',
    'attachments_no_files' => 'Ingen filer er blevet overført',
    'attachments_explain_link' => 'You can attach a link if you\'d prefer not to upload a file. This can be a link to another page or a link to a file in the cloud.',
    'attachments_link_name' => 'Link Name',
    'attachment_link' => 'Attachment link',
    'attachments_link_url' => 'Link til filen',
    'attachments_link_url_hint' => 'Url of site or file',
    'attach' => 'Attach',
    'attachments_edit_file' => 'Rediger fil',
    'attachments_edit_file_name' => 'Filnavn',
    'attachments_edit_drop_upload' => 'Drop files or click here to upload and overwrite',
    'attachments_order_updated' => 'Attachment order updated',
    'attachments_updated_success' => 'Attachment details updated',
    'attachments_deleted' => 'Attachment deleted',
    'attachments_file_uploaded' => 'Filen blev uploadet korrekt',
    'attachments_file_updated' => 'Filen blev opdateret korrekt',
    'attachments_link_attached' => 'Link successfully attached to page',
    'templates' => 'Skabeloner',
    'templates_set_as_template' => 'Page is a template',
    'templates_explain_set_as_template' => 'You can set this page as a template so its contents be utilized when creating other pages. Other users will be able to use this template if they have view permissions for this page.',
    'templates_replace_content' => 'Replace page content',
    'templates_append_content' => 'Append to page content',
    'templates_prepend_content' => 'Prepend to page content',

    // Profile View
    'profile_user_for_x' => 'User for :time',
    'profile_created_content' => 'Created Content',
    'profile_not_created_pages' => ':userName has not created any pages',
    'profile_not_created_chapters' => ':userName has not created any chapters',
    'profile_not_created_books' => ':userName has not created any books',
    'profile_not_created_shelves' => ':userName has not created any shelves',

    // Comments
    'comment' => 'Kommentar',
    'comments' => 'Kommentarer',
    'comment_add' => 'Tilføj kommentar',
    'comment_placeholder' => 'Skriv en kommentar her',
    'comment_count' => '{0} No Comments|{1} 1 Comment|[2,*] :count Comments',
    'comment_save' => 'Gem kommentar',
    'comment_saving' => 'Saving comment...',
    'comment_deleting' => 'Deleting comment...',
    'comment_new' => 'Ny kommentar',
    'comment_created' => 'commented :createDiff',
    'comment_updated' => 'Updated :updateDiff by :username',
    'comment_deleted_success' => 'Kommentar slettet',
    'comment_created_success' => 'Kommentaren er tilføjet',
    'comment_updated_success' => 'Kommentaren er opdateret',
    'comment_delete_confirm' => 'Er du sikker på, at du vil slette denne kommentar?',
    'comment_in_reply_to' => 'In reply to :commentId',

    // Revision
    'revision_delete_confirm' => 'Are you sure you want to delete this revision?',
    'revision_restore_confirm' => 'Are you sure you want to restore this revision? The current page contents will be replaced.',
    'revision_delete_success' => 'Revision deleted',
    'revision_cannot_delete_latest' => 'Cannot delete the latest revision.'
];