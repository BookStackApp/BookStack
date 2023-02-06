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
    'password_hint' => 'Muss mindestens 8 Zeichen lang sein',
    'forgot_password' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'ldap_email_hint' => 'Bitte gib eine E-Mail-Adresse ein, um diese mit dem Account zu nutzen.',
    'create_account' => 'Account registrieren',
    'already_have_account' => 'Bereits ein Konto erstellt?',
    'dont_have_account' => 'Noch kein Konto erstellt?',
    'social_login' => 'Mit Sozialem Netzwerk anmelden',
    'social_registration' => 'Mit Sozialem Netzwerk registrieren',
    'social_registration_text' => 'Mit einem dieser Dienste registrieren oder anmelden',

    'register_thanks' => 'Vielen Dank für deine Registrierung!',
    'register_confirm' => 'Bitte prüfe deinen Posteingang und bestätige die Registrierung.',
    'registrations_disabled' => 'Eine Registrierung ist momentan nicht möglich',
    'registration_email_domain_invalid' => 'Du kannst dich mit dieser E-Mail nicht registrieren.',
    'register_success' => 'Vielen Dank für deine Registrierung! Du bist jetzt registriert und eingeloggt.',

    // Login auto-initiation
    'auto_init_starting' => 'Versuche Anmeldung',
    'auto_init_starting_desc' => 'Wir kontaktieren dein Authentifizierungssystem, um den Anmeldevorgang zu starten. Wenn nach 5 Sekunden kein Fortschritt zu sehen ist, kannst du versuchen, auf den unten stehenden Link zu klicken.',
    'auto_init_start_link' => 'Mit der Authentifizierung fortfahren',

    // Password Reset
    'reset_password' => 'Passwort vergessen',
    'reset_password_send_instructions' => 'Bitte gib Deine E-Mail-Adresse ein. Danach erhältst Du eine E-Mail mit einem Link zum Zurücksetzen deines Passwortes.',
    'reset_password_send_button' => 'Passwort zurücksetzen',
    'reset_password_sent' => 'Ein Link zum Zurücksetzen des Passworts wird an :email gesendet, wenn diese E-Mail-Adresse im System gefunden wird.',
    'reset_password_success' => 'Dein Passwort wurde erfolgreich zurückgesetzt.',
    'email_reset_subject' => 'Passwort zurücksetzen für :appName',
    'email_reset_text' => 'Du erhältst diese E-Mail, weil jemand versucht hat, dein Passwort zurückzusetzen.',
    'email_reset_not_requested' => 'Wenn du das Zurücksetzen des Passworts nicht angefordert hast, ist keine weitere Aktion erforderlich.',

    // Email Confirmation
    'email_confirm_subject' => 'Bestätige Deine E-Mail-Adresse für :appName',
    'email_confirm_greeting' => 'Danke, dass Du dich für :appName registrierst hast!',
    'email_confirm_text' => 'Bitte bestätige Deine E-Mail-Adresse, indem Du auf die Schaltfläche klickst:',
    'email_confirm_action' => 'E-Mail-Adresse bestätigen',
    'email_confirm_send_error' => 'Leider konnte die für die Registrierung notwendige E-Mail zur Bestätigung deiner E-Mail-Adresse nicht versandt werden. Bitte kontaktiere deinen Systemadministrator!',
    'email_confirm_success' => 'Deine E-Mail Adresse wurde bestätigt! Du solltest nun in der Lage sein, dich mit deiner E-Mail-Adresse anzumelden.',
    'email_confirm_resent' => 'Bestätigungs-E-Mail wurde erneut versendet, bitte überprüfe deinen Posteingang.',
    'email_confirm_thanks' => 'Vielen Dank für das Bestätigen!',
    'email_confirm_thanks_desc' => 'Bitte warte einen Augenblick, während deine Bestätigung bearbeitet wird. Wenn Du nach 3 Sekunden nicht weitergeleitet wirst, drücke unten den "Weiter" Link, um fortzufahren.',

    'email_not_confirmed' => 'E-Mail-Adresse ist nicht bestätigt',
    'email_not_confirmed_text' => 'Deine E-Mail-Adresse ist bisher nicht bestätigt.',
    'email_not_confirmed_click_link' => 'Bitte klicke auf den Link in der E-Mail, die du nach der Registrierung erhalten hast.',
    'email_not_confirmed_resend' => 'Wenn Du die E-Mail nicht erhalten hast, kannst Du die Nachricht erneut anfordern. Fülle hierzu bitte das folgende Formular aus:',
    'email_not_confirmed_resend_button' => 'Bestätigungs-E-Mail erneut senden',

    // User Invite
    'user_invite_email_subject' => 'Du wurdest eingeladen :appName beizutreten!',
    'user_invite_email_greeting' => 'Ein Konto wurde für dich auf :appName erstellt.',
    'user_invite_email_text' => 'Klicke auf die Schaltfläche unten, um ein Passwort festzulegen und Zugriff zu erhalten:',
    'user_invite_email_action' => 'Konto-Passwort festlegen',
    'user_invite_page_welcome' => 'Willkommen bei :appName!',
    'user_invite_page_text' => 'Um die Anmeldung abzuschließen und Zugriff auf :appName zu bekommen, muss noch ein Passwort festgelegt werden. Dieses wird in Zukunft für die Anmeldung benötigt.',
    'user_invite_page_confirm_button' => 'Passwort bestätigen',
    'user_invite_success_login' => 'Passwort gesetzt, du solltest nun in der Lage sein, dich mit deinem Passwort an :appName anzumelden!',

    // Multi-factor Authentication
    'mfa_setup' => 'Multi-Faktor-Authentifizierung einrichten',
    'mfa_setup_desc' => 'Richte eine Multi-Faktor-Authentifizierung als zusätzliche Sicherheitsstufe für dein Benutzerkonto ein.',
    'mfa_setup_configured' => 'Bereits konfiguriert',
    'mfa_setup_reconfigure' => 'Umkonfigurieren',
    'mfa_setup_remove_confirmation' => 'Bist du sicher, dass du diese Multi-Faktor-Authentifizierungsmethode entfernen möchtest?',
    'mfa_setup_action' => 'Einrichtung',
    'mfa_backup_codes_usage_limit_warning' => 'Du hast weniger als 5 Backup-Codes übrig. Bitte erstelle und speichere einen neuen Satz,  bevor Du keine Codes mehr hast, um zu verhindern, dass du von deinem Konto ausgesperrt wirst.',
    'mfa_option_totp_title' => 'Mobile App',
    'mfa_option_totp_desc' => 'Um Mehrfach-Faktor-Authentifizierung nutzen zu können, benötigst du eine mobile Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Backup Code',
    'mfa_option_backup_codes_desc' => 'Speichere eine Reihe von einmaligen Backup-Codes an einem sicheren Ort. Du kannst damit deine identität bestätigen.',
    'mfa_gen_confirm_and_enable' => 'Bestätigen und aktivieren',
    'mfa_gen_backup_codes_title' => 'Backup-Codes einrichten',
    'mfa_gen_backup_codes_desc' => 'Speichere die folgende Liste der Codes an einem sicheren Ort. Wenn du auf das System zugreifst, kannst du einen der Codes als zweiten Authentifizierungsmechanismus verwenden.',
    'mfa_gen_backup_codes_download' => 'Codes herunterladen',
    'mfa_gen_backup_codes_usage_warning' => 'Jeder Code kann nur einmal verwendet werden',
    'mfa_gen_totp_title' => 'Mobile App einrichten',
    'mfa_gen_totp_desc' => 'Um Mehrfach-Faktor-Authentifizierung nutzen zu können, benötigst du eine mobile Anwendung, die TOTP unterstützt, wie Google Authenticator, Authy oder Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Scanne den QR-Code unten mit deiner bevorzugten Authentifizierungs-App, um zu beginnen.',
    'mfa_gen_totp_verify_setup' => 'Setup überprüfen',
    'mfa_gen_totp_verify_setup_desc' => 'Überprüfe, dass alles funktioniert, indem du einen Code aus deiner Authentifizierungs-App in das Eingabefeld unten eingibst:',
    'mfa_gen_totp_provide_code_here' => 'Gib hier den von der App generierten Code ein',
    'mfa_verify_access' => 'Zugriff überprüfen',
    'mfa_verify_access_desc' => 'Dein Benutzerkonto erfordert, dass du deine Identität über eine zusätzliche Verifikationsebene bestätigst, bevor du Zugriff erhältst. Verifiziere diese mit einer deiner konfigurierten Methoden, um fortzufahren.',
    'mfa_verify_no_methods' => 'Keine Methoden konfiguriert',
    'mfa_verify_no_methods_desc' => 'Es konnten keine Multi-Faktor-Authentifizierungsmethoden für dein Konto gefunden werden. Du musst mindestens eine Methode einrichten, bevor du Zugriff erhältst.',
    'mfa_verify_use_totp' => 'Mit einer mobilen App verifizieren',
    'mfa_verify_use_backup_codes' => 'Mit einem Backup-Code verifizieren',
    'mfa_verify_backup_code' => 'Backup-Code',
    'mfa_verify_backup_code_desc' => 'Gib einen deiner verbleibenden Backup-Codes unten ein:',
    'mfa_verify_backup_code_enter_here' => 'Backup-Code hier eingeben',
    'mfa_verify_totp_desc' => 'Gib den Code ein, der mit deiner mobilen App generiert wurde:',
    'mfa_setup_login_notification' => 'Multi-Faktor-Methode konfiguriert. Bitte melde dich jetzt erneut mit der konfigurierten Methode an.',
];
