<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'As credenciais fornecidas não correspondem aos nossos registos.',
    'throttle' => 'Demasiadas tentativas de início de sessão. Por favor, tente novamente em :seconds segundos.',

    // Login & Register
    'sign_up' => 'Criar conta',
    'log_in' => 'Iniciar sessão',
    'log_in_with' => 'Iniciar sessão com :socialDriver',
    'sign_up_with' => 'Criar conta com :socialDriver',
    'logout' => 'Terminar sessão',

    'name' => 'Nome',
    'username' => 'Nome de utilizador',
    'email' => 'E-mail',
    'password' => 'Palavra-passe',
    'password_confirm' => 'Confirmar Palavra-passe',
    'password_hint' => 'Deve ser maior que 7 caracteres',
    'forgot_password' => 'Esqueceu-se da palavra-passe?',
    'remember_me' => 'Lembrar-se de mim',
    'ldap_email_hint' => 'Por favor insira um endereço de e-mail para esta conta.',
    'create_account' => 'Criar Conta',
    'already_have_account' => 'Já possui uma conta?',
    'dont_have_account' => 'Não possui uma conta?',
    'social_login' => 'Inicio de Sessão com Redes Sociais',
    'social_registration' => 'Registo com Redes Sociais',
    'social_registration_text' => 'Registe e inicie sessão com recurso a outro serviço.',

    'register_thanks' => 'Obrigado por se registar!',
    'register_confirm' => 'Por favor, verifique o seu e-mail e carregue no botão de confirmação para aceder :appName.',
    'registrations_disabled' => 'Os registos estão temporariamente desativados',
    'registration_email_domain_invalid' => 'O domínio de e-mail usado não tem acesso permitido a esta aplicação',
    'register_success' => 'Obrigado por se registar! Você está agora registado e com a sessão iniciada.',

    // Password Reset
    'reset_password' => 'Redefinir Senha',
    'reset_password_send_instructions' => 'Insira o seu endereço de e-mail abaixo, e uma mensagem com o link de redefinição de palavra-passe será lhe enviada.',
    'reset_password_send_button' => 'Enviar o Link de Redefinição',
    'reset_password_sent' => 'Um link de redefinição de palavra-passe será enviado para :email, se o endereço de e-mail for encontrado no sistema.',
    'reset_password_success' => 'A sua palavra-passe foi redefinida com sucesso.',
    'email_reset_subject' => 'Redefina a sua palavra-passe de :appName',
    'email_reset_text' => 'Você recebeu este e-mail pois recebemos uma solicitação de redefinição de senha para a sua conta.',
    'email_reset_not_requested' => 'Caso não tenha sido você a solicitar a redefinição de senha, ignore este e-mail.',

    // Email Confirmation
    'email_confirm_subject' => 'Confirme o seu endereço de e-mail para :appName',
    'email_confirm_greeting' => 'Obrigado por se registar em :appName!',
    'email_confirm_text' => 'Por favor, confirme o seu endereço de e-mail ao carregar no botão abaixo:',
    'email_confirm_action' => 'Confirmar E-mail',
    'email_confirm_send_error' => 'A confirmação do endereço de e-mail é requerida, mas o sistema não pôde enviar a mensagem. Por favor, entre em contacto com o administrador para se certificar que o serviço de envio de e-mails está corretamente configurado.',
    'email_confirm_success' => 'O seu endereço de e-mail foi confirmado!',
    'email_confirm_resent' => 'E-mail de confirmação reenviado. Por favor, verifique a sua caixa de entrada.',

    'email_not_confirmed' => 'Endereço de E-mail Não Confirmado',
    'email_not_confirmed_text' => 'O seu endereço de e-mail ainda não foi confirmado.',
    'email_not_confirmed_click_link' => 'Por favor, carregue no link que se encontra no e-mail que lhe foi enviado após o seu registo.',
    'email_not_confirmed_resend' => 'Caso não encontre o e-mail poderá reenviar a confirmação utilizando o formulário abaixo.',
    'email_not_confirmed_resend_button' => 'Reenviar o E-mail de Confirmação',

    // User Invite
    'user_invite_email_subject' => 'Você recebeu um convite para se juntar a :appName!',
    'user_invite_email_greeting' => 'Uma conta foi criada para si em :appName.',
    'user_invite_email_text' => 'Carregue no botão abaixo para definir uma palavra-passe de conta e obter acesso:',
    'user_invite_email_action' => 'Defina a Palavra-passe da Conta',
    'user_invite_page_welcome' => 'Bem-vindo(a) a :appName!',
    'user_invite_page_text' => 'Para finalizar a sua conta e obter acesso, precisa de definir uma senha que será utilizada para efetuar login em :appName em visitas futuras.',
    'user_invite_page_confirm_button' => 'Confirmar Palavra-Passe',
    'user_invite_success' => 'Palavra-passe definida, tem agora acesso a :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configurar autenticação de múltiplos fatores',
    'mfa_setup_desc' => 'Configure a autenticação multi-fatores como uma camada extra de segurança para sua conta de utilizador.',
    'mfa_setup_configured' => 'Já configurado',
    'mfa_setup_reconfigure' => 'Reconfigurar',
    'mfa_setup_remove_confirmation' => 'Tem a certeza que deseja remover este método de autenticação de múltiplos fatores?',
    'mfa_setup_action' => 'Configuração',
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
