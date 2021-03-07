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
    'user_invite_success' => 'Palavra-passe definida, tem agora acesso a :appName!'
];