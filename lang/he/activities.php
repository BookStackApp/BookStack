<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'הדף נוצר',
    'page_create_notification'    => 'הדף נוצר בהצלחה',
    'page_update'                 => 'הדף עודכן',
    'page_update_notification'    => 'הדף עודכן בהצלחה',
    'page_delete'                 => 'הדף נמחק',
    'page_delete_notification'    => 'הדף הוסר בהצלחה',
    'page_restore'                => 'הדף שוחזר',
    'page_restore_notification'   => 'הדף שוחזר בהצלחה',
    'page_move'                   => 'דף הועבר',
    'page_move_notification'      => 'הדף הוזז בהצלחה',

    // Chapters
    'chapter_create'              => 'פרק נוצר',
    'chapter_create_notification' => 'הפרק נוצר בהצלחה',
    'chapter_update'              => 'פרק עודכן',
    'chapter_update_notification' => 'הפרק עודכן בהצלחה',
    'chapter_delete'              => 'פרק נמחק',
    'chapter_delete_notification' => 'הפרק נמחק בהצלחה',
    'chapter_move'                => 'פרק הועבר',
    'chapter_move_notification' => 'פרק הוזז בהצלחה',

    // Books
    'book_create'                 => 'ספר נוצר',
    'book_create_notification'    => 'ספר נוצר בהצלחה',
    'book_create_from_chapter'              => 'המר פרק לספר',
    'book_create_from_chapter_notification' => 'הפרק הומר בהצלחה לספר',
    'book_update'                 => 'ספר הועדכן',
    'book_update_notification'    => 'ספר התעדכן בהצלחה',
    'book_delete'                 => 'ספר נמחק',
    'book_delete_notification'    => 'ספר נמחק בהצלחה',
    'book_sort'                   => 'ספר ממויין',
    'book_sort_notification'      => 'ספר מויין מחדש בהצלחה',

    // Bookshelves
    'bookshelf_create'            => 'מדף נוצר',
    'bookshelf_create_notification'    => 'המדף נוצר בהצלחה',
    'bookshelf_create_from_book'    => 'המר ספר למדף',
    'bookshelf_create_from_book_notification'    => 'הספר הוסב בהצלחה למדף',
    'bookshelf_update'                 => 'מדף עודכן',
    'bookshelf_update_notification'    => 'מדף עודכן בהצלחה',
    'bookshelf_delete'                 => 'מדף שנמחק',
    'bookshelf_delete_notification'    => 'מדף נמחק בהצלחה',

    // Revisions
    'revision_restore' => 'גרסא שוחזרה',
    'revision_delete' => 'גרסא הוסרה',
    'revision_delete_notification' => 'גרסא הוסרה בהצלחה',

    // Favourites
    'favourite_add_notification' => '":name" הוסף למועדפים',
    'favourite_remove_notification' => '":name" הוסר מהמועדפים',

    // Watching
    'watch_update_level_notification' => 'העדפות צפייה עודכנו בהצלחה',

    // Auth
    'auth_login' => 'מחובר',
    'auth_register' => 'נרשם כמשתמש חדש',
    'auth_password_reset_request' => 'בקשת איפוס סיסמה למשתמש בוצעה בהצלחה',
    'auth_password_reset_update' => 'איפוס סיסמה למשתמש',
    'mfa_setup_method' => 'הגדרת אימות דו-שלבי פעיל',
    'mfa_setup_method_notification' => 'הגדרת אימות דו-שלבי בוצע בהצלחה',
    'mfa_remove_method' => 'הגדרת אימות דו-שלבי הוסר',
    'mfa_remove_method_notification' => 'אפשרות אימות דו-שלבי הוסר בהצלחה',

    // Settings
    'settings_update' => 'הגדרות עודכנו בהצלחה',
    'settings_update_notification' => 'ההגדרות עודכנו בהצלחה',
    'maintenance_action_run' => 'פעולות תחזוקה שהופעלו',

    // Webhooks
    'webhook_create' => 'webook נוצר',
    'webhook_create_notification' => 'יצירת Webhook בוצעה בהצלחה',
    'webhook_update' => 'webhook עודכן',
    'webhook_update_notification' => 'webook עודכן בהצלחה',
    'webhook_delete' => 'Webhook נמחק',
    'webhook_delete_notification' => 'Webook נמחק בהצלחה',

    // Imports
    'import_create' => 'יבוא נוצר',
    'import_create_notification' => 'יבוא עודכן בהצלחה',
    'import_run' => 'יבוא עודכן',
    'import_run_notification' => 'תוכן יובא בהצלחה',
    'import_delete' => 'יבוא נמחק',
    'import_delete_notification' => 'יבוא נמחק בהצלחה',

    // Users
    'user_create' => 'משתמש חדש נוצר',
    'user_create_notification' => 'משתמש נוצר בהצלחה',
    'user_update' => 'משתמש עודכן',
    'user_update_notification' => 'משתמש עודכן בהצלחה',
    'user_delete' => 'משתמש נמחק',
    'user_delete_notification' => 'משתמש הוסר בהצלחה',

    // API Tokens
    'api_token_create' => 'API Token נוצר',
    'api_token_create_notification' => 'API Token נוצר בהצלחה',
    'api_token_update' => 'API Token עודכן',
    'api_token_update_notification' => 'API Token עודכן בהצלחה',
    'api_token_delete' => 'API Token נמחק',
    'api_token_delete_notification' => 'API Token נמחק בהצלחה',

    // Roles
    'role_create' => 'תפקיד נוצר',
    'role_create_notification' => 'תפקיד נוצר בהצלחה',
    'role_update' => 'תפקיד עודכן',
    'role_update_notification' => 'תפקיד עודכן בהצלחה',
    'role_delete' => 'תפקיד נמחק',
    'role_delete_notification' => 'תפקיד נמחק בהצלחה',

    // Recycle Bin
    'recycle_bin_empty' => 'סל המחזור רוקן',
    'recycle_bin_restore' => 'שוחזר מסל המחזור',
    'recycle_bin_destroy' => 'נמחק מסל המחזור',

    // Comments
    'commented_on'                => 'הגיב/ה על',
    'comment_create'              => 'הערה הוספה',
    'comment_update'              => 'תגובה הוספה',
    'comment_delete'              => 'תגובה נמחקה',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'הרשאות עודכנו',
];
