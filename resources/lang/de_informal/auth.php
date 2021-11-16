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
    'email_confirm_success' => 'Your email has been confirmed! You should now be able to login using this email address.',
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
    'user_invite_success_login' => 'Password set, you should now be able to login using your set password to access :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Multi-Faktor-Authentifizierung einrichten',
    'mfa_setup_desc' => 'Richten Sie Multi-Faktor-Authentifizierung als zusätzliche Sicherheitsstufe für Ihr Benutzerkonto ein.',
    'mfa_setup_configured' => 'Bereits konfiguriert',
    'mfa_setup_reconfigure' => 'Umkonfigurieren',
    'mfa_setup_remove_confirmation' => 'Sind Sie sicher, dass Sie diese Multi-Faktor-Authentifizierungsmethode entfernen möchten?',
    'mfa_setup_action' => 'Einrichtung',
    'mfa_backup_codes_usage_limit_warning' => 'Sie haben weniger als 5 Backup-Codes übrig, Bitte erstellen und speichern Sie ein neues Set bevor Sie keine Codes mehr haben, um zu verhindern, dass Sie von Ihrem Konto gesperrt werden.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'Um Mehrfach-Faktor-Authentifizierung nutzen zu können, benötigen Sie eine mobile Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Code',
    'mfa_option_backup_codes_desc' => 'Speichern Sie sicher eine Reihe von einmaligen Backup-Codes, die Sie eingeben können, um Ihre Identität zu überprüfen.',
    'mfa_gen_confirm_and_enable' => 'Bestätigen und aktivieren',
    'mfa_gen_backup_codes_title' => 'Backup-Codes einrichten',
    'mfa_gen_backup_codes_desc' => 'Speichern Sie die folgende Liste der Codes an einem sicheren Ort. Wenn Sie auf das System zugreifen, können Sie einen der Codes als zweiten Authentifizierungsmechanismus verwenden.',
    'mfa_gen_backup_codes_download' => 'Download Codes',
    'mfa_gen_backup_codes_usage_warning' => 'Jeder Code kann nur einmal verwendet werden',
    'mfa_gen_totp_title' => 'Mobile App einrichten',
    'mfa_gen_totp_desc' => 'Um Mehrfach-Faktor-Authentifizierung nutzen zu können, benötigen Sie eine mobile Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scannen Sie den QR-Code unten mit Ihrer bevorzugten Authentifizierungs-App, um loszulegen.',
    'mfa_gen_totp_verify_setup' => 'Setup überprüfen',
    'mfa_gen_totp_verify_setup_desc' => 'Überprüfen Sie, dass alles funktioniert, indem Sie einen Code in Ihrer Authentifizierungs-App in das Eingabefeld unten eingeben:',
    'mfa_gen_totp_provide_code_here' => 'Geben Sie hier Ihre App generierten Code ein',
    'mfa_verify_access' => 'Zugriff überprüfen',
    'mfa_verify_access_desc' => 'Ihr Benutzerkonto erfordert, dass Sie Ihre Identität über eine zusätzliche Verifikationsebene bestätigen, bevor Sie den Zugriff gewähren. Überprüfen Sie mit einer Ihrer konfigurierten Methoden, um fortzufahren.',
    'mfa_verify_no_methods' => 'Keine Methoden konfiguriert',
    'mfa_verify_no_methods_desc' => 'Es konnten keine Mehrfach-Faktor-Authentifizierungsmethoden für Ihr Konto gefunden werden. Sie müssen mindestens eine Methode einrichten, bevor Sie Zugriff erhalten.',
    'mfa_verify_use_totp' => 'Mit einer mobilen App verifizieren',
    'mfa_verify_use_backup_codes' => 'Mit einem Backup-Code überprüfen',
    'mfa_verify_backup_code' => 'Backup-Code',
    'mfa_verify_backup_code_desc' => 'Geben Sie einen Ihrer verbleibenden Backup-Codes unten ein:',
    'mfa_verify_backup_code_enter_here' => 'Backup-Code hier eingeben',
    'mfa_verify_totp_desc' => 'Geben Sie den Code ein, der mit Ihrer mobilen App generiert wurde:',
    'mfa_setup_login_notification' => 'Multi-Faktor-Methode konfiguriert. Bitte melden Sie sich jetzt erneut mit der konfigurierten Methode an.',
];
