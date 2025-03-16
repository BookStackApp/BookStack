<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'sayfa oluşturdu',
    'page_create_notification'    => 'Sayfa Başarıyla Oluşturuldu',
    'page_update'                 => 'sayfayı güncelledi',
    'page_update_notification'    => 'Sayfa başarıyla güncellendi',
    'page_delete'                 => 'sayfa silindi',
    'page_delete_notification'    => 'Sayfa başarıyla silindi',
    'page_restore'                => 'sayfayı eski haline getirdi',
    'page_restore_notification'   => 'Sayfa Başarıyla Eski Haline Getirildi',
    'page_move'                   => 'sayfa taşındı',
    'page_move_notification'      => 'Sayfa başarıyla taşındı',

    // Chapters
    'chapter_create'              => 'bölüm oluşturdu',
    'chapter_create_notification' => 'Bölüm başarıyla oluşturuldu',
    'chapter_update'              => 'bölümü güncelledi',
    'chapter_update_notification' => 'Bölüm başarıyla güncellendi',
    'chapter_delete'              => 'bölümü sildi',
    'chapter_delete_notification' => 'Bölüm başarıyla silindi',
    'chapter_move'                => 'bölümü taşıdı',
    'chapter_move_notification' => 'Bölüm başarıyla taşındı',

    // Books
    'book_create'                 => 'kitap oluşturdu',
    'book_create_notification'    => 'Kitap başarıyla oluşturuldu',
    'book_create_from_chapter'              => 'converted chapter to book',
    'book_create_from_chapter_notification' => 'Bölüm başarıyla kitaba dönüştürüldü',
    'book_update'                 => 'güncellenen kitap',
    'book_update_notification'    => 'Kitap başarıyla güncellendi',
    'book_delete'                 => 'kitabı sildi',
    'book_delete_notification'    => 'Kitap başarıyla silindi',
    'book_sort'                   => 'kitabı sıraladı',
    'book_sort_notification'      => 'Kitap başarıyla yeniden sıralandı',

    // Bookshelves
    'bookshelf_create'            => 'kitaplık oluşturuldu',
    'bookshelf_create_notification'    => 'Kitaplık başarıyla oluşturuldu',
    'bookshelf_create_from_book'    => 'converted book to shelf',
    'bookshelf_create_from_book_notification'    => 'Kitap başarıyla kitaplığa dönüştürüldü',
    'bookshelf_update'                 => 'updated shelf',
    'bookshelf_update_notification'    => 'Kitaplık başarıyla güncellendi',
    'bookshelf_delete'                 => 'deleted shelf',
    'bookshelf_delete_notification'    => 'Kitaplık başarıyla silindi',

    // Revisions
    'revision_restore' => 'geri yüklenen revizyon',
    'revision_delete' => 'silinmiş revizyon',
    'revision_delete_notification' => 'Değişiklik başarıyla silindi',

    // Favourites
    'favourite_add_notification' => '":name" favorilerinize eklendi',
    'favourite_remove_notification' => '":name" favorilerinizden çıkarıldı',

    // Watching
    'watch_update_level_notification' => 'Watch preferences successfully updated',

    // Auth
    'auth_login' => 'oturum açıldı',
    'auth_register' => 'yeni kullanıcı olarak kayıt yapıldı',
    'auth_password_reset_request' => 'talep edilmiş kullanıcı parola sıfırlamaları',
    'auth_password_reset_update' => 'Kullanıcı parolasını sıfırla',
    'mfa_setup_method' => 'uygulanan MFA yöntemi',
    'mfa_setup_method_notification' => 'Çok aşamalı kimlik doğrulama yöntemi başarıyla yapılandırıldı',
    'mfa_remove_method' => 'kaldırılan MFA yöntemi',
    'mfa_remove_method_notification' => 'Çok aşamalı kimlik doğrulama yöntemi başarıyla kaldırıldı',

    // Settings
    'settings_update' => 'güncellenmiş ayarlar',
    'settings_update_notification' => 'Ayarlar başarıyla güncellendi',
    'maintenance_action_run' => 'bakım işlemine başla',

    // Webhooks
    'webhook_create' => 'web kancası oluşturuldu',
    'webhook_create_notification' => 'Web kancası başarıyla oluşturuldu',
    'webhook_update' => 'web kancası güncellendi',
    'webhook_update_notification' => 'Web kancası başarıyla güncellendi',
    'webhook_delete' => 'web kancası silindi',
    'webhook_delete_notification' => 'Web kancası başarıyla silindi',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'oluşturan kullanıcı',
    'user_create_notification' => 'Kullanıcı başarıyla oluşturuldu',
    'user_update' => 'updated user',
    'user_update_notification' => 'Kullanıcı başarıyla güncellendi',
    'user_delete' => 'kullanıcı silindi',
    'user_delete_notification' => 'Kullanıcı başarıyla silindi',

    // API Tokens
    'api_token_create' => 'created API token',
    'api_token_create_notification' => 'API anahtarı başarıyla oluşturuldu',
    'api_token_update' => 'updated API token',
    'api_token_update_notification' => 'API anahtarı başarıyla güncellendi',
    'api_token_delete' => 'deleted API token',
    'api_token_delete_notification' => 'API anahtarı başarıyla silindi',

    // Roles
    'role_create' => 'oluşturulan rol',
    'role_create_notification' => 'Rol başarıyla oluşturuldu',
    'role_update' => 'güncellenmiş rol',
    'role_update_notification' => 'Rol başarıyla güncellendi',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Rol başarıyla silindi',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'çöp kutusundan geri getirilen',
    'recycle_bin_destroy' => 'çöp kutusundan kaldırılan',

    // Comments
    'commented_on'                => 'yorum yaptı',
    'comment_create'              => 'eklenen yorum',
    'comment_update'              => 'güncellenen yorum',
    'comment_delete'              => 'silinen yorum',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'güncellenmiş izinler',
];
