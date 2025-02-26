<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'telah membuat halaman',
    'page_create_notification'    => 'Jenis Halaman berhasil dibuat',
    'page_update'                 => 'halaman telah diperbaharui',
    'page_update_notification'    => 'Halaman berhasil diperbarui',
    'page_delete'                 => 'halaman dihapus',
    'page_delete_notification'    => 'Halaman berhasil dihapus',
    'page_restore'                => 'halaman telah dipulihkan',
    'page_restore_notification'   => 'Halaman berhasil dipulihkan',
    'page_move'                   => 'halaman dipindahkan',
    'page_move_notification'      => 'Halaman berhasil dipindahkan',

    // Chapters
    'chapter_create'              => 'membuat bab',
    'chapter_create_notification' => 'Bab berhasil dibuat',
    'chapter_update'              => 'bab diperbaharui',
    'chapter_update_notification' => 'Bab berhasil diperbarui',
    'chapter_delete'              => 'hapus bab',
    'chapter_delete_notification' => 'Bab berhasil dihapus',
    'chapter_move'                => 'bab dipindahkan',
    'chapter_move_notification' => 'Bab berhasil dipindahkan',

    // Books
    'book_create'                 => 'membuat buku',
    'book_create_notification'    => 'Buku berhasil dibuat',
    'book_create_from_chapter'              => 'mengkonversi bab ke buku',
    'book_create_from_chapter_notification' => 'Bab berhasil dikonversi menjadi buku',
    'book_update'                 => 'update buku',
    'book_update_notification'    => 'Buku berhasil diperbarui',
    'book_delete'                 => 'hapus buku',
    'book_delete_notification'    => 'Buku berhasil dihapus',
    'book_sort'                   => 'buku yang diurutkan',
    'book_sort_notification'      => 'Buku berhasil diurutkan',

    // Bookshelves
    'bookshelf_create'            => 'membuat rak',
    'bookshelf_create_notification'    => 'Rak berhasil dibuat',
    'bookshelf_create_from_book'    => 'mengkonversi buku ke rak',
    'bookshelf_create_from_book_notification'    => 'Buku berhasil dikonversi menjadi rak',
    'bookshelf_update'                 => 'memperbarui rak',
    'bookshelf_update_notification'    => 'Rak berhasil diperbarui',
    'bookshelf_delete'                 => 'menghapus rak',
    'bookshelf_delete_notification'    => 'Rak berhasil dihapus',

    // Revisions
    'revision_restore' => 'revisi yang dipulihkan',
    'revision_delete' => 'revisi yang dihapus',
    'revision_delete_notification' => 'Revisi berhasil dihapus',

    // Favourites
    'favourite_add_notification' => '":name" telah ditambahkan ke favorit Anda',
    'favourite_remove_notification' => '":name" telah dihapus dari favorit Anda',

    // Watching
    'watch_update_level_notification' => 'Preferensi pantauan berhasil diperbarui',

    // Auth
    'auth_login' => 'telah masuk',
    'auth_register' => 'daftar sebagai user baru',
    'auth_password_reset_request' => 'permintaan pengguna mengatur ulang kata sandi',
    'auth_password_reset_update' => 'atur ulang kata sandi pengguna',
    'mfa_setup_method' => 'metode MFA yang dikonfigurasi',
    'mfa_setup_method_notification' => 'Metode multi-faktor sukses dikonfigurasi',
    'mfa_remove_method' => 'menghapus metode MFA',
    'mfa_remove_method_notification' => 'Metode multi-faktor sukses dihapus',

    // Settings
    'settings_update' => 'updated settings',
    'settings_update_notification' => 'Pengaturan berhasil diperbarui',
    'maintenance_action_run' => 'menjalankan tindakan pemeliharaan',

    // Webhooks
    'webhook_create' => 'membuat webhook',
    'webhook_create_notification' => 'Webhook berhasil dibuat',
    'webhook_update' => 'memperbarui webhook',
    'webhook_update_notification' => 'Webhook berhasil diperbarui',
    'webhook_delete' => 'menghapus webhook',
    'webhook_delete_notification' => 'Webhook berhasil dihapus',

    // Imports
    'import_create' => 'created import',
    'import_create_notification' => 'Import successfully uploaded',
    'import_run' => 'updated import',
    'import_run_notification' => 'Content successfully imported',
    'import_delete' => 'deleted import',
    'import_delete_notification' => 'Import successfully deleted',

    // Users
    'user_create' => 'pengguna yang dibuat',
    'user_create_notification' => 'Pengguna berhasil dibuat',
    'user_update' => 'perbarui Pengguna',
    'user_update_notification' => 'Pengguna berhasil diperbarui',
    'user_delete' => 'pengguna yang dihapus',
    'user_delete_notification' => 'Pengguna berhasil dihapus',

    // API Tokens
    'api_token_create' => 'API token yang dibuat',
    'api_token_create_notification' => 'Token API berhasil dibuat',
    'api_token_update' => 'token API yang diperbarui',
    'api_token_update_notification' => 'token API berhasil dirubah',
    'api_token_delete' => 'token API yang dihapus',
    'api_token_delete_notification' => 'token API berhasil dihapus ',

    // Roles
    'role_create' => 'created role',
    'role_create_notification' => 'Peran berhasil dibuat',
    'role_update' => 'updated role',
    'role_update_notification' => 'Peran berhasil diperbarui',
    'role_delete' => 'deleted role',
    'role_delete_notification' => 'Peran berhasil dihapus',

    // Recycle Bin
    'recycle_bin_empty' => 'emptied recycle bin',
    'recycle_bin_restore' => 'restored from recycle bin',
    'recycle_bin_destroy' => 'removed from recycle bin',

    // Comments
    'commented_on'                => 'berkomentar pada',
    'comment_create'              => 'added comment',
    'comment_update'              => 'updated comment',
    'comment_delete'              => 'deleted comment',

    // Sort Rules
    'sort_rule_create' => 'created sort rule',
    'sort_rule_create_notification' => 'Sort rule successfully created',
    'sort_rule_update' => 'updated sort rule',
    'sort_rule_update_notification' => 'Sort rule successfully updated',
    'sort_rule_delete' => 'deleted sort rule',
    'sort_rule_delete_notification' => 'Sort rule successfully deleted',

    // Other
    'permissions_update'          => 'izin diperbarui',
];
