<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Poverilnice se ne ujemajo s podatki v naši bazi.',
    'throttle' => 'Prekoračili ste število možnih prijav. Poskusite znova čez :seconds sekund.',

    // Login & Register
    'sign_up' => 'Registracija',
    'log_in' => 'Prijavi se',
    'log_in_with' => 'Prijavi se z :socialDriver',
    'sign_up_with' => 'Registriraj se z :socialDriver',
    'logout' => 'Odjavi se',

    'name' => 'Ime',
    'username' => 'Uporabniško ime',
    'email' => 'E-pošta',
    'password' => 'Geslo',
    'password_confirm' => 'Potrdi geslo',
    'password_hint' => 'Must be at least 8 characters',
    'forgot_password' => 'Pozabljeno geslo?',
    'remember_me' => 'Zapomni si me',
    'ldap_email_hint' => 'Prosimo vpišite e-poštni naslov za ta račun.',
    'create_account' => 'Ustvari račun',
    'already_have_account' => 'Že imate račun?',
    'dont_have_account' => 'Nimate računa?',
    'social_login' => 'Prijava z računi družbenih omrežij',
    'social_registration' => 'Registracija z družbenim omrežjem',
    'social_registration_text' => 'Registrirajte in prijavite se za uporabo drugih možnosti.',

    'register_thanks' => 'Hvala za registracijo!',
    'register_confirm' => 'Prosimo preverite vaš e-poštni predal in kliknite na potrditveni gumb za dostop :appName.',
    'registrations_disabled' => 'Registracija trenutno ni mogoča',
    'registration_email_domain_invalid' => 'Ta e-poštna domena nima dostopa do te aplikacije',
    'register_success' => 'Hvala za registracijo! Sedaj ste registrirani in prijavljeni.',

    // Password Reset
    'reset_password' => 'Ponastavi geslo',
    'reset_password_send_instructions' => 'Spodaj vpišite vaš e-poštni naslov in prejeli boste e-pošto s povezavo za ponastavitev gesla.',
    'reset_password_send_button' => 'Pošlji povezavo za ponastavitev',
    'reset_password_sent' => 'V kolikor e-poštni naslov :email obstaja v sistemu, bo nanj poslana povezava za ponastavitev gesla.',
    'reset_password_success' => 'Vaše geslo je bilo uspešno spremenjeno.',
    'email_reset_subject' => 'Ponastavi svoje :appName geslo',
    'email_reset_text' => 'To e-poštno sporočilo ste prejeli, ker smo prejeli zahtevo za ponastavitev gesla za vaš račun.',
    'email_reset_not_requested' => 'Če niste zahtevali ponastavitve gesla, vam ni potrebno ničesar storiti.',

    // Email Confirmation
    'email_confirm_subject' => 'Potrdi svojo e-pošto za :appName',
    'email_confirm_greeting' => 'Hvala ker ste se pridružili :appName!',
    'email_confirm_text' => 'Potrdite svoj e-naslov s klikom spodnjega gumba:',
    'email_confirm_action' => 'Potrdi e-pošto',
    'email_confirm_send_error' => 'E-poštna potrditev je zahtevana ampak sistem ni mogel poslati e-pošte. Kontaktirajte administratorja, da zagotovite, da je e-pošta pravilno nastavljena.',
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
    'email_confirm_resent' => 'Poslali smo vam potrditveno sporočilo. Prosimo preverite svojo elektronsko pošto.',

    'email_not_confirmed' => 'Elektronski naslov ni potrjen',
    'email_not_confirmed_text' => 'Vaš e-naslov še ni bil potrjen.',
    'email_not_confirmed_click_link' => 'Prosimo kliknite na link v e-poštnem sporočilu, ki ste ga prejeli kmalu po registraciji.',
    'email_not_confirmed_resend' => 'Če ne najdete e-pošte jo lahko ponovno pošljete s potrditvijo obrazca.',
    'email_not_confirmed_resend_button' => 'Ponovno pošlji potrditveno e-pošto',

    // User Invite
    'user_invite_email_subject' => 'Povabljen si bil da se pridružiš :appName!',
    'user_invite_email_greeting' => 'Račun je bil ustvarjen zate na :appName.',
    'user_invite_email_text' => 'Klikni na spodnji gumb, da si nastaviš geslo in dobiš dostop:',
    'user_invite_email_action' => 'Nastavi geslo za račun',
    'user_invite_page_welcome' => 'Dobrodošli na :appName!',
    'user_invite_page_text' => 'Za zaključiti in pridobiti dostop si morate nastaviti geslo, ki bo uporabljeno za prijavo v :appName.',
    'user_invite_page_confirm_button' => 'Potrdi geslo',
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

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
