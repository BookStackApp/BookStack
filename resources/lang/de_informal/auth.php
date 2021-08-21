<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Die eingegebenen Anmeldedaten sind ungültig.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuche es in :seconds Sekunden erneut.',

    // Login & Register
    'sign_up' => 'Registrieren',
    'log_in' => 'Anmelden',
    'log_in_with' => 'Anmelden mit :socialDriver',
    'sign_up_with' => 'Registrieren mit :socialDriver',
    'logout' => 'Abmelden',

    'name' => 'Name',
    'username' => 'Benutzername',
    'email' => 'E-Mail',
    'password' => 'Passwort',
    'password_confirm' => 'Passwort bestätigen',
    'password_hint' => 'Mindestlänge: 7 Zeichen',
    'forgot_password' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'ldap_email_hint' => 'Bitte gib eine E-Mail-Adresse ein, um diese mit dem Account zu nutzen.',
    'create_account' => 'Account registrieren',
    'already_have_account' => 'Bereits ein Konto erstellt?',
    'dont_have_account' => 'Noch kein Konto erstellt?',
    'social_login' => 'Mit Sozialem Netzwerk anmelden',
    'social_registration' => 'Mit Sozialem Netzwerk registrieren',
    'social_registration_text' => 'Mit einer dieser Dienste registrieren oder anmelden',

    'register_thanks' => 'Vielen Dank für deine Registrierung!',
    'register_confirm' => 'Bitte prüfe Deinen Posteingang und bestätig die Registrierung.',
    'registrations_disabled' => 'Eine Registrierung ist momentan nicht möglich',
    'registration_email_domain_invalid' => 'Du kannst dich mit dieser E-Mail nicht registrieren.',
    'register_success' => 'Vielen Dank für deine Registrierung! Du bist jetzt registriert und eingeloggt.',


    // Password Reset
    'reset_password' => 'Passwort vergessen',
    'reset_password_send_instructions' => 'Bitte gib Deine E-Mail-Adresse ein. Danach erhältst Du eine E-Mail mit einem Link zum Zurücksetzen Deines Passwortes.',
    'reset_password_send_button' => 'Passwort zurücksetzen',
    'reset_password_sent' => 'Ein Link zum Zurücksetzen des Passworts wird an :email gesendet, wenn diese E-Mail-Adresse im System gefunden wird.',
    'reset_password_success' => 'Dein Passwort wurde erfolgreich zurückgesetzt.',
    'email_reset_subject' => 'Passwort zurücksetzen für :appName',
    'email_reset_text' => 'Du erhältsts diese E-Mail, weil jemand versucht hat, Dein Passwort zurückzusetzen.',
    'email_reset_not_requested' => 'Wenn du das zurücksetzen des Passworts nicht angefordert hast, ist keine weitere Aktion erforderlich.',


    // Email Confirmation
    'email_confirm_subject' => 'Bestätige Deine E-Mail-Adresse für :appName',
    'email_confirm_greeting' => 'Danke, dass Du dich für :appName registrierst hast!',
    'email_confirm_text' => 'Bitte bestätige Deine E-Mail-Adresse, indem Du auf die Schaltfläche klickst:',
    'email_confirm_action' => 'E-Mail-Adresse bestätigen',
    'email_confirm_send_error' => 'Leider konnte die für die Registrierung notwendige E-Mail zur Bestätigung Deiner E-Mail-Adresse nicht versandt werden. Bitte kontaktiere den Systemadministrator!',
    'email_confirm_success' => 'Deine E-Mail-Adresse wurde bestätigt!',
    'email_confirm_resent' => 'Bestätigungs-E-Mail wurde erneut versendet, bitte überprüfe Deinen Posteingang.',

    'email_not_confirmed' => 'E-Mail-Adresse ist nicht bestätigt',
    'email_not_confirmed_text' => 'Deine E-Mail-Adresse ist bisher nicht bestätigt.',
    'email_not_confirmed_click_link' => 'Bitte klicke auf den Link in der E-Mail, die Du nach der Registrierung erhalten hast.',
    'email_not_confirmed_resend' => 'Wenn Du die E-Mail nicht erhalten hast, kannst Du die Nachricht erneut anfordern. Fülle hierzu bitte das folgende Formular aus:',
    'email_not_confirmed_resend_button' => 'Bestätigungs-E-Mail erneut senden',

    // User Invite
    'user_invite_email_subject' => 'Du wurdest eingeladen :appName beizutreten!',
    'user_invite_email_greeting' => 'Ein Konto wurde für dich auf :appName erstellt.',
    'user_invite_email_text' => 'Klicke auf die Schaltfläche unten, um ein Passwort festzulegen und Zugriff zu erhalten:',
    'user_invite_email_action' => 'Konto-Passwort festlegen',
    'user_invite_page_welcome' => 'Willkommen bei :appName!',
    'user_invite_page_text' => 'Um die Anmeldung abzuschließen und Zugriff auf :appName zu bekommen muss noch ein Passwort festgelegt werden. Dieses wird in Zukunft zum Einloggen benötigt.',
    'user_invite_page_confirm_button' => 'Passwort bestätigen',
    'user_invite_success' => 'Das Passwort wurde gesetzt, du hast nun Zugriff auf :appName!',

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