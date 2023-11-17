<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Annettuja käyttäjätietoja ei löydy.',
    'throttle' => 'Liikaa kirjautumisyrityksiä. Yritä uudelleen :seconds sekunnin päästä.',

    // Login & Register
    'sign_up' => 'Rekisteröidy',
    'log_in' => 'Kirjaudu sisään',
    'log_in_with' => 'Kirjaudu sisään palvelulla :socialDriver',
    'sign_up_with' => 'Rekisteröidy palvelulla :socialDriver',
    'logout' => 'Kirjaudu ulos',

    'name' => 'Nimi',
    'username' => 'Käyttäjätunnus',
    'email' => 'Sähköposti',
    'password' => 'Salasana',
    'password_confirm' => 'Vahvista salasana',
    'password_hint' => 'Tulee olla vähintään 8 merkkiä',
    'forgot_password' => 'Unohditko salasanan?',
    'remember_me' => 'Muista minut',
    'ldap_email_hint' => 'Ole hyvä ja anna käyttäjätilin sähköpostiosoite.',
    'create_account' => 'Luo käyttäjätili',
    'already_have_account' => 'Onko sinulla jo käyttäjätili?',
    'dont_have_account' => 'Eikö sinulla ole käyttäjätiliä?',
    'social_login' => 'Kirjaudu sosiaalisen median käyttäjätilillä',
    'social_registration' => 'Rekisteröidy sosiaalisen median käyttäjätilillä',
    'social_registration_text' => 'Rekisteröidy ja kirjaudu sisään käyttämällä toista palvelua.',

    'register_thanks' => 'Kiitos rekisteröitymisestä!',
    'register_confirm' => 'Tarkista sähköpostisi ja paina vahvistuspainiketta päästäksesi sovellukseen :appName.',
    'registrations_disabled' => 'Rekisteröityminen on tällä hetkellä pois käytöstä',
    'registration_email_domain_invalid' => 'Tämän sähköpostiosoitteen verkkotunnuksella ei ole pääsyä tähän sovellukseen',
    'register_success' => 'Kiitos liittymisestä! Olet nyt rekisteröitynyt ja kirjautunut sisään.',

    // Login auto-initiation
    'auto_init_starting' => 'Kirjautumisyritys',
    'auto_init_starting_desc' => 'Otamme yhteyttä tunnistautumisjärjestelmääsi aloittaaksemme kirjautumisprosessin. Jos 5 sekunnin jälkeen ei tapahdu mitään, voit yrittää klikata alla olevaa linkkiä.',
    'auto_init_start_link' => 'Jatka tunnistautumisen avulla',

    // Password Reset
    'reset_password' => 'Palauta salasana',
    'reset_password_send_instructions' => 'Syötä sähköpostiosoitteesi alla olevaan kenttään, niin sinulle lähetetään sähköpostiviesti, jossa on salasanan palautuslinkki.',
    'reset_password_send_button' => 'Lähetä palautuslinkki',
    'reset_password_sent' => 'Salasanan palautuslinkki lähetetään osoitteeseen :email, jos kyseinen sähköpostiosoite löytyy järjestelmästä.',
    'reset_password_success' => 'Salasanasi on onnistuneesti palautettu.',
    'email_reset_subject' => 'Palauta salasanasi sivustolle :appName',
    'email_reset_text' => 'Saat tämän sähköpostiviestin, koska saimme käyttäjätiliäsi koskevan salasanan palautuspyynnön.',
    'email_reset_not_requested' => 'Jos et ole pyytänyt salasanan palauttamista, mitään toimenpiteitä ei tarvita.',

    // Email Confirmation
    'email_confirm_subject' => 'Vahvista sähköpostisi sovelluksessa :appName',
    'email_confirm_greeting' => 'Kiitos liittymisestä sovellukseen :appName!',
    'email_confirm_text' => 'Vahvista sähköpostiosoitteesi klikkaamalla alla olevaa painiketta:',
    'email_confirm_action' => 'Vahvista sähköpostiosoite',
    'email_confirm_send_error' => 'Sähköpostivahvistusta vaaditaan, mutta järjestelmä ei pystynyt lähettämään sähköpostia. Ota yhteyttä ylläpitäjään varmistaaksesi, että sähköpostiasetukset on määritetty oikein.',
    'email_confirm_success' => 'Sähköpostisi on vahvistettu! Sinun pitäisi nyt pystyä kirjautumaan sisään tällä sähköpostiosoitteella.',
    'email_confirm_resent' => 'Vahvistussähköposti on lähetetty uudelleen, tarkista saapuneet sähköpostisi.',
    'email_confirm_thanks' => 'Kiitos vahvistuksesta!',
    'email_confirm_thanks_desc' => 'Odota hetki, kun vahvistuksesi käsitellään. Jos sinua ei ohjata uudelleen 3 sekunnin kuluttua, paina alla olevaa "Jatka"-linkkiä.',

    'email_not_confirmed' => 'Sähköpostiosoitetta ei ole vahvistettu',
    'email_not_confirmed_text' => 'Sähköpostiosoitettasi ei ole vielä vahvistettu.',
    'email_not_confirmed_click_link' => 'Klikkaa rekisteröitymisen jälkeen saapuneessa sähköpostissa olevaa vahvistuslinkkiä.',
    'email_not_confirmed_resend' => 'Jos et löydä sähköpostia, voit lähettää sen uudelleen alla olevalla lomakkeella.',
    'email_not_confirmed_resend_button' => 'Lähetä vahvistusviesti uudelleen',

    // User Invite
    'user_invite_email_subject' => 'Sinut on kutsuttu liittymään sivustoon :appName!',
    'user_invite_email_greeting' => 'Sinulle on luotu käyttäjätili sivustolla :appName.',
    'user_invite_email_text' => 'Klikkaa alla olevaa painiketta asettaaksesi tilin salasanan ja saadaksesi pääsyn:',
    'user_invite_email_action' => 'Aseta käyttäjätilin salasana',
    'user_invite_page_welcome' => 'Tervetuloa sivustolle :appName!',
    'user_invite_page_text' => 'Viimeistelläksesi käyttäjätilisi ja saadaksesi pääsyn sinun on asetettava salasana, jolla kirjaudut jatkossa sivustolle :appName.',
    'user_invite_page_confirm_button' => 'Vahvista salasana',
    'user_invite_success_login' => 'Salasana asetettu, sinun pitäisi nyt pystyä kirjautumaan sivustolle :appName käyttämällä antamaasi salasanaa!',

    // Multi-factor Authentication
    'mfa_setup' => 'Määritä monivaiheinen tunnistautuminen',
    'mfa_setup_desc' => 'Määritä monivaiheinen tunnistautuminen käyttäjätilisi turvallisuuden parantamiseksi.',
    'mfa_setup_configured' => 'Määritetty',
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
