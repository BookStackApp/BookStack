<?php
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
    'failed' => 'Las credenciales no concuerdan con nuestros registros.',
    'throttle' => 'Demasiados intentos fallidos de conexión. Por favor intente nuevamente en :seconds segundos.',

    /**
     * Login & Register
     */
    'sign_up' => 'Registrarse',
    'log_in' => 'Acceder',
    'log_in_with' => 'Acceder con :socialDriver',
    'sign_up_with' => 'Registrarse con :socialDriver',
    'logout' => 'Logout',

    'name' => 'Nombre',
    'username' => 'Nombre de usuario',
    'email' => 'Correo electrónico',
    'password' => 'Contraseña',
    'password_confirm' => 'Confirmar contraseña',
    'password_hint' => 'Debe contener al menos 7 caracteres',
    'forgot_password' => '¿Olvidó la contraseña?',
    'remember_me' => 'Recordarme',
    'ldap_email_hint' => 'Por favor introduzca un correo electrónico para utilizar con esta cuenta.',
    'create_account' => 'Crear una cuenta',
    'social_login' => 'Acceso con cuenta Social',
    'social_registration' => 'Registro con cuenta Social',
    'social_registration_text' => 'Registrar y entrar utilizando otro servicio.',

    'register_thanks' => '¡Gracias por registrarse!',
    'register_confirm' => 'Por favor verifique su correo electrónico y presione en el botón de confirmación enviado para acceder a :appName.',
    'registrations_disabled' => 'Los registros están deshabilitados actualmente',
    'registration_email_domain_invalid' => 'Este dominio de correo electrónico no tiene acceso a esta aplicación',
    'register_success' => '¡Gracias por registrarse! Ahora se encuentra registrado y ha accedido a la aplicación.',


    /**
     * Password Reset
     */
    'reset_password' => 'Restablecer la contraseña',
    'reset_password_send_instructions' => 'Introduzca su correo electrónico a continuación y se le enviará un correo electrónico con un enlace para la restauración',
    'reset_password_send_button' => 'Enviar enlace de restauración',
    'reset_password_sent_success' => 'Se envió un enlace para restablecer la contraseña a :email.',
    'reset_password_success' => 'Su contraseña se restableció con éxito.',

    'email_reset_subject' => 'Restauración de la contraseña de para la aplicación :appName',
    'email_reset_text' => 'Ud. esta recibiendo este correo electrónico debido a que recibimos una solicitud de restauración de la contraseña de su cuenta.',
    'email_reset_not_requested' => 'Si ud. no solicitó un cambio de contraseña, no se requiere ninguna acción.',


    /**
     * Email Confirmation
     */
    'email_confirm_subject' => 'Confirme su correo electrónico en :appName',
    'email_confirm_greeting' => '¡Gracias por unirse a :appName!',
    'email_confirm_text' => 'Por favor confirme su dirección de correo electrónico presionando en el siguiente botón:',
    'email_confirm_action' => 'Confirmar correo electrónico',
    'email_confirm_send_error' => 'Se pidió confirmación de correo electrónico pero el sistema no pudo enviar el correo electrónico. Contacte al administrador para asegurarse que el correo electrónico está configurado correctamente.',
    'email_confirm_success' => '¡Su correo electrónico hasido confirmado!',
    'email_confirm_resent' => 'Correo electrónico de confirmación reenviado, Por favor verifique su bandeja de entrada.',

    'email_not_confirmed' => 'Dirección de correo electrónico no confirmada',
    'email_not_confirmed_text' => 'Su cuenta de correo electrónico todavía no ha sido confirmada.',
    'email_not_confirmed_click_link' => 'Por favor verifique el correo electrónico con el enlace de confirmación que fue enviado luego de registrarse.',
    'email_not_confirmed_resend' => 'Si no puede encontrar el correo electrónico, puede solicitar el renvío del correo electrónico de confirmación rellenando el formulario a continuación.',
    'email_not_confirmed_resend_button' => 'Reenviar correo electrónico de confirmación',
];
