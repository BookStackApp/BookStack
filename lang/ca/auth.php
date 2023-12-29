<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Les credencials no coincideixen amb les que hi ha emmagatzemades.',
    'throttle' => 'Massa intents d’inici de sessió. Torneu-ho a provar d’aquí a :seconds segons.',

    // Login & Register
    'sign_up' => 'Registra-m’hi',
    'log_in' => 'Inicia la sessió',
    'log_in_with' => 'Inicia la sessió amb :socialDriver',
    'sign_up_with' => 'Registra-m’hi amb :socialDriver',
    'logout' => 'Tanca la sessió',

    'name' => 'Nom',
    'username' => 'Nom d’usuari',
    'email' => 'Adreça electrònica',
    'password' => 'Contrasenya',
    'password_confirm' => 'Confirmeu la contrasenya',
    'password_hint' => 'Cal que tingui un mínim de 8 caràcters',
    'forgot_password' => 'Heu oblidat la contrasenya?',
    'remember_me' => 'Recorda’m',
    'ldap_email_hint' => 'Introduïu una adreça electrònica per a aquest compte.',
    'create_account' => 'Crea el compte',
    'already_have_account' => 'Ja teniu un compte?',
    'dont_have_account' => 'No teniu cap compte?',
    'social_login' => 'Inici de sessió amb xarxes socials',
    'social_registration' => 'Registre amb xarxes socials',
    'social_registration_text' => 'Registreu-vos i inicieu la sessió fent servir un altre servei.',

    'register_thanks' => 'Gràcies per registrar-vos!',
    'register_confirm' => 'Reviseu el vostre correu electrònic i feu clic al botó de confirmació per a accedir a :appName.',
    'registrations_disabled' => 'Actualment, els registres estan desactivats',
    'registration_email_domain_invalid' => 'Aquest domini de correu electrònic no té accés a aquesta aplicació',
    'register_success' => 'Gràcies per registrar-vos! Ja us hi heu registrat i heu iniciat la sessió.',

    // Login auto-initiation
    'auto_init_starting' => 'S’està provant d’iniciar la sessió',
    'auto_init_starting_desc' => 'Estem contactant amb el vostre sistema d’autenticació per a començar el procés d’inici de sessió. Si no hi ha cap progrés d’aquí a 5 segons, proveu de fer clic a l’enllaç de sota.',
    'auto_init_start_link' => 'Continua amb l’autenticació',

    // Password Reset
    'reset_password' => 'Restableix la contrasenya',
    'reset_password_send_instructions' => 'Introduïu la vostra adreça electrònica a continuació i us enviarem un correu electrònic amb un enllaç per a restablir la contrasenya.',
    'reset_password_send_button' => 'Envia l’enllaç de restabliment',
    'reset_password_sent' => 'S’enviarà un enllaç per a restablir la contrasenya a :email, si es troba aquesta adreça al sistema.',
    'reset_password_success' => 'La contrasenya s’ha restablert correctament.',
    'email_reset_subject' => 'Restabliu la contrasenya a :appName',
    'email_reset_text' => 'Rebeu aquest correu electrònic perquè s’ha fet una petició de restabliment de contrasenya per al vostre compte.',
    'email_reset_not_requested' => 'Si no heu demanat restablir la contrasenya, no cal que emprengueu cap acció.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmeu la vostra adreça electrònica a :appName',
    'email_confirm_greeting' => 'Gràcies per unir-vos a :appName!',
    'email_confirm_text' => 'Confirmeu la vostra adreça electrònica fent clic al botó a continuació:',
    'email_confirm_action' => 'Confirma el correu',
    'email_confirm_send_error' => 'Cal confirmar l’adreça electrònica, però el sistema no ha pogut enviar el correu electrònic. Poseu-vos en contacte amb l’administrador perquè s’asseguri que el correu electrònic està ben configurat.',
    'email_confirm_success' => 'Heu confirmat el vostre correu electrònic! Ara hauríeu de poder iniciar la sessió fent servir aquesta adreça electrònica.',
    'email_confirm_resent' => 'S’ha tornat a enviar el correu electrònic de confirmació. Reviseu la vostra safata d’entrada.',
    'email_confirm_thanks' => 'Gràcies per la confirmació!',
    'email_confirm_thanks_desc' => 'Espereu uns instants mentre gestionem la confirmació. Si no se us redirigeix d’aquí a 3 segons, premeu l’enllaç «Continua» de sota per a continuar.',

    'email_not_confirmed' => 'Adreça electrònica no confirmada',
    'email_not_confirmed_text' => 'La vostra adreça electrònica encara no està confirmada.',
    'email_not_confirmed_click_link' => 'Feu clic a l’enllaç del correu electrònic que us vam enviar poc després que us registréssiu.',
    'email_not_confirmed_resend' => 'Si no el trobeu, podeu tornar a enviar el correu electrònic de confirmació enviant el formulari a continuació.',
    'email_not_confirmed_resend_button' => 'Torna a enviar el correu de confirmació',

    // User Invite
    'user_invite_email_subject' => 'Us han convidat a unir-vos a :appName!',
    'user_invite_email_greeting' => 'S’ha creat un compte en el vostre nom a :appName.',
    'user_invite_email_text' => 'Feu clic al botó a continuació per a definir una contrasenya per al compte i obtenir-hi accés:',
    'user_invite_email_action' => 'Defineix una contrasenya per al compte',
    'user_invite_page_welcome' => 'Us donem la benvinguda a :appName!',
    'user_invite_page_text' => 'Per a enllestir el vostre compte i obtenir-hi accés, cal que definiu una contrasenya, que es farà servir per a iniciar la sessió a :appName en futures visites.',
    'user_invite_page_confirm_button' => 'Confirma la contrasenya',
    'user_invite_success_login' => 'S’ha definit la contrasenya. Ara hauríeu de poder iniciar la sessió fent servir la contrasenya que heu definit per a accedir a :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configura l’autenticació de múltiple factor',
    'mfa_setup_desc' => 'Configureu l’autenticació de múltiple factor com a capa extra de seguretat en el vostre compte d’usuari.',
    'mfa_setup_configured' => 'Ja està configurada',
    'mfa_setup_reconfigure' => 'Torna-la a configurar',
    'mfa_setup_remove_confirmation' => 'Segur que voleu suprimir aquest mètode d’autenticació de múltiple factor?',
    'mfa_setup_action' => 'Configura',
    'mfa_backup_codes_usage_limit_warning' => 'Teniu menys de 5 codis de seguretat restants. Genereu-ne i emmagatzemeu-ne un nou conjunt abans que se us acabin perquè no perdeu l’accés al vostre compte.',
    'mfa_option_totp_title' => 'Aplicació mòbil',
    'mfa_option_totp_desc' => 'Per a fer servir l’autenticació de múltiple factor us caldrà una aplicació mòbil que suporti TOTP, com ara Google Authenticador, Authy o Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Codis de seguretat',
    'mfa_option_backup_codes_desc' => 'Deseu de manera segura codis de seguretat d’un sol ús que podeu introduir per a verificar la vostra identitat.',
    'mfa_gen_confirm_and_enable' => 'Confirma i activa',
    'mfa_gen_backup_codes_title' => 'Configuració de codis de seguretat',
    'mfa_gen_backup_codes_desc' => 'Deseu la següent llista de codis en un lloc segur. Quan accediu al sistema, podeu fer servir un dels codis com a segon mètode d’autenticació.',
    'mfa_gen_backup_codes_download' => 'Baixa els codis',
    'mfa_gen_backup_codes_usage_warning' => 'Cada codi es pot fer servir només una vegada',
    'mfa_gen_totp_title' => 'Configuració de l’aplicació mòbil',
    'mfa_gen_totp_desc' => 'Per a fer servir l’autenticació de múltiple factor us caldrà una aplicació mòbil que suporti TOTP, com ara Google Authenticador, Authy o Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Per a començar, escanegeu el codi QR següent fent servir la vostre aplicació d’autenticació preferida.',
    'mfa_gen_totp_verify_setup' => 'Verifica la configuració',
    'mfa_gen_totp_verify_setup_desc' => 'Verifiqueu que tot funciona introduint un codi, generat amb l’aplicació d’autenticació, a la capsa de text següent:',
    'mfa_gen_totp_provide_code_here' => 'Proporcioneu aquí el codi generat per l’aplicació',
    'mfa_verify_access' => 'Verifica l’accés',
    'mfa_verify_access_desc' => 'El vostre compte d’usuari requereix que confirmeu la vostra identitat amb un nivell addicional de verificació abans que pugueu obtenir-hi accés. Verifiqueu-la fent servir un dels vostres mètodes configurats per a continuar.',
    'mfa_verify_no_methods' => 'No hi ha cap mètode configurat',
    'mfa_verify_no_methods_desc' => 'No s’ha trobat cap mètode d’autenticació de múltiple factor al vostre compte. Cal que configureu almenys un mètode abans d’obtenir-hi accés.',
    'mfa_verify_use_totp' => 'Verifica fent servir una aplicació mòbil',
    'mfa_verify_use_backup_codes' => 'Verifica fent servir un codi de seguretat',
    'mfa_verify_backup_code' => 'Codi de seguretat',
    'mfa_verify_backup_code_desc' => 'Introduïu un dels vostres codis de seguretat restants a continuació:',
    'mfa_verify_backup_code_enter_here' => 'Introduïu aquí el codi de seguretat',
    'mfa_verify_totp_desc' => 'Introduïu el codi generat amb la vostra aplicació mòbil a continuació:',
    'mfa_setup_login_notification' => 'S’ha configurat el mètode d’autenticació de múltiple factor. Torneu a iniciar la sessió fent servir el mètode que heu configurat.',
];
