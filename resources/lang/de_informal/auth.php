<?php

// Extends 'de'
return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuche es in :seconds Sekunden erneut.',

    /**
     * Login & Register
     */
    'ldap_email_hint' => 'Bitte gib eine E-Mail-Adresse ein, um diese mit dem Account zu nutzen.',
    'register_confirm' => 'Bitte prüfe Deinen Posteingang und bestätig die Registrierung.',
    'registration_email_domain_invalid' => 'Du kannst dich mit dieser E-Mail nicht registrieren.',
    'register_success' => 'Vielen Dank für Deine Registrierung! Die Daten sind gespeichert und Du bist angemeldet.',

    /**
     * Password Reset
     */
    'reset_password_send_instructions' => 'Bitte gib Deine E-Mail-Adresse ein. Danach erhältst Du eine E-Mail mit einem Link zum Zurücksetzen Deines Passwortes.',
    'reset_password_sent_success' => 'Eine E-Mail mit dem Link zum Zurücksetzen Deines Passwortes wurde an :email gesendet.',
    'reset_password_success' => 'Dein Passwort wurde erfolgreich zurückgesetzt.',
    'email_reset_text' => 'Du erhältsts diese E-Mail, weil jemand versucht hat, Dein Passwort zurückzusetzen.',
    'email_reset_not_requested' => 'Wenn Du das nicht warst, brauchst Du nichts weiter zu tun.',

    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Bestätige Deine E-Mail-Adresse für :appName',
    'email_confirm_greeting' => 'Danke, dass Du dich für :appName registrierst hast!',
    'email_confirm_text' => 'Bitte bestätige Deine E-Mail-Adresse, indem Du auf die Schaltfläche klickst:',
    'email_confirm_send_error' => 'Leider konnte die für die Registrierung notwendige E-Mail zur Bestätigung Deine E-Mail-Adresse nicht versandt werden. Bitte kontaktiere den Systemadministrator!',
    'email_confirm_success' => 'Deine E-Mail-Adresse wurde best&auml;tigt!',
    'email_confirm_resent' => 'Bestätigungs-E-Mail wurde erneut versendet, bitte überprüfe Deinen Posteingang.',
    'email_not_confirmed_text' => 'Deine E-Mail-Adresse ist bisher nicht bestätigt.',
    'email_not_confirmed_click_link' => 'Bitte klicke auf den Link in der E-Mail, die Du nach der Registrierung erhalten hast.',
    'email_not_confirmed_resend' => 'Wenn Du die E-Mail nicht erhalten hast, kannst Du die Nachricht erneut anfordern. Fülle hierzu bitte das folgende Formular aus:',
];