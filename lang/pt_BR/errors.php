<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Você não tem permissão para acessar a página requerida.',
    'permissionJson' => 'Você não tem permissão para realizar a ação requerida.',

    // Auth
    'error_user_exists_different_creds' => 'Um usuário com o e-mail :email já existe mas com credenciais diferentes.',
    'email_already_confirmed' => 'E-mail já foi confirmado. Tente efetuar o login.',
    'email_confirmation_invalid' => 'Esse token de confirmação não é válido ou já foi utilizado. Por favor, tente cadastrar-se novamente.',
    'email_confirmation_expired' => 'O token de confirmação já expirou. Um novo e-mail foi enviado.',
    'email_confirmation_awaiting' => 'O endereço de e-mail da conta em uso precisa ser confirmado',
    'ldap_fail_anonymous' => 'O acesso LDAP falhou ao tentar usar o anonymous bind',
    'ldap_fail_authed' => 'O acesso LDAP falhou ao tentar os detalhes do dn e senha fornecidos',
    'ldap_extension_not_installed' => 'A extensão LDAP PHP não está instalada',
    'ldap_cannot_connect' => 'Não foi possível conectar ao servidor LDAP. Conexão inicial falhou',
    'saml_already_logged_in' => 'Login já efetuado',
    'saml_user_not_registered' => 'O usuário :name não está cadastrado e o cadastro automático está desativado',
    'saml_no_email_address' => 'Não foi possível encontrar um endereço de e-mail para este usuário nos dados providos pelo sistema de autenticação externa',
    'saml_invalid_response_id' => 'A requisição do sistema de autenticação externa não foi reconhecia por um processo iniciado por esta aplicação. Após o login, navegar para o caminho anterior pode causar um problema.',
    'saml_fail_authed' => 'Login utilizando :system falhou. Sistema não forneceu autorização bem sucedida',
    'oidc_already_logged_in' => 'Já está logado',
    'oidc_user_not_registered' => 'O usuário :name não está registrado e o registro automático está desativado',
    'oidc_no_email_address' => 'Não foi possível encontrar um endereço de e-mail para este usuário, nos dados fornecidos pelo sistema de autenticação externa',
    'oidc_fail_authed' => 'Login usando :system falhou, o sistema não forneceu autorização com sucesso',
    'social_no_action_defined' => 'Nenhuma ação definida',
    'social_login_bad_response' => "Erro recebido durante o login :socialAccount: \n:error",
    'social_account_in_use' => 'Essa conta :socialAccount já está em uso. Por favor, tente entrar utilizando a opção :socialAccount.',
    'social_account_email_in_use' => 'O e-mail :email já está em uso. Se você já tem uma conta você poderá se conectar a conta :socialAccount a partir das configurações de seu perfil.',
    'social_account_existing' => 'Essa conta :socialAccount já está vinculada a esse perfil.',
    'social_account_already_used_existing' => 'Essa conta :socialAccount já está sendo utilizada por outro usuário.',
    'social_account_not_used' => 'Essa conta :socialAccount não está vinculada a nenhum usuário. Por favor vincule a conta nas suas configurações de perfil. ',
    'social_account_register_instructions' => 'Se você não tem uma conta, você poderá se cadastrar usando a opção :socialAccount.',
    'social_driver_not_found' => 'Social driver não encontrado',
    'social_driver_not_configured' => 'Seus parâmetros socials de :socialAccount não estão configurados corretamente.',
    'invite_token_expired' => 'Esse link de convite expirou. Alternativamente, você pode tentar redefinir a senha da sua conta.',

    // System
    'path_not_writable' => 'O caminho de destino (:filePath) de upload de arquivo não possui permissão de escrita. Certifique-se que ele possui direitos de escrita no servidor.',
    'cannot_get_image_from_url' => 'Não foi possível obter a imagem a partir de :url',
    'cannot_create_thumbs' => 'O servidor não pôde criar as miniaturas de imagem. Por favor, verifique se a extensão GD PHP está instalada.',
    'server_upload_limit' => 'O servidor não permite o upload de arquivos com esse tamanho. Por favor, tente fazer o upload de arquivos de menor tamanho.',
    'uploaded'  => 'O servidor não permite o upload de arquivos com esse tamanho. Por favor, tente fazer o upload de arquivos de menor tamanho.',

    // Drawing & Images
    'image_upload_error' => 'Um erro aconteceu enquanto o servidor tentava efetuar o upload da imagem',
    'image_upload_type_error' => 'O tipo de imagem que está sendo enviada é inválido',
    'image_upload_replace_type' => 'Image file replacements must be of the same type',
    'drawing_data_not_found' => 'Dados de desenho não puderam ser carregados. Talvez o arquivo de desenho não exista mais ou você não tenha permissão para acessá-lo.',

    // Attachments
    'attachment_not_found' => 'Anexo não encontrado',
    'attachment_upload_error' => 'Um erro ocorreu ao efetuar o upload do arquivo anexado',

    // Pages
    'page_draft_autosave_fail' => 'Falha ao tentar salvar o rascunho. Certifique-se que a conexão de internet está funcional antes de tentar salvar essa página',
    'page_draft_delete_fail' => 'Failed to delete page draft and fetch current page saved content',
    'page_custom_home_deletion' => 'Não é possível excluir uma página que está definida como página inicial',

    // Entities
    'entity_not_found' => 'Entidade não encontrada',
    'bookshelf_not_found' => 'Prateleira não encontrada',
    'book_not_found' => 'Livro não encontrado',
    'page_not_found' => 'Página não encontrada',
    'chapter_not_found' => 'Capítulo não encontrado',
    'selected_book_not_found' => 'O livro selecionado não foi encontrado',
    'selected_book_chapter_not_found' => 'O Livro ou Capítulo selecionado não foi encontrado',
    'guests_cannot_save_drafts' => 'Convidados não podem salvar rascunhos',

    // Users
    'users_cannot_delete_only_admin' => 'Você não pode excluir o único admin',
    'users_cannot_delete_guest' => 'Você não pode excluir o usuário convidado',

    // Roles
    'role_cannot_be_edited' => 'Esse cargo não pode ser editado',
    'role_system_cannot_be_deleted' => 'Esse cargo é um cargo do sistema e não pode ser excluído',
    'role_registration_default_cannot_delete' => 'Esse cargo não poderá se excluído enquanto estiver registrado como o cargo padrão',
    'role_cannot_remove_only_admin' => 'Este usuário é o único usuário vinculado ao cargo de administrador. Atribua o cargo de administrador a outro usuário antes de tentar removê-lo aqui.',

    // Comments
    'comment_list' => 'Ocorreu um erro ao buscar os comentários.',
    'cannot_add_comment_to_draft' => 'Você não pode adicionar comentários a um rascunho.',
    'comment_add' => 'Ocorreu um erro ao adicionar o comentário.',
    'comment_delete' => 'Ocorreu um erro ao excluir o comentário.',
    'empty_comment' => 'Não é possível adicionar um comentário vazio.',

    // Error pages
    '404_page_not_found' => 'Página Não Encontrada',
    'sorry_page_not_found' => 'Desculpe, a página que você está procurando não pôde ser encontrada.',
    'sorry_page_not_found_permission_warning' => 'Se você esperava que esta página existisse, talvez você não tenha permissão para visualizá-la.',
    'image_not_found' => 'Imagem não encontrada',
    'image_not_found_subtitle' => 'Desculpe, o arquivo de imagem que você estava procurando não pôde ser encontrado.',
    'image_not_found_details' => 'Se você esperava que esta imagem existisse, ela pode ter sido excluída.',
    'return_home' => 'Retornar à página inicial',
    'error_occurred' => 'Ocorreu um Erro',
    'app_down' => ':appName está fora do ar no momento',
    'back_soon' => 'Voltaremos em breve.',

    // API errors
    'api_no_authorization_found' => 'Nenhum token de autorização encontrado na requisição',
    'api_bad_authorization_format' => 'Um token de autorização foi encontrado na requisição, mas o formato parece incorreto',
    'api_user_token_not_found' => 'Nenhum token de API correspondente foi encontrado para o token de autorização fornecido',
    'api_incorrect_token_secret' => 'O segredo fornecido para o token de API usado está incorreto',
    'api_user_no_api_permission' => 'O proprietário do token de API utilizado não tem permissão para fazer requisições de API',
    'api_user_token_expired' => 'O token de autenticação expirou',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Erro encontrado ao enviar um e-mail de teste:',

];
