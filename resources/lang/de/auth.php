<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Die eingegebenen Anmeldedaten sind ungültig.',
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
    'password_confirm' => 'Passwort best&auml;tigen',
    'password_hint' => 'Mindestlänge: 7 Zeichen',
    'forgot_password' => 'Passwort vergessen?',
    'remember_me' => 'Angemeldet bleiben',
    'ldap_email_hint' => 'Bitte geben Sie eine E-Mail-Adresse ein, um diese mit dem Account zu nutzen.',
    'create_account' => 'Account registrieren',
    'already_have_account' => 'Bereits ein Konto erstellt?',
    'dont_have_account' => 'Noch kein Konto erstellt?',
    'social_login' => 'Mit Sozialem Netzwerk anmelden',
    'social_registration' => 'Mit Sozialem Netzwerk registrieren',
    'social_registration_text' => 'Mit einer dieser Dienste registrieren oder anmelden',

    'register_thanks' => 'Vielen Dank für Ihre Registrierung!',
    'register_confirm' => 'Bitte prüfen Sie Ihren Posteingang und bestätigen Sie die Registrierung.',
    'registrations_disabled' => 'Eine Registrierung ist momentan nicht möglich',
    'registration_email_domain_invalid' => 'Sie können sich mit dieser E-Mail nicht registrieren.',
    'register_success' => 'Vielen Dank für Ihre Registrierung! Die Daten sind gespeichert und Sie sind angemeldet.',


    // Password Reset
    'reset_password' => 'Passwort vergessen',
    'reset_password_send_instructions' => 'Bitte geben Sie Ihre E-Mail-Adresse ein. Danach erhalten Sie eine E-Mail mit einem Link zum Zurücksetzen Ihres Passwortes.',
    'reset_password_send_button' => 'Passwort zurücksetzen',
    'reset_password_sent' => 'A password reset link will be sent to :email if that email address is found in the system.',
    'reset_password_success' => 'Ihr Passwort wurde erfolgreich zurückgesetzt.',
    'email_reset_subject' => 'Passwort zurücksetzen für :appName',
    'email_reset_text' => 'Sie erhalten diese E-Mail, weil jemand versucht hat, Ihr Passwort zurückzusetzen.',
    'email_reset_not_requested' => 'Wenn Sie das nicht waren, brauchen Sie nichts weiter zu tun.',


    // Email Confirmation
    'email_confirm_subject' => 'Bestätigen Sie Ihre E-Mail-Adresse für :appName',
    'email_confirm_greeting' => 'Danke, dass Sie sich für :appName registriert haben!',
    'email_confirm_text' => 'Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf die Schaltfläche klicken:',
    'email_confirm_action' => 'E-Mail-Adresse bestätigen',
    'email_confirm_send_error' => 'Leider konnte die für die Registrierung notwendige E-Mail zur bestätigung Ihrer E-Mail-Adresse nicht versandt werden. Bitte kontaktieren Sie den Systemadministrator!',
    'email_confirm_success' => 'Ihre E-Mail-Adresse wurde best&auml;tigt!',
    'email_confirm_resent' => 'Bestätigungs-E-Mail wurde erneut versendet, bitte überprüfen Sie Ihren Posteingang.',

    'email_not_confirmed' => 'E-Mail-Adresse ist nicht bestätigt',
    'email_not_confirmed_text' => 'Ihre E-Mail-Adresse ist bisher nicht bestätigt.',
    'email_not_confirmed_click_link' => 'Bitte klicken Sie auf den Link in der E-Mail, die Sie nach der Registrierung erhalten haben.',
    'email_not_confirmed_resend' => 'Wenn Sie die E-Mail nicht erhalten haben, können Sie die Nachricht erneut anfordern. Füllen Sie hierzu bitte das folgende Formular aus:',
    'email_not_confirmed_resend_button' => 'Bestätigungs-E-Mail erneut senden',

    // User Invite
    'user_invite_email_subject' => 'Du wurdest eingeladen :appName beizutreten!',
    'user_invite_email_greeting' => 'Ein Konto wurde für Sie auf :appName erstellt.',
    'user_invite_email_text' => 'Klicken Sie auf die Schaltfläche unten, um ein Passwort festzulegen und Zugriff zu erhalten:',
    'user_invite_email_action' => 'Account-Passwort festlegen',
    'user_invite_page_welcome' => 'Willkommen bei :appName!',
    'user_invite_page_text' => 'Um die Anmeldung abzuschließen und Zugriff auf :appName zu bekommen muss noch ein Passwort festgelegt werden. Dieses wird in Zukunft zum Einloggen benötigt.',
    'user_invite_page_confirm_button' => 'Passwort wiederholen',
    'user_invite_success' => 'Passwort gesetzt, Sie haben nun Zugriff auf :appName!'
];