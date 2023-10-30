<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Uchbu ma‘lumotlar, bizdagi ma‘lumotlarga mos kelmadi.',
    'throttle' => 'Kirishga urinishlar juda ko‘p. Iltimos :seconds soniyadan so‘ng urinib ko‘ring.',

    // Login & Register
    'sign_up' => 'Ro‘yxatdan o‘tish',
    'log_in' => 'Kirish',
    'log_in_with' => ':socialDriver orqali kirish',
    'sign_up_with' => ':socialDriver orqali ro‘yxatdan o‘tish',
    'logout' => 'Chiqish',

    'name' => 'Ism',
    'username' => 'Foydalanuvchi nomi',
    'email' => 'Elektron pochta',
    'password' => 'Parol',
    'password_confirm' => 'Parolni tasdiqlash',
    'password_hint' => 'Kamida 8 belgi bo‘lishi kerak',
    'forgot_password' => 'Parolni unutdingizmi?',
    'remember_me' => 'Eslab qoling',
    'ldap_email_hint' => 'Ush hisob bilan o‘tish uchun emailni kiritish.',
    'create_account' => 'Profil yaratildi',
    'already_have_account' => 'Profilingiz bormi?',
    'dont_have_account' => 'Profilingiz yo‘qmi?',
    'social_login' => 'Ijtimoiy tarmoqlar orqali kirish',
    'social_registration' => 'Ijtimoiy tarmoqlar orqali ro‘yxatdan o‘tish',
    'social_registration_text' => 'Boshqa tarmoqdan foydalanish.',

    'register_thanks' => 'Ro‘yxatdan o‘tganingiz uchun rahmat!',
    'register_confirm' => ':appName dan foydalanish uchun iltimos emailingizga yuborilgan xatni ochib, tasdiqlovchi link orqali o‘ting.',
    'registrations_disabled' => 'Hozirda ro‘yxatdan o‘tish yopilgan',
    'registration_email_domain_invalid' => 'Ush domendagi email bilan ro‘yxatdan o‘tib bo‘lmaydi',
    'register_success' => 'Ro‘yxatdan o‘tganingiz uchun rahmat! Endi siz ushbu hisob bilan saytga kirishingiz mumkin.',

    // Login auto-initiation
    'auto_init_starting' => 'Kirishga urinish',
    'auto_init_starting_desc' => 'Kirish jarayonini boshlash uchun kirish orqali murojaat qilyapmiz. Agar 5 soniyadan keyin hech qanday o‘zgarish bo‘lmasa, havolani bosib ko‘ tiklash mumkin.',
    'auto_init_start_link' => 'Kirish uchun bosing',

    // Password Reset
    'reset_password' => 'Parolni qayta tiklash',
    'reset_password_send_instructions' => 'Parolni tiklash manzilini olish uchun emailingizni maydonga kiriting.',
    'reset_password_send_button' => 'Tiklash manzilini yuborish',
    'reset_password_sent' => 'Agar tizimda ushbu elektron pochta manzili topilsa, parolni tiklash havolasi :email manziliga yuboriladi.',
    'reset_password_success' => 'Parolingiz yaxshilandi.',
    'email_reset_subject' => ':appName parolingizni tiklash',
    'email_reset_text' => 'Profilingiz uchun parolni oʻ Soʻrovini olganimiz uchun sizga bu xat keldi.',
    'email_reset_not_requested' => 'Agar sizga parolni tiklashni so‘ramagan bo‘lsangiz, boshqa hech qanday harakat talab qilish mumkin.',

    // Email Confirmation
    'email_confirm_subject' => ':appName orqali elektron pochtangizni tasdiqlang',
    'email_confirm_greeting' => ':appName\'ga qo‘shilganingiz uchun tashakkur!',
    'email_confirm_text' => 'Quyidagi tugmani bosish orqali elektron pochta manzilingizni tasdiqlang:',
    'email_confirm_action' => 'E-pochta manzilini tasdiqlash',
    'email_confirm_send_error' => 'Elektron pochtani tasdiqlash talab qilinadi, lekin tizim elektron pochta xabarini yubora olmadi. Elektron pochta toʻgʻri sozlanganligiga ishonch hosil qilish uchun administrator bilan bogʻlaning.',
    'email_confirm_success' => 'Emailingiz tasdiqlandi! Endi siz ushbu elektron pochta manzilidan foydalanib tizimga kirishingiz kerak.',
    'email_confirm_resent' => 'Tasdiqlash xati qayta yuborildi. Iltimos, pochta qutingizni tekshiring.',
    'email_confirm_thanks' => 'Tasdiqlaganingiz uchun tashakkur!',
    'email_confirm_thanks_desc' => 'Tasdiqlash jarayoni tugaguncha biroz kuting. Agar 3 soniyadan keyin qayta yoʻnaltirilmasangiz, davom etish uchun quyidagi “Davom etish” havolasini bosing.',

    'email_not_confirmed' => 'Elektron pochta manzili tasdiqlanmagan',
    'email_not_confirmed_text' => 'Sizning elektron pochta manzilingiz hali tasdiqlanmagan.',
    'email_not_confirmed_click_link' => 'Roʻyxatdan oʻtganingizdan soʻng, elektron pochtaga yuborilgan havolani bosing.',
    'email_not_confirmed_resend' => 'Agar elektron pochta manzilini topa olmasangiz, quyidagi shaklni yuborish orqali tasdiqlash xatini qayta yuborishingiz mumkin.',
    'email_not_confirmed_resend_button' => 'Tasdiqlash xatini qayta yuborish',

    // User Invite
    'user_invite_email_subject' => 'Siz :appName ilovasiga qo‘shilishga taklif qilindingiz!',
    'user_invite_email_greeting' => 'Siz uchun :appName ilovasida hisob yaratildi.',
    'user_invite_email_text' => 'Hisob qaydnomasi parolini o‘rnatish va unga kirish uchun quyidagi tugmani bosing:',
    'user_invite_email_action' => 'Hisob parolini o‘rnating',
    'user_invite_page_welcome' => ':appName ga xush kelibsiz!',
    'user_invite_page_text' => 'Hisob qaydnomangizni yakunlash va kirish huquqini qo‘lga kiritish uchun parolni o‘rnatishingiz kerak, undan keyingi tashriflaringizda :appName tizimiga kirish uchun foydalaniladi.',
    'user_invite_page_confirm_button' => 'Parolni tasdiqlang',
    'user_invite_success_login' => 'Parol o‘rnatilgan, endi siz o‘rnatilgan parolingizdan foydalanib tizimga kirishingiz kerak: appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Ko‘p faktorli autentifikatsiyani sozlash',
    'mfa_setup_desc' => 'Ko‘p faktorli autentifikatsiyani foydalanuvchi hisobingiz uchun qo‘shimcha xavfsizlik qatlami sifatida o‘rnatish.',
    'mfa_setup_configured' => 'Allaqachon sozlangan',
    'mfa_setup_reconfigure' => 'Qayta sozlang',
    'mfa_setup_remove_confirmation' => 'Haqiqatan ham bu koʻp faktorli autentifikatsiya usulini olib tashlamoqchimisiz?',
    'mfa_setup_action' => 'Sozlash; O‘rnatish',
    'mfa_backup_codes_usage_limit_warning' => 'Sizda 5 tadan kam zaxira kodingiz qoldi. Profilingiz bloklanib qolmasligi uchun kodlar tugashidan oldin yangi to‘plamni yarating va saqlang.',
    'mfa_option_totp_title' => 'Mobil ilova',
    'mfa_option_totp_desc' => 'Ko‘p faktorli autentifikatsiyadan foydalanish uchun sizga Google Authenticator, Authy yoki Microsoft Authenticator kabi OTPni qo‘llab-quvvatlaydigan mobil ilova kerak bo‘ladi.',
    'mfa_option_backup_codes_title' => 'Zaxira kodlari',
    'mfa_option_backup_codes_desc' => 'Shaxsingizni tasdiqlash uchun kiritishingiz mumkin bo‘lgan bir martalik zaxira kodlari to‘plamini xavfsiz saqlang.',
    'mfa_gen_confirm_and_enable' => 'Tasdiqlash va yoqish',
    'mfa_gen_backup_codes_title' => 'Zaxira kodlarini sozlash',
    'mfa_gen_backup_codes_desc' => 'Quyidagi kodlar ro‘yxatini xavfsiz joyda saqlang. Tizimga kirishda siz kodlardan birini ikkinchi autentifikatsiya mexanizmi sifatida ishlatishingiz mumkin.',
    'mfa_gen_backup_codes_download' => 'Kodlarni yuklab olish',
    'mfa_gen_backup_codes_usage_warning' => 'Har bir kod faqat bir marta ishlatilishi mumkin',
    'mfa_gen_totp_title' => 'Mobil ilovani sozlash',
    'mfa_gen_totp_desc' => 'Ko‘p faktorli autentifikatsiyadan foydalanish uchun sizga Google Authenticator, Authy yoki Microsoft Authenticator kabi TOTPni qo‘llab-quvvatlaydigan mobil ilova kerak bo‘ladi.',
    'mfa_gen_totp_scan' => 'Ishni boshlash uchun siz tanlagan autentifikatsiya ilovasi yordamida quyidagi QR kodni skanerlang.',
    'mfa_gen_totp_verify_setup' => 'Oʻrnatishni tasdiqlang',
    'mfa_gen_totp_verify_setup_desc' => 'Quyidagi kiritish maydoniga autentifikatsiya ilovangizda yaratilgan kodni kiritish orqali hammasi ishlayotganiga ishonch hosil qiling:',
    'mfa_gen_totp_provide_code_here' => 'Bu yerda ilovangiz tomonidan yaratilgan kodni kiriting',
    'mfa_verify_access' => 'Kirishni tasdiqlang',
    'mfa_verify_access_desc' => 'Sizning foydalanuvchi hisobingiz sizga ruxsat berishdan oldin shaxsingizni tasdiqlashning qoʻshimcha darajasi orqali tasdiqlashingizni talab qiladi. Davom etish uchun sozlangan usullardan biri yordamida tasdiqlang.',
    'mfa_verify_no_methods' => 'Hech qanday usul sozlanmagan',
    'mfa_verify_no_methods_desc' => 'Profilingiz uchun ko‘p faktorli autentifikatsiya usullari topilmadi. Kirishdan oldin kamida bitta usulni sozlashingiz kerak.',
    'mfa_verify_use_totp' => 'Mobil ilova yordamida tasdiqlang',
    'mfa_verify_use_backup_codes' => 'Zaxira kod yordamida tasdiqlang',
    'mfa_verify_backup_code' => 'Zaxira kodi',
    'mfa_verify_backup_code_desc' => 'Qolgan zaxira kodlaringizdan birini pastga kiriting:',
    'mfa_verify_backup_code_enter_here' => 'Bu yerga zaxira kodini kiriting',
    'mfa_verify_totp_desc' => 'Quyida mobil ilovangiz yordamida yaratilgan kodni kiriting:',
    'mfa_setup_login_notification' => 'Ko‘p faktorli usul sozlangan. Iltimos, endi sozlangan usul yordamida qayta kiring.',
];
