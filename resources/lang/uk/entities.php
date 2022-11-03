<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Shelves, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Недавно створено',
    'recently_created_pages' => 'Нещодавно створені сторінки',
    'recently_updated_pages' => 'Нещодавно оновлені сторінки',
    'recently_created_chapters' => 'Нещодавно створені розділи',
    'recently_created_books' => 'Нещодавно створені книги',
    'recently_created_shelves' => 'Нещодавно створені полиці',
    'recently_update' => 'Недавно оновлено',
    'recently_viewed' => 'Недавно переглянуто',
    'recent_activity' => 'Остання активність',
    'create_now' => 'Створити зараз',
    'revisions' => 'Версія',
    'meta_revision' => 'Версія #:revisionCount',
    'meta_created' => 'Створено :timeLength',
    'meta_created_name' => ':user створив :timeLength',
    'meta_updated' => 'Оновлено :timeLength',
    'meta_updated_name' => ':user оновив :timeLength',
    'meta_owned_name' => 'Власник :user',
    'meta_reference_page_count' => 'Посилання на 1 сторінку|Посилання на :count сторінок',
    'entity_select' => 'Вибір об\'єкта',
    'entity_select_lack_permission' => 'У вас немає необхідних прав для вибору цього елемента',
    'images' => 'Зображення',
    'my_recent_drafts' => 'Мої останні чернетки',
    'my_recently_viewed' => 'Мої недавні перегляди',
    'my_most_viewed_favourites' => 'Мої найпопулярніші улюблені',
    'my_favourites' => 'Моє обране',
    'no_pages_viewed' => 'Ви не переглядали жодної сторінки',
    'no_pages_recently_created' => 'Не було створено жодної сторінки',
    'no_pages_recently_updated' => 'Немає недавно оновлених сторінок',
    'export' => 'Експорт',
    'export_html' => 'Вбудований веб-файл',
    'export_pdf' => 'PDF файл',
    'export_text' => 'Текстовий файл',
    'export_md' => 'Файл розмітки',

    // Permissions and restrictions
    'permissions' => 'Дозволи',
    'permissions_desc' => 'Встановіть тут дозволи, щоб перевизначити права за замовчуванням, які надаються ролями користувачів.',
    'permissions_book_cascade' => 'Дозволи, встановлені на книги будуть автоматично каскадом до дитячих глав та сторінок, якщо вони не матимуть свої дозволи.',
    'permissions_chapter_cascade' => 'Дозволи, встановлені для глав будуть автоматично каскадом на дочірні сторінки, якщо вони не матимуть своїх прав.',
    'permissions_save' => 'Зберегти дозволи',
    'permissions_owner' => 'Власник',
    'permissions_role_everyone_else' => 'Всі інші',
    'permissions_role_everyone_else_desc' => 'Встановити дозвіл для всіх ролей не спеціально перевизначений.',
    'permissions_role_override' => 'Змінити права доступу для ролі',

    // Search
    'search_results' => 'Результати пошуку',
    'search_total_results_found' => ':count результатів знайдено|:count всього результатів знайдено',
    'search_clear' => 'Очистити пошук',
    'search_no_pages' => 'Немає сторінок, які відповідають цьому пошуку',
    'search_for_term' => 'Шукати :term',
    'search_more' => 'Більше результатів',
    'search_advanced' => 'Розширений пошук',
    'search_terms' => 'Пошукові фрази',
    'search_content_type' => 'Тип вмісту',
    'search_exact_matches' => 'Точна відповідність',
    'search_tags' => 'Пошукові теги',
    'search_options' => 'Параметри',
    'search_viewed_by_me' => 'Переглянуто мною',
    'search_not_viewed_by_me' => 'Не переглянуто мною',
    'search_permissions_set' => 'Налаштування дозволів',
    'search_created_by_me' => 'Створено мною',
    'search_updated_by_me' => 'Оновлено мною',
    'search_owned_by_me' => 'Належать мені',
    'search_date_options' => 'Параметри дати',
    'search_updated_before' => 'Оновлено до',
    'search_updated_after' => 'Оновлено після',
    'search_created_before' => 'Створено до',
    'search_created_after' => 'Створено після',
    'search_set_date' => 'Встановити дату',
    'search_update' => 'Оновити пошук',

    // Shelves
    'shelf' => 'Полиця',
    'shelves' => 'Полиці',
    'x_shelves' => ':count Полиця|:count Полиць',
    'shelves_empty' => 'Жодних полиць не було створено',
    'shelves_create' => 'Створити нову полицю',
    'shelves_popular' => 'Популярні полиці',
    'shelves_new' => 'Нові полиці',
    'shelves_new_action' => 'Нова полиця',
    'shelves_popular_empty' => 'Найпопулярніші полиці з\'являться тут.',
    'shelves_new_empty' => 'Тут будуть з\'являтися останні створені полиці.',
    'shelves_save' => 'Зберегти полицю',
    'shelves_books' => 'Книги на цій полиці',
    'shelves_add_books' => 'Додати книги до цієї полиці',
    'shelves_drag_books' => 'Перетягніть книги нижче, щоб додати їх до цієї полиці',
    'shelves_empty_contents' => 'Ця полиця не має призначених їй книг',
    'shelves_edit_and_assign' => 'Редагувати полицю для присвоєння книг',
    'shelves_edit_named' => 'Редагувати полицю :name',
    'shelves_edit' => 'Редагувати полицю',
    'shelves_delete' => 'Видалити полицю',
    'shelves_delete_named' => 'Видалити полицю :name',
    'shelves_delete_explain' => "Це видалить полицю з ім'ям ':name'. Якщо містить книги, не буде видалено.",
    'shelves_delete_confirmation' => 'Ви упевнені, що хочете видалити цю полицю?',
    'shelves_permissions' => 'Дозволи полиці',
    'shelves_permissions_updated' => 'Дозволи полиці оновлено',
    'shelves_permissions_active' => 'Дозволи полиці активні',
    'shelves_permissions_cascade_warning' => 'Дозволи на полицях не каскадують автоматично до вміщених книг. Це тому, що книга може стояти на кількох полицях. Однак дозволи можна скопіювати до дочірніх книг за допомогою наведеної нижче опції.',
    'shelves_copy_permissions_to_books' => 'Копіювати дозволи на книги',
    'shelves_copy_permissions' => 'Копіювати дозволи',
    'shelves_copy_permissions_explain' => 'Це застосує поточні налаштування дозволів цієї полиці до всіх книг, які містяться в ній. Перед активацією переконайтеся, що будь-які зміни в дозволах цієї полиці збережено.',
    'shelves_copy_permission_success' => 'Права полиці скопійовано до :count книг',

    // Books
    'book' => 'Книга',
    'books' => 'Книги',
    'x_books' => ':count книга|:count книг',
    'books_empty' => 'Немає створених книг',
    'books_popular' => 'Популярні книги',
    'books_recent' => 'Останні книги',
    'books_new' => 'Нові книги',
    'books_new_action' => 'Нова книга',
    'books_popular_empty' => 'Найпопулярніші книги з\'являться тут.',
    'books_new_empty' => 'Найновіші книги з\'являться тут.',
    'books_create' => 'Створити нову книгу',
    'books_delete' => 'Видалити книгу',
    'books_delete_named' => 'Видалити книгу :bookName',
    'books_delete_explain' => 'Це призведе до видалення книги з назвою \':bookName\'. Всі сторінки та розділи будуть видалені.',
    'books_delete_confirmation' => 'Ви впевнені, що хочете видалити цю книгу?',
    'books_edit' => 'Редагувати книгу',
    'books_edit_named' => 'Редагувати книгу :bookName',
    'books_form_book_name' => 'Назва книги',
    'books_save' => 'Зберегти книгу',
    'books_permissions' => 'Дозволи на книгу',
    'books_permissions_updated' => 'Дозволи на книгу оновлено',
    'books_empty_contents' => 'Для цієї книги не створено жодної сторінки або розділів.',
    'books_empty_create_page' => 'Створити нову сторінку',
    'books_empty_sort_current_book' => 'Сортувати поточну книгу',
    'books_empty_add_chapter' => 'Додати розділ',
    'books_permissions_active' => 'Діючі дозволи на книгу',
    'books_search_this' => 'Шукати цю книгу',
    'books_navigation' => 'Навігація по книзі',
    'books_sort' => 'Сортувати вміст книги',
    'books_sort_named' => 'Сортувати книгу :bookName',
    'books_sort_name' => 'Сортувати за назвою',
    'books_sort_created' => 'Сортувати за датою створення',
    'books_sort_updated' => 'Сортувати за датою оновлення',
    'books_sort_chapters_first' => 'Спершу розділи',
    'books_sort_chapters_last' => 'Розділи в кінці',
    'books_sort_show_other' => 'Показати інші книги',
    'books_sort_save' => 'Зберегти нове замовлення',
    'books_copy' => 'Копіювати книгу',
    'books_copy_success' => 'Сторінка успішно скопійована',

    // Chapters
    'chapter' => 'Розділ',
    'chapters' => 'Розділи',
    'x_chapters' => ':count розділ|:count розділів',
    'chapters_popular' => 'Популярні розділи',
    'chapters_new' => 'Новий розділ',
    'chapters_create' => 'Створити новий розділ',
    'chapters_delete' => 'Видалити розділ',
    'chapters_delete_named' => 'Видалити розділ :chapterName',
    'chapters_delete_explain' => 'Це видалить розділ під назвою \':chapterName\'. Усі сторінки, що існують у цьому розділі, також будуть видалені.',
    'chapters_delete_confirm' => 'Ви впевнені, що хочете видалити цей розділ?',
    'chapters_edit' => 'Редагувати розділ',
    'chapters_edit_named' => 'Редагувати розділ :chapterName',
    'chapters_save' => 'Зберегти розділ',
    'chapters_move' => 'Перемістити розділ',
    'chapters_move_named' => 'Перемістити розділ :chapterName',
    'chapter_move_success' => 'Розділ переміщено до :bookName',
    'chapters_copy' => 'Копіювати розділ',
    'chapters_copy_success' => 'Розділ успішно скопійовано',
    'chapters_permissions' => 'Дозволи розділу',
    'chapters_empty' => 'У цьому розділі немає сторінок.',
    'chapters_permissions_active' => 'Діючі дозволи на розділ',
    'chapters_permissions_success' => 'Дозволи на розділ оновлено',
    'chapters_search_this' => 'Шукати в цьому розділі',
    'chapter_sort_book' => 'Сортувати книгу',

    // Pages
    'page' => 'Сторінка',
    'pages' => 'Сторінки',
    'x_pages' => ':count сторінка|:count сторінок',
    'pages_popular' => 'Популярні сторінки',
    'pages_new' => 'Нова сторінка',
    'pages_attachments' => 'Вкладення',
    'pages_navigation' => 'Навігація по сторінці',
    'pages_delete' => 'Видалити сторінку',
    'pages_delete_named' => 'Видалити сторінку :pageName',
    'pages_delete_draft_named' => 'Видалити чернетку :pageName',
    'pages_delete_draft' => 'Видалити чернетку',
    'pages_delete_success' => 'Сторінка видалена',
    'pages_delete_draft_success' => 'Чернетка видалена',
    'pages_delete_confirm' => 'Ви впевнені, що хочете видалити цю сторінку?',
    'pages_delete_draft_confirm' => 'Ви впевнені, що хочете видалити цю чернетку?',
    'pages_editing_named' => 'Редагування сторінки :pageName',
    'pages_edit_draft_options' => 'Параметри чернетки',
    'pages_edit_save_draft' => 'Зберегти чернетку',
    'pages_edit_draft' => 'Редагувати чернетку сторінки',
    'pages_editing_draft' => 'Редагування чернетки',
    'pages_editing_page' => 'Редагування сторінки',
    'pages_edit_draft_save_at' => 'Чернетка збережена о ',
    'pages_edit_delete_draft' => 'Видалити чернетку',
    'pages_edit_discard_draft' => 'Відхилити чернетку',
    'pages_edit_switch_to_markdown' => 'Змінити редактор на Markdown',
    'pages_edit_switch_to_markdown_clean' => '(Очистити вміст)',
    'pages_edit_switch_to_markdown_stable' => '(Стабілізувати вміст)',
    'pages_edit_switch_to_wysiwyg' => 'Змінити редактор на WYSIWYG',
    'pages_edit_set_changelog' => 'Встановити журнал змін',
    'pages_edit_enter_changelog_desc' => 'Введіть короткий опис внесених вами змін',
    'pages_edit_enter_changelog' => 'Введіть список змін',
    'pages_editor_switch_title' => 'Змінити редактор',
    'pages_editor_switch_are_you_sure' => 'Ви впевнені, що хочете змінити редактор цієї сторінки?',
    'pages_editor_switch_consider_following' => 'Врахуйте наступне при зміні редакторів:',
    'pages_editor_switch_consideration_a' => 'Після збереження нова опція редактора буде використовуватися будь-якими майбутніми редакторами, включаючи ті, які не можуть змінювати сам редактор редакторів.',
    'pages_editor_switch_consideration_b' => 'Це може потенційно призвести до втрати деталізації та синтаксису за певних обставин.',
    'pages_editor_switch_consideration_c' => 'Мітка або список змін, зроблених з часу останнього збереження, не буде зберігатися в цих змінах.',
    'pages_save' => 'Зберегти сторінку',
    'pages_title' => 'Заголовок сторінки',
    'pages_name' => 'Назва сторінки',
    'pages_md_editor' => 'Редактор',
    'pages_md_preview' => 'Попередній перегляд',
    'pages_md_insert_image' => 'Вставити зображення',
    'pages_md_insert_link' => 'Вставити посилання на об\'єкт',
    'pages_md_insert_drawing' => 'Вставити малюнок',
    'pages_not_in_chapter' => 'Сторінка не знаходиться в розділі',
    'pages_move' => 'Перемістити сторінку',
    'pages_move_success' => 'Сторінку переміщено до ":parentName"',
    'pages_copy' => 'Копіювати сторінку',
    'pages_copy_desination' => 'Ціль копіювання',
    'pages_copy_success' => 'Сторінка успішно скопійована',
    'pages_permissions' => 'Дозволи на сторінку',
    'pages_permissions_success' => 'Дозволи на сторінку оновлено',
    'pages_revision' => 'Версія',
    'pages_revisions' => 'Версія сторінки',
    'pages_revisions_desc' => 'Listed below are all the past revisions of this page. You can look back upon, compare, and restore old page versions if permissions allow. The full history of the page may not be fully reflected here since, depending on system configuration, old revisions could be auto-deleted.',
    'pages_revisions_named' => 'Версії сторінки для :pageName',
    'pages_revision_named' => 'Версія сторінки для :pageName',
    'pages_revision_restored_from' => 'Відновлено з #:id; :summary',
    'pages_revisions_created_by' => 'Створена',
    'pages_revisions_date' => 'Дата версії',
    'pages_revisions_number' => '#',
    'pages_revisions_sort_number' => 'Revision Number',
    'pages_revisions_numbered' => 'Версія #:id',
    'pages_revisions_numbered_changes' => 'Зміни версії #:id',
    'pages_revisions_editor' => 'Тип редактора',
    'pages_revisions_changelog' => 'Історія змін',
    'pages_revisions_changes' => 'Зміни',
    'pages_revisions_current' => 'Поточна версія',
    'pages_revisions_preview' => 'Попередній перегляд',
    'pages_revisions_restore' => 'Відновити',
    'pages_revisions_none' => 'Ця сторінка не має версій',
    'pages_copy_link' => 'Копіювати посилання',
    'pages_edit_content_link' => 'Редагувати вміст',
    'pages_permissions_active' => 'Активні дозволи сторінки',
    'pages_initial_revision' => 'Початкова публікація',
    'pages_references_update_revision' => 'Автоматичне оновлення системних посилань',
    'pages_initial_name' => 'Нова сторінка',
    'pages_editing_draft_notification' => 'Ви наразі редагуєте чернетку, що була збережена останньою :timeDiff.',
    'pages_draft_edited_notification' => 'З того часу ця сторінка була оновлена. Рекомендуємо відмовитися від цього проекту.',
    'pages_draft_page_changed_since_creation' => 'Ця сторінка була оновлена, оскільки була створена ця чернетка. Рекомендується відхилити цей проект або перейматися тим, що ви не перезапишете будь-які зміни в сторінках.',
    'pages_draft_edit_active' => [
        'start_a' => ':count користувачі(в) почали редагувати цю сторінку',
        'start_b' => ':userName розпочав редагування цієї сторінки',
        'time_a' => 'з моменту останньої оновлення сторінки',
        'time_b' => 'за останні :minCount хвилин',
        'message' => ':start :time. Будьте обережні, щоб не перезаписати оновлення інших!',
    ],
    'pages_draft_discarded' => 'Чернетка відхилена, редактор оновлено з поточним вмістом сторінки',
    'pages_specific' => 'Конкретна сторінка',
    'pages_is_template' => 'Шаблон сторінки',

    // Editor Sidebar
    'page_tags' => 'Теги сторінки',
    'chapter_tags' => 'Теги розділів',
    'book_tags' => 'Теги книг',
    'shelf_tags' => 'Теги полиць',
    'tag' => 'Тег',
    'tags' =>  'Теги',
    'tags_index_desc' => 'Tags can be applied to content within the system to apply a flexible form of categorization. Tags can have both a key and value, with the value being optional. Once applied, content can then be queried using the tag name and value.',
    'tag_name' =>  'Назва тегу',
    'tag_value' => 'Значення тегу (необов\'язково)',
    'tags_explain' => "Додайте кілька тегів, щоб краще класифікувати ваш вміст. \n Ви можете присвоїти значення тегу для більш глибокої організації.",
    'tags_add' => 'Додати ще один тег',
    'tags_remove' => 'Видалити цей тег',
    'tags_usages' => 'Усього тегів використано',
    'tags_assigned_pages' => 'Призначено до сторінок',
    'tags_assigned_chapters' => 'Призначені до груп',
    'tags_assigned_books' => 'Призначено до книг',
    'tags_assigned_shelves' => 'Призначені до полиць',
    'tags_x_unique_values' => ':count унікальних значень',
    'tags_all_values' => 'Всі значення',
    'tags_view_tags' => 'Перегляд міток',
    'tags_view_existing_tags' => 'Перегляд існуючих тегів',
    'tags_list_empty_hint' => 'Теги можуть бути призначені через бічну панель редактора сторінки, або під час редагування деталей книги, глави чи полиці.',
    'attachments' => 'Вкладення',
    'attachments_explain' => 'Завантажте файли, або додайте посилання, які відображатимуться на вашій сторінці. Їх буде видно на бічній панелі сторінки.',
    'attachments_explain_instant_save' => 'Зміни тут зберігаються миттєво.',
    'attachments_items' => 'Додані елементи',
    'attachments_upload' => 'Завантажити файл',
    'attachments_link' => 'Приєднати посилання',
    'attachments_set_link' => 'Встановити посилання',
    'attachments_delete' => 'Дійсно хочете видалити це вкладення?',
    'attachments_dropzone' => 'Перетягніть файли, або натисніть тут щоб прикріпити файл',
    'attachments_no_files' => 'Файли не завантажені',
    'attachments_explain_link' => 'Ви можете приєднати посилання, якщо не бажаєте завантажувати файл. Це може бути посилання на іншу сторінку або посилання на файл у хмарі.',
    'attachments_link_name' => 'Назва посилання',
    'attachment_link' => 'Посилання на вкладення',
    'attachments_link_url' => 'Посилання на файл',
    'attachments_link_url_hint' => 'URL-адреса сайту або файлу',
    'attach' => 'Приєднати',
    'attachments_insert_link' => 'Додати посилання на вкладення',
    'attachments_edit_file' => 'Редагувати файл',
    'attachments_edit_file_name' => 'Назва файлу',
    'attachments_edit_drop_upload' => 'Перетягніть файли, або натисніть тут щоб завантажити та перезаписати',
    'attachments_order_updated' => 'Порядок вкладень оновлено',
    'attachments_updated_success' => 'Деталі вкладень оновлено',
    'attachments_deleted' => 'Вкладення видалено',
    'attachments_file_uploaded' => 'Файл успішно завантажений',
    'attachments_file_updated' => 'Файл успішно оновлено',
    'attachments_link_attached' => 'Посилання успішно додано до сторінки',
    'templates' => 'Шаблони',
    'templates_set_as_template' => 'Сторінка це шаблон',
    'templates_explain_set_as_template' => 'Ви можете встановити цю сторінку як шаблон, щоб її вміст використовувався під час створення інших сторінок. Інші користувачі зможуть користуватися цим шаблоном, якщо вони мають права перегляду для цієї сторінки.',
    'templates_replace_content' => 'Замінити вміст сторінки',
    'templates_append_content' => 'Додати до вмісту сторінки',
    'templates_prepend_content' => 'Додати на початок вмісту сторінки',

    // Profile View
    'profile_user_for_x' => 'Користувач вже :time',
    'profile_created_content' => 'Створений контент',
    'profile_not_created_pages' => ':userName не створив жодної сторінки',
    'profile_not_created_chapters' => ':userName не створив жодного розділу',
    'profile_not_created_books' => ':userName не створив жодної книги',
    'profile_not_created_shelves' => ':userName не створив жодної полиці',

    // Comments
    'comment' => 'Коментар',
    'comments' => 'Коментарі',
    'comment_add' => 'Додати коментар',
    'comment_placeholder' => 'Залиште коментар тут',
    'comment_count' => '{0} Без коментарів|{1} 1 коментар|[2,*] :count коментарі(в)',
    'comment_save' => 'Зберегти коментар',
    'comment_saving' => 'Збереження коментаря...',
    'comment_deleting' => 'Видалення коментаря...',
    'comment_new' => 'Новий коментар',
    'comment_created' => 'прокоментував :createDiff',
    'comment_updated' => 'Оновлено :updateDiff користувачем :username',
    'comment_deleted_success' => 'Коментар видалено',
    'comment_created_success' => 'Коментар додано',
    'comment_updated_success' => 'Коментар оновлено',
    'comment_delete_confirm' => 'Ви впевнені, що хочете видалити цей коментар?',
    'comment_in_reply_to' => 'У відповідь на :commentId',

    // Revision
    'revision_delete_confirm' => 'Ви впевнені, що хочете видалити цю версію?',
    'revision_restore_confirm' => 'Дійсно відновити цю версію? Вміст поточної сторінки буде замінено.',
    'revision_delete_success' => 'Версія видалена',
    'revision_cannot_delete_latest' => 'Неможливо видалити останню версію.',

    // Copy view
    'copy_consider' => 'Будь ласка, наведені нижче при копіюванні вмісту.',
    'copy_consider_permissions' => 'Спеціальні налаштування дозволів не будуть скопійовані.',
    'copy_consider_owner' => 'Ви станете власником всіх скопійованих матеріалів.',
    'copy_consider_images' => 'Файли зображень сторінки не будуть дубльовані і оригінальні зображення збережуть зв\'язок з сторінкою, до якої вони були завантажені.',
    'copy_consider_attachments' => 'Вкладення сторінки не буде скопійовано.',
    'copy_consider_access' => 'Зміна розташування або дозволів може призвести до доступу до цього вмісту без попереднього доступу.',

    // Conversions
    'convert_to_shelf' => 'Перетворити на полиця',
    'convert_to_shelf_contents_desc' => 'Ви можете перетворити цю книгу на нову полицю з одним змістом. Розділи, що містяться в цій книзі, будуть перетворені в нові книги. Якщо ця книга містить будь-які сторінки, яких немає у главі, цю книгу буде перейменовано і містить такі сторінки, а ця книга стане частиною нової полиці.',
    'convert_to_shelf_permissions_desc' => 'Будь-які дозволи, встановлені на цій книзі, будуть скопійовані в нову полицю та до всіх нових дитячих книг, які не мають прав на виконання. Зверніть увагу, що дозволи на полицях не автоматично каскад до вмісту в межах, як вони роблять для книг.',
    'convert_book' => 'Перетворити книгу',
    'convert_book_confirm' => 'Ви впевнені, що хочете конвертувати цю книгу?',
    'convert_undo_warning' => 'Це не так легко відмінити.',
    'convert_to_book' => 'Конвертувати в книгу',
    'convert_to_book_desc' => 'Ви можете конвертувати цей розділ в нову книгу з одним контентом. Будь-які дозволи, встановлені на цьому розділі, будуть скопійовані в нову книгу, але будь-які успадковані дозволи, з батьківської книги не буде скопійований, що може призвести до зміни контролю доступу.',
    'convert_chapter' => 'Перетворити розділ',
    'convert_chapter_confirm' => 'Ви впевнені, що хочете конвертувати цей розділ?',

    // References
    'references' => 'Посилання',
    'references_none' => 'Немає відслідковуваних посилань для цього елемента.',
    'references_to_desc' => 'Показані нижче всі відомі сторінки в системі, що посилаються на цей елемент.',
];
