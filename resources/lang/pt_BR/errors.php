<?php

return [

    /**
     * Error text strings.
     */

    // Permissions
    'permission' => 'Você não tem permissões para acessar a página requerida.',
    'permissionJson' => 'Você não tem permissão para realizar a ação requerida.',

    // Auth
    'error_user_exists_different_creds' => 'Um usuário com o e-mail :email já existe mas com credenciais diferentes.',
    'email_already_confirmed' => 'E-mail já foi confirmado. Tente efetuar o login.',
    'email_confirmation_invalid' => 'Esse token de confirmação não é válido ou já foi utilizado. Por favor, tente efetuar o registro novamente.',
    'email_confirmation_expired' => 'O token de confirmação já expirou. Um novo e-mail foi enviado.',
    'ldap_fail_anonymous' => 'O acesso LDAP falhou ao tentar usar o anonymous bind',
    'ldap_fail_authed' => 'O acesso LDAPfalou ao tentar os detalhes do dn e senha fornecidos',
    'ldap_extension_not_installed' => 'As extensões LDAP PHP não estão instaladas',
    'ldap_cannot_connect' => 'Não foi possível conectar ao servidor LDAP. Conexão inicial falhou',
    'social_no_action_defined' => 'Nenhuma ação definida',
    'social_account_in_use' => 'Essa conta :socialAccount já está em uso. Por favor, tente se logar usando a opção :socialAccount',
    'social_account_email_in_use' => 'O e-mail :email já está e muso. Se você já tem uma conta você poderá se conectar a conta :socialAccount a partir das configurações de seu perfil.',
    'social_account_existing' => 'Essa conta :socialAccount já está atrelada a esse perfil.',
    'social_account_already_used_existing' => 'Essa conta :socialAccount já está sendo usada por outro usuário.',
    'social_account_not_used' => 'Essa conta :socialAccount não está atrelada a nenhum usuário. Por favor, faça o link da conta com suas configurações de perfil. ',
    'social_account_register_instructions' => 'Se você não tem uma conta, você poderá fazer o registro usando a opção :socialAccount',
    'social_driver_not_found' => 'Social driver não encontrado',
    'social_driver_not_configured' => 'Seus parâmetros socials de :socialAccount não estão configurados corretamente.',

    // System
    'path_not_writable' => 'O caminho de destino (:filePath) de upload de arquivo não possui permissão de escrita. Certifique-se que ele possui direitos de escrita no servidor.',
    'cannot_get_image_from_url' => 'Não foi possivel capturar a imagem a partir de :url',
    'cannot_create_thumbs' => 'O servidor não pôde criar as miniaturas de imagem. Por favor, verifique se a extensão GD PHP está instalada.',
    'server_upload_limit' => 'O servidor não permite o upload de arquivos com esse tamanho. Por favor, tente fazer o upload de arquivos de menor tamanho.',
    'image_upload_error' => 'Um erro aconteceu enquanto o servidor tentava efetuar o upload da imagem',

    // Attachments
    'attachment_page_mismatch' => 'Erro de \'Page mismatch\' durante a atualização do anexo',

    // Pages
    'page_draft_autosave_fail' => 'Falou ao tentar salvar o rascunho. Certifique-se que a conexão de internet está funcional antes de tentar salvar essa página',
    'page_custom_home_deletion' => 'Não pode deletar uma página que está definida como página inicial',

    // Entities
    'entity_not_found' => 'Entidade não encontrada',
    'book_not_found' => 'Livro não encontrado',
    'page_not_found' => 'Página não encontrada',
    'chapter_not_found' => 'Capítulo não encontrado',
    'selected_book_not_found' => 'O livro selecionado não foi encontrado',
    'selected_book_chapter_not_found' => 'O Livro selecionado ou Capítulo não foi encontrado',
    'guests_cannot_save_drafts' => 'Convidados não podem salvar rascunhos',

    // Users
    'users_cannot_delete_only_admin' => 'Você não pode excluir o conteúdo, apenas o admin.',
    'users_cannot_delete_guest' => 'Você não pode excluir o usuário convidado',

    // Roles
    'role_cannot_be_edited' => 'Esse perfil não poed ser editado',
    'role_system_cannot_be_deleted' => 'Esse perfil é um perfil de sistema e não pode ser excluído',
    'role_registration_default_cannot_delete' => 'Esse perfil não poderá se excluído enquando estiver registrado como o perfil padrão',

    // comments
    'comment_list' => 'Ocorreu um erro ao buscar os comentários.',
    'cannot_add_comment_to_draft' => 'Você não pode adicionar comentários a um rascunho.',
    'comment_add' => 'Ocorreu um erro ao adicionar o comentário.',
    'comment_delete' => 'Ocorreu um erro ao excluir o comentário.',
    'empty_comment' => 'Não é possível adicionar um comentário vazio.',

    // Error pages
    '404_page_not_found' => 'Página não encontrada',
    'sorry_page_not_found' => 'Desculpe, a página que você está procurando não pôde ser encontrada.',
    'return_home' => 'Retornar à página principal',
    'error_occurred' => 'Um erro ocorreu',
    'app_down' => ':appName está fora do ar no momento',
    'back_soon' => 'Voltaremos em seguida.',
];