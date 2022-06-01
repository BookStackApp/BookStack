<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Недавно созданные',
    'recently_created_pages' => 'Недавно созданные страницы',
    'recently_updated_pages' => 'Недавно обновленные страницы',
    'recently_created_chapters' => 'Недавно созданные главы',
    'recently_created_books' => 'Недавно созданные книги',
    'recently_created_shelves' => 'Недавно созданные полки',
    'recently_update' => 'Недавно обновленные',
    'recently_viewed' => 'Недавно просмотренные',
    'recent_activity' => 'Недавние действия',
    'create_now' => 'Создать сейчас',
    'revisions' => 'Версии',
    'meta_revision' => 'Версия #:revisionCount',
    'meta_created' => 'Создано :timeLength',
    'meta_created_name' => ':user создал :timeLength',
    'meta_updated' => 'Обновлено :timeLength',
    'meta_updated_name' => ':user обновил :timeLength',
    'meta_owned_name' => 'Владелец :user',
    'entity_select' => 'Выбор объекта',
    'images' => 'Изображения',
    'my_recent_drafts' => 'Мои последние черновики',
    'my_recently_viewed' => 'Мои недавние просмотры',
    'my_most_viewed_favourites' => 'Популярное избранное',
    'my_favourites' => 'Мое избранное',
    'no_pages_viewed' => 'Вы не просматривали ни одной страницы',
    'no_pages_recently_created' => 'Нет недавно созданных страниц',
    'no_pages_recently_updated' => 'Нет недавно обновленных страниц',
    'export' => 'Экспорт',
    'export_html' => 'Веб файл',
    'export_pdf' => 'PDF файл',
    'export_text' => 'Текстовый файл',
    'export_md' => 'Файл Markdown',

    // Permissions and restrictions
    'permissions' => 'Разрешения',
    'permissions_intro' => 'После включения опции эти разрешения будут иметь приоритет над любыми установленными разрешениями роли.',
    'permissions_enable' => 'Включение пользовательских разрешений',
    'permissions_save' => 'Сохранить разрешения',
    'permissions_owner' => 'Владелец',

    // Search
    'search_results' => 'Результаты поиска',
    'search_total_results_found' => 'Найден :count результат|Найдено :count результата|Найдено :count результатов',
    'search_clear' => 'Очистить поиск',
    'search_no_pages' => 'Нет страниц, соответствующих этому поиску',
    'search_for_term' => 'Искать :term',
    'search_more' => 'Еще результаты',
    'search_advanced' => 'Расширенный поиск',
    'search_terms' => 'Поисковые запросы',
    'search_content_type' => 'Тип содержимого',
    'search_exact_matches' => 'Точные соответствия',
    'search_tags' => 'Поиск по тегам',
    'search_options' => 'Параметры',
    'search_viewed_by_me' => 'Просмотрено мной',
    'search_not_viewed_by_me' => 'Не просматривалось мной',
    'search_permissions_set' => 'Набор разрешений',
    'search_created_by_me' => 'Создано мной',
    'search_updated_by_me' => 'Обновлено мной',
    'search_owned_by_me' => 'Созданные мной',
    'search_date_options' => 'Параметры даты',
    'search_updated_before' => 'Обновлено до',
    'search_updated_after' => 'Обновлено после',
    'search_created_before' => 'Создано до',
    'search_created_after' => 'Создано после',
    'search_set_date' => 'Установить дату',
    'search_update' => 'Обновить поиск',

    // Shelves
    'shelf' => 'Полка',
    'shelves' => 'Полки',
    'x_shelves' => ':count полка|:count полки|:count полок',
    'shelves_long' => 'Книжные полки',
    'shelves_empty' => 'Полки не созданы',
    'shelves_create' => 'Создать новую полку',
    'shelves_popular' => 'Популярные полки',
    'shelves_new' => 'Новые полки',
    'shelves_new_action' => 'Новая полка',
    'shelves_popular_empty' => 'Популярные полки появятся здесь.',
    'shelves_new_empty' => 'Последние созданные полки появятся здесь.',
    'shelves_save' => 'Сохранить полку',
    'shelves_books' => 'Книги из этой полки',
    'shelves_add_books' => 'Добавить книгу в эту полку',
    'shelves_drag_books' => 'Перетащите книги сюда, чтобы добавить их на эту полку',
    'shelves_empty_contents' => 'На этой полке нет книг',
    'shelves_edit_and_assign' => 'Изменить полку для привязки книг',
    'shelves_edit_named' => 'Редактировать полку :name',
    'shelves_edit' => 'Редактировать книжную полку',
    'shelves_delete' => 'Удалить книжную полку',
    'shelves_delete_named' => 'Удалить книжную полку :name',
    'shelves_delete_explain' => "Это приведет к удалению полки с именем ':name'. Привязанные книги удалены не будут.",
    'shelves_delete_confirmation' => 'Вы уверены, что хотите удалить эту полку?',
    'shelves_permissions' => 'Доступы к книжной полке',
    'shelves_permissions_updated' => 'Доступы к книжной полке обновлены',
    'shelves_permissions_active' => 'Действующие разрешения книжной полки',
    'shelves_permissions_cascade_warning' => 'Разрешения на полки не наследуются автоматически содержащимся в них книгам. Это происходит потому, что книга может находиться на нескольких полках. Однако разрешения могут быть установлены для книг полки с помощью опции, приведенной ниже.',
    'shelves_copy_permissions_to_books' => 'Наследовать доступы книгам',
    'shelves_copy_permissions' => 'Копировать доступы',
    'shelves_copy_permissions_explain' => 'Это применит текущие настройки доступов этой книжной полки ко всем книгам, содержащимся внутри. Перед активацией убедитесь, что все изменения в доступах этой книжной полки сохранены.',
    'shelves_copy_permission_success' => 'Доступы книжной полки скопированы для :count книг',

    // Books
    'book' => 'Книга',
    'books' => 'Книги',
    'x_books' => ':count книга|:count книги|:count книг',
    'books_empty' => 'Нет созданных книг',
    'books_popular' => 'Популярные книги',
    'books_recent' => 'Недавние книги',
    'books_new' => 'Новые книги',
    'books_new_action' => 'Новая книга',
    'books_popular_empty' => 'Здесь появятся самые популярные книги.',
    'books_new_empty' => 'Здесь появятся самые последние созданные книги.',
    'books_create' => 'Создать новую книгу',
    'books_delete' => 'Удалить книгу',
    'books_delete_named' => 'Удалить книгу :bookName',
    'books_delete_explain' => 'Это удалит книги с именем \':bookName\'. Все разделы и страницы будут удалены.',
    'books_delete_confirmation' => 'Вы действительно хотите удалить эту книгу?',
    'books_edit' => 'Редактировать книгу',
    'books_edit_named' => 'Редактировать книгу :bookName',
    'books_form_book_name' => 'Название книги',
    'books_save' => 'Сохранить книгу',
    'books_permissions' => 'Разрешения на книгу',
    'books_permissions_updated' => 'Разрешения на книгу обновлены',
    'books_empty_contents' => 'Для этой книги нет страниц или разделов.',
    'books_empty_create_page' => 'Создать новую страницу',
    'books_empty_sort_current_book' => 'Сортировка текущей книги',
    'books_empty_add_chapter' => 'Добавить главу',
    'books_permissions_active' => 'Действующие разрешения книги',
    'books_search_this' => 'Поиск в этой книге',
    'books_navigation' => 'Навигация по книге',
    'books_sort' => 'Сортировка содержимого книги',
    'books_sort_named' => 'Сортировка книги :bookName',
    'books_sort_name' => 'По имени',
    'books_sort_created' => 'По дате создания',
    'books_sort_updated' => 'По дате обновления',
    'books_sort_chapters_first' => 'Главы в начале',
    'books_sort_chapters_last' => 'Главы в конце',
    'books_sort_show_other' => 'Показать другие книги',
    'books_sort_save' => 'Сохранить новый порядок',
    'books_copy' => 'Копировать книгу',
    'books_copy_success' => 'Книга успешно скопирована',

    // Chapters
    'chapter' => 'Глава',
    'chapters' => 'Главы',
    'x_chapters' => ':count глава|:count главы|:count глав',
    'chapters_popular' => 'Популярные главы',
    'chapters_new' => 'Новая глава',
    'chapters_create' => 'Создать новую главу',
    'chapters_delete' => 'Удалить главу',
    'chapters_delete_named' => 'Удалить главу :chapterName',
    'chapters_delete_explain' => 'Это действие удалит главу с названием \':chapterName\'. Все страницы, которые существуют в этой главе, также будут удалены.',
    'chapters_delete_confirm' => 'Вы действительно хотите удалить эту главу?',
    'chapters_edit' => 'Редактировать главу',
    'chapters_edit_named' => 'Редактировать главу :chapterName',
    'chapters_save' => 'Сохранить главу',
    'chapters_move' => 'Переместить главу',
    'chapters_move_named' => 'Переместить главу :chapterName',
    'chapter_move_success' => 'Глава перемещена в :bookName',
    'chapters_copy' => 'Копировать главу',
    'chapters_copy_success' => 'Глава успешно скопирована',
    'chapters_permissions' => 'Разрешения главы',
    'chapters_empty' => 'В этой главе нет страниц.',
    'chapters_permissions_active' => 'Действующие разрешения главы',
    'chapters_permissions_success' => 'Разрешения главы обновлены',
    'chapters_search_this' => 'Искать в этой главе',

    // Pages
    'page' => 'Страница',
    'pages' => 'Страницы',
    'x_pages' => ':count страница|:count страницы|:count страниц',
    'pages_popular' => 'Популярные страницы',
    'pages_new' => 'Новая страница',
    'pages_attachments' => 'Вложения',
    'pages_navigation' => 'Навигация на странице',
    'pages_delete' => 'Удалить страницу',
    'pages_delete_named' => 'Удалить страницу :pageName',
    'pages_delete_draft_named' => 'Удалить черновик :pageName',
    'pages_delete_draft' => 'Удалить черновик',
    'pages_delete_success' => 'Страница удалена',
    'pages_delete_draft_success' => 'Черновик удален',
    'pages_delete_confirm' => 'Вы действительно хотите удалить эту страницу?',
    'pages_delete_draft_confirm' => 'Вы действительно хотите удалить этот черновик?',
    'pages_editing_named' => 'Редактирование страницы :pageName',
    'pages_edit_draft_options' => 'Параметры черновика',
    'pages_edit_save_draft' => 'Сохранить черновик',
    'pages_edit_draft' => 'Редактировать черновик',
    'pages_editing_draft' => 'Редактирование черновика',
    'pages_editing_page' => 'Редактирование страницы',
    'pages_edit_draft_save_at' => 'Черновик сохранён в ',
    'pages_edit_delete_draft' => 'Удалить черновик',
    'pages_edit_discard_draft' => 'Отменить черновик',
    'pages_edit_switch_to_markdown' => 'Переключиться на Markdown',
    'pages_edit_switch_to_markdown_clean' => 'Только Markdown (с возможными потерями форматирования)',
    'pages_edit_switch_to_markdown_stable' => 'Полное сохранение форматирования (HTML)',
    'pages_edit_switch_to_wysiwyg' => 'Переключиться в WYSIWYG',
    'pages_edit_set_changelog' => 'Задать список изменений',
    'pages_edit_enter_changelog_desc' => 'Введите краткое описание внесенных изменений',
    'pages_edit_enter_changelog' => 'Введите список изменений',
    'pages_editor_switch_title' => 'Переключить редактор',
    'pages_editor_switch_are_you_sure' => 'Вы уверены, что хотите изменить редактор для этой страницы?',
    'pages_editor_switch_consider_following' => 'При изменении редактора учитывайте следующее:',
    'pages_editor_switch_consideration_a' => 'После сохранения новая опция редактора будет использоваться любыми пользователями, которые будут редактировать данную страницу, включая тех, которые не смогут самостоятельно изменить тип редактора.',
    'pages_editor_switch_consideration_b' => 'Это потенциально может привести к потере деталей и синтаксиса при определенных обстоятельствах.',
    'pages_editor_switch_consideration_c' => 'Изменения в тегах или журнале, сделанные с момента последнего сохранения, не сохраняются в этом изменении.',
    'pages_save' => 'Сохранить страницу',
    'pages_title' => 'Заголовок страницы',
    'pages_name' => 'Название страницы',
    'pages_md_editor' => 'Редактор',
    'pages_md_preview' => 'Просмотр',
    'pages_md_insert_image' => 'Вставить изображение',
    'pages_md_insert_link' => 'Вставить ссылку на объект',
    'pages_md_insert_drawing' => 'Вставить рисунок',
    'pages_not_in_chapter' => 'Страница не находится в главе',
    'pages_move' => 'Переместить страницу',
    'pages_move_success' => 'Страница перемещена в \':parentName\'',
    'pages_copy' => 'Скопировать страницу',
    'pages_copy_desination' => 'Скопировать в',
    'pages_copy_success' => 'Страница скопирована',
    'pages_permissions' => 'Разрешения страницы',
    'pages_permissions_success' => 'Pазрешения страницы обновлены',
    'pages_revision' => 'Версия',
    'pages_revisions' => 'Версии страницы',
    'pages_revisions_named' => 'Версии страницы для :pageName',
    'pages_revision_named' => 'Версия страницы для :pageName',
    'pages_revision_restored_from' => 'Восстановлено из #:id; :summary',
    'pages_revisions_created_by' => 'Создана',
    'pages_revisions_date' => 'Дата версии',
    'pages_revisions_number' => '#',
    'pages_revisions_numbered' => 'Версия #:id',
    'pages_revisions_numbered_changes' => 'Изменения в версии #:id',
    'pages_revisions_editor' => 'Тип редактора',
    'pages_revisions_changelog' => 'Список изменений',
    'pages_revisions_changes' => 'Изменения',
    'pages_revisions_current' => 'Текущая версия',
    'pages_revisions_preview' => 'Просмотр',
    'pages_revisions_restore' => 'Восстановить',
    'pages_revisions_none' => 'У этой страницы нет других версий',
    'pages_copy_link' => 'Копировать ссылку',
    'pages_edit_content_link' => 'Изменить содержание',
    'pages_permissions_active' => 'Действующие разрешения на страницу',
    'pages_initial_revision' => 'Первоначальное издание',
    'pages_initial_name' => 'Новая страница',
    'pages_editing_draft_notification' => 'В настоящее время вы редактируете черновик, который был сохранён :timeDiff.',
    'pages_draft_edited_notification' => 'Эта страница была обновлена до этого момента. Рекомендуется отменить этот черновик.',
    'pages_draft_page_changed_since_creation' => 'Эта страница была обновлена с момента создания данного черновика. Рекомендуется выбросить этот черновик или следить за тем, чтобы не перезаписать все изменения на странице.',
    'pages_draft_edit_active' => [
        'start_a' => ':count пользователей начали редактирование этой страницы',
        'start_b' => ':userName начал редактирование этой страницы',
        'time_a' => 'поскольку последние страницы были обновлены',
        'time_b' => 'за последние :minCount минут',
        'message' => ':start :time. Будьте осторожны, чтобы не перезаписывать друг друга!',
    ],
    'pages_draft_discarded' => 'Черновик сброшен, редактор обновлен текущим содержимым страницы',
    'pages_specific' => 'Конкретная страница',
    'pages_is_template' => 'Шаблон страницы',

    // Editor Sidebar
    'page_tags' => 'Теги страницы',
    'chapter_tags' => 'Теги главы',
    'book_tags' => 'Теги книги',
    'shelf_tags' => 'Теги полки',
    'tag' => 'Тег',
    'tags' =>  'Теги',
    'tag_name' =>  'Имя тега',
    'tag_value' => 'Значение тега (опционально)',
    'tags_explain' => "Добавьте теги, чтобы лучше классифицировать ваш контент. \\n Вы можете присвоить значение тегу для более глубокой организации.",
    'tags_add' => 'Добавить тег',
    'tags_remove' => 'Удалить этот тег',
    'tags_usages' => 'Всего использовано тегов',
    'tags_assigned_pages' => 'Назначено на страницы',
    'tags_assigned_chapters' => 'Назначено на главы',
    'tags_assigned_books' => 'Назначено на книги',
    'tags_assigned_shelves' => 'Назначено на полки',
    'tags_x_unique_values' => 'Уникальные значения: :count',
    'tags_all_values' => 'Все значения',
    'tags_view_tags' => 'Посмотреть теги',
    'tags_view_existing_tags' => 'Просмотр имеющихся тегов',
    'tags_list_empty_hint' => 'Теги можно присваивать через боковую панель редактора страниц или при редактировании сведений о книге, главе или полке.',
    'attachments' => 'Вложения',
    'attachments_explain' => 'Загрузите несколько файлов или добавьте ссылку для отображения на своей странице. Они видны на боковой панели страницы.',
    'attachments_explain_instant_save' => 'Изменения здесь сохраняются мгновенно.',
    'attachments_items' => 'Прикрепленные элементы',
    'attachments_upload' => 'Загрузить файл',
    'attachments_link' => 'Присоединить ссылку',
    'attachments_set_link' => 'Установить ссылку',
    'attachments_delete' => 'Вы уверены, что хотите удалить это вложение?',
    'attachments_dropzone' => 'Перетащите файл сюда или нажмите здесь, чтобы загрузить файл',
    'attachments_no_files' => 'Файлы не загружены',
    'attachments_explain_link' => 'Вы можете присоединить ссылку, если вы предпочитаете не загружать файл. Это может быть ссылка на другую страницу или ссылка на файл в облаке.',
    'attachments_link_name' => 'Название ссылки',
    'attachment_link' => 'Ссылка на вложение',
    'attachments_link_url' => 'Ссылка на файл',
    'attachments_link_url_hint' => 'URL-адрес сайта или файла',
    'attach' => 'Прикрепить',
    'attachments_insert_link' => 'Добавить ссылку на вложение',
    'attachments_edit_file' => 'Редактировать файл',
    'attachments_edit_file_name' => 'Название файла',
    'attachments_edit_drop_upload' => 'Перетащите файлы или нажмите здесь, чтобы загрузить и перезаписать',
    'attachments_order_updated' => 'Порядок вложений обновлен',
    'attachments_updated_success' => 'Детали вложения обновлены',
    'attachments_deleted' => 'Вложение удалено',
    'attachments_file_uploaded' => 'Файл успешно загружен',
    'attachments_file_updated' => 'Файл успешно обновлен',
    'attachments_link_attached' => 'Ссылка успешно присоединена к странице',
    'templates' => 'Шаблоны',
    'templates_set_as_template' => 'Страница является шаблоном',
    'templates_explain_set_as_template' => 'Вы можете назначить эту страницу в качестве шаблона, её содержимое будет использоваться при создании других страниц. Пользователи смогут использовать этот шаблон в случае, если имеют разрешения на просмотр этой страницы.',
    'templates_replace_content' => 'Заменить содержимое страницы',
    'templates_append_content' => 'Добавить к содержанию страницы',
    'templates_prepend_content' => 'Добавить в начало содержимого страницы',

    // Profile View
    'profile_user_for_x' => 'Пользователь уже :time',
    'profile_created_content' => 'Созданный контент',
    'profile_not_created_pages' => ':userName не создал ни одной страницы',
    'profile_not_created_chapters' => ':userName не создал ни одной главы',
    'profile_not_created_books' => ':userName не создал ни одной книги',
    'profile_not_created_shelves' => ':userName не создал ни одной полки',

    // Comments
    'comment' => 'Комментарий',
    'comments' => 'Комментарии',
    'comment_add' => 'Комментировать',
    'comment_placeholder' => 'Оставить комментарий здесь',
    'comment_count' => '{0} Нет комментариев|{1} 1 комментарий|[2,*] :count комментария',
    'comment_save' => 'Сохранить комментарий',
    'comment_saving' => 'Сохранение комментария...',
    'comment_deleting' => 'Удаление комментария...',
    'comment_new' => 'Новый комментарий',
    'comment_created' => 'прокомментировал :createDiff',
    'comment_updated' => 'Обновлен :updateDiff пользователем :username',
    'comment_deleted_success' => 'Комментарий удален',
    'comment_created_success' => 'Комментарий добавлен',
    'comment_updated_success' => 'Комментарий обновлен',
    'comment_delete_confirm' => 'Удалить этот комментарий?',
    'comment_in_reply_to' => 'В ответ на :commentId',

    // Revision
    'revision_delete_confirm' => 'Удалить эту версию?',
    'revision_restore_confirm' => 'Вы уверены, что хотите восстановить эту версию? Текущее содержимое страницы будет заменено.',
    'revision_delete_success' => 'Версия удалена',
    'revision_cannot_delete_latest' => 'Нельзя удалить последнюю версию.',

    // Copy view
    'copy_consider' => 'При копировании содержимого, пожалуйста, учтите следующее.',
    'copy_consider_permissions' => 'Пользовательские настройки прав доступа не будут скопированы.',
    'copy_consider_owner' => 'Вы станете владельцем всего скопированного контента.',
    'copy_consider_images' => 'Файлы изображений страницы не будут дублироваться и исходные изображения сохранят их отношение к странице, в которую они были загружены изначально.',
    'copy_consider_attachments' => 'Вложения страницы не будут скопированы.',
    'copy_consider_access' => 'Изменение положения, владельца или разрешений может привести к тому, что контент будет доступен пользователям, у которых не было доступа ранее.',
];
