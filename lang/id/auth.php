<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Kredensial tidak cocok dengan catatan kami.',
    'throttle' => 'Terlalu banyak upaya masuk. Silahkan mencoba lagi dalam :seconds detik.',

    // Login & Register
    'sign_up' => 'Daftar',
    'log_in' => 'Gabung',
    'log_in_with' => 'Masuk dengan :socialDriver',
    'sign_up_with' => 'Daftar dengan :socialDriver',
    'logout' => 'Keluar',

    'name' => 'Nama',
    'username' => 'Nama Pengguna',
    'email' => 'Email',
    'password' => 'Kata Sandi',
    'password_confirm' => 'Konfirmasi Kata Sandi',
    'password_hint' => 'Harus minimal 8 karakter',
    'forgot_password' => 'Lupa Password?',
    'remember_me' => 'Ingat saya',
    'ldap_email_hint' => 'Harap masukkan email yang akan digunakan untuk akun ini.',
    'create_account' => 'Membuat Akun',
    'already_have_account' => 'Sudah punya akun?',
    'dont_have_account' => 'Tidak punya akun?',
    'social_login' => 'Masuk dengan sosial media',
    'social_registration' => 'Daftar dengan sosial media',
    'social_registration_text' => 'Daftar dan masuk menggunakan layanan lain.',

    'register_thanks' => 'Terima kasih telah mendaftar!',
    'register_confirm' => 'Silakan periksa email Anda dan klik tombol konfirmasi untuk mengakses :appName.',
    'registrations_disabled' => 'Pendaftaran saat ini dinonaktifkan',
    'registration_email_domain_invalid' => 'Domain email tersebut tidak memiliki akses ke aplikasi ini',
    'register_success' => 'Terima kasih telah mendaftar! Anda sekarang terdaftar dan masuk.',

    // Login auto-initiation
    'auto_init_starting' => 'Mencoba masuk',
    'auto_init_starting_desc' => 'Kami sedang menghubungi sistem autentikasi Anda untuk memulai proses login. Jika tidak ada kemajuan setelah 5 detik, Anda dapat mencoba mengklik link di bawah ini.',
    'auto_init_start_link' => 'Lanjutkan dengan otentikasi',

    // Password Reset
    'reset_password' => 'Atur ulang kata sandi',
    'reset_password_send_instructions' => 'Masukkan email Anda di bawah ini dan Anda akan dikirimi email dengan tautan pengaturan ulang kata sandi.',
    'reset_password_send_button' => 'Kirim Tautan Atur Ulang',
    'reset_password_sent' => 'Tautan pengaturan ulang kata sandi akan dikirim ke :email jika alamat email ditemukan di sistem.',
    'reset_password_success' => 'Kata sandi Anda telah berhasil diatur ulang.',
    'email_reset_subject' => 'Atur ulang kata sandi :appName anda',
    'email_reset_text' => 'Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi untuk akun Anda.',
    'email_reset_not_requested' => 'Jika Anda tidak meminta pengaturan ulang kata sandi, tidak ada tindakan lebih lanjut yang diperlukan.',

    // Email Confirmation
    'email_confirm_subject' => 'Konfirmasikan email Anda di :appName',
    'email_confirm_greeting' => 'Terima kasih telah bergabung :appName!',
    'email_confirm_text' => 'Silakan konfirmasi alamat email Anda dengan mengklik tombol di bawah ini:',
    'email_confirm_action' => 'Konfirmasi email',
    'email_confirm_send_error' => 'Konfirmasi email diperlukan tetapi sistem tidak dapat mengirim email. Hubungi admin untuk memastikan email disiapkan dengan benar.',
    'email_confirm_success' => 'Email Anda sudah terkonfirmasi! Anda seharusnya sudah bisa masuk menggunakan email ini.',
    'email_confirm_resent' => 'Email konfirmasi dikirim ulang, Harap periksa kotak masuk Anda.',
    'email_confirm_thanks' => 'Terima kasih untuk mengkonfirmasi!',
    'email_confirm_thanks_desc' => 'Harap tunggu sebentar, konfirmasi Anda sedang ditangani. Jika Anda tidak dipindahkan setelah 3 detik, tekan link "Selanjutnya" dibawah ini untuk melanjutkan.',

    'email_not_confirmed' => 'Alamat Email Tidak Dikonfirmasi',
    'email_not_confirmed_text' => 'Alamat email Anda belum dikonfirmasi.',
    'email_not_confirmed_click_link' => 'Silakan klik link di email yang dikirimkan segera setelah Anda mendaftar.',
    'email_not_confirmed_resend' => 'Jika Anda tidak dapat menemukan email tersebut, Anda dapat mengirim ulang email konfirmasi dengan mengirimkan formulir di bawah ini.',
    'email_not_confirmed_resend_button' => 'Mengirimkan kembali email konfirmasi',

    // User Invite
    'user_invite_email_subject' => 'Anda telah diundang untuk bergabung di :appName!',
    'user_invite_email_greeting' => 'Sebuah akun telah dibuat untuk Anda di :appName.',
    'user_invite_email_text' => 'Klik tombol di bawah untuk mengatur kata sandi akun dan mendapatkan akses:',
    'user_invite_email_action' => 'Atur Kata Sandi Akun',
    'user_invite_page_welcome' => 'Selamat datang di :appName!',
    'user_invite_page_text' => 'Untuk menyelesaikan akun Anda dan mendapatkan akses, Anda perlu mengatur kata sandi yang akan digunakan untuk masuk ke :appName pada kunjungan berikutnya.',
    'user_invite_page_confirm_button' => 'Konfirmasi Kata sandi',
    'user_invite_success_login' => 'Kata sandi diset, Anda seharusnya sudah bisa masuk menggunakan kata sandi yang sudah diset untuk mengakses :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Atur Multi-Factor Otentikasi',
    'mfa_setup_desc' => 'Mengatur multi-factor otentikasi sebagai tambahan ekstra keamanan untuk akun Anda.',
    'mfa_setup_configured' => 'Sudah dikonfigurasi',
    'mfa_setup_reconfigure' => 'Konfigurasi ulang',
    'mfa_setup_remove_confirmation' => 'Apakah Anda yakin ingin menghapus metode autentikasi multi-faktor ini?',
    'mfa_setup_action' => 'Atur',
    'mfa_backup_codes_usage_limit_warning' => 'Anda memiliki kurang dari 5 kode cadangan yang tersisa. Harap buat dan simpan set baru sebelum Anda kehabisan kode untuk mencegah akun Anda terkunci.',
    'mfa_option_totp_title' => 'Aplikasi Seluler',
    'mfa_option_totp_desc' => 'Untuk menggunakan autentikasi multi-faktor, Anda memerlukan aplikasi seluler yang mendukung TOTP seperti Google Authenticator, Authy, atau Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Kode Cadangan',
    'mfa_option_backup_codes_desc' => 'Menghasilkan serangkaian kode cadangan sekali pakai yang akan Anda masukkan saat masuk untuk memverifikasi identitas Anda. Pastikan untuk menyimpannya di tempat yang aman.',
    'mfa_gen_confirm_and_enable' => 'Konfirmasi dan Aktifkan',
    'mfa_gen_backup_codes_title' => 'Pengaturan Kode Cadangan',
    'mfa_gen_backup_codes_desc' => 'Simpan daftar kode di bawah ini di tempat yang aman. Saat mengakses sistem, Anda dapat menggunakan salah satu kode sebagai mekanisme autentikasi kedua.',
    'mfa_gen_backup_codes_download' => 'Unduh Kode',
    'mfa_gen_backup_codes_usage_warning' => 'Setiap kode hanya dapat digunakan satu kali',
    'mfa_gen_totp_title' => 'Pengaturan Aplikasi Seluler',
    'mfa_gen_totp_desc' => 'Untuk menggunakan autentikasi multi-faktor, Anda memerlukan aplikasi seluler yang mendukung TOTP seperti Google Authenticator, Authy, atau Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Pindai kode QR di bawah ini menggunakan aplikasi autentikasi pilihan Anda untuk memulai.',
    'mfa_gen_totp_verify_setup' => 'Verifikasi Pengaturan',
    'mfa_gen_totp_verify_setup_desc' => 'Verifikasi bahwa semuanya berfungsi dengan memasukkan kode yang dibuat dalam aplikasi autentikasi Anda pada kolom input di bawah ini:',
    'mfa_gen_totp_provide_code_here' => 'Berikan kode yang dihasilkan aplikasi Anda di sini',
    'mfa_verify_access' => 'Verifikasi Akses',
    'mfa_verify_access_desc' => 'Akun pengguna Anda mengharuskan Anda mengonfirmasi identitas Anda melalui tingkat verifikasi tambahan sebelum Anda diberikan akses. Verifikasi menggunakan salah satu metode yang telah Anda konfigurasikan untuk melanjutkan.',
    'mfa_verify_no_methods' => 'Tidak Ada Metode yang Dikonfigurasi',
    'mfa_verify_no_methods_desc' => 'Tidak ada metode autentikasi multi-faktor yang ditemukan untuk akun Anda. Anda perlu menyiapkan setidaknya satu metode sebelum mendapatkan akses.',
    'mfa_verify_use_totp' => 'Verifikasi menggunakan aplikasi seluler',
    'mfa_verify_use_backup_codes' => 'Verifikasi menggunakan kode cadangan',
    'mfa_verify_backup_code' => 'Kode Cadangan',
    'mfa_verify_backup_code_desc' => 'Masukkan salah satu kode cadangan Anda yang tersisa di bawah ini:',
    'mfa_verify_backup_code_enter_here' => 'Masukkan kode cadangan di sini',
    'mfa_verify_totp_desc' => 'Masukkan kode yang dibuat menggunakan aplikasi seluler Anda di bawah ini:',
    'mfa_setup_login_notification' => 'Metode multi-faktor dikonfigurasi. Silakan masuk lagi menggunakan metode yang dikonfigurasi.',
];
