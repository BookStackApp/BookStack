<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Ezek a hitelesítő adatok nem egyeznek a rögzítettekkel.',
    'throttle' => 'Túl sok bejelentkezési próbálkozás. :seconds múlva lehet újra megpróbálni.',

    // Login & Register
    'sign_up' => 'Regisztráció',
    'log_in' => 'Bejelentkezés',
    'log_in_with' => 'Bejelentkezés ezzel: :socialDriver',
    'sign_up_with' => 'Regisztráció ezzel: :socialDriver',
    'logout' => 'Kijelentkezés',

    'name' => 'Név',
    'username' => 'Felhasználónév',
    'email' => 'Email',
    'password' => 'Jelszó',
    'password_confirm' => 'Jelszó megerősítése',
    'password_hint' => 'Legalább 8 karakter hosszú legyen',
    'forgot_password' => 'Elfelejtett jelszó?',
    'remember_me' => 'Emlékezzen rám',
    'ldap_email_hint' => 'Adjon meg egy e-mail címet amelyet a felhasználói fiókhoz szeretne használni.',
    'create_account' => 'Fiók létrehozása',
    'already_have_account' => 'Rendelkezik már felhasználói fiókkal?',
    'dont_have_account' => 'Még nincs felhasználói fiókja?',
    'social_login' => 'Közösségi bejelentkezés',
    'social_registration' => 'Közösségi regisztráció',
    'social_registration_text' => 'Regisztráció és bejelentkezés másik szolgáltatással.',

    'register_thanks' => 'Köszönjük a regisztrációt!',
    'register_confirm' => 'Ellenőrizni kell a megadott email címet és a megerősítő gombra kell kattintani :appName eléréséhez.',
    'registrations_disabled' => 'A regisztráció jelenleg le van tiltva',
    'registration_email_domain_invalid' => 'Ebből az email tartományról nem lehet hozzáférni ehhez az alkalmazáshoz',
    'register_success' => 'Köszönjük a regisztrációt! A regisztráció és a bejelentkezés megtörtént.',

    // Login auto-initiation
    'auto_init_starting' => 'Bejelentkezési kísérlet',
    'auto_init_starting_desc' => 'Kapcsolatba lépünk az azonosítási rendszereddel, hogy elkezdjük a bejelentkezési folyamatot. Ha 5 másodperc után sem történik előrelépés, próbálkozhatsz az alábbi linkre kattintva.',
    'auto_init_start_link' => 'Folytatás azonosítással',

    // Password Reset
    'reset_password' => 'Jelszó visszaállítása',
    'reset_password_send_instructions' => 'Meg kell adni az email címet amire egy jelszó visszaállító hivatkozás lesz elküldve.',
    'reset_password_send_button' => 'Visszaállító hivatkozás elküldése',
    'reset_password_sent' => 'A jelszó-visszaállító linket e-mailben fogjuk elküldeni a(z) :email címre, ha beállításra került a rendszerben.',
    'reset_password_success' => 'A jelszó sikeresen visszaállítva.',
    'email_reset_subject' => ':appName jelszó visszaállítása',
    'email_reset_text' => 'Ezt az emailt azért küldtük mert egy jelszó visszaállításra vonatkozó kérést kaptunk ebből a fiókból.',
    'email_reset_not_requested' => 'Ha nem történt jelszó visszaállításra vonatkozó kérés, akkor nincs szükség további intézkedésre.',

    // Email Confirmation
    'email_confirm_subject' => ':appName alkalmazásban beállított email címet meg kell erősíteni',
    'email_confirm_greeting' => ':appName köszöni a csatlakozást!',
    'email_confirm_text' => 'Az email címet a lenti gombra kattintva lehet megerősíteni:',
    'email_confirm_action' => 'Email megerősítése',
    'email_confirm_send_error' => 'Az email megerősítés kötelező, de a rendszer nem tudta elküldeni az emailt. Fel kell venni a kapcsolatot az adminisztrátorral és meg kell győződni róla, hogy az email beállítások megfelelőek.',
    'email_confirm_success' => 'Az e-mail címed sikeresen meg lett erősítve, most már be tudsz jelentkezni az e-mail címed használatával.',
    'email_confirm_resent' => 'Megerősítő email újraküldve. Ellenőrizni kell a bejövő üzeneteket.',
    'email_confirm_thanks' => 'Köszönjük a megerősítést!',
    'email_confirm_thanks_desc' => 'Kérlek, várj egy pillanatot, amíg a megerősítésedet kezeljük. Ha nem kerülsz átirányításra 3 másodperc után, kattints a "Folytatás" linkre az alábbiakban a továbbhaladáshoz.',

    'email_not_confirmed' => 'Az email cím nincs megerősítve',
    'email_not_confirmed_text' => 'Az email cím még nincs megerősítve.',
    'email_not_confirmed_click_link' => 'Rá kell kattintani a regisztráció után nem sokkal elküldött emailben található hivatkozásra.',
    'email_not_confirmed_resend' => 'Ha nem érkezik meg a megerősítő email, a lenti űrlap beküldésével újra lehet küldeni.',
    'email_not_confirmed_resend_button' => 'Megerősítő email újraküldése',

    // User Invite
    'user_invite_email_subject' => 'Ez egy meghívó :appName weboldalhoz!',
    'user_invite_email_greeting' => 'Létre lett hozva egy fiók az :appName weboldalon.',
    'user_invite_email_text' => 'Jelszó beállításához és hozzáféréshez a lenti gombra kell kattintani:',
    'user_invite_email_action' => 'Fiók jelszó beállítása',
    'user_invite_page_welcome' => ':appName üdvözöl!',
    'user_invite_page_text' => 'A fiók véglegesítéséhez és a hozzáféréshez be kell állítani egy jelszót ami :appName weboldalon lesz használva a bejelentkezéshez.',
    'user_invite_page_confirm_button' => 'Jelszó megerősítése',
    'user_invite_success_login' => 'Jelszó beállítva. Most már be tudsz jelentkezni a beállított jelszóval a következő rendszerbe: :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Többlépcsős azonosítás beállítása',
    'mfa_setup_desc' => 'Állítsa be a többlépcsős azonosítást egy extra biztonsági rétegként a felhasználói fiókjához.',
    'mfa_setup_configured' => 'Már beállítva',
    'mfa_setup_reconfigure' => 'Újrakonfigurálás',
    'mfa_setup_remove_confirmation' => 'Biztosan ki szeretné kapcsolni a többlépcsős azonosítást?',
    'mfa_setup_action' => 'Beállítások',
    'mfa_backup_codes_usage_limit_warning' => 'Kevesebb, mint 5 visszaállítási kódja maradt. Kérem, hogy generáljon új kódokat, hogy csökkentse a rendszerből való kizárásának esélyét.',
    'mfa_option_totp_title' => 'Mobilalkalmazás',
    'mfa_option_totp_desc' => 'A többlépcsős azonosításhoz olyan mobilalkalmazásra lesz szükséged, amely támogatja a TOTP-t, például a Google Authenticator, az Authy vagy a Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Visszaállítási kulcsok',
    'mfa_option_backup_codes_desc' => 'Biztonságosan tárolja el az egyszer használatos visszaállítási kódjait, amiket a későbbiekben fel tud majd használni bejelentkezése során.',
    'mfa_gen_confirm_and_enable' => 'Jóváhagyás és engedélyezés',
    'mfa_gen_backup_codes_title' => 'Visszaállítási kódok beállítása',
    'mfa_gen_backup_codes_desc' => 'Tárolja el egy biztonságos helyen az alábbi kódokat. Bejelentkezés során fel tudja használni őket másodlagos bejelentkezési kódként.',
    'mfa_gen_backup_codes_download' => 'Kódok letöltése',
    'mfa_gen_backup_codes_usage_warning' => 'A kódok egyszerhasználatosak',
    'mfa_gen_totp_title' => 'Mobilalkalmazás beállítása',
    'mfa_gen_totp_desc' => 'A többlépcsős azonosításhoz olyan mobilalkalmazásra lesz szükséged, amely támogatja a TOTP-t, például a Google Authenticator, az Authy vagy a Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Szkenneld be az alábbi QR-kódot az általad használt azonosító alkalmazásoddal, hogy használhasd az alkalmazást.',
    'mfa_gen_totp_verify_setup' => 'Beállítások ellenőrzése',
    'mfa_gen_totp_verify_setup_desc' => 'Ellenőrizd, hogy minden működik, azzal hogy beírod a kapott kódot amit az authentikátor alkalmazás generált az alábbi beviteli mezőbe:',
    'mfa_gen_totp_provide_code_here' => 'Add meg az alkalmazás által generált kódot ide',
    'mfa_verify_access' => 'Hozzáférés ellenőrzése',
    'mfa_verify_access_desc' => 'Felhasználói fiókja megköveteli, hogy erősítse meg személyazonosságát egy további ellenőrzési szinttel, mielőtt hozzáférést kapna. A folytatáshoz használja az egyik konfigurált módszert.',
    'mfa_verify_no_methods' => 'Nincs konfigurálva MFA',
    'mfa_verify_no_methods_desc' => 'Nem található többlépcsős hitelesítési módszer a fiókjához. A hozzáféréshez legalább egy módszert be kell állítania.',
    'mfa_verify_use_totp' => 'Ellenőrzés mobil alkalmazás használatával',
    'mfa_verify_use_backup_codes' => 'Ellenőrzés visszaállítási kóddal',
    'mfa_verify_backup_code' => 'Visszaállítási kód',
    'mfa_verify_backup_code_desc' => 'Adjon meg egy még fel nem használt visszaállítási kódot:',
    'mfa_verify_backup_code_enter_here' => 'Írd be a tartalék kódot',
    'mfa_verify_totp_desc' => 'Írja be alább a mobilalkalmazásával generált kódot:',
    'mfa_setup_login_notification' => 'Többfaktoros hitelesítés konfigurálva. Kérjük, most jelentkezzen be újra a konfigurált módszerrel.',
];
