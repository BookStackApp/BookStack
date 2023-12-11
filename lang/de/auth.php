<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Diese Anmeldedaten stimmen nicht mit unseren Aufzeichnungen überein.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuchen Sie es in :seconds Sekunden erneut.',

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
    'password_hint' => 'Muss mindestens 8 Zeichen lang sein',
    'forgot_password' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'ldap_email_hint' => 'Bitte geben Sie eine E-Mail-Adresse ein, um diese mit dem Account zu nutzen.',
    'create_account' => 'Account erstellen',
    'already_have_account' => 'Sie haben bereits einen Account?',
    'dont_have_account' => 'Sie haben noch keinen Account?',
    'social_login' => 'Mit sozialem Netzwerk anmelden',
    'social_registration' => 'Mit sozialem Netzwerk registrieren',
    'social_registration_text' => 'Mit einem anderen Dienst registrieren oder anmelden.',

    'register_thanks' => 'Vielen Dank für Ihre Registrierung!',
    'register_confirm' => 'Bitte prüfen Sie Ihren Posteingang und bestätigen Sie die Registrierung, um :appName nutzen zu können.',
    'registrations_disabled' => 'Eine Registrierung ist momentan nicht möglich',
    'registration_email_domain_invalid' => 'Sie können sich mit dieser E-Mail-Adresse nicht für diese Anwendung registrieren',
    'register_success' => 'Vielen Dank für Ihre Registrierung! Die Daten sind gespeichert und Sie sind angemeldet.',

    // Login auto-initiation
    'auto_init_starting' => 'Anmeldeversuche',
    'auto_init_starting_desc' => 'Wir verbinden und mit Ihrem Authentifizierungssystem um den Anmeldeprozess zu starten. Sollte es nach 5 Sekunden nicht weitergehen, klicken Sie bitte auf den unten stehenden Link.',
    'auto_init_start_link' => 'Mit Authentifizierung fortfahren',

    // Password Reset
    'reset_password' => 'Passwort zurücksetzen',
    'reset_password_send_instructions' => 'Bitte geben Sie Ihre E-Mail-Adresse ein. Danach erhalten Sie eine E-Mail mit einem Link zum Zurücksetzen Ihres Passwortes.',
    'reset_password_send_button' => 'Link zum Zurücksetzen senden',
    'reset_password_sent' => 'Ein Link zum Zurücksetzen des Passworts wird an :email gesendet, wenn diese E-Mail-Adresse im System gefunden wird.',
    'reset_password_success' => 'Ihr Passwort wurde erfolgreich zurückgesetzt.',
    'email_reset_subject' => 'Passwort zurücksetzen für :appName',
    'email_reset_text' => 'Sie erhalten diese E-Mail, weil jemand versucht hat, Ihr Passwort zurückzusetzen.',
    'email_reset_not_requested' => 'Wenn Sie das Zurücksetzen des Passworts nicht angefordert haben, ist keine weitere Aktion erforderlich.',

    // Email Confirmation
    'email_confirm_subject' => 'Bestätigen Sie Ihre E-Mail-Adresse für :appName',
    'email_confirm_greeting' => 'Danke, dass Sie sich für :appName registriert haben!',
    'email_confirm_text' => 'Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf die Schaltfläche klicken:',
    'email_confirm_action' => 'E-Mail-Adresse bestätigen',
    'email_confirm_send_error' => 'Leider konnte die für die Registrierung notwendige E-Mail zur Bestätigung Ihrer E-Mail-Adresse nicht versandt werden. Bitte kontaktieren Sie den Systemadministrator.',
    'email_confirm_success' => 'Ihre E-Mail wurde bestätigt! Sie sollten nun in der Lage sein, sich mit dieser E-Mail-Adresse anzumelden.',
    'email_confirm_resent' => 'Bestätigungs-E-Mail wurde erneut versendet, bitte überprüfen Sie Ihren Posteingang.',
    'email_confirm_thanks' => 'Vielen Dank für das Bestätigen!',
    'email_confirm_thanks_desc' => 'Bitte warten Sie einen Augenblick, während Ihre Bestätigung bearbeitet wird. Wenn Sie nach 3 Sekunden nicht weitergeleitet werden, drücken Sie unten den „Weiter“-Link, um fortzufahren.',

    'email_not_confirmed' => 'E-Mail-Adresse ist nicht bestätigt',
    'email_not_confirmed_text' => 'Ihre E-Mail-Adresse ist bisher nicht bestätigt.',
    'email_not_confirmed_click_link' => 'Bitte klicken Sie auf den Link in der E-Mail, die Sie nach der Registrierung erhalten haben.',
    'email_not_confirmed_resend' => 'Wenn Sie die E-Mail nicht erhalten haben, können Sie die Nachricht erneut anfordern. Füllen Sie hierzu bitte das folgende Formular aus.',
    'email_not_confirmed_resend_button' => 'Bestätigungs-E-Mail erneut senden',

    // User Invite
    'user_invite_email_subject' => 'Sie wurden eingeladen, :appName beizutreten!',
    'user_invite_email_greeting' => 'Ein Konto wurde für Sie auf :appName erstellt.',
    'user_invite_email_text' => 'Klicken Sie auf die Schaltfläche unten, um ein Passwort festzulegen und Zugriff zu erhalten:',
    'user_invite_email_action' => 'Account-Passwort festlegen',
    'user_invite_page_welcome' => 'Willkommen bei :appName!',
    'user_invite_page_text' => 'Um die Anmeldung abzuschließen und Zugriff auf :appName zu bekommen, muss noch ein Passwort festgelegt werden. Dieses wird in Zukunft für die Anmeldung benötigt.',
    'user_invite_page_confirm_button' => 'Passwort bestätigen',
    'user_invite_success_login' => 'Passwort gesetzt, Sie sollten nun in der Lage sein, sich mit Ihrem Passwort an :appName anzumelden!',

    // Multi-factor Authentication
    'mfa_setup' => 'Multi-Faktor-Authentifizierung einrichten',
    'mfa_setup_desc' => 'Richten Sie Multi-Faktor-Authentifizierung als zusätzliche Sicherheitsstufe für Ihr Benutzerkonto ein.',
    'mfa_setup_configured' => 'Bereits konfiguriert',
    'mfa_setup_reconfigure' => 'Umkonfigurieren',
    'mfa_setup_remove_confirmation' => 'Sind Sie sicher, dass Sie diese Multi-Faktor-Authentifizierungsmethode entfernen möchten?',
    'mfa_setup_action' => 'Einrichtung',
    'mfa_backup_codes_usage_limit_warning' => 'Sie haben weniger als 5 Backup-Codes übrig, bitte erstellen und speichern Sie ein neues Set, bevor Ihnen die Codes ausgehen, um zu verhindern, dass Sie aus Ihrem Konto ausgesperrt werden.',
    'mfa_option_totp_title' => 'Handy-App',
    'mfa_option_totp_desc' => 'Um Mehrfach-Faktor-Authentifizierung nutzen zu können, benötigen Sie eine Handy-Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup-Codes',
    'mfa_option_backup_codes_desc' => 'Speichern Sie sicher eine Reihe von einmaligen Backup-Codes, die Sie eingeben können, um Ihre Identität zu überprüfen.',
    'mfa_gen_confirm_and_enable' => 'Bestätigen und aktivieren',
    'mfa_gen_backup_codes_title' => 'Backup-Codes einrichten',
    'mfa_gen_backup_codes_desc' => 'Speichern Sie die folgende Liste von Codes an einem sicheren Ort. Wenn Sie auf das System zugreifen, können Sie einen der Codes als zweiten Authentifizierungsmechanismus verwenden.',
    'mfa_gen_backup_codes_download' => 'Codes herunterladen',
    'mfa_gen_backup_codes_usage_warning' => 'Jeder Code kann nur einmal verwendet werden',
    'mfa_gen_totp_title' => 'Handy-App einrichten',
    'mfa_gen_totp_desc' => 'Um Multi-Faktor-Authentifizierung nutzen zu können, benötigen Sie eine Handy-Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scannen Sie den QR-Code unten mit Ihrer bevorzugten Authentifizierungs-App, um loszulegen.',
    'mfa_gen_totp_verify_setup' => 'Setup überprüfen',
    'mfa_gen_totp_verify_setup_desc' => 'Überprüfen Sie, dass alles funktioniert, indem Sie einen von Ihrer Authentifizierungs-App generierten Code in das Eingabefeld unten eingeben:',
    'mfa_gen_totp_provide_code_here' => 'Geben Sie hier Ihren App-generierten Code ein',
    'mfa_verify_access' => 'Zugriff überprüfen',
    'mfa_verify_access_desc' => 'Ihr Benutzerkonto erfordert, dass Sie Ihre Identität über eine zusätzliche Verifikationsebene bestätigen, bevor Sie Zugriff erhalten. Nutzen Sie dazu eine Ihrer konfigurierten Methoden, um fortzufahren.',
    'mfa_verify_no_methods' => 'Keine Methoden konfiguriert',
    'mfa_verify_no_methods_desc' => 'Es konnten keine Multi-Faktor-Authentifizierungsmethoden für Ihr Konto gefunden werden. Sie müssen mindestens eine Methode einrichten, bevor Sie Zugriff erhalten.',
    'mfa_verify_use_totp' => 'Mit einer Handy-App verifizieren',
    'mfa_verify_use_backup_codes' => 'Mit einem Backup-Code überprüfen',
    'mfa_verify_backup_code' => 'Backup-Code',
    'mfa_verify_backup_code_desc' => 'Geben Sie unten einen Ihrer verbleibenden Backup-Codes ein:',
    'mfa_verify_backup_code_enter_here' => 'Backup-Code hier eingeben',
    'mfa_verify_totp_desc' => 'Geben Sie den Code ein, der mit Ihrer Handy-App generiert wurde:',
    'mfa_setup_login_notification' => 'Multi-Faktor-Methode konfiguriert. Bitte melden Sie sich jetzt erneut mit der konfigurierten Methode an.',
];
