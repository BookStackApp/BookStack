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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Email konfirmasi dikirim ulang, Harap periksa kotak masuk Anda.',

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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Konfigurasi ulang',
    'mfa_setup_remove_confirmation' => 'Apakah Anda yakin ingin menghapus metode autentikasi multi-faktor ini?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Aplikasi Seluler',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Kode Cadangan',
    'mfa_option_backup_codes_desc' => 'Securely store a set of one-time-use backup codes which you can enter to verify your identity.',
    'mfa_gen_confirm_and_enable' => 'Confirm and Enable',
    'mfa_gen_backup_codes_title' => 'Backup Codes Setup',
    'mfa_gen_backup_codes_desc' => 'Store the below list of codes in a safe place. When accessing the system you\'ll be able to use one of the codes as a second authentication mechanism.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Each code can only be used once',
    'mfa_gen_totp_title' => 'Mobile App Setup',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verifikasi menggunakan aplikasi seluler',
    'mfa_verify_use_backup_codes' => 'Verifikasi menggunakan kode cadangan',
    'mfa_verify_backup_code' => 'Kode Cadangan',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
