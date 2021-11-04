<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'As credenciais fornecidas não puderam ser validadas em nossos registros.',
    'throttle' => 'Muitas tentativas de login. Por favor, tente novamente em :seconds segundos.',

    // Login & Register
    'sign_up' => 'Criar Conta',
    'log_in' => 'Entrar',
    'log_in_with' => 'Entrar com :socialDriver',
    'sign_up_with' => 'Cadastre-se com :socialDriver',
    'logout' => 'Sair',

    'name' => 'Nome',
    'username' => 'Nome de Usuário',
    'email' => 'E-mail',
    'password' => 'Senha',
    'password_confirm' => 'Confirmar Senha',
    'password_hint' => 'Deve ser maior que 7 caracteres',
    'forgot_password' => 'Esqueceu a senha?',
    'remember_me' => 'Lembrar de mim',
    'ldap_email_hint' => 'Por favor, digite um e-mail para essa conta.',
    'create_account' => 'Criar Conta',
    'already_have_account' => 'Já possui uma conta?',
    'dont_have_account' => 'Não possui uma conta?',
    'social_login' => 'Login Social',
    'social_registration' => 'Cadastro Social',
    'social_registration_text' => 'Cadastre-se e entre utilizando outro serviço.',

    'register_thanks' => 'Obrigado por se cadastrar!',
    'register_confirm' => 'Por favor, verifique seu e-mail e clique no botão de confirmação para acessar :appName.',
    'registrations_disabled' => 'Cadastros estão temporariamente desabilitados',
    'registration_email_domain_invalid' => 'O domínio de e-mail usado não tem acesso permitido a essa aplicação',
    'register_success' => 'Obrigado por se cadastrar! Você agora encontra-se cadastrado(a) e logado(a).',

    // Password Reset
    'reset_password' => 'Redefinir Senha',
    'reset_password_send_instructions' => 'Insira seu e-mail abaixo e uma mensagem com o link de redefinição de senha lhe será enviada.',
    'reset_password_send_button' => 'Enviar o Link de Redefinição',
    'reset_password_sent' => 'Um link de redefinição de senha será enviado para :email se o endereço de e-mail for encontrado no sistema.',
    'reset_password_success' => 'Sua senha foi redefinida com sucesso.',
    'email_reset_subject' => 'Redefina a senha de :appName',
    'email_reset_text' => 'Você recebeu esse e-mail pois recebemos uma solicitação de redefinição de senha para a sua conta.',
    'email_reset_not_requested' => 'Caso não tenha sido você a solicitar a redefinição de senha, ignore esse e-mail.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirme seu e-mail para :appName',
    'email_confirm_greeting' => 'Obrigado por se cadastrar em :appName!',
    'email_confirm_text' => 'Por favor, confirme seu endereço de e-mail clicando no botão abaixo:',
    'email_confirm_action' => 'Confirmar E-mail',
    'email_confirm_send_error' => 'A confirmação de e-mail é requerida, mas o sistema não pôde enviar a mensagem. Por favor, entre em contato com o administrador para se certificar que o serviço de envio de e-mails está corretamente configurado.',
    'email_confirm_success' => 'Seu e-mail foi confirmado!',
    'email_confirm_resent' => 'E-mail de confirmação reenviado. Por favor, verifique sua caixa de entrada.',

    'email_not_confirmed' => 'Endereço de E-mail Não Confirmado',
    'email_not_confirmed_text' => 'Seu endereço de e-mail ainda não foi confirmado.',
    'email_not_confirmed_click_link' => 'Por favor, clique no link no e-mail que foi enviado após o cadastro.',
    'email_not_confirmed_resend' => 'Caso não encontre o e-mail você poderá reenviar a confirmação usando o formulário abaixo.',
    'email_not_confirmed_resend_button' => 'Reenviar o E-mail de Confirmação',

    // User Invite
    'user_invite_email_subject' => 'Você recebeu um convite para :appName!',
    'user_invite_email_greeting' => 'Uma conta foi criada para você em :appName.',
    'user_invite_email_text' => 'Clique no botão abaixo para definir uma senha de conta e obter acesso:',
    'user_invite_email_action' => 'Defina a Senha da Conta',
    'user_invite_page_welcome' => 'Bem-vindo(a) a :appName!',
    'user_invite_page_text' => 'Para finalizar sua conta e obter acesso, você precisa definir uma senha que será usada para efetuar login em :appName em futuras visitas.',
    'user_invite_page_confirm_button' => 'Confirmar Senha',
    'user_invite_success' => 'Senha definida, você agora tem acesso a :appName!',

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
