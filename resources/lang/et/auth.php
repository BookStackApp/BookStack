<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Kasutajanimi ja parool ei klapi.',
    'throttle' => 'Liiga palju sisselogimiskatseid. Proovi uuesti :seconds sekundi pärast.',

    // Login & Register
    'sign_up' => 'Registreeru',
    'log_in' => 'Logi sisse',
    'log_in_with' => 'Logi sisse :socialDriver abil',
    'sign_up_with' => 'Registreeru :socialDriver abil',
    'logout' => 'Logi välja',

    'name' => 'Nimi',
    'username' => 'Kasutajanimi',
    'email' => 'E-post',
    'password' => 'Parool',
    'password_confirm' => 'Kinnita parool',
    'password_hint' => 'Peab olema rohkem kui 7 tähemärki',
    'forgot_password' => 'Unustasid parooli?',
    'remember_me' => 'Jäta mind meelde',
    'ldap_email_hint' => 'Sisesta kasutajakonto e-posti aadress.',
    'create_account' => 'Loo konto',
    'already_have_account' => 'Kasutajakonto juba olemas?',
    'dont_have_account' => 'Sul ei ole veel kontot?',
    'social_login' => 'Social Login',
    'social_registration' => 'Social Registration',
    'social_registration_text' => 'Registreeru ja logi sisse välise teenuse kaudu.',

    'register_thanks' => 'Aitäh, et registreerusid!',
    'register_confirm' => 'Vaata oma postkasti ja klõpsa kinnitusnupul, et rakendusele :appName ligi pääseda.',
    'registrations_disabled' => 'Registreerumine on hetkel keelatud',
    'registration_email_domain_invalid' => 'Sellel e-posti domeenil ei ole rakendusele ligipääsu',
    'register_success' => 'Aitäh, et registreerusid! Oled nüüd sisse logitud.',

    // Password Reset
    'reset_password' => 'Lähtesta parool',
    'reset_password_send_instructions' => 'Siseta oma e-posti aadress ning sulle saadetakse link parooli lähtestamiseks.',
    'reset_password_send_button' => 'Saada lähtestamise link',
    'reset_password_sent' => 'Kui süsteemis leidub e-posti aadress :email, saadetakse sinna link parooli lähtestamiseks.',
    'reset_password_success' => 'Sinu parool on edukalt lähtestatud.',
    'email_reset_subject' => 'Lähtesta oma :appName parool',
    'email_reset_text' => 'Said selle e-kirja, sest meile laekus soov sinu konto parooli lähtestamiseks.',
    'email_reset_not_requested' => 'Kui sa ei soovinud parooli lähtestada, ei pea sa rohkem midagi tegema.',

    // Email Confirmation
    'email_confirm_subject' => 'Kinnita oma :appName konto e-posti aadress',
    'email_confirm_greeting' => 'Aitäh, et liitusid rakendusega :appName!',
    'email_confirm_text' => 'Palun kinnita oma e-posti aadress, klõpsates alloleval nupul:',
    'email_confirm_action' => 'Kinnita e-posti aadress',
    'email_confirm_send_error' => 'E-posti aadressi kinnitamine on vajalik, aga e-kirja saatmine ebaõnnestus. Võta ühendust administraatoriga.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Kinnituskiri on saadetud, vaata oma postkasti.',

    'email_not_confirmed' => 'E-posti aadress ei ole kinnitatud',
    'email_not_confirmed_text' => 'Sinu e-posti aadress ei ole veel kinnitatud.',
    'email_not_confirmed_click_link' => 'Klõpsa lingil e-kirjas, mis saadeti sulle pärast registreerumist.',
    'email_not_confirmed_resend' => 'Kui sa ei leia e-kirja, siis saad alloleva vormi abil selle uuesti saata.',
    'email_not_confirmed_resend_button' => 'Saada kinnituskiri uuesti',

    // User Invite
    'user_invite_email_subject' => 'Sind on kutsutud liituma rakendusega :appName!',
    'user_invite_email_greeting' => 'Sulle on loodud kasutajakonto rakenduses :appName.',
    'user_invite_email_text' => 'Vajuta allolevale nupule, et seada parool ja ligipääs saada:',
    'user_invite_email_action' => 'Sea konto parool',
    'user_invite_page_welcome' => 'Tere tulemast rakendusse :appName!',
    'user_invite_page_text' => 'Registreerumise lõpetamiseks ja ligipääsu saamiseks pead seadma parooli, millega edaspidi rakendusse sisse logid.',
    'user_invite_page_confirm_button' => 'Kinnita parool',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Seadista mitmeastmeline autentimine',
    'mfa_setup_desc' => 'Seadista mitmeastmeline autentimine, et oma kasutajakonto turvalisust tõsta.',
    'mfa_setup_configured' => 'Juba seadistatud',
    'mfa_setup_reconfigure' => 'Seadista ümber',
    'mfa_setup_remove_confirmation' => 'Kas oled kindel, et soovid selle mitmeastmelise autentimise meetodi eemaldada?',
    'mfa_setup_action' => 'Seadista',
    'mfa_backup_codes_usage_limit_warning' => 'Sul on vähem kui 5 varukoodi järel. Genereeri ja hoiusta uus komplekt enne, kui nad otsa saavad, et vältida oma kasutajakontole ligipääsu kaotamist.',
    'mfa_option_totp_title' => 'Mobiilirakendus',
    'mfa_option_totp_desc' => 'Mitmeastmelise autentimise kasutamiseks on sul vaja TOTP-toega mobiilirakendust, nagu Google Authenticator, Authy või Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Varukoodid',
    'mfa_option_backup_codes_desc' => 'Hoiusta kindlas kohas komplekt ühekordseid varukoode, millega saad oma isikut tuvastada.',
    'mfa_gen_confirm_and_enable' => 'Kinnita ja lülita sisse',
    'mfa_gen_backup_codes_title' => 'Varukoodide seadistamine',
    'mfa_gen_backup_codes_desc' => 'Hoiusta allolevad koodid turvalises kohas. Saad neid kasutada sisselogimisel sekundaarse autentimismeetodina.',
    'mfa_gen_backup_codes_download' => 'Laadi koodid alla',
    'mfa_gen_backup_codes_usage_warning' => 'Igat koodi saab ainult ühe korra kasutada',
    'mfa_gen_totp_title' => 'Mobiilirakenduse seadistamine',
    'mfa_gen_totp_desc' => 'Mitmeastmelise autentimise kasutamiseks on sul vaja TOTP-toega mobiilirakendust, nagu Google Authenticator, Authy või Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Alustamiseks skaneeri allolevat QR-koodi oma eelistatud rakendusega.',
    'mfa_gen_totp_verify_setup' => 'Kontrolli seadistust',
    'mfa_gen_totp_verify_setup_desc' => 'Veendu, et kõik toimib korrektselt, sisestades oma rakenduse genereeritud koodi allolevasse tekstikasti:',
    'mfa_gen_totp_provide_code_here' => 'Sisesta rakenduse genereeritud kood siia',
    'mfa_verify_access' => 'Kinnita ligipääs',
    'mfa_verify_access_desc' => 'Sinu konto nõuab ligipääsuks täiendava kinnitusmeetodi abil oma isiku tuvastamist. Jätkamiseks vali üks järgnevatest meetoditest.',
    'mfa_verify_no_methods' => 'Ühtegi meetodit pole seadistatud',
    'mfa_verify_no_methods_desc' => 'Sinu kontole pole lisatud ühtegi mitmeastmelise autentimise meetodit. Ligipääsu saamiseks pead seadistama vähemalt ühe meetodi.',
    'mfa_verify_use_totp' => 'Tuvasta mobiilirakendusega',
    'mfa_verify_use_backup_codes' => 'Tuvasta varukoodiga',
    'mfa_verify_backup_code' => 'Varukood',
    'mfa_verify_backup_code_desc' => 'Sisesta allpool üks oma järelejäänud varukoodidest:',
    'mfa_verify_backup_code_enter_here' => 'Sisesta varukood siia',
    'mfa_verify_totp_desc' => 'Sisesta oma mobiilirakenduse poolt genereeritud kood allpool:',
    'mfa_setup_login_notification' => 'Mitmeastmeline autentimine seadistatud. Logi nüüd uuesti sisse, kasutades seadistatud meetodit.',
];
