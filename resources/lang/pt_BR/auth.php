<?php
/**
 * Authentication Language Lines
 * The following language lines are used during authentication for various
 * messages that we need to display to the user.
 */
return [

    'failed' => 'As credenciais fornecidas não puderam ser validadas em nossos registros..',
    'throttle' => 'Muitas tentativas de login. Por favor, tente novamente em :seconds segundos.',

    // Login & Register
    'sign_up' => 'Registrar-se',
    'log_in' => 'Entrar',
    'log_in_with' => 'Entrar com :socialDriver',
    'sign_up_with' => 'Registrar com :socialDriver',
    'logout' => 'Sair',

    'name' => 'Nome',
    'username' => 'Nome de Usuário',
    'email' => 'E-mail',
    'password' => 'Senha',
    'password_confirm' => 'Confirmar Senha',
    'password_hint' => 'Senha deverá ser maior que 7 caracteres',
    'forgot_password' => 'Esqueceu a senha?',
    'remember_me' => 'Lembrar de mim',
    'ldap_email_hint' => 'Por favor, digite um e-mail para essa conta.',
    'create_account' => 'Criar conta',
    'already_have_account' => 'Você já possui uma conta?',
    'dont_have_account' => 'Não possui uma conta?',
    'social_login' => 'Login social',
    'social_registration' => 'Registro social',
    'social_registration_text' => 'Registre e entre usando outro serviço.',

    'register_thanks' => 'Obrigado por efetuar o registro!',
    'register_confirm' => 'Por favor, verifique seu e-mail e clique no botão de confirmação para acessar :appName.',
    'registrations_disabled' => 'Registros estão temporariamente desabilitados',
    'registration_email_domain_invalid' => 'O domínio de e-mail usado não tem acesso permitido a essa aplicação',
    'register_success' => 'Obrigado por se registrar! Você agora encontra-se registrado e logado..',


    // Password Reset
    'reset_password' => 'Resetar senha',
    'reset_password_send_instructions' => 'Digite seu e-mail abaixo e o sistema enviará uma mensagem com o link de reset de senha.',
    'reset_password_send_button' => 'Enviar o link de reset de senha',
    'reset_password_sent_success' => 'Um link de reset de senha foi enviado para :email.',
    'reset_password_success' => 'Sua senha foi resetada com sucesso.',
    'email_reset_subject' => 'Resetar a senha de :appName',
    'email_reset_text' => 'Você recebeu esse e-mail pois recebemos uma solicitação de reset de senha para sua conta.',
    'email_reset_not_requested' => 'Caso não tenha sido você a solicitar o reset de senha, ignore esse e-mail.',


    // Email Confirmation
    'email_confirm_subject' => 'Confirme seu e-mail para :appName',
    'email_confirm_greeting' => 'Obrigado por se registrar em :appName!',
    'email_confirm_text' => 'Por favor, confirme seu endereço de e-mail clicando no botão abaixo:',
    'email_confirm_action' => 'Confirmar E-mail',
    'email_confirm_send_error' => 'E-mail de confirmação é requerido, mas o sistema não pôde enviar a mensagem. Por favor, entre em contato com o admin para se certificar que o serviço de envio de e-mails está corretamente configurado.',
    'email_confirm_success' => 'Seu e-mail foi confirmado!',
    'email_confirm_resent' => 'E-mail de confirmação reenviado. Por favor, cheque sua caixa postal.',

    'email_not_confirmed' => 'Endereço de e-mail não foi confirmado',
    'email_not_confirmed_text' => 'Seu endereço de e-mail ainda não foi confirmado.',
    'email_not_confirmed_click_link' => 'Por favor, clique no link no e-mail que foi enviado após o registro.',
    'email_not_confirmed_resend' => 'Caso não encontre o e-mail você poderá reenviar a confirmação usando o formulário abaixo.',
    'email_not_confirmed_resend_button' => 'Reenviar o e-mail de confirmação',

    // User Invite
    'user_invite_email_subject' => 'You have been invited to join :appName!',
    'user_invite_email_greeting' => 'An account has been created for you on :appName.',
    'user_invite_email_text' => 'Click the button below to set an account password and gain access:',
    'user_invite_email_action' => 'Set Account Password',
    'user_invite_page_welcome' => 'Welcome to :appName!',
    'user_invite_page_text' => 'To finalise your account and gain access you need to set a password which will be used to log-in to :appName on future visits.',
    'user_invite_page_confirm_button' => 'Confirm Password',
    'user_invite_success' => 'Password set, you now have access to :appName!'
];