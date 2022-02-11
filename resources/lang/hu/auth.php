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
    'ldap_email_hint' => 'A fiókhoz használt email cím megadása.',
    'create_account' => 'Fiók létrehozása',
    'already_have_account' => 'Korábban volt beállítva fiók?',
    'dont_have_account' => 'Még nincs beállítva fiók?',
    'social_login' => 'Közösségi bejelentkezés',
    'social_registration' => 'Közösségi regisztráció',
    'social_registration_text' => 'Regisztráció és bejelentkezés másik szolgáltatással.',

    'register_thanks' => 'Köszönjük a regisztrációt!',
    'register_confirm' => 'Ellenőrizni kell a megadott email címet és a megerősítő gombra kell kattintani :appName eléréséhez.',
    'registrations_disabled' => 'A regisztráció jelenleg le van tiltva',
    'registration_email_domain_invalid' => 'Ebből az email tartományról nem lehet hozzáférni ehhez az alkalmazáshoz',
    'register_success' => 'Köszönjük a regisztrációt! A regisztráció és a bejelentkezés megtörtént.',

    // Password Reset
    'reset_password' => 'Jelszó visszaállítása',
    'reset_password_send_instructions' => 'Meg kell adni az email címet amire egy jelszó visszaállító hivatkozás lesz elküldve.',
    'reset_password_send_button' => 'Visszaállító hivatkozás elküldése',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Megerősítő email újraküldve. Ellenőrizni kell a bejövő üzeneteket.',

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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Beállítások',
    'mfa_backup_codes_usage_limit_warning' => 'Kevesebb, mint 5 visszaállítási kódja maradt. Kérem, hogy generáljon új kódokat, hogy csökkentse a rendszerből való kizárásának esélyét.',
    'mfa_option_totp_title' => 'Mobilalkalmazás',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Visszaállítási kulcsok',
    'mfa_option_backup_codes_desc' => 'Biztonságosan tárolja el az egyszer használatos visszaállítási kódjait, amiket a későbbiekben fel tud majd használni bejelentkezése során.',
    'mfa_gen_confirm_and_enable' => 'Jóváhagyás és engedélyezés',
    'mfa_gen_backup_codes_title' => 'Visszaállítási kódok beállítása',
    'mfa_gen_backup_codes_desc' => 'Tárolja el egy biztonságos helyen az alábbi kódokat. Bejelentkezés során fel tudja használni őket másodlagos bejelentkezési kódként.',
    'mfa_gen_backup_codes_download' => 'Kódok letöltése',
    'mfa_gen_backup_codes_usage_warning' => 'A kódok egyszerhasználatosak',
    'mfa_gen_totp_title' => 'Mobilalkalmazás beállítása',
    'mfa_gen_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scan the QR code below using your preferred authentication app to get started.',
    'mfa_gen_totp_verify_setup' => 'Verify Setup',
    'mfa_gen_totp_verify_setup_desc' => 'Verify that all is working by entering a code, generated within your authentication app, in the input box below:',
    'mfa_gen_totp_provide_code_here' => 'Provide your app generated code here',
    'mfa_verify_access' => 'Verify Access',
    'mfa_verify_access_desc' => 'Your user account requires you to confirm your identity via an additional level of verification before you\'re granted access. Verify using one of your configured methods to continue.',
    'mfa_verify_no_methods' => 'No Methods Configured',
    'mfa_verify_no_methods_desc' => 'No multi-factor authentication methods could be found for your account. You\'ll need to set up at least one method before you gain access.',
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Ellenőrzés visszaállítási kóddal',
    'mfa_verify_backup_code' => 'Visszaállítási kód',
    'mfa_verify_backup_code_desc' => 'Adjon meg egy még fel nem használt visszaállítási kódot:',
    'mfa_verify_backup_code_enter_here' => 'Írd be a tartalék kódot',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
