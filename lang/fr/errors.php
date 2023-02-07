<?php
/**
 * Text shown in error messaging.
 */
return [

    // Permissions
    'permission' => 'Vous n\'avez pas les droits pour accéder à cette page.',
    'permissionJson' => 'Vous n\'avez pas les droits pour exécuter cette action.',

    // Auth
    'error_user_exists_different_creds' => 'Un utilisateur avec l\'adresse :email existe déjà.',
    'email_already_confirmed' => 'Cet e-mail a déjà été validé, vous pouvez vous connecter.',
    'email_confirmation_invalid' => 'Cette confirmation est invalide. Veuillez essayer de vous inscrire à nouveau.',
    'email_confirmation_expired' => 'Le jeton de confirmation est périmé. Un nouvel e-mail vous a été envoyé.',
    'email_confirmation_awaiting' => 'L\'adresse e-mail du compte utilisé doit être confirmée',
    'ldap_fail_anonymous' => 'L\'accès LDAP anonyme n\'a pas abouti',
    'ldap_fail_authed' => 'L\'accès LDAP n\'a pas abouti avec cet utilisateur et ce mot de passe',
    'ldap_extension_not_installed' => 'L\'extension PHP LDAP n\'est pas installée',
    'ldap_cannot_connect' => 'Impossible de se connecter au serveur LDAP, la connexion initiale a échoué',
    'saml_already_logged_in' => 'Déjà connecté',
    'saml_user_not_registered' => 'L\'utilisateur :name n\'est pas enregistré et l\'enregistrement automatique est désactivé',
    'saml_no_email_address' => 'Impossible de trouver une adresse e-mail, pour cet utilisateur, dans les données fournies par le système d\'authentification externe',
    'saml_invalid_response_id' => 'La requête du système d\'authentification externe n\'est pas reconnue par un processus démarré par cette application. Naviguer après une connexion peut causer ce problème.',
    'saml_fail_authed' => 'Connexion avec :system échouée, le système n\'a pas fourni l\'autorisation réussie',
    'oidc_already_logged_in' => 'Déjà connecté',
    'oidc_user_not_registered' => 'L\'utilisateur :name n\'est pas enregistré et l\'enregistrement automatique est désactivé',
    'oidc_no_email_address' => 'Impossible de trouver une adresse e-mail pour cet utilisateur, dans les données fournies par le système d\'authentification externe',
    'oidc_fail_authed' => 'La connexion en utilisant :system a échoué, le système n\'a pas fourni d\'autorisation avec succès',
    'social_no_action_defined' => 'Pas d\'action définie',
    'social_login_bad_response' => "Erreur pendant la tentative de connexion à :socialAccount : \n:error",
    'social_account_in_use' => 'Ce compte :socialAccount est déjà utilisé. Essayez de vous connecter via :socialAccount.',
    'social_account_email_in_use' => 'L\'email :email est déjà utilisé. Si vous avez déjà un compte :socialAccount, vous pouvez le rattacher à votre profil existant.',
    'social_account_existing' => 'Ce compte :socialAccount est déjà rattaché à votre profil.',
    'social_account_already_used_existing' => 'Ce compte :socialAccount est déjà utilisé par un autre utilisateur.',
    'social_account_not_used' => 'Ce compte :socialAccount n\'est lié à aucun utilisateur. ',
    'social_account_register_instructions' => 'Si vous n\'avez pas encore de compte, vous pouvez en créer un avec l\'option :socialAccount.',
    'social_driver_not_found' => 'Pilote de compte de réseaux sociaux absent',
    'social_driver_not_configured' => 'Vos préférences pour le compte :socialAccount sont incorrectes.',
    'invite_token_expired' => 'Le lien de cette invitation a expiré. Vous pouvez essayer de réinitialiser votre mot de passe.',

    // System
    'path_not_writable' => 'Impossible d\'écrire dans :filePath. Assurez-vous d\'avoir les droits d\'écriture sur le serveur',
    'cannot_get_image_from_url' => 'Impossible de récupérer l\'image depuis :url',
    'cannot_create_thumbs' => 'Le serveur ne peut pas créer de miniature, vérifier que l\'extension PHP GD est installée.',
    'server_upload_limit' => 'La taille du fichier est trop grande.',
    'uploaded'  => 'Le serveur n\'autorise pas l\'envoi d\'un fichier de cette taille. Veuillez essayer avec une taille de fichier réduite.',
    'file_upload_timeout' => 'Le téléchargement du fichier a expiré.',

    // Drawing & Images
    'image_upload_error' => 'Une erreur est survenue pendant l\'envoi de l\'image',
    'image_upload_type_error' => 'Le format de l\'image envoyée n\'est pas valide',
    'drawing_data_not_found' => 'Les données de dessin n\'ont pas pu être chargées. Le fichier de dessin peut ne plus exister ou vous n\'avez pas la permission d\'y accéder.',

    // Attachments
    'attachment_not_found' => 'Fichier joint non trouvé',

    // Pages
    'page_draft_autosave_fail' => 'Le brouillon n\'a pas pu être enregistré. Vérifiez votre connexion internet',
    'page_custom_home_deletion' => 'Impossible de supprimer une page définie comme page d\'accueil',

    // Entities
    'entity_not_found' => 'Entité non trouvée',
    'bookshelf_not_found' => 'Étagère introuvable',
    'book_not_found' => 'Livre non trouvé',
    'page_not_found' => 'Page non trouvée',
    'chapter_not_found' => 'Chapitre non trouvé',
    'selected_book_not_found' => 'Ce livre n\'a pas été trouvé',
    'selected_book_chapter_not_found' => 'Ce livre ou chapitre n\'a pas été trouvé',
    'guests_cannot_save_drafts' => 'Les invités ne peuvent pas enregistrer de brouillons',

    // Users
    'users_cannot_delete_only_admin' => 'Vous ne pouvez pas supprimer le dernier administrateur',
    'users_cannot_delete_guest' => 'Vous ne pouvez pas supprimer l\'utilisateur invité',

    // Roles
    'role_cannot_be_edited' => 'Ce rôle ne peut pas être modifié',
    'role_system_cannot_be_deleted' => 'Ceci est un rôle du système et ne peut pas être supprimé',
    'role_registration_default_cannot_delete' => 'Ce rôle ne peut pas être supprimé tant qu\'il est le rôle par défaut',
    'role_cannot_remove_only_admin' => 'Ceci est le seul compte administrateur. Assignez un nouvel administrateur avant de le supprimer ici.',

    // Comments
    'comment_list' => 'Une erreur s\'est produite lors de la récupération des commentaires.',
    'cannot_add_comment_to_draft' => 'Vous ne pouvez pas ajouter de commentaires à un brouillon.',
    'comment_add' => 'Une erreur s\'est produite lors de l\'ajout du commentaire.',
    'comment_delete' => 'Une erreur s\'est produite lors de la suppression du commentaire.',
    'empty_comment' => 'Impossible d\'ajouter un commentaire vide.',

    // Error pages
    '404_page_not_found' => 'Page non trouvée',
    'sorry_page_not_found' => 'Désolé, cette page n\'a pas pu être trouvée.',
    'sorry_page_not_found_permission_warning' => 'Si cette page est censée exister, il se peut que vous n\'ayez pas l\'autorisation de la consulter.',
    'image_not_found' => 'Image non trouvée',
    'image_not_found_subtitle' => 'Désolé, l\'image que vous cherchez ne peut être trouvée.',
    'image_not_found_details' => 'Si cette image était censée exister, il se pourrait qu\'elle ait été supprimée.',
    'return_home' => 'Retour à l\'accueil',
    'error_occurred' => 'Une erreur est survenue',
    'app_down' => ':appName n\'est pas en service pour le moment',
    'back_soon' => 'Nous serons bientôt de retour.',

    // API errors
    'api_no_authorization_found' => 'Aucun jeton d\'autorisation trouvé pour la demande',
    'api_bad_authorization_format' => 'Un jeton d\'autorisation a été trouvé pour la requête, mais le format semble incorrect',
    'api_user_token_not_found' => 'Aucun jeton API correspondant n\'a été trouvé pour le jeton d\'autorisation fourni',
    'api_incorrect_token_secret' => 'Le secret fourni pour le jeton d\'API utilisé est incorrect',
    'api_user_no_api_permission' => 'Le propriétaire du jeton API utilisé n\'a pas la permission de passer des requêtes API',
    'api_user_token_expired' => 'Le jeton d\'autorisation utilisé a expiré',

    // Settings & Maintenance
    'maintenance_test_email_failure' => 'Erreur émise lors de l\'envoi d\'un e-mail de test :',

];
