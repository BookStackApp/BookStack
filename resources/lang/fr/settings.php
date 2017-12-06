<?php

return [

    /**
     * Settings text strings
     * Contains all text strings used in the general settings sections of BookStack
     * including users and roles.
     */

    'settings' => 'Préférences',
    'settings_save' => 'Enregistrer les préférences',
    'settings_save_success' => 'Préférences enregistrées',

    /**
     * App settings
     */

    'app_settings' => 'Préférences de l\'application',
    'app_name' => 'Nom de l\'application',
    'app_name_desc' => 'Ce nom est affiché dans l\'en-tête et les e-mails.',
    'app_name_header' => 'Afficher le nom dans l\'en-tête ?',
    'app_public_viewing' => 'Accepter le visionnage public des pages ?',
    'app_secure_images' => 'Activer l\'ajout d\'image sécurisé ?',
    'app_secure_images_desc' => 'Pour des questions de performances, toutes les images sont publiques. Cette option ajoute une chaîne aléatoire difficile à deviner dans les URLs des images.',
    'app_editor' => 'Editeur des pages',
    'app_editor_desc' => 'Sélectionnez l\'éditeur qui sera utilisé pour modifier les pages.',
    'app_custom_html' => 'HTML personnalisé dans l\'en-tête',
    'app_custom_html_desc' => 'Le contenu inséré ici sera ajouté en bas de la balise <head> de toutes les pages. Vous pouvez l\'utiliser pour ajouter du CSS personnalisé ou un tracker analytique.',
    'app_logo' => 'Logo de l\'Application',
    'app_logo_desc' => 'Cette image doit faire 43px de hauteur. <br>Les images plus larges seront réduites.',
    'app_primary_color' => 'Couleur principale de l\'application',
    'app_primary_color_desc' => 'Cela devrait être une valeur hexadécimale. <br>Laisser vide pour rétablir la couleur par défaut.',

    /**
     * Registration settings
     */

    'reg_settings' => 'Préférence pour l\'inscription',
    'reg_allow' => 'Accepter l\'inscription ?',
    'reg_default_role' => 'Rôle par défaut lors de l\'inscription',
    'reg_confirm_email' => 'Obliger la confirmation par e-mail ?',
    'reg_confirm_email_desc' => 'Si la restriction de domaine est activée, la confirmation sera automatiquement obligatoire et cette valeur sera ignorée.',
    'reg_confirm_restrict_domain' => 'Restreindre l\'inscription à un domaine',
    'reg_confirm_restrict_domain_desc' => 'Entrez une liste de domaines acceptés lors de l\'inscription, séparés par une virgule. Les utilisateur recevront un e-mail de confirmation à cette adresse. <br> Les utilisateurs pourront changer leur adresse après inscription s\'ils le souhaitent.',
    'reg_confirm_restrict_domain_placeholder' => 'Aucune restriction en place',

    /**
     * Role settings
     */

    'roles' => 'Rôles',
    'role_user_roles' => 'Rôles des utilisateurs',
    'role_create' => 'Créer un nouveau rôle',
    'role_create_success' => 'Rôle créé avec succès',
    'role_delete' => 'Supprimer le rôle',
    'role_delete_confirm' => 'Ceci va supprimer le rôle \':roleName\'.',
    'role_delete_users_assigned' => 'Ce rôle a :userCount utilisateurs assignés. Vous pouvez choisir un rôle de remplacement pour ces utilisateurs.',
    'role_delete_no_migration' => "Ne pas assigner de nouveau rôle",
    'role_delete_sure' => 'Êtes vous sûr(e) de vouloir supprimer ce rôle ?',
    'role_delete_success' => 'Le rôle a été supprimé avec succès',
    'role_edit' => 'Modifier le rôle',
    'role_details' => 'Détails du rôle',
    'role_name' => 'Nom du rôle',
    'role_desc' => 'Courte description du rôle',
    'role_system' => 'Permissions système',
    'role_manage_users' => 'Gérer les utilisateurs',
    'role_manage_roles' => 'Gérer les rôles et permissions',
    'role_manage_entity_permissions' => 'Gérer les permissions sur les livres, chapitres et pages',
    'role_manage_own_entity_permissions' => 'Gérer les permissions de ses propres livres, chapitres, et pages',
    'role_manage_settings' => 'Gérer les préférences de l\'application',
    'role_asset' => 'Asset Permissions',
    'role_asset_desc' => 'These permissions control default access to the assets within the system. Permissions on Books, Chapters and Pages will override these permissions.',
    'role_all' => 'Tous',
    'role_own' => 'Propres',
    'role_controlled_by_asset' => 'Controlled by the asset they are uploaded to',
    'role_save' => 'Enregistrer le rôle',
    'role_update_success' => 'Rôle mis à jour avec succès',
    'role_users' => 'Utilisateurs ayant ce rôle',
    'role_users_none' => 'Aucun utilisateur avec ce rôle actuellement',

    /**
     * Users
     */

    'users' => 'Utilisateurs',
    'user_profile' => 'Profil d\'utilisateur',
    'users_add_new' => 'Ajouter un nouvel utilisateur',
    'users_search' => 'Chercher les utilisateurs',
    'users_role' => 'Rôles des utilisateurs',
    'users_external_auth_id' => 'Identifiant d\'authentification externe',
    'users_password_warning' => 'Remplissez ce fomulaire uniquement si vous souhaitez changer de mot de passe:',
    'users_system_public' => 'Cet utilisateur représente les invités visitant votre instance. Il est assigné automatiquement aux invités.',
    'users_books_view_type' => 'Disposition d\'affichage préférée pour les livres',
    'users_delete' => 'Supprimer un utilisateur',
    'users_delete_named' => 'Supprimer l\'utilisateur :userName',
    'users_delete_warning' => 'Ceci va supprimer \':userName\' du système.',
    'users_delete_confirm' => 'Êtes-vous sûr(e) de vouloir supprimer cet utilisateur ?',
    'users_delete_success' => 'Utilisateurs supprimés avec succès',
    'users_edit' => 'Modifier l\'utilisateur',
    'users_edit_profile' => 'Modifier le profil',
    'users_edit_success' => 'Utilisateur mis à jour avec succès',
    'users_avatar' => 'Avatar de l\'utilisateur',
    'users_avatar_desc' => 'Cette image doit être un carré d\'environ 256px.',
    'users_preferred_language' => 'Langue préférée',
    'users_social_accounts' => 'Comptes sociaux',
    'users_social_accounts_info' => 'Vous pouvez connecter des réseaux sociaux à votre compte pour vous connecter plus rapidement. Déconnecter un compte n\'enlèvera pas les accès autorisés précédemment sur votre compte de réseau social.',
    'users_social_connect' => 'Connecter le compte',
    'users_social_disconnect' => 'Déconnecter le compte',
    'users_social_connected' => 'Votre compte :socialAccount a été ajouté avec succès.',
    'users_social_disconnected' => 'Votre compte :socialAccount a été déconnecté avec succès',

];
