<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Dee indtastede brugeroplysninger stemmer ikke overens med vores registreringer.',
    'throttle' => 'For mange mislykkede loginforsøg. Prøv igen om :seconds sekunder.',

    // Login & Register
    'sign_up' => 'Registrér',
    'log_in' => 'Log ind',
    'log_in_with' => 'Log ind med :socialDriver',
    'sign_up_with' => 'Registrér med :socialDriver',
    'logout' => 'Log ud',

    'name' => 'Navn',
    'username' => 'Brugernavn',
    'email' => 'E-mail',
    'password' => 'Adgangskode',
    'password_confirm' => 'Bekræft adgangskode',
    'password_hint' => 'Skal være på mindst 7 karakterer',
    'forgot_password' => 'Glemt Adgangskode?',
    'remember_me' => 'Husk mig',
    'ldap_email_hint' => 'Angiv venligst din kontos e-mail.',
    'create_account' => 'Opret konto',
    'already_have_account' => 'Har du allerede en konto?',
    'dont_have_account' => 'Har du ikke en konto?',
    'social_login' => 'Social Log ind',
    'social_registration' => 'Social Registrering',
    'social_registration_text' => 'Registrér og log ind med anden service.',

    'register_thanks' => 'Tak for registreringen!',
    'register_confirm' => 'Check venligst din e-mail og klik deri på bekræftelses knappen for at tilgå :appName.',
    'registrations_disabled' => 'Registrering er i øjeblikket deaktiveret',
    'registration_email_domain_invalid' => 'E-Mail domænet har ikke adgang til denne applikation',
    'register_success' => 'Tak for din registrering. Du er nu registeret og logget ind.',


    // Password Reset
    'reset_password' => 'Nulstil adgangskode',
    'reset_password_send_instructions' => 'Indtast din E-Mail herunder og du vil blive sendt en E-Mail med et link til at nulstille din adgangskode.',
    'reset_password_send_button' => 'Send link til nulstilling',
    'reset_password_sent' => 'Et link til nulstilling af adgangskode sendes til :email, hvis den e-mail-adresse findes i systemet.',
    'reset_password_success' => 'Din adgangskode er blevet nulstillet.',
    'email_reset_subject' => 'Nulstil din :appName adgangskode',
    'email_reset_text' => 'Du modtager denne E-Mail fordi vi har modtaget en anmodning om at nulstille din adgangskode.',
    'email_reset_not_requested' => 'Hvis du ikke har anmodet om at få din adgangskode nulstillet, behøver du ikke at foretage dig noget.',


    // Email Confirmation
    'email_confirm_subject' => 'Bekræft din E-Mail på :appName',
    'email_confirm_greeting' => 'Tak for at oprette dig på :appName!',
    'email_confirm_text' => 'Bekræft venligst din E-Mail adresse ved at klikke på linket nedenfor:',
    'email_confirm_action' => 'Bekræft E-Mail',
    'email_confirm_send_error' => 'E-Mail-bekræftelse kræves, men systemet kunne ikke sende E-Mailen. Kontakt administratoren for at sikre, at E-Mail er konfigureret korrekt.',
    'email_confirm_success' => 'Din E-Mail er blevet bekræftet!',
    'email_confirm_resent' => 'Bekræftelsesmail sendt, tjek venligst din indboks.',

    'email_not_confirmed' => 'E-Mail adresse ikke bekræftet',
    'email_not_confirmed_text' => 'Din E-Mail adresse er endnu ikke blevet bekræftet.',
    'email_not_confirmed_click_link' => 'Klik venligst på linket i E-Mailen der blev sendt kort efter du registrerede dig.',
    'email_not_confirmed_resend' => 'Hvis du ikke kan finde E-Mailen, kan du du få gensendt bekræftelsesemailen ved at trykke herunder.',
    'email_not_confirmed_resend_button' => 'Gensend bekræftelsesemail',

    // User Invite
    'user_invite_email_subject' => 'Du er blevet inviteret til :appName!',
    'user_invite_email_greeting' => 'En konto er blevet oprettet til dig på :appName.',
    'user_invite_email_text' => 'Klik på knappen nedenunderm for at sætte en adgangskode og opnå adgang:',
    'user_invite_email_action' => 'Set adgangskode',
    'user_invite_page_welcome' => 'Velkommen til :appName!',
    'user_invite_page_text' => 'For at færdiggøre din konto og få adgang skal du indstille en adgangskode, der bruges til at logge ind på :appName ved fremtidige besøg.',
    'user_invite_page_confirm_button' => 'Bekræft adgangskode',
    'user_invite_success' => 'Adgangskode indstillet, du har nu adgang til :appName!',

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