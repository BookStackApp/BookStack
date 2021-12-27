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
    'password_hint' => 'Deve conter pelo menos 8 caracteres',
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
    'email_confirm_success' => 'Seu e-mail foi confirmado! Agora você pode de entrar usando este endereço de e-mail.',
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
    'user_invite_success_login' => 'Senha definida, agora você pode fazer login usando sua senha para acessar :appName!',

    // Multi-factor Authentication
    'mfa_setup' => 'Configurar autenticação multi-fator',
    'mfa_setup_desc' => 'A autenticação multi-fator adiciona outra camada de segurança à sua conta.',
    'mfa_setup_configured' => 'Configurado',
    'mfa_setup_reconfigure' => 'Reconfigurar',
    'mfa_setup_remove_confirmation' => 'Você tem certeza que deseja remover o método de autenticação de vários fatores?',
    'mfa_setup_action' => 'Configurações',
    'mfa_backup_codes_usage_limit_warning' => 'Você tem menos de 5 códigos de backup restantes, Por favor, gere e armazene um novo conjunto antes de esgotar suas opções de códigos de backup para evitar estar bloqueado para fora da sua conta.',
    'mfa_option_totp_title' => 'Aplicativo Móvel',
    'mfa_option_totp_desc' => 'Para usar a autenticação multi-fator, você precisará de um aplicativo móvel que suporte TOTP como o Google Authenticator, Authy ou o Microsoft Authenticator.',
    'mfa_option_backup_codes_title' => 'Códigos de backup',
    'mfa_option_backup_codes_desc' => 'Armazene com segurança um conjunto de códigos de backup únicos que você pode inserir para verificar sua identidade.',
    'mfa_gen_confirm_and_enable' => 'Confirmar e habilitar',
    'mfa_gen_backup_codes_title' => 'Configuração dos Códigos de Backup',
    'mfa_gen_backup_codes_desc' => 'Armazene a lista de códigos abaixo em um lugar seguro. Ao acessar o sistema você poderá usar um dos códigos como segundo mecanismo de autenticação.',
    'mfa_gen_backup_codes_download' => 'Baixar códigos',
    'mfa_gen_backup_codes_usage_warning' => 'Cada código só poderá ser usado uma vez',
    'mfa_gen_totp_title' => 'Configuração de Aplicativos Móveis',
    'mfa_gen_totp_desc' => 'Para usar a autenticação multi-fator, você precisará de um aplicativo móvel que suporte TOTP como o Google Authenticator, Authy ou o Microsoft Authenticator.',
    'mfa_gen_totp_scan' => 'Leia o código QR abaixo usando o aplicativo de autenticação de sua preferência para começar.',
    'mfa_gen_totp_verify_setup' => 'Verificar configuração',
    'mfa_gen_totp_verify_setup_desc' => 'Verifique se tudo está funcionando digitando um código, gerado dentro do seu aplicativo de autenticação, na caixa de entrada abaixo:',
    'mfa_gen_totp_provide_code_here' => 'Insira o código gerado pelo aplicativo aqui',
    'mfa_verify_access' => 'Verificar Acesso',
    'mfa_verify_access_desc' => 'Sua conta de usuário requer que você confirme sua identidade por meio de um nível adicional de verificação antes de conceder o acesso. Verifique o uso de um dos métodos configurados para continuar.',
    'mfa_verify_no_methods' => 'Nenhum método configurado',
    'mfa_verify_no_methods_desc' => 'Nenhum método de autenticação multi-fator foi encontrado em sua conta. Você precisará configurar pelo menos um método antes de ter acesso.',
    'mfa_verify_use_totp' => 'Verificar usando um aplicativo móvel',
    'mfa_verify_use_backup_codes' => 'Verificar usando um código de backup',
    'mfa_verify_backup_code' => 'Código de backup',
    'mfa_verify_backup_code_desc' => 'Insira um dos seus códigos de backup restantes abaixo:',
    'mfa_verify_backup_code_enter_here' => 'Digite o código de backup',
    'mfa_verify_totp_desc' => 'Digite o código, gerado através do seu aplicativo móvel, abaixo:',
    'mfa_setup_login_notification' => 'Método de multi-fatores configurado, por favor faça login novamente usando o método configurado.',
];
