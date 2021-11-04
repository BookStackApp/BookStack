<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Uppgifterna stämmer inte överrens med våra register.',
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
    'password_hint' => 'Måste vara fler än 7 tecken',
    'forgot_password' => 'Glömt lösenord?',
    'remember_me' => 'Kom ihåg mig',
    'ldap_email_hint' => 'Vänligen ange en e-postadress att använda till kontot.',
    'create_account' => 'Skapa konto',
    'already_have_account' => 'Har du redan en användare?',
    'dont_have_account' => 'Har du ingen användare?',
    'social_login' => 'Logga in genom socialt medie',
    'social_registration' => 'Registrera dig genom socialt media',
    'social_registration_text' => 'Registrera dig och logga in genom en annan tjänst.',

    'register_thanks' => 'Tack för din registrering!',
    'register_confirm' => 'Vänligen kontrollera din mail och klicka på bekräftelselänken för att få tillgång till :appName.',
    'registrations_disabled' => 'Registrering är för närvarande avstängd',
    'registration_email_domain_invalid' => 'Den e-postadressen har inte tillgång till den här applikationen',
    'register_success' => 'Tack för din registrering! Du är nu registerad och inloggad.',

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
    'email_confirm_success' => 'Din e-post har bekräftats',
    'email_confirm_resent' => 'Bekräftelsemailet har skickats på nytt, kolla din mail',

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
    'user_invite_success' => 'Lösenord satt, du har nu tillgång till :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Setup Multi-Factor Authentication',
    'mfa_setup_desc' => 'Setup multi-factor authentication as an extra layer of security for your user account.',
    'mfa_setup_configured' => 'Already configured',
    'mfa_setup_reconfigure' => 'Reconfigure',
    'mfa_setup_remove_confirmation' => 'Are you sure you want to remove this multi-factor authentication method?',
    'mfa_setup_action' => 'Setup',
    'mfa_backup_codes_usage_limit_warning' => 'You have less than 5 backup codes remaining, Please generate and store a new set before you run out of codes to prevent being locked out of your account.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'To use multi-factor authentication you\'ll need a mobile application that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Codes',
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
    'mfa_verify_use_totp' => 'Verify using a mobile app',
    'mfa_verify_use_backup_codes' => 'Verify using a backup code',
    'mfa_verify_backup_code' => 'Backup Code',
    'mfa_verify_backup_code_desc' => 'Enter one of your remaining backup codes below:',
    'mfa_verify_backup_code_enter_here' => 'Enter backup code here',
    'mfa_verify_totp_desc' => 'Enter the code, generated using your mobile app, below:',
    'mfa_setup_login_notification' => 'Multi-factor method configured, Please now login again using the configured method.',
];
