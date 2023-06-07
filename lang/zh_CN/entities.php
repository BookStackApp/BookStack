<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => '最近创建',
    'recently_created_pages' => '最近创建的页面',
    'recently_updated_pages' => '最近更新的页面',
    'recently_created_chapters' => '最近创建的章节',
    'recently_created_books' => '最近创建的图书',
    'recently_created_shelves' => '最近创建的书架',
    'recently_update' => '最近更新',
    'recently_viewed' => '最近查看',
    'recent_activity' => '近期活动',
    'create_now' => '立刻创建',
    'revisions' => '修订历史',
    'meta_revision' => '版本号 #:revisionCount',
    'meta_created' => '创建于 :timeLength',
    'meta_created_name' => '由 :user 创建于 :timeLength',
    'meta_updated' => '更新于 :timeLength',
    'meta_updated_name' => '由 :user 更新于 :timeLength',
    'meta_owned_name' => '拥有者 :user',
    'meta_reference_page_count' => '被 :count 个页面引用|被 :count 个页面引用',
    'entity_select' => '选择项目',
    'entity_select_lack_permission' => '您没有选择此项目所需的权限',
    'images' => '图片',
    'my_recent_drafts' => '我最近的草稿',
    'my_recently_viewed' => '我最近看过',
    'my_most_viewed_favourites' => '我浏览最多的收藏',
    'my_favourites' => '我的收藏',
    'no_pages_viewed' => '您尚未查看任何页面',
    'no_pages_recently_created' => '最近没有页面被创建',
    'no_pages_recently_updated' => '最近没有页面被更新',
    'export' => '导出',
    'export_html' => '网页文件',
    'export_pdf' => 'PDF文件',
    'export_text' => '纯文本文件',
    'export_md' => 'Markdown 文件',

    // Permissions and restrictions
    'permissions' => '权限',
    'permissions_desc' => '在此处设置权限以覆盖用户角色提供的默认权限。',
    'permissions_book_cascade' => '书籍上设置的权限将自动应用到子章节和子页面，除非它们有自己的权限设置。',
    'permissions_chapter_cascade' => '章节上设置的权限将自动应用到子页面，除非它们有自己的权限设置。',
    'permissions_save' => '保存权限',
    'permissions_owner' => '拥有者',
    'permissions_role_everyone_else' => '其他所有人',
    'permissions_role_everyone_else_desc' => '为所有未被特别覆盖的角色设置权限。',
    'permissions_role_override' => '覆盖角色权限',
    'permissions_inherit_defaults' => '继承默认值',

    // Search
    'search_results' => '搜索结果',
    'search_total_results_found' => '共找到了:count个结果',
    'search_clear' => '清除搜索',
    'search_no_pages' => '没有找到相匹配的页面',
    'search_for_term' => '“:term”的搜索结果',
    'search_more' => '更多结果',
    'search_advanced' => '高级搜索',
    'search_terms' => '搜索关键词',
    'search_content_type' => '种类',
    'search_exact_matches' => '精确匹配',
    'search_tags' => '标签搜索',
    'search_options' => '选项',
    'search_viewed_by_me' => '我看过的',
    'search_not_viewed_by_me' => '我没看过的',
    'search_permissions_set' => '权限设置',
    'search_created_by_me' => '我创建的',
    'search_updated_by_me' => '我更新的',
    'search_owned_by_me' => '我拥有的',
    'search_date_options' => '日期选项',
    'search_updated_before' => '在此之前更新',
    'search_updated_after' => '在此之后更新',
    'search_created_before' => '在此之前创建',
    'search_created_after' => '在此之后创建',
    'search_set_date' => '设置日期',
    'search_update' => '只显示更新操作',

    // Shelves
    'shelf' => '书架',
    'shelves' => '书架',
    'x_shelves' => ':count 书架|:count 书架',
    'shelves_empty' => '当前未创建书架',
    'shelves_create' => '创建新书架',
    'shelves_popular' => '热门书架',
    'shelves_new' => '新书架',
    'shelves_new_action' => '新书架',
    'shelves_popular_empty' => '最热门的书架',
    'shelves_new_empty' => '最新创建的书架',
    'shelves_save' => '保存书架',
    'shelves_books' => '书籍已在此书架里',
    'shelves_add_books' => '将书籍加入此书架',
    'shelves_drag_books' => '拖动下面的图书将其添加到此书架',
    'shelves_empty_contents' => '这个书架没有分配图书',
    'shelves_edit_and_assign' => '编辑书架以分配图书',
    'shelves_edit_named' => '编辑书架 :name',
    'shelves_edit' => '编辑书架',
    'shelves_delete' => '删除书架',
    'shelves_delete_named' => '删除书架 :name',
    'shelves_delete_explain' => "此操作将删除书架 ”:name”。书架中的图书不会被删除。",
    'shelves_delete_confirmation' => '您确定要删除此书架吗？',
    'shelves_permissions' => '书架权限',
    'shelves_permissions_updated' => '书架权限已更新',
    'shelves_permissions_active' => '书架权限已启用',
    'shelves_permissions_cascade_warning' => '书架上的权限不会自动应用到书架里的图书上，这是因为图书可以在多个书架上存在。使用下面的选项可以将权限复制到书架里的图书上。',
    'shelves_copy_permissions_to_books' => '将权限复制到图书',
    'shelves_copy_permissions' => '复制权限',
    'shelves_copy_permissions_explain' => '此操作会将此书架的当前权限设置应用于其中包含的所有图书上。 启用前请确保已保存对此书架权限的任何更改。',
    'shelves_copy_permission_success' => '书架权限已复制到 :count 本图书上',

    // Books
    'book' => '图书',
    'books' => '图书',
    'x_books' => ':count 本书',
    'books_empty' => '不存在已创建的书',
    'books_popular' => '热门图书',
    'books_recent' => '最近的书',
    'books_new' => '新书',
    'books_new_action' => '新书',
    'books_popular_empty' => '最受欢迎的图书将出现在这里。',
    'books_new_empty' => '最近创建的图书将出现在这里。',
    'books_create' => '创建图书',
    'books_delete' => '删除图书',
    'books_delete_named' => '删除图书「:bookName」',
    'books_delete_explain' => '此操作将删除图书 “:bookName”。图书中的所有的章节和页面都会被删除。',
    'books_delete_confirmation' => '您确定要删除此图书吗？',
    'books_edit' => '编辑图书',
    'books_edit_named' => '编辑图书「:bookName」',
    'books_form_book_name' => '书名',
    'books_save' => '保存图书',
    'books_permissions' => '图书权限',
    'books_permissions_updated' => '图书权限已更新',
    'books_empty_contents' => '本书目前没有页面或章节。',
    'books_empty_create_page' => '创建页面',
    'books_empty_sort_current_book' => '排序当前图书',
    'books_empty_add_chapter' => '添加章节',
    'books_permissions_active' => '图书权限已启用',
    'books_search_this' => '搜索这本书',
    'books_navigation' => '图书导航',
    'books_sort' => '排序图书内容',
    'books_sort_desc' => '移动并重新排序书中的章节和页面。你也可以添加其他图书，这样就可以方便地在图书之间移动章节和页面。',
    'books_sort_named' => '排序图书「:bookName」',
    'books_sort_name' => '按名称排序',
    'books_sort_created' => '创建时间排序',
    'books_sort_updated' => '按更新时间排序',
    'books_sort_chapters_first' => '章节正序',
    'books_sort_chapters_last' => '章节倒序',
    'books_sort_show_other' => '显示其他图书',
    'books_sort_save' => '保存新顺序',
    'books_sort_show_other_desc' => '在此添加其他图书进入排序界面，这样就可以轻松跨图书重新排序。',
    'books_sort_move_up' => '上移',
    'books_sort_move_down' => '下移',
    'books_sort_move_prev_book' => '移动到上一图书',
    'books_sort_move_next_book' => '移动到下一图书',
    'books_sort_move_prev_chapter' => '移动到上一章节',
    'books_sort_move_next_chapter' => '移动到下一章节',
    'books_sort_move_book_start' => '移动到图书开头',
    'books_sort_move_book_end' => '移动到图书结尾',
    'books_sort_move_before_chapter' => '移动到章节前',
    'books_sort_move_after_chapter' => '移至章节后',
    'books_copy' => '复制图书',
    'books_copy_success' => '图书已成功复制',

    // Chapters
    'chapter' => '章节',
    'chapters' => '章节',
    'x_chapters' => ':count个章节',
    'chapters_popular' => '热门章节',
    'chapters_new' => '新章节',
    'chapters_create' => '创建章节',
    'chapters_delete' => '删除章节',
    'chapters_delete_named' => '删除章节「:chapterName」',
    'chapters_delete_explain' => '此操作将删除章节 “:chapterName”。章节中的所有页面都会被删除。',
    'chapters_delete_confirm' => '您确定要删除此章节吗？',
    'chapters_edit' => '编辑章节',
    'chapters_edit_named' => '编辑章节「:chapterName」',
    'chapters_save' => '保存章节',
    'chapters_move' => '移动章节',
    'chapters_move_named' => '移动章节「:chapterName」',
    'chapter_move_success' => '章节移动到「:bookName」',
    'chapters_copy' => '复制章节',
    'chapters_copy_success' => '章节已成功复制',
    'chapters_permissions' => '章节权限',
    'chapters_empty' => '本章目前没有页面。',
    'chapters_permissions_active' => '章节权限已启用',
    'chapters_permissions_success' => '章节权限已更新',
    'chapters_search_this' => '从本章节搜索',
    'chapter_sort_book' => '排序图书',

    // Pages
    'page' => '页面',
    'pages' => '页面',
    'x_pages' => ':count个页面',
    'pages_popular' => '热门页面',
    'pages_new' => '新页面',
    'pages_attachments' => '附件',
    'pages_navigation' => '页面导航',
    'pages_delete' => '删除页面',
    'pages_delete_named' => '删除页面“:pageName”',
    'pages_delete_draft_named' => '删除草稿页面“:pageName”',
    'pages_delete_draft' => '删除草稿页面',
    'pages_delete_success' => '页面已删除',
    'pages_delete_draft_success' => '草稿页面已删除',
    'pages_delete_confirm' => '您确定要删除此页面吗？',
    'pages_delete_draft_confirm' => '您确定要删除此草稿页面吗？',
    'pages_editing_named' => '正在编辑页面“:pageName”',
    'pages_edit_draft_options' => '草稿选项',
    'pages_edit_save_draft' => '保存草稿',
    'pages_edit_draft' => '编辑页面草稿',
    'pages_editing_draft' => '正在编辑草稿',
    'pages_editing_page' => '正在编辑页面',
    'pages_edit_draft_save_at' => '草稿保存于 ',
    'pages_edit_delete_draft' => '删除草稿',
    'pages_edit_discard_draft' => '放弃草稿',
    'pages_edit_switch_to_markdown' => '切换到 Markdown 编辑器',
    'pages_edit_switch_to_markdown_clean' => '（整理内容）',
    'pages_edit_switch_to_markdown_stable' => '（保留内容）',
    'pages_edit_switch_to_wysiwyg' => '切换到所见即所得编辑器',
    'pages_edit_set_changelog' => '更新说明',
    'pages_edit_enter_changelog_desc' => '输入对您所做更改的简要说明',
    'pages_edit_enter_changelog' => '输入更新说明',
    'pages_editor_switch_title' => '切换编辑器',
    'pages_editor_switch_are_you_sure' => '您确定要更改此页面的编辑器吗？',
    'pages_editor_switch_consider_following' => '更改编辑器时请注意以下事项：',
    'pages_editor_switch_consideration_a' => '一旦保存，任何未来的编辑都将使用新的编辑器，包括那些没有权限自行更改编辑器类型的用户。',
    'pages_editor_switch_consideration_b' => '在某些情况下这可能会导致丢失页面格式或功能损坏。',
    'pages_editor_switch_consideration_c' => '上次保存后修改的标签和更改日志将不会被保存。',
    'pages_save' => '保存页面',
    'pages_title' => '页面标题',
    'pages_name' => '页面名',
    'pages_md_editor' => '编者',
    'pages_md_preview' => '预览',
    'pages_md_insert_image' => '插入图片',
    'pages_md_insert_link' => '插入项目链接',
    'pages_md_insert_drawing' => '插入图表',
    'pages_md_show_preview' => '显示预览',
    'pages_md_sync_scroll' => '同步预览滚动',
    'pages_not_in_chapter' => '本页面不在某章节中',
    'pages_move' => '移动页面',
    'pages_move_success' => '页面已移动到「:parentName」',
    'pages_copy' => '复制页面',
    'pages_copy_desination' => '复制目的地',
    'pages_copy_success' => '页面复制完成',
    'pages_permissions' => '页面权限',
    'pages_permissions_success' => '页面权限已更新',
    'pages_revision' => '修订',
    'pages_revisions' => '页面修订',
    'pages_revisions_desc' => '下面列出的是该页面的所有过去修订。如果权限允许，您可以回顾、比较和恢复旧的页面版本。页面的完整历史可能不会在这里完全反映出来，因为根据系统配置，旧的修订可能会被自动删除。',
    'pages_revisions_named' => '“:pageName”页面修订',
    'pages_revision_named' => '“:pageName”页面修订',
    'pages_revision_restored_from' => '恢复到 #:id :summary',
    'pages_revisions_created_by' => '创建者',
    'pages_revisions_date' => '修订日期',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => '修订号',
    'pages_revisions_numbered' => '修订 #:id',
    'pages_revisions_numbered_changes' => '修改 #:id ',
    'pages_revisions_editor' => '编辑器类型',
    'pages_revisions_changelog' => '更新说明',
    'pages_revisions_changes' => '查看更改',
    'pages_revisions_current' => '当前版本',
    'pages_revisions_preview' => '预览',
    'pages_revisions_restore' => '恢复',
    'pages_revisions_none' => '此页面没有修订',
    'pages_copy_link' => '复制链接',
    'pages_edit_content_link' => '跳转到编辑器中的部分',
    'pages_pointer_enter_mode' => 'Enter section select mode',
    'pages_pointer_label' => 'Page Section Options',
    'pages_pointer_permalink' => 'Page Section Permalink',
    'pages_pointer_include_tag' => 'Page Section Include Tag',
    'pages_pointer_toggle_link' => 'Permalink mode, Press to show include tag',
    'pages_pointer_toggle_include' => 'Include tag mode, Press to show permalink',
    'pages_permissions_active' => '页面权限已启用',
    'pages_initial_revision' => '初始发布',
    'pages_references_update_revision' => '系统自动更新的内部链接',
    'pages_initial_name' => '新页面',
    'pages_editing_draft_notification' => '您正在编辑在 :timeDiff 内保存的草稿.',
    'pages_draft_edited_notification' => '此后，此页面已经被更新，建议您放弃此草稿。',
    'pages_draft_page_changed_since_creation' => '这个页面在您的草稿创建后被其他用户更新了，您目前的草稿不包含新的内容。建议您放弃此草稿，或是注意不要覆盖新的页面更改。',
    'pages_draft_edit_active' => [
        'start_a' => ':count 位用户已开始编辑此页面',
        'start_b' => '用户 “:userName” 已经开始编辑此页面',
        'time_a' => '自页面上次更新以来',
        'time_b' => '在最近 :minCount 分钟',
        'message' => ':time :start。注意不要覆盖他人的更新！',
    ],
    'pages_draft_discarded' => '草稿已丢弃，编辑器已更新到当前页面内容。',
    'pages_specific' => '具体页面',
    'pages_is_template' => '页面模板',

    // Editor Sidebar
    'page_tags' => '页面标签',
    'chapter_tags' => '章节标签',
    'book_tags' => '图书标签',
    'shelf_tags' => '书架标签',
    'tag' => '标签',
    'tags' =>  '标签',
    'tags_index_desc' => '标签是一种灵活的分类形式，可以应用于系统内的内容。标签可以有一个键和值，值是可选的。应用后就可以使用标签的名称和值来搜索内容。',
    'tag_name' =>  '标签名称',
    'tag_value' => '标签值 (可选)',
    'tags_explain' => "添加一些标签以更好地对您的内容进行分类。\n您可以为标签分配一个值，以进行更好的进行管理。",
    'tags_add' => '添加另一个标签',
    'tags_remove' => '删除此标签',
    'tags_usages' => '标签总使用量',
    'tags_assigned_pages' => '有这个标签的页面',
    'tags_assigned_chapters' => '有这个标签的章节',
    'tags_assigned_books' => '有这个标签的图书',
    'tags_assigned_shelves' => '有这个标签的书架',
    'tags_x_unique_values' => ':count 个不重复项目',
    'tags_all_values' => '所有值',
    'tags_view_tags' => '查看标签',
    'tags_view_existing_tags' => '查看已有的标签',
    'tags_list_empty_hint' => '您可以在页面编辑器的侧边栏添加标签，或者在编辑图书、章节、书架时添加。',
    'attachments' => '附件',
    'attachments_explain' => '上传一些文件或附加一些链接显示在您的网页上。这些在页面的侧边栏中可见。',
    'attachments_explain_instant_save' => '这里的更改将立即保存。',
    'attachments_upload' => '上传文件',
    'attachments_link' => '附加链接',
    'attachments_upload_drop' => '或者，您也可以将文件拖放到这里并将其作为附件上传',
    'attachments_set_link' => '设置链接',
    'attachments_delete' => '您确定要删除此附件吗？',
    'attachments_dropzone' => '将文件拖放到此处上传',
    'attachments_no_files' => '尚未上传文件',
    'attachments_explain_link' => '如果您不想上传文件，则可以附加链接，这可以是指向其他页面的链接，也可以是指向云端文件的链接。',
    'attachments_link_name' => '链接名',
    'attachment_link' => '附件链接',
    'attachments_link_url' => '链接到文件',
    'attachments_link_url_hint' => '网站或文件的网址',
    'attach' => '附加',
    'attachments_insert_link' => '将附加链接添加到页面',
    'attachments_edit_file' => '编辑文件',
    'attachments_edit_file_name' => '文件名',
    'attachments_edit_drop_upload' => '删除文件或点击这里上传并覆盖',
    'attachments_order_updated' => '附件顺序已更新',
    'attachments_updated_success' => '附件信息已更新',
    'attachments_deleted' => '附件已删除',
    'attachments_file_uploaded' => '附件上传成功',
    'attachments_file_updated' => '附件更新成功',
    'attachments_link_attached' => '链接成功附加到页面',
    'templates' => '模板',
    'templates_set_as_template' => '设置为模板',
    'templates_explain_set_as_template' => '您可以将此页面设置为模板，以便在创建其他页面时利用其内容。 如果其他用户对此页面具有查看权限，则将可以使用此模板。',
    'templates_replace_content' => '替换页面内容',
    'templates_append_content' => '附加到页面内容',
    'templates_prepend_content' => '追加到页面内容',

    // Profile View
    'profile_user_for_x' => '来这里:time了',
    'profile_created_content' => '已创建内容',
    'profile_not_created_pages' => ':userName尚未创建任何页面',
    'profile_not_created_chapters' => ':userName尚未创建任何章节',
    'profile_not_created_books' => ':userName尚未创建任何图书',
    'profile_not_created_shelves' => ':userName 尚未创建任何书架',

    // Comments
    'comment' => '评论',
    'comments' => '评论',
    'comment_add' => '添加评论',
    'comment_placeholder' => '在这里评论',
    'comment_count' => '{0} 无评论|{1} 1 条评论|[2,*] :count 条评论',
    'comment_save' => '保存评论',
    'comment_saving' => '正在保存评论...',
    'comment_deleting' => '正在删除评论...',
    'comment_new' => '新评论',
    'comment_created' => '评论于 :createDiff',
    'comment_updated' => '更新于 :updateDiff (:username)',
    'comment_deleted_success' => '评论已删除',
    'comment_created_success' => '评论已添加',
    'comment_updated_success' => '评论已更新',
    'comment_delete_confirm' => '您确定要删除这条评论？',
    'comment_in_reply_to' => '回复 :commentId',

    // Revision
    'revision_delete_confirm' => '您确定要删除此修订版吗？',
    'revision_restore_confirm' => '您确定要恢复到此修订版吗？恢复后当前页面内容将被替换。',
    'revision_delete_success' => '修订删除',
    'revision_cannot_delete_latest' => '无法删除最新版本。',

    // Copy view
    'copy_consider' => '复制内容时请注意以下事项。',
    'copy_consider_permissions' => '自定义权限设置将不会被复制。',
    'copy_consider_owner' => '您将成为所有已复制内容的所有者。',
    'copy_consider_images' => '页面中的图像文件不会被复制，原始图像将保留它们与最初上传到的页面的关系。',
    'copy_consider_attachments' => '页面中的附件不会被复制。',
    'copy_consider_access' => '改变位置、所有者或权限可能会导致此内容被以前无法访问的人访问。',

    // Conversions
    'convert_to_shelf' => '转换为书架',
    'convert_to_shelf_contents_desc' => '你可以将这本书转换为具有相同内容的新书架。本书中的章节将被转换为图书。如果这本书包含有任何不在章节分类中的页面，那么将会有一本单独的图书包含这些页面，这本书也将成为新书架的一部分。',
    'convert_to_shelf_permissions_desc' => '在这本书上设置的任何权限都将复制到所有未强制执行权限的新书架和新子图书上。请注意，书架上的权限不会像图书那样继承到内容物上。',
    'convert_book' => '转换图书',
    'convert_book_confirm' => '您确定要转换此图书吗？',
    'convert_undo_warning' => '这可不能轻易撤消。',
    'convert_to_book' => '转换为图书',
    'convert_to_book_desc' => '您可以将此章节转换为具有相同内容的新图书。此章节中设置的任何权限都将复制到新图书上，但从父图书继承的任何权限都不会被复制，这可能会导致访问控制发生变化。',
    'convert_chapter' => '转换章节',
    'convert_chapter_confirm' => '您确定要转换此章节吗？',

    // References
    'references' => '引用',
    'references_none' => '没有跟踪到对此项目的引用。',
    'references_to_desc' => '下面显示的是系统中所有已知链接到这个项目的页面。',
];
