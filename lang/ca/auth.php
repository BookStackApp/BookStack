<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Aquestes credencials no existeixen al nostre registre.',
    'throttle' => 'Massa intents d’inici de sessió. Torneu-ho a provar d’aquí :seconds segons.',

    // Login & Register
    'sign_up' => 'Registreu-vos',
    'log_in' => 'Inicia sessió',
    'log_in_with' => 'Inicia sessió amb :socialDriver',
    'sign_up_with' => 'Registreu-vos amb :socialDriver',
    'logout' => 'Tanca la sessió',

    'name' => 'Nom',
    'username' => 'Nom d’usuari',
    'email' => 'Correu electrònic',
    'password' => 'Contrasenya',
    'password_confirm' => 'Confirmeu la contrasenya',
    'password_hint' => 'Ha de tenir almenys 8 caràcters',
    'forgot_password' => 'Heu oblidat la contrasenya?',
    'remember_me' => 'Recorda’m',
    'ldap_email_hint' => 'Introduïu un correu electrònic per al compte.',
    'create_account' => 'Creeu un compte',
    'already_have_account' => 'Ja teniu un compte?',
    'dont_have_account' => 'No teniu cap compte?',
    'social_login' => 'Inici de sessió amb una xarxa social',
    'social_registration' => 'Registre amb una xarxa social',
    'social_registration_text' => 'Registreu-vos i inicieu sessió amb un altre servei.',

    'register_thanks' => 'Gràcies per registrar-vos.',
    'register_confirm' => 'Comproveu el correu electrònic i cliqueu el botó de confirmació per a accedir a :appName.',
    'registrations_disabled' => 'Els registres estan desactivats',
    'registration_email_domain_invalid' => 'Aquest domini de correu electrònic no té accés a l’aplicació.',
    'register_success' => 'Gràcies per registrar-vos Us heu registrat i heu iniciat sessió.',

    // Login auto-initiation
    'auto_init_starting' => 'S’està provant d’iniciar sessió',
    'auto_init_starting_desc' => 'Estem contactant amb el vostre sistema d’autenticació per a començar el procés d’inici sessió. Si d’aquí 5 segons no hi ha hagut cap progrés proveu de clicar l’enllaç.',
    'auto_init_start_link' => 'Continua amb la l’autenticació',

    // Password Reset
    'reset_password' => 'Reinicialitza la contrasenya',
    'reset_password_send_instructions' => 'Introduïu la vostra adreça electrònica i us enviarem un correu electrònic amb un enllaç de reinicialització de la contrasenya.',
    'reset_password_send_button' => 'Envia’m un enllaç de reinicialització',
    'reset_password_sent' => 'Si l’adreça electrònica :email existeix al sistema, us hi enviarem un enllaç de reinicialització de contrasenya.',
    'reset_password_success' => 'S’ha reinicialitzat la contrasenya.',
    'email_reset_subject' => 'Reinicialització de la contrasenya de :appName',
    'email_reset_text' => 'Heu rebut aquest correu electrònic perquè hem rebut una sol·licitud de reinicialització de contrasenya per al vostre compte.',
    'email_reset_not_requested' => 'Si no heu sol·licitat la reinicialització de la contrasenya, no cal que feu res més.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirmeu l’adreça electrònica de :appName',
    'email_confirm_greeting' => 'Gràcies per registrar-vos a :appName!',
    'email_confirm_text' => 'Cliqueu el botó per a confirmar l’adreça electrònica:',
    'email_confirm_action' => 'Confirmeu d’adreça electrònica',
    'email_confirm_send_error' => 'S’ha de confirmar de l’adreça electrònica però no s’ha pogut enviar el correu de confirmació. Contacteu l’administrador per a assegurar-vos que el correu està ben configurat.',
    'email_confirm_success' => 'S’ha confirmat el correu electrònic. Ara hauríeu de poder iniciar sessió amb aquesta adreça electrònica.',
    'email_confirm_resent' => 'S’ha enviat el correu de confirmació. Comproveu la safata d’entrada.',
    'email_confirm_thanks' => 'Gràcies per la confirmació.',
    'email_confirm_thanks_desc' => 'Espereu-vos un moment mentre es gestiona la confirmació. Si d’aquí 3 segons no se us ha redirigit, cliqueu l’enllaç &laquo;Continua&raquo; per a continuar.',

    'email_not_confirmed' => 'No s’ha confirmat l’adreça de correu electrònic',
    'email_not_confirmed_text' => 'Encara no heu confirmat l’adreça electrònica.',
    'email_not_confirmed_click_link' => 'Cliqueu l’enllaç que hi ha al correu electrònic que se us va enviar en registrar-vos.',
    'email_not_confirmed_resend' => 'Si no trobeu el correu electrònic ompliu el formulari a continuació i us n’enviarem un altre.',
    'email_not_confirmed_resend_button' => 'Torna a enviar el correu de confirmació',

    // User Invite
    'user_invite_email_subject' => 'Us han convidat a utilitzar :appName!',
    'user_invite_email_greeting' => 'Us han creat un compte a :appName.',
    'user_invite_email_text' => 'Cliqueu el botó per a configurar una contrasenya pel compte i poder-hi accedir:',
    'user_invite_email_action' => 'Configura la contrasenya del compte',
    'user_invite_page_welcome' => 'Us donem la benvinguda a :appName!',
    'user_invite_page_text' => 'Per a poder accedir al compte heu de configurar una contrasenya que s’utilitzarà per a iniciar sessió a :appName d’ara endavant.',
    'user_invite_page_confirm_button' => 'Confirma la contrasenya',
    'user_invite_success_login' => 'S’ha configurat la contrasenya. Ara hauríeu de poder iniciar sessió a :appName amb la contrasenya configurada.',

    // Multi-factor Authentication
    'mfa_setup' => 'Configuració de l’autenticació multifactorial',
    'mfa_setup_desc' => 'Configureu l’autenticació multifactorial per a afegir una capa de seguretat extra al vostre compte d’usuari.',
    'mfa_setup_configured' => 'Ja està configurat',
    'mfa_setup_reconfigure' => 'Torna-la a configurar',
    'mfa_setup_remove_confirmation' => 'Esteu segur que voleu eliminar aquest mètode d’autenticació multifactorial?',
    'mfa_setup_action' => 'Configura',
    'mfa_backup_codes_usage_limit_warning' => 'Us queden menys de 5 codis de suport. Genereu-ne de nous i deseu-los abans de quedar-vos-en sense per a evitar perdre l’accés al  compte.',
    'mfa_option_totp_title' => 'Aplicació mòbil',
    'mfa_option_totp_desc' => 'Per a utilitzar l’autenticació multifactorial heu de tenir una aplicació que sigui compatible amb TOPT (contrasenyes d’un sol ús basades en el temps) com ara Google Authenticator, Authy, o Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Codis de suport',
    'mfa_option_backup_codes_desc' => 'Genereu un joc de codis de suport d’un sol ús que haureu d’introduir per a verificar la vostra identitat. Assegureu-vos de desar-los en un lloc segur.',
    'mfa_gen_confirm_and_enable' => 'Confirma i activa',
    'mfa_gen_backup_codes_title' => 'Configuració dels codis de suport',
    'mfa_gen_backup_codes_desc' => 'Deseu els codis en un lloc segur. Podreu utilitzar un dels codis com a un altre mètode d’autenticació per a iniciar sessió.',
    'mfa_gen_backup_codes_download' => 'Baixa els codis',
    'mfa_gen_backup_codes_usage_warning' => 'Només podeu utilitzar cada codi un sol cop.',
    'mfa_gen_totp_title' => 'Configuració de l’aplicació mòbil',
    'mfa_gen_totp_desc' => 'Per a utilitzar l’autenticació multifactorial heu de tenir una aplicació que sigui compatible amb TOPT (contrasenyes d’un sol ús basades en el temps) com ara Google Authenticator, Authy, o Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Escanegeu el codi QR amb l’aplicació d’autenticació que preferiu per a començar.',
    'mfa_gen_totp_verify_setup' => 'Verificació de la configuració',
    'mfa_gen_totp_verify_setup_desc' => 'Introduïu un dels codis generats per l’aplicació d’autenticació per a verificar que tot funciona:',
    'mfa_gen_totp_provide_code_here' => 'Codi',
    'mfa_verify_access' => 'Verifica l’accés',
    'mfa_verify_access_desc' => 'Heu de verificar la vostra identitat amb un nivell de verificació addicional per a iniciar sessió. Verifiqueu-la amb un dels mètodes que heu configurat per a continuar.',
    'mfa_verify_no_methods' => 'No hi ha cap mètode configurat',
    'mfa_verify_no_methods_desc' => 'No hi ha cap mètode d’autenticació multifactorial configurat al vostre compte. Heu de configurar com a mínim un mètode per a iniciar sessió.',
    'mfa_verify_use_totp' => 'Verificació amb una aplicació mòbil',
    'mfa_verify_use_backup_codes' => 'Verificació amb un codi de suport',
    'mfa_verify_backup_code' => 'Codi de suport',
    'mfa_verify_backup_code_desc' => 'Introduïu un dels codis de suport que us quedin:',
    'mfa_verify_backup_code_enter_here' => 'Codi:',
    'mfa_verify_totp_desc' => 'Introduïu el codi generat per l’aplicació mòbil:',
    'mfa_setup_login_notification' => 'S’ha configurat el mètode multifactorial. Torneu a iniciar sessió amb el mètode configurat.',
];
