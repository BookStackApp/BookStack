<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Les credencials no coincideixen amb les que tenim emmagatzemades.',
    'throttle' => 'Massa intents d\'iniciar la sessió. Torneu-ho a provar d\'aquí a :seconds segons.',

    // Login & Register
    'sign_up' => 'Registra-m\'hi',
    'log_in' => 'Inicia la sessió',
    'log_in_with' => 'Inicia la sessió amb :socialDriver',
    'sign_up_with' => 'Registra-m\'hi amb :socialDriver',
    'logout' => 'Tanca la sessió',

    'name' => 'Nom',
    'username' => 'Nom d\'usuari',
    'email' => 'Adreça electrònica',
    'password' => 'Contrasenya',
    'password_confirm' => 'Confirmeu la contrasenya',
    'password_hint' => 'Cal que tingui més de 7 caràcters',
    'forgot_password' => 'Heu oblidat la contrasenya?',
    'remember_me' => 'Recorda\'m',
    'ldap_email_hint' => 'Introduïu una adreça electrònica per a aquest compte.',
    'create_account' => 'Crea el compte',
    'already_have_account' => 'Ja teniu un compte?',
    'dont_have_account' => 'No teniu cap compte?',
    'social_login' => 'Inici de sessió social',
    'social_registration' => 'Registre social',
    'social_registration_text' => 'Registreu-vos i inicieu la sessió fent servir un altre servei.',

    'register_thanks' => 'Gràcies per registrar-vos!',
    'register_confirm' => 'Reviseu el vostre correu electrònic i feu clic al botó de confirmació per a accedir a :appName.',
    'registrations_disabled' => 'Actualment, els registres estan desactivats',
    'registration_email_domain_invalid' => 'Aquest domini de correu electrònic no té accés a aquesta aplicació',
    'register_success' => 'Gràcies per registrar-vos! Ja us hi heu registrat i heu iniciat la sessió.',


    // Password Reset
    'reset_password' => 'Restableix la contrasenya',
    'reset_password_send_instructions' => 'Introduïu la vostra adreça electrònica a continuació i us enviarem un correu electrònic amb un enllaç per a restablir la contrasenya.',
    'reset_password_send_button' => 'Envia l\'enllaç de restabliment',
    'reset_password_sent' => 'S\'enviarà un enllaç per a restablir la contrasenya a :email, si es troba aquesta adreça al sistema.',
    'reset_password_success' => 'La vostra contrasenya s\'ha restablert correctament.',
    'email_reset_subject' => 'Restabliu la contrasenya a :appName',
    'email_reset_text' => 'Rebeu aquest correu electrònic perquè heu rebut una petició de restabliment de contrasenya per al vostre compte.',
    'email_reset_not_requested' => 'Si no heu demanat restablir la contrasenya, no cal que prengueu cap acció.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirmeu la vostra adreça electrònica a :appName',
    'email_confirm_greeting' => 'Gràcies per unir-vos a :appName!',
    'email_confirm_text' => 'Confirmeu la vostra adreça electrònica fent clic al botó a continuació:',
    'email_confirm_action' => 'Confirma el correu',
    'email_confirm_send_error' => 'Cal confirmar l\'adreça electrònica, però el sistema no ha pogut enviar el correu electrònic. Poseu-vos en contacte amb l\'administrador perquè s\'asseguri que el correu electrònic està ben configurat.',
    'email_confirm_success' => 'S\'ha confirmat el vostre correu electrònic!',
    'email_confirm_resent' => 'S\'ha tornat a enviar el correu electrònic de confirmació. Reviseu la vostra safata d\'entrada.',

    'email_not_confirmed' => 'Adreça electrònica no confirmada',
    'email_not_confirmed_text' => 'La vostra adreça electrònica encara no està confirmada.',
    'email_not_confirmed_click_link' => 'Feu clic a l\'enllaç del correu electrònic que us vam enviar poc després que us registréssiu.',
    'email_not_confirmed_resend' => 'Si no podeu trobar el correu, podeu tornar a enviar el correu electrònic de confirmació enviant el formulari a continuació.',
    'email_not_confirmed_resend_button' => 'Torna a enviar el correu de confirmació',

    // User Invite
    'user_invite_email_subject' => 'Us han convidat a unir-vos a :appName!',
    'user_invite_email_greeting' => 'Us hem creat un compte en el vostre nom a :appName.',
    'user_invite_email_text' => 'Feu clic al botó a continuació per a definir una contrasenya per al compte i obtenir-hi accés:',
    'user_invite_email_action' => 'Defineix una contrasenya per al compte',
    'user_invite_page_welcome' => 'Us donem la benvinguda a :appName!',
    'user_invite_page_text' => 'Per a enllestir el vostre compte i obtenir-hi accés, cal que definiu una contrasenya, que es farà servir per a iniciar la sessió a :appName en futures visites.',
    'user_invite_page_confirm_button' => 'Confirma la contrasenya',
    'user_invite_success' => 'S\'ha establert la contrasenya, ara ja teniu accés a :appName!'
];
