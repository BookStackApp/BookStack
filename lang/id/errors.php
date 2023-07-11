<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Anda tidak memiliki izin untuk mengakses halaman yang diminta.',
    'permissionJson' => 'Anda tidak memiliki izin untuk melakukan tindakan yang diminta.',

    // Auth
    'error_user_exists_different_creds' => 'Pengguna dengan email :email sudah ada tetapi dengan kredensial berbeda.',
    'email_already_confirmed' => 'Email telah dikonfirmasi, Coba masuk.',
    'email_confirmation_invalid' => 'Token konfirmasi ini tidak valid atau telah digunakan, Silakan coba mendaftar lagi.',
    'email_confirmation_expired' => 'Token konfirmasi telah kedaluwarsa, Email konfirmasi baru telah dikirim.',
    'email_confirmation_awaiting' => 'Alamat email untuk akun yang digunakan perlu dikonfirmasi',
    'ldap_fail_anonymous' => 'Akses LDAP gagal menggunakan pengikatan anonim',
    'ldap_fail_authed' => 'Akses LDAP gagal menggunakan rincian dn & sandi yang diberikan',
    'ldap_extension_not_installed' => 'Ekstensi LDAP PHP tidak terpasang',
    'ldap_cannot_connect' => 'Tidak dapat terhubung ke server ldap, Koneksi awal gagal',
    'saml_already_logged_in' => 'Telah masuk',
    'saml_user_not_registered' => 'Pengguna :name tidak terdaftar dan pendaftaran otomatis dinonaktifkan',
    'saml_no_email_address' => 'Tidak dapat menemukan sebuah alamat email untuk pengguna ini, dalam data yang diberikan oleh sistem autentikasi eksternal',
    'saml_invalid_response_id' => 'Permintaan dari sistem otentikasi eksternal tidak dikenali oleh sebuah proses yang dimulai oleh aplikasi ini. Menavigasi kembali setelah masuk dapat menyebabkan masalah ini.',
    'saml_fail_authed' => 'Masuk menggunakan :system gagal, sistem tidak memberikan otorisasi yang berhasil',
    'oidc_already_logged_in' => 'Already logged in',
    'oidc_user_not_registered' => 'The user :name is not registered and automatic registration is disabled',
    'oidc_no_email_address' => 'Could not find an email address, for this user, in the data provided by the external authentication system',
    'oidc_fail_authed' => 'Login using :system failed, system did not provide successful authorization',
    'social_no_action_defined' => 'Tidak ada tindakan yang ditentukan',
    'social_login_bad_response' => "Kesalahan yang diterima selama masuk menggunakan :socialAccount : \n:error",
    'social_account_in_use' => 'Akun :socialAccount ini sudah digunakan, Coba masuk melalui opsi :socialAccount.',
    'social_account_email_in_use' => 'Email :email sudah digunakan. Jika Anda sudah memiliki akun, Anda dapat menghubungkan :socialAccount Anda dari pengaturan profil Anda.',
    'social_account_existing' => 'Akun :socialAccount ini sudah dilampirkan ke profil Anda.',
    'social_account_already_used_existing' => 'Akun :socialAccount ini sudah digunakan oleh pengguna lain.',
    'social_account_not_used' => 'Akun :socialAccount ini tidak ditautkan ke pengguna mana pun. Harap lampirkan di dalam pengaturan profil Anda. ',
    'social_account_register_instructions' => 'Jika Anda belum memiliki akun, Anda dapat mendaftarkan akun menggunakan opsi :socialAccount.',
    'social_driver_not_found' => 'Pengemudi sosial tidak ditemukan',
    'social_driver_not_configured' => 'Pengaturan sosial :socialAccount Anda tidak dikonfigurasi dengan benar.',
    'invite_token_expired' => 'Tautan undangan ini telah kedaluwarsa. Sebagai gantinya, Anda dapat mencoba mengatur ulang kata sandi akun Anda.',

    // System
    'path_not_writable' => 'Jalur berkas :filePath tidak dapat diunggah. Pastikan berkas tersebut dapat ditulis ke server.',
    'cannot_get_image_from_url' => 'Tidak dapat mengambil gambar dari :url',
    'cannot_create_thumbs' => 'Server tidak dapat membuat thumbnail. Harap periksa apakah Anda telah memasang ekstensi GD PHP.',
    'server_upload_limit' => 'Server tidak mengizinkan unggahan dengan ukuran ini. Harap coba ukuran berkas yang lebih kecil.',
    'uploaded'  => 'Server tidak mengizinkan unggahan dengan ukuran ini. Harap coba ukuran berkas yang lebih kecil.',

    // Drawing & Images
    'image_upload_error' => 'Terjadi kesalahan saat mengunggah gambar',
    'image_upload_type_error' => 'Jenis gambar yang diunggah tidak valid',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'drawing_data_not_found' => 'Drawing data could not be loaded. The drawing file might no longer exist or you may not have permission to access it.',

    // Attachments
    'attachment_not_found' => 'Lampiran tidak ditemukan',
    'attachment_upload_error' => 'An error occurred uploading the attachment file',

    // Pages
    'page_draft_autosave_fail' => 'Gagal menyimpan draf. Pastikan Anda memiliki koneksi internet sebelum menyimpan halaman ini',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'Tidak dapat menghapus sebuah halaman saat diatur sebagai sebuah halaman beranda',

    // Entities
    'entity_not_found' => 'Entitas tidak ditemukan',
    'bookshelf_not_found' => 'Shelf not found',
    'book_not_found' => 'Buku tidak ditemukan',
    'page_not_found' => 'Halaman tidak ditemukan',
    'chapter_not_found' => 'Bab tidak ditemukan',
    'selected_book_not_found' => 'Buku yang dipilih tidak ditemukan',
    'selected_book_chapter_not_found' => 'Buku atau Bab yang dipilih tidak ditemukan',
    'guests_cannot_save_drafts' => 'Tamu tidak dapat menyimpan Draf',

    // Users
    'users_cannot_delete_only_admin' => 'Anda tidak dapat menghapus satu-satunya admin',
    'users_cannot_delete_guest' => 'Anda tidak dapat menghapus pengguna tamu',

    // Roles
    'role_cannot_be_edited' => 'Peran ini tidak dapat disunting',
    'role_system_cannot_be_deleted' => 'Peran ini adalah peran sistem dan tidak dapat dihapus',
    'role_registration_default_cannot_delete' => 'Peran ini tidak dapat dihapus jika disetel sebagai peran pendaftaran default',
    'role_cannot_remove_only_admin' => 'Pengguna ini adalah satu-satunya pengguna yang ditetapkan ke peran administrator. Tetapkan peran administrator untuk pengguna lain sebelum mencoba untuk menghapusnya di sini.',

    // Comments
    'comment_list' => 'Terjadi kesalahan saat mengambil komentar.',
    'cannot_add_comment_to_draft' => 'Anda tidak dapat menambahkan komentar ke draf.',
    'comment_add' => 'Terjadi kesalahan saat menambahkan / memperbarui komentar.',
    'comment_delete' => 'Terjadi kesalahan saat menghapus komentar.',
    'empty_comment' => 'Tidak dapat menambahkan komentar kosong.',

    // Error pages
    '404_page_not_found' => 'Halaman tidak ditemukan',
    'sorry_page_not_found' => 'Maaf, Halaman yang Anda cari tidak dapat ditemukan.',
    'sorry_page_not_found_permission_warning' => 'Jika Anda mengharapkan halaman ini ada, Anda mungkin tidak memiliki izin untuk melihatnya.',
    'image_not_found' => 'Gambar tidak ditemukan',
    'image_not_found_subtitle' => 'Maaf, Berkas gambar yang Anda cari tidak dapat ditemukan.',
    'image_not_found_details' => 'Jika Anda mengharapkan gambar ini ada, gambar itu mungkin telah dihapus.',
    'return_home' => 'Kembali ke home',
    'error_occurred' => 'Terjadi kesalahan',
    'app_down' => ':appName sedang down sekarang',
    'back_soon' => 'Ini akan segera kembali.',

    // API errors
    'api_no_authorization_found' => 'Tidak ada token otorisasi yang ditemukan pada permintaan tersebut',
    'api_bad_authorization_format' => 'Token otorisasi ditemukan pada permintaan tetapi formatnya salah',
    'api_user_token_not_found' => 'Tidak ditemukan token API yang cocok untuk token otorisasi yang diberikan',
    'api_incorrect_token_secret' => 'Rahasia yang diberikan untuk token API bekas yang diberikan salah',
    'api_user_no_api_permission' => 'Pemilik token API yang digunakan tidak memiliki izin untuk melakukan panggilan API',
    'api_user_token_expired' => 'Token otorisasi yang digunakan telah kedaluwarsa',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Kesalahan dilempar saat mengirim email uji:',

];
