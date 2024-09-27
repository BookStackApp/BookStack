<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Crëwyd yn Ddiweddar',
    'recently_created_pages' => 'Tudalennau a Grëwyd yn Ddiweddar',
    'recently_updated_pages' => 'Tudalennau a Ddiweddarwyd yn Ddiweddar',
    'recently_created_chapters' => 'Penodau a Grëwyd yn Ddiweddar',
    'recently_created_books' => 'Llyfrau a Grëwyd yn Ddiweddar',
    'recently_created_shelves' => 'Silffoedd a Grëwyd yn Ddiweddar',
    'recently_update' => 'Diweddarwyd yn Ddiweddar',
    'recently_viewed' => 'Gwelwyd yn Ddiweddar',
    'recent_activity' => 'Gweithgaredd Diweddar',
    'create_now' => 'Creu un nawr',
    'revisions' => 'Diwygiadau',
    'meta_revision' => 'Diwygiad #:revisionCount',
    'meta_created' => 'Crëwyd',
    'meta_created_name' => 'Crëwyd :timeLength gan :user',
    'meta_updated' => 'Diweddarwyd :timeLength',
    'meta_updated_name' => 'Diweddarwyd :timeLength gan :user',
    'meta_owned_name' => 'Mae\'n eiddo i :user',
    'meta_reference_count' => 'Cyfeirir ato gan :count eitem|Cyfeirir ato gan :count o eitemau',
    'entity_select' => 'Dewis Endid',
    'entity_select_lack_permission' => 'Nid oes gennych y caniatâd angenrheidiol i ddewis yr eitem hon',
    'images' => 'Delweddau',
    'my_recent_drafts' => 'Fy Nrafftiau Diweddar',
    'my_recently_viewed' => 'Edrych yn Ddiweddar',
    'my_most_viewed_favourites' => 'Fy Ffefrynnau Mwyaf Poblogaidd',
    'my_favourites' => 'Fy Ffefrynnau',
    'no_pages_viewed' => 'Nid ydych wedi edrych ar unrhyw dudalennau',
    'no_pages_recently_created' => 'Nid oes unrhyw dudalennau wedi\'u creu\'n ddiweddar',
    'no_pages_recently_updated' => 'Nid oes unrhyw dudalennau wedi\'u diweddaru\'n ddiweddar',
    'export' => 'Allforio',
    'export_html' => 'Ffeil Gwe wedi\'i Chynnwys',
    'export_pdf' => 'Ffeil PDF',
    'export_text' => 'Ffeil Testun Plaen',
    'export_md' => 'Ffeil Markdown',
    'default_template' => 'Templed Tudalen Diofyn',
    'default_template_explain' => 'Clustnodwch dempled tudalen a fydd yn cael ei ddefnyddio fel y cynnwys diofyn ar gyfer pob tudalen a grëwyd yn yr eitem hon. Cofiwch y bydd hwn ond yn cael ei ddefnyddio os yw’r sawl a grëodd y dudalen â mynediad gweld i’r dudalen dempled a ddewiswyd.',
    'default_template_select' => 'Dewiswch dudalen templed',

    // Permissions and restrictions
    'permissions' => 'Caniatâd',
    'permissions_desc' => 'Gosodwch ganiatâd yma i ddiystyru\'r caniatâd diofyn a ddarperir gan rolau defnyddwyr.',
    'permissions_book_cascade' => 'Bydd caniatâd a osodir ar lyfrau yn rhaeadru’n awtomatig i benodau a thudalennau plant, oni bai bod ganddynt eu caniatâd diffiniedig eu hunain.',
    'permissions_chapter_cascade' => 'Bydd caniatâd a osodir ar benodau yn rhaeadru’n awtomatig i dudalennau plant, oni bai bod ganddynt eu caniatâd diffiniedig eu hunain.',
    'permissions_save' => 'Cadw Caniatâd',
    'permissions_owner' => 'Perchennog',
    'permissions_role_everyone_else' => 'Pawb arall',
    'permissions_role_everyone_else_desc' => 'Gosod caniatâd ar gyfer pob rôl nad ydynt yn cael eu diystyru\'n benodol.',
    'permissions_role_override' => 'Diystyru caniatâd ar gyfer rôl',
    'permissions_inherit_defaults' => 'Etifeddu rhagosodiadau',

    // Search
    'search_results' => 'Canlyniadau Chwilio',
    'search_total_results_found' => 'Cafwyd :count canlyniad|Cafwyd cyfanswm o :count canlyniad',
    'search_clear' => 'Clirio\'r Chwiliad',
    'search_no_pages' => 'Nid oedd unrhyw dudalennau yn cyfateb â\'r chwiliad hwn',
    'search_for_term' => 'Chwilio am :term',
    'search_more' => 'Mwy o Ganlyniadau',
    'search_advanced' => 'Math o Gynnwys',
    'search_terms' => 'Termau Chwilio',
    'search_content_type' => 'Math o Gynnwys',
    'search_exact_matches' => 'Union Gyfatebiaethau',
    'search_tags' => 'Tagio Chwiliadau',
    'search_options' => 'Opsiynau',
    'search_viewed_by_me' => 'Gwelwyd gennyf fi',
    'search_not_viewed_by_me' => 'Nas gwelwyd gennyf fi',
    'search_permissions_set' => 'Gosod Caniatâd',
    'search_created_by_me' => 'Crëwyd gennyf fi',
    'search_updated_by_me' => 'Diweddarwyd gennyf fi',
    'search_owned_by_me' => 'Yn eiddo i mi',
    'search_date_options' => 'Opsiynau Dyddiad',
    'search_updated_before' => 'Diweddarwyd cyn',
    'search_updated_after' => 'Diweddarwyd ar ôl',
    'search_created_before' => 'Crëwyd cyn',
    'search_created_after' => 'Crëwyd ar ôl',
    'search_set_date' => 'Gosod Dyddiad',
    'search_update' => 'Diweddaru Chwiliad',

    // Shelves
    'shelf' => 'Silff',
    'shelves' => 'Silffau',
    'x_shelves' => ':count Silff|:count Shelves',
    'shelves_empty' => 'Ni chrëwyd unrhyw silffoedd',
    'shelves_create' => 'Creu Silff Newydd',
    'shelves_popular' => 'Silffoedd Poblogaidd',
    'shelves_new' => 'Silffau Newydd',
    'shelves_new_action' => 'Silff Newydd',
    'shelves_popular_empty' => 'Bydd y silffoedd mwyaf poblogaidd yn ymddangos yma.',
    'shelves_new_empty' => 'Bydd y silffoedd a grëwyd fwyaf diweddar yn ymddangos yma.',
    'shelves_save' => 'Cadw Silff',
    'shelves_books' => 'Llyfrau ar y silff hon',
    'shelves_add_books' => 'Ychwanegu llyfrau i\'r silff hon',
    'shelves_drag_books' => 'Llusgwch lyfrau isod i\'w hychwanegu at y silff hon',
    'shelves_empty_contents' => 'Nid oes gan y silff hon unrhyw lyfrau wedi’u clustnodi iddi',
    'shelves_edit_and_assign' => 'Golygu silff i glustnodi llyfrau',
    'shelves_edit_named' => 'Golygu Silff :name',
    'shelves_edit' => 'Golygu Silff',
    'shelves_delete' => 'Dileu Silff',
    'shelves_delete_named' => 'Dileu Silff :name',
    'shelves_delete_explain' => "Bydd hyn yn dileu'r silff gyda'r enw ':name'. Ni fydd llyfrau wedi'u cynnwys yn cael eu dileu.",
    'shelves_delete_confirmation' => 'Ydych chi\'n siŵr eich bod chi eisiau dileu\'r silff hon?',
    'shelves_permissions' => 'Caniatâd Silffoedd',
    'shelves_permissions_updated' => 'Diweddarwyd Caniatâd Silffoedd',
    'shelves_permissions_active' => 'Caniatâd Silffoedd yn Weithredol',
    'shelves_permissions_cascade_warning' => 'Nid yw caniatâd ar silffoedd yn rhaeadru’n awtomatig i lyfrau sydd wedi\'u cynnwys. Mae hyn oherwydd y gall llyfr fodoli ar silffoedd lluosog. Fodd bynnag, gellir copïo caniatâd i lawr i lyfrau plant gan ddefnyddio\'r opsiwn a geir isod.',
    'shelves_permissions_create' => 'Dim ond ar gyfer copïo caniatâd i lyfrau plant y defnyddir caniatâd creu silff gan ddefnyddio\'r camau isod. Nid ydynt yn rheoli\'r gallu i greu llyfrau.',
    'shelves_copy_permissions_to_books' => 'Copïo Caniatâd i Lyfrau',
    'shelves_copy_permissions' => 'Copïo Caniatâd',
    'shelves_copy_permissions_explain' => 'Bydd hyn yn cymhwyso gosodiadau caniatâd presennol y silff hon i bob llyfr sydd wedi\'u cynnwys ynddi. Cyn ysgogi, gwnewch yn siŵr bod unrhyw newidiadau i ganiatâd y silff hon wedi\'u cadw.',
    'shelves_copy_permission_success' => 'Caniatâd silff wedi\'i gopïo i :count o lyfrau',

    // Books
    'book' => 'Llyfr',
    'books' => 'Llyfrau',
    'x_books' => ':count Llyfr|:count o Lyfrau',
    'books_empty' => 'Ni chrëwyd unrhyw llyfrau',
    'books_popular' => 'Llyfrau Poblogaidd',
    'books_recent' => 'Llyfrau Diweddar',
    'books_new' => 'Llyfrau Newydd',
    'books_new_action' => 'Llyfr Newydd',
    'books_popular_empty' => 'Bydd y llyfrau mwyaf poblogaidd yn ymddangos yma.',
    'books_new_empty' => 'Bydd y llyfrau a grëwyd fwyaf diweddar yn ymddangos yma.',
    'books_create' => 'Creu Llyfr Newydd',
    'books_delete' => 'Dileu Llyfr',
    'books_delete_named' => 'Dileu :bookName Llyfr',
    'books_delete_explain' => 'Bydd hyn yn dileu\'r llyfr gyda\'r enw ‘:bookName’. Bydd yr holl dudalennau a phenodau yn cael eu dileu.',
    'books_delete_confirmation' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r llyfr hwn?',
    'books_edit' => 'Golygu\'r Llyfr',
    'books_edit_named' => 'Golygu :bookName Llyfr',
    'books_form_book_name' => 'Enw\'r Llyfr',
    'books_save' => 'Cadw Llyfr',
    'books_permissions' => 'Caniatâd Llyfr',
    'books_permissions_updated' => 'Diweddarwyd Caniatâd Llyfr',
    'books_empty_contents' => 'Ni chrëwyd unrhyw dudalennau neu benodau ar gyfer y llyfr hwn.',
    'books_empty_create_page' => 'Creu tudalen newydd',
    'books_empty_sort_current_book' => 'Trefnu’r llyfr presennol',
    'books_empty_add_chapter' => 'Ychwanegu pennod',
    'books_permissions_active' => 'Caniatâd Llyfr yn Weithredol',
    'books_search_this' => 'Chwilio\'r llyfr hwn',
    'books_navigation' => 'Llywio Llyfr',
    'books_sort' => 'Trefnu Cynnwys Llyfr',
    'books_sort_desc' => 'Symudwch benodau a thudalennau o fewn llyfr i ad-drefnu ei gynnwys. Gellir ychwanegu llyfrau eraill sy\'n caniatáu symud penodau a thudalennau yn hawdd rhwng llyfrau.',
    'books_sort_named' => 'Trefnu Llyfr :bookName',
    'books_sort_name' => 'Trefnu yn ôl Enw',
    'books_sort_created' => 'Trefnu yn ôl Dyddiad Creu',
    'books_sort_updated' => 'Trefnu yn ôl Dyddiad Diweddaru',
    'books_sort_chapters_first' => 'Penodau yn Gyntaf',
    'books_sort_chapters_last' => 'Penodau yn Olaf',
    'books_sort_show_other' => 'Dangos Llyfrau Eraill',
    'books_sort_save' => 'Cadw’r Drefn Newydd',
    'books_sort_show_other_desc' => 'Ychwanegwch lyfrau eraill yma i\'w cynnwys yn y gwaith didoli, a chaniatáu ad-drefnu hawdd rhwng llyfrau.',
    'books_sort_move_up' => 'Symud i Fyny',
    'books_sort_move_down' => 'Symud i Lawr',
    'books_sort_move_prev_book' => 'Symud i\'r Llyfr Blaenorol',
    'books_sort_move_next_book' => 'Symud i\'r Llyfr Nesaf',
    'books_sort_move_prev_chapter' => 'Symud i\'r Bennod Flaenorol',
    'books_sort_move_next_chapter' => 'Symud i\'r Bennod Nesaf',
    'books_sort_move_book_start' => 'Symud i Ddechrau\'r Llyfr',
    'books_sort_move_book_end' => 'Symud i Ddiwedd y Llyfr',
    'books_sort_move_before_chapter' => 'Symud i’r Bennod Cynt',
    'books_sort_move_after_chapter' => 'Symud i’r Bennod Ddilynol',
    'books_copy' => 'Copio Llyfr',
    'books_copy_success' => 'Llyfr wedi\'i copio\'n llwyddiannus',

    // Chapters
    'chapter' => 'Pennod',
    'chapters' => 'Penodau',
    'x_chapters' => ':count Pennod|:count Penodau',
    'chapters_popular' => 'Penodau Poblogaidd',
    'chapters_new' => 'Pennod Newydd',
    'chapters_create' => 'Creu Pennod Newydd',
    'chapters_delete' => 'Dileu Pennod',
    'chapters_delete_named' => 'Dileu :chapterName Pennod',
    'chapters_delete_explain' => 'Bydd hyn yn dileu\'r bennod gyda\'r enw \':chapterName\'. Bydd yr holl dudalennau sy\'n bodoli yn y bennod hon hefyd yn cael eu dileu.',
    'chapters_delete_confirm' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r bennod hon?',
    'chapters_edit' => 'Ychwanegu Pennod',
    'chapters_edit_named' => 'Ychwanegu Pennod :chapterName',
    'chapters_save' => 'Cadw Pennod',
    'chapters_move' => 'Symud Pennod',
    'chapters_move_named' => 'Symud Pennod :chapterName',
    'chapters_copy' => 'Copïo Pennod',
    'chapters_copy_success' => 'Pennod wedi\'i chopïo\'n llwyddiannus',
    'chapters_permissions' => 'Pennau Taith Pennod',
    'chapters_empty' => 'Does dim tudalennau yn y bennod hon ar hyn o bryd.',
    'chapters_permissions_active' => 'Caniatâd Pennod yn Weithredol',
    'chapters_permissions_success' => 'Diweddarwyd Caniatâd Pennod',
    'chapters_search_this' => 'Chwilio yn y bennod hon',
    'chapter_sort_book' => 'Trefnu Llyfr',

    // Pages
    'page' => 'Tudalen',
    'pages' => 'Tudalennau',
    'x_pages' => ':count Tudalen|:count Tudalennau',
    'pages_popular' => 'Tudalennau Poblogaidd',
    'pages_new' => 'Tudalen Newydd',
    'pages_attachments' => 'Atodiadau',
    'pages_navigation' => 'Llywio Tudalen',
    'pages_delete' => 'Dileu Tudalen',
    'pages_delete_named' => 'Dileu :pageName Tudalen',
    'pages_delete_draft_named' => 'Dileu Tudalen Ddrafft :pageName',
    'pages_delete_draft' => 'Dileu Tudalen Ddrafft',
    'pages_delete_success' => 'Tudalen wedi\'i dileu',
    'pages_delete_draft_success' => 'Tudalen ddrafft wedi’i dileu',
    'pages_delete_warning_template' => 'Mae\'r dudalen hon yn cael ei defnyddio\'n weithredol fel templed tudalen diofyn llyfr neu bennod. Ni fydd gan y llyfrau neu\'r penodau hyn dempled tudalen diofyn wedi\'i glustnodi ar ôl dileu\'r dudalen hon.',
    'pages_delete_confirm' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r dudalen hon?',
    'pages_delete_draft_confirm' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r dudalen ddrafft hon?',
    'pages_editing_named' => 'Golygu Tudalen :pageName',
    'pages_edit_draft_options' => 'Opsiynau Drafft',
    'pages_edit_save_draft' => 'Cadw Drafft',
    'pages_edit_draft' => 'Golygu Tudalen Ddrafft',
    'pages_editing_draft' => 'Golygu Drafft',
    'pages_editing_page' => 'Golygu Tudalen',
    'pages_edit_draft_save_at' => 'Cadwyd drafft ar ',
    'pages_edit_delete_draft' => 'Dileu Drafft',
    'pages_edit_delete_draft_confirm' => 'Ydych chi\'n siŵr eich bod am ddileu eich newidiadau i’r dudalen ddrafft? Bydd eich holl newidiadau, ers eu cadw ddiwethaf, yn cael eu colli a bydd y golygydd yn cael ei ddiweddaru gyda\'r dudalen ddiweddaraf nad yw\'n ddrafft.',
    'pages_edit_discard_draft' => 'Gwaredu Drafft',
    'pages_edit_switch_to_markdown' => 'Newid i’r Golygydd Markdown',
    'pages_edit_switch_to_markdown_clean' => '(Cynnwys Glân)',
    'pages_edit_switch_to_markdown_stable' => '(Cynnwys Glân)',
    'pages_edit_switch_to_wysiwyg' => 'Newid i Olygydd WYSIWYG',
    'pages_edit_switch_to_new_wysiwyg' => 'Switch to new WYSIWYG',
    'pages_edit_switch_to_new_wysiwyg_desc' => '(In Alpha Testing)',
    'pages_edit_set_changelog' => 'Gosod Changelog',
    'pages_edit_enter_changelog_desc' => 'Rhowch ddisgrifiad byr o\'r newidiadau rydych wedi\'u gwneud',
    'pages_edit_enter_changelog' => 'Cofnodwch Changelog',
    'pages_editor_switch_title' => 'Newid Golygydd',
    'pages_editor_switch_are_you_sure' => 'Ydych chi\'n siŵr eich bod eisiau newid y golygydd ar gyfer y dudalen hon?',
    'pages_editor_switch_consider_following' => 'Ystyriwch y canlynol wrth newid golygyddion:',
    'pages_editor_switch_consideration_a' => 'Ar ôl ei gadw, bydd yr opsiwn golygydd newydd yn cael ei ddefnyddio gan unrhyw olygydd yn y dyfodol, gan gynnwys y rhai na fyddant efallai\'n gallu newid y math o olygydd eu hunain.',
    'pages_editor_switch_consideration_b' => 'Gall hyn arwain at golli manylion a Syntax mewn rhai amgylchiadau.',
    'pages_editor_switch_consideration_c' => 'Ni fydd newidiadau tag neu changelog, a wnaed ers eu cadw ddiwethaf, yn parhau ar draws y newid hwn.',
    'pages_save' => 'Cadw Tudalen',
    'pages_title' => 'Teitl y Dudalen',
    'pages_name' => 'Enw\'r Dudalen',
    'pages_md_editor' => 'Golygydd',
    'pages_md_preview' => 'Rhagolwg',
    'pages_md_insert_image' => 'Mewnosod Delwedd',
    'pages_md_insert_link' => 'Mewnosod Dolen Endid',
    'pages_md_insert_drawing' => 'Mewnosod Llun',
    'pages_md_show_preview' => 'Dangos rhagolwg',
    'pages_md_sync_scroll' => 'Cydamseru sgrôl ragolwg',
    'pages_drawing_unsaved' => 'Canfuwyd Llun heb ei Gadw',
    'pages_drawing_unsaved_confirm' => 'Canfuwyd data llun heb ei gadw o ymgais aflwyddiannus blaenorol i gadw llun. Hoffech chi adfer a pharhau i olygu\'r llun heb ei gadw?',
    'pages_not_in_chapter' => 'Nid yw\'r dudalen mewn pennod',
    'pages_move' => 'Symud Tudalen',
    'pages_copy' => 'Copïo Tudalen',
    'pages_copy_desination' => 'Copïo Cyrchfan',
    'pages_copy_success' => 'Tudalen wedi\'i chreu\'n llwyddiannus',
    'pages_permissions' => 'Pennau Taith Tudalen',
    'pages_permissions_success' => 'Pennau taith tudalen wedi\'u diweddaru',
    'pages_revision' => 'Diwygiad',
    'pages_revisions' => 'Diwygiadau\'r Dudalen',
    'pages_revisions_desc' => 'Isod ceir holl ddiwygiadau blaenorol y dudalen hon. Gallwch edrych yn ôl ar, cymharu, ac adfer hen fersiynau o’r dudalen os oes gennych y caniatâd priodol. Efallai na fydd hanes llawn y dudalen yn cael ei adlewyrchu\'n llawn yma oherwydd, gan ddibynnu ar ffurfweddiad y system, gallai hen fersiynau fod wedi’u dileu’n awtomatig.',
    'pages_revisions_named' => 'Diwygiadau Tudalen ar gyfer :pageName',
    'pages_revision_named' => 'Diwygiad Tudalen ar gyfer :pageName',
    'pages_revision_restored_from' => 'Adferwyd o #:id; :summary',
    'pages_revisions_created_by' => 'Crëwyd gan',
    'pages_revisions_date' => 'Dyddiad Adolygu',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Rhif Diwygiad',
    'pages_revisions_numbered' => 'Diwygiad #:id',
    'pages_revisions_numbered_changes' => 'Diwygiad #:id Newidiadau',
    'pages_revisions_editor' => 'Math o Olygydd',
    'pages_revisions_changelog' => 'Changelog',
    'pages_revisions_changes' => 'Newidiadau',
    'pages_revisions_current' => 'Fersiwn Bresennol',
    'pages_revisions_preview' => 'Rhagolwg',
    'pages_revisions_restore' => 'Adfer',
    'pages_revisions_none' => 'Nid oes gan y dudalen hon unrhyw ddiwygiadau',
    'pages_copy_link' => 'Copïo Dolen',
    'pages_edit_content_link' => 'Neidio i\'r adran yn y golygydd',
    'pages_pointer_enter_mode' => 'Rhowch y modd dethol adran',
    'pages_pointer_label' => 'Dewisiadau Adran Tudalen',
    'pages_pointer_permalink' => 'Dolen Barhaol Adran Tudalen',
    'pages_pointer_include_tag' => 'Adran Tudalen Cynnwys Tag',
    'pages_pointer_toggle_link' => 'Modd dolen barhaol, Pwyswch i ddangos cynnwys tag',
    'pages_pointer_toggle_include' => 'Modd cynnwys tag, Pwyswch i ddangos dolen barhaol',
    'pages_permissions_active' => 'Caniatâd Tudalen yn Weithredol',
    'pages_initial_revision' => 'Cyhoeddi cychwynnol',
    'pages_references_update_revision' => 'Diweddariad awtomatig y system o ddolenni mewnol',
    'pages_initial_name' => 'Tudalen Newydd',
    'pages_editing_draft_notification' => 'Rydych chi wrthi’n golygu drafft a gafodd ei gadw ddiwethaf ar :timeDiff.',
    'pages_draft_edited_notification' => 'Mae\'r dudalen hon wedi\'i diweddaru ers hynny. Argymhellir eich bod yn dileu\'r drafft hwn.',
    'pages_draft_page_changed_since_creation' => 'Mae\'r dudalen hon wedi\'i diweddaru ers i\'r drafft hwn gael ei greu. Argymhellir eich bod yn dileu\'r drafft hwn neu\'n sicrhau nad ydych yn ysgrifennu unrhyw newidiadau i’r dudalen.',
    'pages_draft_edit_active' => [
        'start_a' => 'Mae :count defnyddiwr wedi dechrau golygu\'r dudalen hon',
        'start_b' => 'Mae :userName wedi dechrau golygu\'r dudalen hon',
        'time_a' => 'ers i\'r dudalen gael ei diweddaru ddiwethaf',
        'time_b' => 'yn y :minCount munud diwethaf',
        'message' => ':start :time. Gofalwch beidio ag ysgrifennu dros ddiweddariadau eich gilydd!',
    ],
    'pages_draft_discarded' => 'Drafft wedi\'i waredu! Mae\'r golygydd wedi\'i ddiweddaru gyda chynnwys presennol y dudalen',
    'pages_draft_deleted' => 'Drafft wedi\'i ddileu! Mae\'r golygydd wedi\'i ddiweddaru gyda chynnwys presennol y dudalen',
    'pages_specific' => 'Tudalen Benodol',
    'pages_is_template' => 'Templed Tudalen',

    // Editor Sidebar
    'toggle_sidebar' => 'Toglo Bar ochr',
    'page_tags' => 'Tagiau Tudalennau',
    'chapter_tags' => 'Tagiau Penodau',
    'book_tags' => 'Tagiau Llyfrau',
    'shelf_tags' => 'Tagiau Silffoedd',
    'tag' => 'Tag',
    'tags' =>  'Tagiau',
    'tags_index_desc' => 'Gellir cymhwyso tagiau i gynnwys o fewn y system i sicrhau categoreiddio hyblyg. Gall tagiau fod ag allwedd a gwerth, gyda\'r gwerth yn ddewisol. Ar ôl ei gymhwyso, gellir cwestiynu’r cynnwys gan ddefnyddio enw a gwerth y tag.',
    'tag_name' =>  'Enw’r Tag',
    'tag_value' => 'Gwerth y Tag (Dewisol)',
    'tags_explain' => "Ychwanegwch rai tagiau i gategoreiddio'ch cynnwys yn well. Gallwch glustnodi gwerth i dag i gael trefn fanylach.",
    'tags_add' => 'Ychwanegu tag arall',
    'tags_remove' => 'Tynnu’r tag hwn',
    'tags_usages' => 'Cyfanswm y defnydd o’r tag',
    'tags_assigned_pages' => 'Clustnodwyd i Dudalennau',
    'tags_assigned_chapters' => 'Clustnodwyd i Benodau',
    'tags_assigned_books' => 'Clustnodwyd i Lyfrau',
    'tags_assigned_shelves' => 'Clustnodwyd i Silffoedd',
    'tags_x_unique_values' => ':count gwerthoedd unigryw',
    'tags_all_values' => 'Pob gwerth',
    'tags_view_tags' => 'Gweld Tagiau',
    'tags_view_existing_tags' => 'Gweld tagiau presennol',
    'tags_list_empty_hint' => 'Gellir clustnodi tagiau trwy far ochr golygydd y dudalen neu wrth olygu manylion llyfr, pennod neu silff.',
    'attachments' => 'Atodiadau',
    'attachments_explain' => 'Uwchlwytho rhai ffeiliau neu atodi rhai dolenni i\'w harddangos ar eich tudalen. Mae\'r rhain i\'w gweld ym mar ochr y dudalen.',
    'attachments_explain_instant_save' => 'Caiff y newidiadau yma eu cadw ar unwaith.',
    'attachments_upload' => 'Uwchlwytho Ffeil',
    'attachments_link' => 'Atodi Dolen',
    'attachments_upload_drop' => 'Neu gallwch lusgo a gollwng ffeil yma i\'w huwchlwytho fel atodiad.',
    'attachments_set_link' => 'Gosod Dolen',
    'attachments_delete' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r atodiad hwn?',
    'attachments_dropzone' => 'Gollyngwch ffeiliau yma i\'w huwchlwytho',
    'attachments_no_files' => 'Nid oes unrhyw ffeiliau wedi\'u huwchlwytho',
    'attachments_explain_link' => 'Gallwch atodi dolen pe bai’n well gennych beidio ag uwchlwytho ffeil. Gall hyn fod yn ddolen i dudalen arall neu\'n ddolen i ffeil yn y cwmwl.',
    'attachments_link_name' => 'Enw’r Ddolen',
    'attachment_link' => 'Dolen atodiad',
    'attachments_link_url' => 'Dolen i ffeil',
    'attachments_link_url_hint' => 'Url y safle neu ffeil',
    'attach' => 'Atodi',
    'attachments_insert_link' => 'Ychwanegu Dolen Atodiad i Dudalen',
    'attachments_edit_file' => 'Golygu Ffeil',
    'attachments_edit_file_name' => 'Enw\'r Ffeil',
    'attachments_edit_drop_upload' => 'Gollwng ffeiliau neu glicio yma i uwchlwytho ac arysgrifennu',
    'attachments_order_updated' => 'Trefn atodiad wedi’i diweddaru',
    'attachments_updated_success' => 'Manylion yr atodiad wedi\'u diweddaru',
    'attachments_deleted' => 'Atodiad wedi’i ddileu',
    'attachments_file_uploaded' => 'Ffeil wedi\'i huwchwytho\'n llwyddiannus',
    'attachments_file_updated' => 'Ffeil wedi\'i diweddaru’n llwyddiannus',
    'attachments_link_attached' => 'Dolen wedi\'i atodi’n llwyddiannus i\'r dudalen',
    'templates' => 'Templedi',
    'templates_set_as_template' => 'Mae\'r dudalen yn dempled',
    'templates_explain_set_as_template' => 'Gallwch osod y dudalen hon fel templed er mwyn gallu defnyddio ei chynnwys wrth greu tudalennau eraill. Bydd modd i ddefnyddwyr eraill ddefnyddio\'r templed hwn os oes ganddynt ganiatâd gweld ar gyfer y dudalen hon.',
    'templates_replace_content' => 'Disodli cynnwys tudalen',
    'templates_append_content' => 'Atodi i gynnwys tudalen',
    'templates_prepend_content' => 'Rhagarweiniad i gynnwys tudalen',

    // Profile View
    'profile_user_for_x' => 'Defnyddiwr am :time',
    'profile_created_content' => 'Cynnwys a Grëwyd',
    'profile_not_created_pages' => 'Nid yw :userName wedi creu unrhyw dudalennau',
    'profile_not_created_chapters' => 'Nid yw :userName wedi creu unrhyw benodau',
    'profile_not_created_books' => 'Nid yw :userName wedi creu unrhyw lyfrau',
    'profile_not_created_shelves' => 'Nid yw :userName wedi creu unrhyw silffoedd',

    // Comments
    'comment' => 'Sylw',
    'comments' => 'Sylwadau',
    'comment_add' => 'Ychwanegu Sylw',
    'comment_placeholder' => 'Gadewch sylw yma',
    'comment_count' => '{0} Dim sylwadau|{1} 1 Sylw| [2,*] :count Sylwadau',
    'comment_save' => 'Cadw Sylw',
    'comment_new' => 'Sylw Newydd',
    'comment_created' => 'sylwodd :createDiff',
    'comment_updated' => 'Diweddarwyd :update gan :username',
    'comment_updated_indicator' => 'Diweddarwyd',
    'comment_deleted_success' => 'Dilëwyd sylw',
    'comment_created_success' => 'Ychwanegwyd sylw',
    'comment_updated_success' => 'Diweddarwyd sylw',
    'comment_delete_confirm' => 'Ydych chi\'n siwr eich bod eisiau dileu\'r sylw hwn?',
    'comment_in_reply_to' => 'Mewn ymateb i :commentId',
    'comment_editor_explain' => 'Dyma\'r sylwadau sydd wedi eu gadael ar y dudalen hon. Gellir ychwanegu a rheoli sylwadau wrth edrych ar y dudalen a gadwyd.',

    // Revision
    'revision_delete_confirm' => 'Ydych chi\'n siŵr eich bod eisiau dileu\'r adolygiad hwn?',
    'revision_restore_confirm' => 'Ydych chi\'n siŵr eich bod eisiau adfer yr adolygiad hwn? Bydd cynnwys presennol y dudalen yn cael ei newid.',
    'revision_cannot_delete_latest' => 'Ni ellir dileu\'r adolygiad diweddaraf.',

    // Copy view
    'copy_consider' => 'Ystyriwch yr isod wrth gopïo cynnwys.',
    'copy_consider_permissions' => 'Ni fydd gosodiadau caniatâd personol yn cael eu copïo.',
    'copy_consider_owner' => 'Byddwch yn dod yn berchennog yr holl gynnwys sydd wedi’i gopïo.',
    'copy_consider_images' => 'Ni fydd ffeiliau delwedd tudalen yn cael eu dyblygu a bydd y delweddau gwreiddiol yn cadw eu perthynas â\'r dudalen y cawsant eu huwchlwytho yn wreiddiol iddi.',
    'copy_consider_attachments' => 'Ni fydd atodiadau tudalen yn cael eu copïo.',
    'copy_consider_access' => 'Gall newid lleoliad, perchennog neu ganiatâd olygu bod y cynnwys hwn yn hygyrch i\'r rhai nad oedd ganddynt fynediad o\'r blaen.',

    // Conversions
    'convert_to_shelf' => 'Trosi i Silff',
    'convert_to_shelf_contents_desc' => 'Gallwch drosi\'r llyfr hwn i silff newydd gyda\'r un cynnwys. Bydd penodau yn y llyfr hwn yn cael eu trosi i lyfrau newydd. Os yw\'r llyfr hwn yn cynnwys unrhyw dudalennau, nad ydynt mewn pennod, bydd y llyfr hwn yn cael ei ailenwi ac yn cynnwys tudalennau o\'r fath, a bydd y llyfr hwn yn dod yn rhan o\'r silff newydd.',
    'convert_to_shelf_permissions_desc' => 'Bydd unrhyw ganiatâd a osodir ar y llyfr hwn yn cael ei gopïo i\'r silff newydd ac i bob llyfr plentyn newydd nad oes ganddynt eu caniatâd eu hunain. Noder nad yw caniatâd ar silffoedd yn rhaeadru’n awtomatig i’r cynnwys oddi mewn, fel y maent ar gyfer llyfrau.',
    'convert_book' => 'Trosi Llyfr',
    'convert_book_confirm' => 'Ydych chi\'n siwr eich bod eisiau trosi’r llyfr hwn?',
    'convert_undo_warning' => 'Ni ellir dad-wneud hyn mor hawdd.',
    'convert_to_book' => 'Trosi i Lyfr',
    'convert_to_book_desc' => 'Gallwch drosi\'r bennod hon i lyfr newydd gyda\'r un cynnwys. Bydd unrhyw ganiatâd a osodir ar y bennod hon yn cael ei gopïo i\'r llyfr newydd ond ni fydd unrhyw ganiatâd a etifeddir o\'r llyfr rhiant yn cael ei gopïo, a allai arwain at newid rheolaeth mynediad.',
    'convert_chapter' => 'Trosi Pennod',
    'convert_chapter_confirm' => 'Ydych chi\'n siŵr eich bod eisiau trosi’r bennod hon?',

    // References
    'references' => 'Cyfeirnodau',
    'references_none' => 'Nid oes unrhyw gyfeirnodau wedi\'u holrhain ar gyfer yr eitem hon.',
    'references_to_desc' => 'Isod ceir yr holl gynnwys hysbys yn y system sy\'n cysylltu â\'r eitem hon.',

    // Watch Options
    'watch' => 'Gwylio',
    'watch_title_default' => 'Dewisiadau Diofyn',
    'watch_desc_default' => 'Newid i weld eich dewisiadau hysbysu diofyn yn unig.',
    'watch_title_ignore' => 'Anwybyddu',
    'watch_desc_ignore' => 'Anwybyddu pob hysbysiad, gan gynnwys y rhai o ddewisiadau lefel defnyddiwr.',
    'watch_title_new' => 'Tudalennau Newydd',
    'watch_desc_new' => 'Rhoi gwybod pan fydd unrhyw dudalen newydd yn cael ei chreu yn yr eitem hon.',
    'watch_title_updates' => 'Diweddariadau Pob Tudalen',
    'watch_desc_updates' => 'Hysbysu am bob tudalen newydd a newid i dudalennau.',
    'watch_desc_updates_page' => 'Hysbysu am bob newid i dudalennau.',
    'watch_title_comments' => 'Pob Diweddariad i Dualennau a Sylwadau',
    'watch_desc_comments' => 'Hysbysu am bob tudalen newydd, newidiadau i dudalennau a sylwadau newydd.',
    'watch_desc_comments_page' => 'Hysbysu am newidiadau i dudalennau a sylwadau newydd.',
    'watch_change_default' => 'Newid dewisiadau hysbysu diofyn',
    'watch_detail_ignore' => 'Anwybyddu hysbysiadau',
    'watch_detail_new' => 'Gwylio am dudalennau newydd',
    'watch_detail_updates' => 'Gwylio tudalennau a diweddariadau newydd',
    'watch_detail_comments' => 'Gwylio tudalennau newydd, diweddariadau a sylwadau',
    'watch_detail_parent_book' => 'Gwylio trwy lyfr rhiant',
    'watch_detail_parent_book_ignore' => 'Anwybyddu trwy lyfr rhiant',
    'watch_detail_parent_chapter' => 'Gwylio trwy bennod rhiant',
    'watch_detail_parent_chapter_ignore' => 'Anwybyddu trwy bennod rhiant',
];
