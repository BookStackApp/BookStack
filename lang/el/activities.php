<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'σελίδα που δημιουργήθηκε',
    'page_create_notification'    => 'Η σελίδα δημιουργήθηκε με επιτυχία',
    'page_update'                 => 'ενημερωμένη σελίδα',
    'page_update_notification'    => 'Η σελίδα ενημερώθηκε με επιτυχία',
    'page_delete'                 => 'διαγραμμένη σελίδα',
    'page_delete_notification'    => 'Η σελίδα διαγράφηκε επιτυχώς',
    'page_restore'                => 'αποκατεστημένη σελίδα',
    'page_restore_notification'   => 'Η σελίδα αποκαταστάθηκε με επιτυχία',
    'page_move'                   => 'Η σελίδα μετακινήθηκε',
    'page_move_notification'      => 'Page successfully moved',

    // Chapters
    'chapter_create'              => 'δημιουργήθηκε κεφάλαιο',
    'chapter_create_notification' => 'Το κεφάλαιο δημιουργήθηκε με επιτυχία',
    'chapter_update'              => 'ενημερωμένο κεφάλαιο',
    'chapter_update_notification' => 'Το κεφάλαιο ενημερώθηκε με επιτυχία',
    'chapter_delete'              => 'διαγραμμένο κεφάλαιο',
    'chapter_delete_notification' => 'Το κεφάλαιο διαγράφηκε επιτυχώς',
    'chapter_move'                => 'το κεφάλαιο μετακινήθηκε',
    'chapter_move_notification' => 'Chapter successfully moved',

    // Books
    'book_create'                 => 'το βιβλίο δημιουργήθηκε',
    'book_create_notification'    => 'Το βιβλίο δημιουργήθηκε με επιτυχία',
    'book_create_from_chapter'              => 'Το κεφάλαιο μετατράπηκε επιτυχώς σε βιβλίο',
    'book_create_from_chapter_notification' => 'Το κεφάλαιο μετατράπηκε επιτυχώς σε βιβλίο',
    'book_update'                 => 'ενημερωμένο βιβλίο',
    'book_update_notification'    => 'Το βιβλίο ενημερώθηκε με επιτυχία',
    'book_delete'                 => 'διαγραμμένο βιβλίο',
    'book_delete_notification'    => 'Το βιβλίο διαγράφηκε επιτυχώς',
    'book_sort'                   => 'ταξινομημένο βιβλίο',
    'book_sort_notification'      => 'Το βιβλίο επαναταξινομήθηκε επιτυχώς',

    // Bookshelves
    'bookshelf_create'            => 'δημιουργήθηκε ράφι',
    'bookshelf_create_notification'    => 'Το ράφι δημιουργήθηκε με επιτυχία',
    'bookshelf_create_from_book'    => 'το βιβλίο μετατράπηκε σε ράφι',
    'bookshelf_create_from_book_notification'    => 'Το βιβλίο μετατράπηκε σε ράφι επιτυχώς',
    'bookshelf_update'                 => 'ενημερωμένο ράφι',
    'bookshelf_update_notification'    => 'Το ράφι ενημερώθηκε επιτυχώς',
    'bookshelf_delete'                 => 'διαγραμμένο ράφι',
    'bookshelf_delete_notification'    => 'Το ράφι ενημερώθηκε επιτυχώς',

    // Revisions
    'revision_restore' => 'restored revision',
    'revision_delete' => 'deleted revision',
    'revision_delete_notification' => 'Revision successfully deleted',

    // Favourites
    'favourite_add_notification' => '":name" προστέθηκε στα αγαπημένα σας',
    'favourite_remove_notification' => '":name" προστέθηκε στα αγαπημένα σας',

    // Auth
    'auth_login' => 'logged in',
    'auth_register' => 'registered as new user',
    'auth_password_reset_request' => 'requested user password reset',
    'auth_password_reset_update' => 'reset user password',
    'mfa_setup_method' => 'configured MFA method',
    'mfa_setup_method_notification' => 'Η μέθοδος πολλαπλών παραγόντων διαμορφώθηκε επιτυχώς',
    'mfa_remove_method' => 'removed MFA method',
    'mfa_remove_method_notification' => 'Η μέθοδος πολλαπλών παραγόντων καταργήθηκε με επιτυχία',

    // Settings
    'settings_update' => 'updated settings',
    'settings_update_notification' => 'Settings successfully updated',
    'maintenance_action_run' => 'ran maintenance action',

    // Webhooks
    'webhook_create' => 'Το webhook δημιουργήθηκε',
    'webhook_create_notification' => 'Το Webhook δημιουργήθηκε με επιτυχία',
    'webhook_update' => 'ενημερωμένο webhook',
    'webhook_update_notification' => 'Το Webhook ενημερώθηκε με επιτυχία',
    'webhook_delete' => 'διαγραμμένο webhook',
    'webhook_delete_notification' => 'Το Webhook διαγράφηκε επιτυχώς',

    // Users
    'user_create' => 'created user',
    'user_create_notification' => 'User successfully created',
    'user_update' => 'updated user',
    'user_update_notification' => 'Ο Χρήστης ενημερώθηκε με επιτυχία',
    'user_delete' => 'deleted user',
    'user_delete_notification' => 'Ο Χρήστης αφαιρέθηκε επιτυχώς',

    // API Tokens
    'api_token_create' => 'created api token',
    'api_token_create_notification' => 'API token successfully created',
    'api_token_update' => 'updated api token',
    'api_token_update_notification' => 'API token successfully updated',
    'api_token_delete' => 'deleted api token',
    'api_token_delete_notification' => 'API token successfully deleted',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Ο Ρόλος δημιουργήθηκε με επιτυχία',
    'role_update' => 'updated role',
    'role_update_notification' => 'Ο Ρόλος ενημερώθηκε με επιτυχία',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Ο Ρόλος διαγράφηκε επιτυχώς',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Other
    'commented_on'                => 'σχολίασε',
    'permissions_update'          => 'ενημερωμένα δικαιώματα',
];
