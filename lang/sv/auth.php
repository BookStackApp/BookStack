<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Uppgifterna stämmer inte överens med våra register.',
    'throttle' => 'För många inloggningsförsök. Prova igen om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Skapa konto',
    'log_in' => 'Logga in',
    'log_in_with' => 'Logga in med :socialDriver',
    'sign_up_with' => 'Registera dig med :socialDriver',
    'logout' => 'Logga ut',

    'name' => 'Namn',
    'username' => 'Användarnamn',
    'email' => 'E-post',
    'password' => 'Lösenord',
    'password_confirm' => 'Bekräfta lösenord',
    'password_hint' => 'Måste vara minst 8 tecken',
    'forgot_password' => 'Glömt lösenord?',
    'remember_me' => 'Kom ihåg mig',
    'ldap_email_hint' => 'Vänligen ange en e-postadress att använda till kontot.',
    'create_account' => 'Skapa konto',
    'already_have_account' => 'Har du redan ett konto?',
    'dont_have_account' => 'Har du ingen användare?',
    'social_login' => 'Logga in genom socialt medie',
    'social_registration' => 'Registrera dig genom socialt media',
    'social_registration_text' => 'Registrera dig och logga in genom en annan tjänst.',

    'register_thanks' => 'Tack för din registrering!',
    'register_confirm' => 'Vänligen kontrollera din mail och klicka på bekräftelselänken för att få tillgång till :appName.',
    'registrations_disabled' => 'Registrering är för närvarande avstängd',
    'registration_email_domain_invalid' => 'Den e-postadressen har inte tillgång till den här applikationen',
    'register_success' => 'Tack för din registrering! Du är nu registerad och inloggad.',

    // Login auto-initiation
    'auto_init_starting' => 'Försöker Logga In',
    'auto_init_starting_desc' => 'Vi kontaktar ditt autentiseringssystem för att starta inloggningsprocessen. Om inget händer efter 5 sekunder kan du prova att klicka på länken nedan.',
    'auto_init_start_link' => 'Fortsätt med autentisering',

    // Password Reset
    'reset_password' => 'Återställ lösenord',
    'reset_password_send_instructions' => 'Ange din e-postadress nedan så skickar vi ett mail med en länk för att återställa ditt lösenord.',
    'reset_password_send_button' => 'Skicka återställningslänk',
    'reset_password_sent' => 'En länk för återställning av lösenord kommer att skickas till :email om den e-postadressen finns i systemet.',
    'reset_password_success' => 'Ditt lösenord har återställts.',
    'email_reset_subject' => 'Återställ ditt lösenord till :appName',
    'email_reset_text' => 'Du får detta mail eftersom vi fått en begäran om att återställa lösenordet till ditt konto.',
    'email_reset_not_requested' => 'Om du inte begärt att få ditt lösenord återställt behöver du inte göra någonting',

    // Email Confirmation
    'email_confirm_subject' => 'Bekräfta din e-post på :appName',
    'email_confirm_greeting' => 'Tack för att du gått med i :appName!',
    'email_confirm_text' => 'Vänligen bekräfta din e-postadress genom att klicka på knappen nedan:',
    'email_confirm_action' => 'Bekräfta e-post',
    'email_confirm_send_error' => 'E-posten behöver bekräftas men systemet kan inte skicka mail. Kontakta adminstratören för att kontrollera att allt är konfigurerat korrekt.',
    'email_confirm_success' => 'Din e-postadress har bekräftats! Du bör nu kunna logga in med denna e-postadress.',
    'email_confirm_resent' => 'Bekräftelsemailet har skickats på nytt, kolla din mail',
    'email_confirm_thanks' => 'Thanks for confirming!',
    'email_confirm_thanks_desc' => 'Please wait a moment while your confirmation is handled. If you are not redirected after 3 seconds press the "Continue" link below to proceed.',

    'email_not_confirmed' => 'E-posadress ej bekräftad',
    'email_not_confirmed_text' => 'Din e-postadress har inte bekräftats ännu.',
    'email_not_confirmed_click_link' => 'Vänligen klicka på länken i det mail du fick strax efter att du registerade dig.',
    'email_not_confirmed_resend' => 'Om du inte hittar mailet kan du begära en ny bekräftelse genom att fylla i formuläret nedan.',
    'email_not_confirmed_resend_button' => 'Skicka bekräftelse på nytt',

    // User Invite
    'user_invite_email_subject' => 'Du har blivit inbjuden att gå med i :appName!',
    'user_invite_email_greeting' => 'Ett konto har skapats för dig i :appName.',
    'user_invite_email_text' => 'Klicka på knappen nedan för att ange ett lösenord och få tillgång:',
    'user_invite_email_action' => 'Ange kontolösenord',
    'user_invite_page_welcome' => 'Välkommen till :appName!',
    'user_invite_page_text' => 'För att slutföra ditt konto och få åtkomst måste du ange ett lösenord som kommer att användas för att logga in på :appName vid framtida besök.',
    'user_invite_page_confirm_button' => 'Bekräfta lösenord',
    'user_invite_success_login' => 'Lösenord inställt, du bör nu kunna logga in med ditt inställda lösenord för att komma åt :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Konfigurera multifaktorsautentisering',
    'mfa_setup_desc' => 'Konfigurera multifaktorsautentisering som ett extra skydd för ditt konto.',
    'mfa_setup_configured' => 'Redan konfigurerad',
    'mfa_setup_reconfigure' => 'Omkonfigurera',
    'mfa_setup_remove_confirmation' => 'Är du säker på att du vill ta bort denna multifaktorautentiseringsmetod?',
    'mfa_setup_action' => 'Konfigurera',
    'mfa_backup_codes_usage_limit_warning' => 'Du har mindre än 5 reservkoder kvar, Vänligen generera och lagra en nya innan du får slut på koder för att förhindra att du inte kommer åt ditt konto.',
    'mfa_option_totp_title' => 'Mobilapp',
    'mfa_option_totp_desc' => 'För att använda multifaktorautentisering behöver du en mobil app som stöder TOTP så som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Reservkoder',
    'mfa_option_backup_codes_desc' => 'Lagra säkert en uppsättning engångsreservkoder som du kan ange för att verifiera din identitet.',
    'mfa_gen_confirm_and_enable' => 'Bekräfta och aktivera',
    'mfa_gen_backup_codes_title' => 'Konfiguration av reservkoder',
    'mfa_gen_backup_codes_desc' => 'Spara nedanstående koder på en säker plats. När du använder systemet kommer du att kunna använda en av koderna som en andra autentiseringsmekanism.',
    'mfa_gen_backup_codes_download' => 'Ladda ner koder',
    'mfa_gen_backup_codes_usage_warning' => 'Varje kod kan endast användas en gång',
    'mfa_gen_totp_title' => 'Konfiguration av mobilapp',
    'mfa_gen_totp_desc' => 'För att använda multifaktorautentisering behöver du en mobil app som stöder TOTP så som Google Authenticator, Authy eller Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Skanna QR-koden nedan med din föredragna autentiseringsapp för att komma igång.',
    'mfa_gen_totp_verify_setup' => 'Verifiera konfiguration',
    'mfa_gen_totp_verify_setup_desc' => 'Kontrollera att allt fungerar genom att ange en kod, genererad i din autentiseringsapp, i rutan nedan:',
    'mfa_gen_totp_provide_code_here' => 'Ange din appgenererade kod här',
    'mfa_verify_access' => 'Verifiera åtkomst',
    'mfa_verify_access_desc' => 'Ditt användarkonto kräver att du bekräftar din identitet via en ytterligare verifieringsmetod innan du får tillgång. Verifiera genom en av dina konfigurerade metoder för att fortsätta.',
    'mfa_verify_no_methods' => 'Inga metoder konfigurerade',
    'mfa_verify_no_methods_desc' => 'Inga multifaktorautentiseringsmetoder kunde hittas för ditt konto. Du måste konfigurera minst en metod innan du får tillgång.',
    'mfa_verify_use_totp' => 'Verifiera med en mobilapp',
    'mfa_verify_use_backup_codes' => 'Verifiera med en reservkod',
    'mfa_verify_backup_code' => 'Reservkod',
    'mfa_verify_backup_code_desc' => 'Ange en av dina återstående reservkoder nedan:',
    'mfa_verify_backup_code_enter_here' => 'Ange reservkod här',
    'mfa_verify_totp_desc' => 'Ange koden, som genereras med din mobilapp, nedan:',
    'mfa_setup_login_notification' => 'Multifaktormetod konfigurerad, Logga nu in igen med den konfigurerade metoden.',
];
