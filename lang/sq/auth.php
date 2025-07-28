<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Këto kredenciale nuk përputhen me të dhënat tona.',
    'throttle' => 'Shumë përpjekje për hyrje. Ju lutemi provoni përsëri në :seconds sekonda.',

    // Login & Register
    'sign_up' => 'Regjistrohu',
    'log_in' => 'Logohu',
    'log_in_with' => 'Logohu me :socialDriver',
    'sign_up_with' => 'Regjistrohu me :socialDriver',
    'logout' => 'Shkyçu',

    'name' => 'Emri',
    'username' => 'Emri i përdoruesit',
    'email' => 'Email',
    'password' => 'Fjalkalimi',
    'password_confirm' => 'Konfirmo fjalëkalimin',
    'password_hint' => 'Duhet të jetë të paktën 8 karaktere',
    'forgot_password' => 'Keni harruar fjalëkalimin?',
    'remember_me' => 'Më mbaj mend',
    'ldap_email_hint' => 'Ju lutem fusni një email që do përdorni për këtë llogari.',
    'create_account' => 'Krijo një llogari',
    'already_have_account' => 'Keni një llogari?',
    'dont_have_account' => 'Nuk keni akoma llogari?',
    'social_login' => 'Kyçu me rrjete sociale',
    'social_registration' => 'Regjistrohu me rrjete sociale',
    'social_registration_text' => 'Regjistrohu dhe logohu duhet përdorur një shërbim tjetër.',

    'register_thanks' => 'Faleminderit që u regjistruat!',
    'register_confirm' => 'Ju lutem kontrolloni emai-in tuaj dhe klikoni te butoni i konfirmimit për të aksesuar :appName.',
    'registrations_disabled' => 'Regjistrimet janë të mbyllura',
    'registration_email_domain_invalid' => 'Ky domain email-i nuk ka akses te ky aplikacion',
    'register_success' => 'Faleminderit që u regjistruar! Ju tani jeni të regjistruar dhe të loguar.',

    // Login auto-initiation
    'auto_init_starting' => 'Përpjekje për t\'u kyçur',
    'auto_init_starting_desc' => 'Jemi duke kontaktuar sistemin e verifikimit për të filluar proçesin e kyçjes. Nëse nuk ka progres për 5 sekonda, klikoni linkun më poshtë.',
    'auto_init_start_link' => 'Vazhdoni me verifikimin',

    // Password Reset
    'reset_password' => 'Rivendosni fjalëkalimin',
    'reset_password_send_instructions' => 'Shkruani email-in tuaj më poshtë dhe do të merrni një link në email për të rikthyer fjalëkalimin.',
    'reset_password_send_button' => 'Dërgo linkun e rikthimit të fjalëkalimit',
    'reset_password_sent' => 'Një link për rikthimin e fjalëkalimit do ju dërgohet në :email nëse adresa e email-it ndodhet në sistem.',
    'reset_password_success' => 'Fjalëkalimi juaj u rikthye me sukses.',
    'email_reset_subject' => 'Rikthe fjalëkalimin për :appName',
    'email_reset_text' => 'Ju po e merrni këtë email sepse ne morëm një kërkesë për rivendosjen e fjalëkalimit për llogarinë tuaj.',
    'email_reset_not_requested' => 'Nëse nuk keni kërkuar rivendosjen e fjalëkalimit, nuk kërkohet asnjë veprim i mëtejshëm.',

    // Email Confirmation
    'email_confirm_subject' => 'Konfirmo email-in tënd në :appName',
    'email_confirm_greeting' => 'Faleminderit që u bashkuat me :appName!',
    'email_confirm_text' => 'Ju lutemi konfirmoni adresën tuaj të email-it duke klikuar butonin më poshtë:',
    'email_confirm_action' => 'Konfirmo email-in',
    'email_confirm_send_error' => 'Kërkohet konfirmimi i email-it, por sistemi nuk mundi ta dërgonte email-in. Kontaktoni administratorin për t\'u siguruar që email-i është konfiguruar saktë.',
    'email_confirm_success' => 'Email-i juaj është konfirmuar! Tani duhet të jeni në gjendje të hyni në sistem duke përdorur këtë adresë email-i.',
    'email_confirm_resent' => 'Emaili i konfirmimit u ridërgua, ju lutem kontrolloni kutinë tuaj postare.',
    'email_confirm_thanks' => 'Faleminderit që konfirmuat!',
    'email_confirm_thanks_desc' => 'Ju lutemi prisni një moment ndërsa konfirmimi juaj përpunohet. Nëse nuk ridrejtoheni pas 3 sekondash, shtypni linkun "Vazhdo" më poshtë për të vazhduar.',

    'email_not_confirmed' => 'Adresa e email-it nuk është konfirmuar',
    'email_not_confirmed_text' => 'Adresa juaj e email-it nuk është konfirmuar ende.',
    'email_not_confirmed_click_link' => 'Ju lutemi klikoni linkun në emailin që ju është dërguar menjëherë pasi u regjistruat.',
    'email_not_confirmed_resend' => 'Nëse nuk mund ta gjeni email-in, mund ta ridërgoni email-in e konfirmimit duke plotësuar formularin më poshtë.',
    'email_not_confirmed_resend_button' => 'Ridërgo emailin e konfirmimit',

    // User Invite
    'user_invite_email_subject' => 'Je ftuar të bashkohesh me :appName!',
    'user_invite_email_greeting' => 'Një llogari është krijuar për ty në :appName.',
    'user_invite_email_text' => 'Klikoni butonin më poshtë për të vendosur një fjalëkalim llogarie dhe për të fituar akses:',
    'user_invite_email_action' => 'Vendos fjalëkalimin e llogarisë',
    'user_invite_page_welcome' => 'Mirë se vini në :appName!',
    'user_invite_page_text' => 'Për të finalizuar llogarinë tuaj dhe për të fituar akses, duhet të vendosni një fjalëkalim i cili do të përdoret për t\'u kyçur në :appName në vizitat e ardhshme.',
    'user_invite_page_confirm_button' => 'Konfirmo fjalëkalimin',
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
    'mfa_option_backup_codes_desc' => 'Generates a set of one-time-use backup codes which you\'ll enter on login to verify your identity. Make sure to store these in a safe & secure place.',
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
