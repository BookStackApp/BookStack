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
    'password_hint' => 'Négy karakternél hosszabbnak kell lennie',
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
    'reset_password_sent_success' => 'Jelszó visszaállító hivatkozás elküldve :email címre.',
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
    'email_confirm_success' => 'Az email cím megerősítve!',
    'email_confirm_resent' => 'Megerősítő email újraküldve. Ellenőrizni kell a bejövő üzeneteket.',

    'email_not_confirmed' => 'Az email cím nincs megerősítve',
    'email_not_confirmed_text' => 'Az email cím még nincs megerősítve.',
    'email_not_confirmed_click_link' => 'Rá kell kattintani a regisztráció után nem sokkal elküldött emailben található hivatkozásra.',
    'email_not_confirmed_resend' => 'Ha nem érkezik meg a megerősítő email, a lenti űrlap beküldésével újra lehet küldeni.',
    'email_not_confirmed_resend_button' => 'Megerősítő email újraküldése',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];