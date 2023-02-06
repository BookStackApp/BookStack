<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'Estas credenciais não coincidem com os nossos registos.',
    'throttle' => 'Demasiadas tentativas de acesso. Tente novamente em :seconds segundos.',

    // Login & Register
    'sign_up' => 'Registar',
    'log_in' => 'Iniciar sessão',
    'log_in_with' => 'Iniciar sessão com :socialDriver',
    'sign_up_with' => 'Criar conta com :socialDriver',
    'logout' => 'Terminar sessão',

    'name' => 'Nome',
    'username' => 'Nome de utilizador',
    'email' => 'E-mail',
    'password' => 'Palavra-passe',
    'password_confirm' => 'Confirmar Palavra-passe',
    'password_hint' => 'Deve ter no mínimo 8 caracteres',
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

    // Login auto-initiation
    'auto_init_starting' => 'Tentando inicar sessão',
    'auto_init_starting_desc' => 'Estamos a aceder ao seu sistema de autenticação para iniciar o processo de login. Se não houver progresso após 5 segundos você pode tentar clicar no link abaixo.',
    'auto_init_start_link' => 'Prosseguir com autenticação',

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
    'email_confirm_success' => 'O seu endereço de email foi confirmado! Neste momento já poderá entrar usando este endereço de email.',
    'email_confirm_resent' => 'E-mail de confirmação reenviado. Por favor, verifique a sua caixa de entrada.',
    'email_confirm_thanks' => 'Obrigado por confirmar!',
    'email_confirm_thanks_desc' => 'Por favor, aguarde um momento enquanto a sua confirmação é tratada. Se não for redirecionado após 3 segundos pressione "Continuar" para prosseguir.',

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
    'user_invite_success_login' => 'Palavra passe definida, agora poderá entrar usado a sua nova palavra passe para acessar :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configurar autenticação de múltiplos fatores',
    'mfa_setup_desc' => 'Configure a autenticação multi-fatores como uma camada extra de segurança para sua conta de utilizador.',
    'mfa_setup_configured' => 'Já configurado',
    'mfa_setup_reconfigure' => 'Reconfigurar',
    'mfa_setup_remove_confirmation' => 'Tem a certeza que deseja remover este método de autenticação de múltiplos fatores?',
    'mfa_setup_action' => 'Configuração',
    'mfa_backup_codes_usage_limit_warning' => 'Você tem menos de 5 códigos de backup restantes, Por favor, gere e armazene um novo conjunto antes de esgotar os códigos para evitar estar bloqueado para fora da sua conta.',
    'mfa_option_totp_title' => 'Aplicação móvel',
    'mfa_option_totp_desc' => 'Para usar a autenticação multi-fator, você precisará de um aplicativo móvel que suporte TOTP como o Autenticador do Google, Authy ou o autenticador Microsoft.',
    'mfa_option_backup_codes_title' => 'Códigos de Backup',
    'mfa_option_backup_codes_desc' => 'Armazene com segurança um conjunto de códigos de backup únicos que você pode inserir para verificar sua identidade.',
    'mfa_gen_confirm_and_enable' => 'Confirmar e ativar',
    'mfa_gen_backup_codes_title' => 'Configuração dos Códigos de Backup',
    'mfa_gen_backup_codes_desc' => 'Armazene a lista de códigos abaixo em um lugar seguro. Ao acessar o sistema você poderá usar um dos códigos como um segundo mecanismo de autenticação.',
    'mfa_gen_backup_codes_download' => 'Transferir códigos',
    'mfa_gen_backup_codes_usage_warning' => 'Cada código só pode ser usado uma vez',
    'mfa_gen_totp_title' => 'Configuração de aplicativo móvel',
    'mfa_gen_totp_desc' => 'Para usar a autenticação multi-fator, você precisará de um aplicativo móvel que suporte TOTP como o Autenticador do Google, Authy ou o autenticador Microsoft.',
    'mfa_gen_totp_scan' => 'Leia o código QR abaixo usando seu aplicativo de autenticação preferido para começar.',
    'mfa_gen_totp_verify_setup' => 'Verificar configuração',
    'mfa_gen_totp_verify_setup_desc' => 'Verifique se tudo está funcionando digitando um código, gerado dentro do seu aplicativo de autenticação, na caixa de entrada abaixo:',
    'mfa_gen_totp_provide_code_here' => 'Forneça o código gerado pelo aplicativo aqui',
    'mfa_verify_access' => 'Verificar Acesso',
    'mfa_verify_access_desc' => 'Sua conta de usuário requer que você confirme sua identidade por meio de um nível adicional de verificação antes de conceder o acesso. Verifique o uso de um dos métodos configurados para continuar.',
    'mfa_verify_no_methods' => 'Nenhum método configurado',
    'mfa_verify_no_methods_desc' => 'Nenhum método de autenticação de vários fatores foi encontrado para a sua conta. Você precisará configurar pelo menos um método antes de ganhar acesso.',
    'mfa_verify_use_totp' => 'Verificar usando um aplicativo móvel',
    'mfa_verify_use_backup_codes' => 'Verificar usando código de backup',
    'mfa_verify_backup_code' => 'Código de backup',
    'mfa_verify_backup_code_desc' => 'Insira um dos seus códigos de backup restantes abaixo:',
    'mfa_verify_backup_code_enter_here' => 'Insira o código de backup aqui',
    'mfa_verify_totp_desc' => 'Digite o código, gerado através do seu aplicativo móvel, abaixo:',
    'mfa_setup_login_notification' => 'Método de multi-fatores configurado, por favor faça login novamente usando o método configurado.',
];
