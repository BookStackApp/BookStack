<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Estas credenciales no coinciden con nuestros registros.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en :seconds segundos.',

    // Login & Register
    'sign_up' => 'Registrarse',
    'log_in' => 'Acceder',
    'log_in_with' => 'Acceder con :socialDriver',
    'sign_up_with' => 'Registrarse con :socialDriver',
    'logout' => 'Cerrar sesión',

    'name' => 'Nombre',
    'username' => 'Usuario',
    'email' => 'Correo electrónico',
    'password' => 'Contraseña',
    'password_confirm' => 'Confirmar Contraseña',
    'password_hint' => 'Debe contener más de 7 caracteres',
    'forgot_password' => '¿Contraseña Olvidada?',
    'remember_me' => 'Recordarme',
    'ldap_email_hint' => 'Por favor introduzca un mail para utilizar con esta cuenta.',
    'create_account' => 'Crear una Cuenta',
    'already_have_account' => '¿Ya tienes una cuenta?',
    'dont_have_account' => '¿No tienes una cuenta?',
    'social_login' => 'Login Social',
    'social_registration' => 'Registro Social',
    'social_registration_text' => 'Registrar y entrar utilizando otro servicio.',

    'register_thanks' => '¡Gracias por registrarse!',
    'register_confirm' => 'Por favor compruebe su correo electrónico y haga clic en el botón de confirmación enviado para acceder a :appName.',
    'registrations_disabled' => 'Los registros están deshabilitados actualmente',
    'registration_email_domain_invalid' => 'Este dominio de correo electrónico no tiene acceso a esta aplicación',
    'register_success' => '¡Gracias por registrarse! Ahora se encuentra registrado y logueado.',


    // Password Reset
    'reset_password' => 'Resetear Contraseña',
    'reset_password_send_instructions' => 'Introduzca su correo electrónico a continuación y le será enviado un correo con un link para la restauración',
    'reset_password_send_button' => 'Enviar Enlace de Reseteo',
    'reset_password_sent' => 'Un enlace para cambiar la contraseña será enviado a su dirección de correo electrónico si existe en nuestro sistema.',
    'reset_password_success' => 'Su password ha sido reseteado de manera éxitosa.',
    'email_reset_subject' => 'Resetee la contraseña de :appName',
    'email_reset_text' => 'Está recibiendo este correo electrónico debido a que recibimos una solicitud de reseteo de contraseña de su cuenta.',
    'email_reset_not_requested' => 'Si no ha solicitado un reseteo de la contraseña, no es requerida ninguna acción por su parte.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirme su correo electrónico en :appName',
    'email_confirm_greeting' => '¡Gracias por unirse a :appName!',
    'email_confirm_text' => 'Por favor confirme su dirección de correo electrónico haciendo click en el siguiente botón:',
    'email_confirm_action' => 'Confirmar Correo Electrónico',
    'email_confirm_send_error' => 'Confirmation de correo electrónico requerida pero el sistema no pudo enviar el correo. Contacte con el administrador para asegurarse de que el correo electrónico está configurado correctamente.',
    'email_confirm_success' => '¡Su correo electrónico ha sido confirmado!',
    'email_confirm_resent' => 'correo electrónico de confirmación reenviado, compruebe su bandeja de entrada.',

    'email_not_confirmed' => 'Dirección de Correo Electrónico no confirmada',
    'email_not_confirmed_text' => 'Su Cuenta de Correo electrónico todavía no ha sido confirmada.',
    'email_not_confirmed_click_link' => 'Por favor siga el enlace en el correo electrónico que ha sido enviado durante el proceso de registro.',
    'email_not_confirmed_resend' => 'Si no puede encontrar el correo electrónico, puede solicitar el renvío del correo electrónico de confirmación rellenando el formulario que se muestra a continuación.',
    'email_not_confirmed_resend_button' => 'Reenviar Correo Electrónico de confirmación',

    // User Invite
    'user_invite_email_subject' => 'As sido invitado a unirte a :appName!',
    'user_invite_email_greeting' => 'Se ha creado una cuenta para usted en :appName.',
    'user_invite_email_text' => 'Clica en el botón a continuación para ajustar una contraseña y poder acceder:',
    'user_invite_email_action' => 'Ajustar la Contraseña de la Cuenta',
    'user_invite_page_welcome' => '¡Bienvenido a :appName!',
    'user_invite_page_text' => 'Para completar la cuenta y tener acceso es necesario que configure una contraseña que se utilizará para entrar en :appName en futuros accesos.',
    'user_invite_page_confirm_button' => 'Confirmar Contraseña',
    'user_invite_success' => '¡Contraseña guardada, ya tiene acceso a :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configurar Autenticación en Dos Pasos',
    'mfa_setup_desc' => 'La autenticación en dos pasos añade una capa de seguridad adicional a tu cuenta de usuario.',
    'mfa_setup_configured' => 'Ya está configurado',
    'mfa_setup_reconfigure' => 'Reconfigurar',
    'mfa_setup_remove_confirmation' => '¿Está seguro de que desea eliminar este método de autenticación en dos pasos?',
    'mfa_setup_action' => 'Configuración',
    'mfa_backup_codes_usage_limit_warning' => 'Quedan menos de 5 códigos de respaldo, Por favor, genera y almacena un nuevo conjunto antes de que te quedes sin códigos para evitar que te bloquees fuera de tu cuenta.',
    'mfa_option_totp_title' => 'Aplicación para móviles',
    'mfa_option_totp_desc' => 'Para utilizar la autenticación en dos pasos necesitarás una aplicación móvil que soporte TOTP como Google Authenticator, Authy o Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Códigos de Respaldo',
    'mfa_option_backup_codes_desc' => 'Almacena de forma segura un conjunto de códigos de respaldo de un solo uso que puedes introducir para verificar tu identidad.',
    'mfa_gen_confirm_and_enable' => 'Confirmar y Activar',
    'mfa_gen_backup_codes_title' => 'Configuración de Códigos de Respaldo',
    'mfa_gen_backup_codes_desc' => 'Guarda la siguiente lista de códigos en un lugar seguro. Al acceder al sistema podrás usar uno de los códigos como un segundo mecanismo de autenticación.',
    'mfa_gen_backup_codes_download' => 'Descargar Códigos',
    'mfa_gen_backup_codes_usage_warning' => 'Cada código sólo puede utilizarse una vez',
    'mfa_gen_totp_title' => 'Configuración de Aplicación móvil',
    'mfa_gen_totp_desc' => 'Para utilizar la autenticación en dos pasos necesitarás una aplicación móvil que soporte TOTP como Google Authenticator, Authy o Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Escanea el código QR mostrado a continuación usando tu aplicación de autenticación preferida para empezar.',
    'mfa_gen_totp_verify_setup' => 'Verificar Configuración',
    'mfa_gen_totp_verify_setup_desc' => 'Verifica que todo está funcionando introduciendo un código, generado en tu aplicación de autenticación, en el campo de texto a continuación:',
    'mfa_gen_totp_provide_code_here' => 'Introduce aquí tu código generado por la aplicación',
    'mfa_verify_access' => 'Verificar Acceso',
    'mfa_verify_access_desc' => 'Tu cuenta de usuario requiere que confirmes tu identidad a través de un nivel adicional de verificación antes de que te conceda el acceso. Verifica tu identidad usando uno de los métodos configurados para continuar.',
    'mfa_verify_no_methods' => 'No hay Métodos Configurados',
    'mfa_verify_no_methods_desc' => 'No se han encontrado métodos de autenticación en dos pasos para tu cuenta. Tendrás que configurar al menos un método antes de obtener acceso.',
    'mfa_verify_use_totp' => 'Verificar usando una aplicación móvil',
    'mfa_verify_use_backup_codes' => 'Verificar usando un código de respaldo',
    'mfa_verify_backup_code' => 'Códigos de Respaldo',
    'mfa_verify_backup_code_desc' => 'Introduzca uno de sus códigos de respaldo restantes a continuación:',
    'mfa_verify_backup_code_enter_here' => 'Introduce el código de respaldo aquí',
    'mfa_verify_totp_desc' => 'Introduzca el código, generado con tu aplicación móvil, a continuación:',
    'mfa_setup_login_notification' => 'Método de dos factores configurado. Por favor, inicia sesión de nuevo utilizando el método configurado.',
];