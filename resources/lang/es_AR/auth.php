<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Las credenciales no concuerdan con nuestros registros.',
    'throttle' => 'Demasiados intentos fallidos de conexión. Por favor intente nuevamente en :seconds segundos.',

    // Login & Register
    'sign_up' => 'Registrarse',
    'log_in' => 'Acceder',
    'log_in_with' => 'Acceder con :socialDriver',
    'sign_up_with' => 'Registrarse con :socialDriver',
    'logout' => 'Salir',

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
    'already_have_account' => '¿Ya tiene una cuenta?',
    'dont_have_account' => '¿No tiene una cuenta?',
    'social_login' => 'Acceso con cuenta Social',
    'social_registration' => 'Registro con cuenta Social',
    'social_registration_text' => 'Registrar y entrar utilizando otro servicio.',

    'register_thanks' => '¡Gracias por registrarse!',
    'register_confirm' => 'Por favor verifique su correo electrónico y presione en el botón de confirmación enviado para acceder a :appName.',
    'registrations_disabled' => 'Los registros están deshabilitados actualmente',
    'registration_email_domain_invalid' => 'Este dominio de correo electrónico no tiene acceso a esta aplicación',
    'register_success' => '¡Gracias por registrarse! Ahora se encuentra registrado y ha accedido a la aplicación.',


    // Password Reset
    'reset_password' => 'Restablecer la contraseña',
    'reset_password_send_instructions' => 'Introduzca su correo electrónico a continuación y se le enviará un correo electrónico con un enlace para la restauración',
    'reset_password_send_button' => 'Enviar enlace de restauración',
    'reset_password_sent' => 'Si la dirección de correo electrónico :email existe en el sistema, se enviará un enlace para restablecer la contraseña.',
    'reset_password_success' => 'Su contraseña se restableció con éxito.',
    'email_reset_subject' => 'Restauración de la contraseña de para la aplicación :appName',
    'email_reset_text' => 'Ud. esta recibiendo este correo electrónico debido a que recibimos una solicitud de restauración de la contraseña de su cuenta.',
    'email_reset_not_requested' => 'Si ud. no solicitó un cambio de contraseña, no se requiere ninguna acción.',


    // Email Confirmation
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

    // User Invite
    'user_invite_email_subject' => 'Lo invitaron a unirse a :appName!',
    'user_invite_email_greeting' => 'Se creó una cuenta para usted en :appName.',
    'user_invite_email_text' => 'Presione el botón de abajo para establecer una contraseña y tener acceso access:',
    'user_invite_email_action' => 'Establecer la contraseña de la cuenta',
    'user_invite_page_welcome' => 'Bienvenido a :appName!',
    'user_invite_page_text' => 'Para finalizar la cuenta y tener acceso debe establcer una contraseña que utilizará para ingresar a :appName en visitas futuras.',
    'user_invite_page_confirm_button' => 'Confirmar Contraseña',
    'user_invite_success' => 'Contraseña establecida, ahora tiene acceso a :appName!',

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