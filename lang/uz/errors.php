<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Sizda soʻralgan sahifaga kirish ruxsatingiz yoʻq.',
    'permissionJson' => 'Sizda soʻralgan amalni bajarish uchun ruxsat yoʻq.',

    // Auth
    'error_user_exists_different_creds' => 'E-pochta manzili boʻlgan foydalanuvchi allaqachon mavjud, ammo hisob ma\'lumotlari boshqacha.',
    'email_already_confirmed' => 'Elektron pochta allaqachon tasdiqlangan, tizimga kiring.',
    'email_confirmation_invalid' => 'Bu tasdiqlovchi token yaroqsiz yoki allaqachon ishlatilgan. Iltimos, qayta roʻyxatdan oʻtishga urinib koʻring.',
    'email_confirmation_expired' => 'Tasdiqlash belgisi muddati tugadi, yangi tasdiqlovchi elektron pochta xabari yuborildi.',
    'email_confirmation_awaiting' => 'Amaldagi hisob uchun elektron pochta manzili tasdiqlanishi kerak',
    'ldap_fail_anonymous' => 'Anonim ulanishdan foydalanib, LDAP ruxsati amalga oshmadi',
    'ldap_fail_authed' => 'Berilgan dn va parol maʼlumotlari yordamida LDAP kirish amalga oshmadi',
    'ldap_extension_not_installed' => 'LDAP PHP kengaytmasi oʻrnatilmagan',
    'ldap_cannot_connect' => 'Ldap serveriga ulanib boʻlmadi, Dastlabki ulanish amalga oshmadi',
    'saml_already_logged_in' => 'Allaqachon tizimga kirgan',
    'saml_user_not_registered' => 'Foydalanuvchi :name roʻyxatdan oʻtmagan va avtomatik roʻyxatdan oʻtish oʻchirilgan',
    'saml_no_email_address' => 'Tashqi autentifikatsiya tizimi tomonidan taqdim etilgan maʼlumotlarda ushbu foydalanuvchi uchun elektron pochta manzili topilmadi',
    'saml_invalid_response_id' => 'Tashqi autentifikatsiya tizimidagi so‘rov ushbu ilova tomonidan boshlangan jarayon tomonidan tan olinmaydi. Kirishdan keyin orqaga qaytish bu muammoga olib kelishi mumkin.',
    'saml_fail_authed' => ':tizim yordamida tizimga kirish amalga oshmadi, tizim muvaffaqiyatli avtorizatsiyani taqdim etmadi',
    'oidc_already_logged_in' => 'Allaqachon tizimga kirgan',
    'oidc_user_not_registered' => 'Foydalanuvchi :name roʻyxatdan oʻtmagan va avtomatik roʻyxatdan oʻtish oʻchirilgan',
    'oidc_no_email_address' => 'Tashqi autentifikatsiya tizimi tomonidan taqdim etilgan maʼlumotlarda ushbu foydalanuvchi uchun elektron pochta manzili topilmadi',
    'oidc_fail_authed' => ':tizim yordamida tizimga kirish amalga oshmadi, tizim muvaffaqiyatli avtorizatsiyani taqdim etmadi',
    'social_no_action_defined' => 'Hech qanday harakat belgilanmagan',
    'social_login_bad_response' => ":socialAccount login paytida xatolik qabul qilindi: \n:xato",
    'social_account_in_use' => 'Bu :socialAccount hisobi allaqachon ishlatilmoqda, :socialAccount opsiyasi orqali tizimga kiring.',
    'social_account_email_in_use' => 'Elektron pochta: elektron pochta allaqachon ishlatilmoqda. Agar sizda allaqachon hisob qaydnomangiz boʻlsa, profil sozlamalaringizdan :socialAccount hisobingizni ulashingiz mumkin.',
    'social_account_existing' => 'Bu :socialAccount allaqachon profilingizga biriktirilgan.',
    'social_account_already_used_existing' => 'Bu :socialAccount hisobi allaqachon boshqa foydalanuvchi tomonidan foydalanilgan.',
    'social_account_not_used' => 'Bu :socialAccount hisobi hech qanday foydalanuvchi bilan bog‘lanmagan. Iltimos, uni profil sozlamalaringizga biriktiring.',
    'social_account_register_instructions' => 'Agar sizda hali hisob qaydnomangiz boʻlmasa, :socialAccount opsiyasidan foydalanib hisob qaydnomangizni roʻyxatdan oʻtkazishingiz mumkin.',
    'social_driver_not_found' => 'Ijtimoiy haydovchi topilmadi',
    'social_driver_not_configured' => 'Sizning :socialAccount ijtimoiy sozlamalaringiz toʻgʻri sozlanmagan.',
    'invite_token_expired' => 'Bu taklif havolasi muddati tugagan. Buning oʻrniga hisobingiz parolini tiklashga urinib koʻrishingiz mumkin.',

    // System
    'path_not_writable' => 'Fayl yoʻli :filePath faylini yuklab boʻlmadi. Uning serverga yozilishi mumkinligiga ishonch hosil qiling.',
    'cannot_get_image_from_url' => ':url dan rasmni olib boʻlmadi',
    'cannot_create_thumbs' => 'Server eskiz yarata olmaydi. GD PHP kengaytmasi oʻrnatilganligini tekshiring.',
    'server_upload_limit' => 'Server bunday hajmdagi yuklashga ruxsat bermaydi. Kichikroq fayl hajmini sinab koʻring.',
    'server_post_limit' => 'The server cannot receive the provided amount of data. Try again with less data or a smaller file.',
    'uploaded'  => 'Server bunday hajmdagi yuklashga ruxsat bermaydi. Kichikroq fayl hajmini sinab koʻring.',

    // Drawing & Images
    'image_upload_error' => 'Rasmni yuklashda xatolik yuz berdi',
    'image_upload_type_error' => 'Yuklanayotgan rasm turi yaroqsiz',
    'image_upload_replace_type' => 'Tasvir faylini almashtirish bir xil turdagi boʻlishi kerak',
    'image_upload_memory_limit' => 'Failed to handle image upload and/or create thumbnails due to system resource limits.',
    'image_thumbnail_memory_limit' => 'Failed to create image size variations due to system resource limits.',
    'image_gallery_thumbnail_memory_limit' => 'Failed to create gallery thumbnails due to system resource limits.',
    'drawing_data_not_found' => 'Chizma maʼlumotlarini yuklab boʻlmadi. Chizma fayli endi mavjud boʻlmasligi yoki unga kirishga ruxsatingiz boʻlmasligi mumkin.',

    // Attachments
    'attachment_not_found' => 'Biriktirma topilmadi',
    'attachment_upload_error' => 'Biriktirilgan faylni yuklashda xatolik yuz berdi',

    // Pages
    'page_draft_autosave_fail' => 'Qoralama saqlanmadi. Ushbu sahifani saqlashdan oldin internet aloqangiz borligiga ishonch hosil qiling',
    'page_draft_delete_fail' => 'Sahifaning qoralamasini o‘chirib bo‘lmadi va joriy sahifada saqlangan kontentni olib bo‘lmadi',
    'page_custom_home_deletion' => 'Bosh sahifa sifatida belgilangan sahifani oʻchirib boʻlmaydi',

    // Entities
    'entity_not_found' => 'Ob\'ekt topilmadi',
    'bookshelf_not_found' => 'Raf topilmadi',
    'book_not_found' => 'Kitob topilmadi',
    'page_not_found' => 'sahifa topilmadi',
    'chapter_not_found' => 'Boʻlim topilmadi',
    'selected_book_not_found' => 'Tanlangan kitob topilmadi',
    'selected_book_chapter_not_found' => 'Tanlangan kitob yoki bob topilmadi',
    'guests_cannot_save_drafts' => 'Mehmonlar qoralamalarni saqlay olmaydi',

    // Users
    'users_cannot_delete_only_admin' => 'Siz yagona administratorni oʻchira olmaysiz',
    'users_cannot_delete_guest' => 'Siz mehmon foydalanuvchini oʻchira olmaysiz',

    // Roles
    'role_cannot_be_edited' => 'Bu rolni tahrirlab bo‘lmaydi',
    'role_system_cannot_be_deleted' => 'Bu rol tizim rolidir va uni oʻchirib boʻlmaydi',
    'role_registration_default_cannot_delete' => 'Standart ro‘yxatga olish roli sifatida belgilangan bo‘lsa, bu rolni o‘chirib bo‘lmaydi',
    'role_cannot_remove_only_admin' => 'Bu foydalanuvchi administrator roliga tayinlangan yagona foydalanuvchi hisoblanadi. Administrator rolini bu yerda olib tashlashdan oldin boshqa foydalanuvchiga tayinlang.',

    // Comments
    'comment_list' => 'Sharhlarni olishda xatolik yuz berdi.',
    'cannot_add_comment_to_draft' => 'Siz qoralamaga izoh qo‘sha olmaysiz.',
    'comment_add' => 'Sharh qo‘shish/yangilashda xatolik yuz berdi.',
    'comment_delete' => 'Fikrni o‘chirishda xatolik yuz berdi.',
    'empty_comment' => 'Boʻsh fikr qoʻshib boʻlmaydi.',

    // Error pages
    '404_page_not_found' => 'Sahifa topilmadi',
    'sorry_page_not_found' => 'Kechirasiz, siz izlayotgan sahifa topilmadi.',
    'sorry_page_not_found_permission_warning' => 'Agar siz ushbu sahifa mavjudligini kutgan boʻlsangiz, uni koʻrish uchun ruxsatingiz boʻlmasligi mumkin.',
    'image_not_found' => 'Rasm topilmadi',
    'image_not_found_subtitle' => 'Kechirasiz, siz izlayotgan rasm fayli topilmadi.',
    'image_not_found_details' => 'Agar siz ushbu rasm mavjudligini kutgan boʻlsangiz, u oʻchirilgan boʻlishi mumkin.',
    'return_home' => 'Uyga qaytish',
    'error_occurred' => 'Xatolik yuz berdi',
    'app_down' => ':appName hozir ishlamayapti',
    'back_soon' => 'Tez orada zaxiralanadi.',

    // API errors
    'api_no_authorization_found' => 'So‘rovda hech qanday avtorizatsiya belgisi topilmadi',
    'api_bad_authorization_format' => 'So‘rovda avtorizatsiya belgisi topildi, lekin format noto‘g‘ri ko‘rindi',
    'api_user_token_not_found' => 'Taqdim etilgan avtorizatsiya tokeniga mos keladigan API tokeni topilmadi',
    'api_incorrect_token_secret' => 'Foydalanilgan API tokeni uchun berilgan sir notoʻgʻri',
    'api_user_no_api_permission' => 'Foydalanilgan API tokeni egasi API qoʻngʻiroqlarini amalga oshirishga ruxsatga ega emas',
    'api_user_token_expired' => 'Amaldagi avtorizatsiya tokeni muddati tugagan',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Sinov xatini yuborishda xatolik yuz berdi:',

    // HTTP errors
    'http_ssr_url_no_match' => 'URL sozlangan ruxsat etilgan SSR xostlariga mos kelmaydi',
];
