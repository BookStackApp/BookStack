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
    'user_invite_success' => 'Adgangskode indstillet, du har nu adgang til :appName!'
];