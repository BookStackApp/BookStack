<?php
/**
 * Activity text strings.
 * Is used for all the text within activity logs & notifications.
 */
return [

    // Pages
    'page_create'                 => 'criou a página',
    'page_create_notification'    => 'Página criada com sucesso',
    'page_update'                 => 'página atualizada',
    'page_update_notification'    => 'Página atualizada com sucesso.',
    'page_delete'                 => 'página eliminada',
    'page_delete_notification'    => 'Página excluída com sucesso.',
    'page_restore'                => 'página restaurada',
    'page_restore_notification'   => 'Página restaurada com sucesso',
    'page_move'                   => 'página movida',
    'page_move_notification'      => 'Página movida com sucesso',

    // Chapters
    'chapter_create'              => 'capítulo criado',
    'chapter_create_notification' => 'Capítulo criado com sucesso',
    'chapter_update'              => 'capítulo atualizado',
    'chapter_update_notification' => 'Capítulo atualizado com sucesso',
    'chapter_delete'              => 'capítulo excluído',
    'chapter_delete_notification' => 'Capítulo excluído com sucesso',
    'chapter_move'                => 'capítulo movido',
    'chapter_move_notification' => 'Capítulo movido com sucesso',

    // Books
    'book_create'                 => 'livro criado',
    'book_create_notification'    => 'Livro criado com sucesso',
    'book_create_from_chapter'              => 'capítulo convertido para livro',
    'book_create_from_chapter_notification' => 'Capítulo convertido em livro com sucesso',
    'book_update'                 => 'livro atualizado',
    'book_update_notification'    => 'Livro atualizado com sucesso',
    'book_delete'                 => 'livro eliminado',
    'book_delete_notification'    => 'Livro eliminado com sucesso',
    'book_sort'                   => 'livro ordenado',
    'book_sort_notification'      => 'Livro reordenado com sucesso',

    // Bookshelves
    'bookshelf_create'            => 'estante criada',
    'bookshelf_create_notification'    => 'Estante criada com sucesso',
    'bookshelf_create_from_book'    => 'livro convertido para estante',
    'bookshelf_create_from_book_notification'    => 'Livro convertido em prateleira com sucesso',
    'bookshelf_update'                 => 'estante atualizada',
    'bookshelf_update_notification'    => 'Estante atualizada com sucesso',
    'bookshelf_delete'                 => 'excluiu a estante',
    'bookshelf_delete_notification'    => 'Estante eliminada com sucesso',

    // Revisions
    'revision_restore' => 'restaurou a revisão',
    'revision_delete' => 'eliminou a revisão',
    'revision_delete_notification' => 'Revisão eliminada com sucesso',

    // Favourites
    'favourite_add_notification' => '":name" foi adicionado aos seus favoritos',
    'favourite_remove_notification' => '":name" foi removido dos seus favoritos',

    // Watching
    'watch_update_level_notification' => 'Ver preferências atualizadas com sucesso',

    // Auth
    'auth_login' => 'iniciou sessão',
    'auth_register' => 'registado como novo utilizador',
    'auth_password_reset_request' => 'pediu a redefinição da palavra-passe',
    'auth_password_reset_update' => 'redifiniu palavra-passe do utilizador',
    'mfa_setup_method' => 'configurou método de autenticação multifatorial',
    'mfa_setup_method_notification' => 'Método de autenticação multifatorial configurado com sucesso',
    'mfa_remove_method' => 'removeu método de autenticação multifatorial',
    'mfa_remove_method_notification' => 'Método de autenticação multifatorial removido com sucesso',

    // Settings
    'settings_update' => 'atualizou as configurações',
    'settings_update_notification' => 'Configurações atualizadas com sucesso',
    'maintenance_action_run' => 'executou ação de manutenção',

    // Webhooks
    'webhook_create' => 'criou webhook',
    'webhook_create_notification' => 'Webhook criado com sucesso',
    'webhook_update' => 'atualizou webhook',
    'webhook_update_notification' => 'Webhook criado com sucesso',
    'webhook_delete' => 'eliminou webhook',
    'webhook_delete_notification' => 'Webhook criado com sucesso',

    // Imports
    'import_create' => 'criou importação',
    'import_create_notification' => 'Importação carregada com sucesso',
    'import_run' => 'atualizou importação',
    'import_run_notification' => 'Conteúdo importado com sucesso',
    'import_delete' => 'apagou importação',
    'import_delete_notification' => 'Importação eliminada com sucesso',

    // Users
    'user_create' => 'ciou utilizador',
    'user_create_notification' => 'Utilizador criado com sucesso',
    'user_update' => 'atualizou utilizador',
    'user_update_notification' => 'Utilizador atualizado com sucesso',
    'user_delete' => 'eliminou utilizador',
    'user_delete_notification' => 'Utilizador removido com sucesso',
    'user_mfa_reset' => 'reiniciou método de autenticação multifatorial para utilizador',
    'user_mfa_reset_notification' => 'Reinicialização dos métodos de autenticação multifatorial',

    // API Tokens
    'api_token_create' => 'token API criado',
    'api_token_create_notification' => 'API token criado com sucesso',
    'api_token_update' => 'API token atualizado',
    'api_token_update_notification' => 'API token atualizado com sucesso',
    'api_token_delete' => 'API token apagado',
    'api_token_delete_notification' => 'API token atualizado com sucesso',

    // Roles
    'role_create' => 'criou papel',
    'role_create_notification' => 'Papel criado com sucesso',
    'role_update' => 'atualizou papel',
    'role_update_notification' => 'Papel atualizado com sucesso',
    'role_delete' => 'eliminou papel',
    'role_delete_notification' => 'Papel excluído com sucesso',

    // Recycle Bin
    'recycle_bin_empty' => 'esvaziou a reciclagem',
    'recycle_bin_restore' => 'restaurou da reciclagem',
    'recycle_bin_destroy' => 'removeu da reciclagem',

    // Comments
    'commented_on'                => 'comentado a',
    'comment_create'              => 'adicionou comentário',
    'comment_update'              => 'atualizou comentário',
    'comment_delete'              => 'eliminou comentário',

    // Sort Rules
    'sort_rule_create' => 'criou regra de ordenação',
    'sort_rule_create_notification' => 'Regra de ordenação criada com sucesso',
    'sort_rule_update' => 'atualizou regra de ordenação',
    'sort_rule_update_notification' => 'Regra de ordenação atualizada com sucesso',
    'sort_rule_delete' => 'apagou regra de ordenação',
    'sort_rule_delete_notification' => 'Regra de ordenação apagada com sucesso',

    // Other
    'permissions_update'          => 'atualizou permissões',
];
